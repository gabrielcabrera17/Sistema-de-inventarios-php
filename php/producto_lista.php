<?php
    /* Si $pagina es mayor que 0, entonces $inicio tomará el valor de ($paginas * $registros) - $registros.
    Si $pagina no es mayor que 0, entonces $inicio tomará el valor de 0.
    En términos más generales, parece ser una lógica utilizada para calcular el valor de $inicio en función de la página actual ($pagina),
    el número total de páginas ($paginas), y la cantidad de registros por página ($registros). La fórmula se ajusta para que el valor de $inicio esté relacionado con la página actual y la cantidad total de registros en el conjunto de datos paginado.*/
    
    $inicio = ($pagina>0) ? (($pagina * $registros)-$registros) : 0;
	$tabla="";

    $campos="producto.producto_id,producto.producto_codigo,producto.producto_nombre,
    producto.producto_precio,producto.producto_stock,producto.producto_foto,
    categoria.categoria_nombre,usuario.usuario_nombre,usuario.usuario_apellido";

	if(isset($busqueda) && $busqueda!=""){

		$consulta_datos="SELECT $campos FROM producto INNER JOIN categoria ON
        producto.categoria_id=categoria.categoria_id INNER JOIN usuario ON
        producto.usuario_id=usuario.usuario_id WHERE producto.producto_codigo LIKE
        '%$busqueda%' OR producto.producto_nombre  LIKE '%$busqueda%' ORDER BY producto.producto_nombre ASC LIMIT $inicio,$registros";

		$consulta_total="SELECT COUNT(producto_id) FROM producto WHERE producto_codigo LIKE 
        '%$busqueda%' OR producto_nombre LIKE '%$busqueda%'";

	}/* en elseif consulta cuando listamos los productos por categoria*/elseif($categoria_id>0){
        $consulta_datos="SELECT $campos FROM producto INNER JOIN categoria ON
        producto.categoria_id=categoria.categoria_id INNER JOIN usuario ON
        producto.usuario_id=usuario.usuario_id WHERE producto.categoria_id='$categoria_id'
         ORDER BY producto.producto_nombre ASC LIMIT $inicio,$registros";

		$consulta_total="SELECT COUNT(producto_id) FROM producto WHERE 
        categoria_id='$categoria_id'";
    }else{

		$consulta_datos="SELECT $campos FROM producto INNER JOIN categoria ON
        producto.categoria_id=categoria.categoria_id INNER JOIN usuario ON
        producto.usuario_id=usuario.usuario_id ORDER BY producto.producto_nombre 
        ASC LIMIT $inicio,$registros";

		$consulta_total="SELECT COUNT(producto_id) FROM producto";
		
	}

    

    $conexion=conexion();
    $datos=$conexion->query($consulta_datos);
    //cuando se selecciona más de un registro se usa fetchAll();
    $datos=$datos->fetchAll();

    $total=$conexion->query($consulta_total);
    $total=(int) $total->fetchColumn();

    //Esta variable contendra el nùmero de paginas que debemos crear
    $Npaginas=ceil($total/$registros);

    

    if($total>=1 && $pagina<=$Npaginas){
        $contador=$inicio+1;
        $pag_inicio=$inicio+1;
        //En un array rows recorremos todos los datos
        foreach($datos as $rows){
            $tabla.='
            <article class="media">
                    <figure class="media-left">
                        <p class="image is-64x64">';
                        //si existe la imagen lo mostramos osino mostramos el default
                        if(is_file("./img/producto/".$rows['producto_foto'])){
                            $tabla.='<img src="./img/producto/'.$rows['producto_foto'].'">';

                        }else{
                            $tabla.='<img src="./img/producto/default.png">';
                        }
                 $tabla.='</p>
                        
                    </figure>
                    <div class="media-content">
                        <div class="content">
                            <p>
                                <strong>'.$contador.' - '.$rows['producto_nombre'].'</strong><br>
                                <strong>CODIGO:</strong> '.$rows['producto_codigo'].', 
                                <strong>PRECIO:</strong> '.$rows['producto_precio'].', 
                                <strong>STOCK:</strong> '.$rows['producto_stock'].', 
                                <strong>CATEGORIA:</strong> '.$rows['categoria_nombre'].', 
                                <strong>REGISTRADO POR:</strong> '.$rows['usuario_nombre'].' '.$rows['usuario_apellido'].'
                            </p>
                        </div>
                        <div class="has-text-right">
                            <a href="index.php?vista=product_img&product_id_up='.$rows['producto_id'].'" class="button is-link is-rounded is-small">Imagen</a>

                            <a href="index.php?vista=product_update&product_id_up='.$rows['producto_id'].'" class="button is-success is-rounded is-small">Actualizar</a>

                            <a href="'.$url.$pagina.'&product_id_del='.$rows['producto_id'].'" class="button is-danger is-rounded is-small">Eliminar</a>
                        </div>
                    </div>
                </article>


                <hr>

           

            ';
            $contador++;
        }
        //tendra el registro total por cada página
        $pag_final=$contador-1;
        
    }else{
        if($total>=1){
            $tabla.='
            <p class="has-text-centered">
                    <a href="'.$url.'1" class="button is-link is-rounded is-small mt-4 mb-4">
                        Haga clic acá para recargar el listado
                    </a>
            </p>
            ';
        }else{
            
          
            $tabla.='<p class="has-text-centered">No hay registros en el sistema</p>';

        }

    }
 

    

    if($total>=1 && $pagina<=$Npaginas){
        $tabla.='    <p class="has-text-right">Mostrando  <strong>'.$pag_inicio.'
        </strong> al <strong>'.$pag_final.'</strong> de un <strong>total de '.$total.'</strong></p>';

    }

    $conexion=null;
    echo $tabla;

    if($total>=1 && $pagina<=$Npaginas){
        // el 7 coloado es simplemente para omitir el error de los $botones
        echo paginador_tablas($pagina,$Npaginas,$url,7);
    }

