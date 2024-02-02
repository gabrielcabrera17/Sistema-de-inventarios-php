<?php
    
    require_once "main.php";

    //verificando si el producto existe
    $product_id=limpiar_cadena($_POST['img_del_id']);

    //Verificar si la categoria existe
    $check_producto=conexion();
    // selecciona cuando el input producto_id = $id
    $check_producto=$check_producto->query("SELECT * FROM  producto WHERE producto_id='$product_id'");

    if($check_producto->rowCount()==1){
       $datos=$check_producto->fetch();
    }else{
        echo '
        <div class="notification is-danger is-light">
            <strong>¡Ocurrio un error inesperado!</strong><br>
            La imagen del producto no existe en el sistema.
        </div>
    ';
    exit();
        
    }
    $check_producto=null;

    #Directorio de imagenes#
    //Aqui estableceremos la variable para la dirección de la imagne
    $img_dir="../img/producto/";
  
    chmod($img_dir,0777);

    //Una vez otorgado los permisos intentaremos eliminar la imagen
    if(is_file($img_dir.$datos['producto_foto'])){
        chmod($img_dir.$datos['producto_foto'],0777);
        //unlink funcion para eliminar un archivo
        if(!unlink($img_dir.$datos['producto_foto'])){
            echo '
            <div class="notification is-danger is-light">
                <strong>¡Ocurrio un error inesperado!</strong><br>
                Error, la imagen no se pudo eliminar del sistema.
            </div>
        ';
        exit();
        }
    }

    //Cambiamos en la base de datos el nombre de la imagen a una cadena vacia
        
    $actualizar_producto=conexion();
    //preparando consulta
    $actualizar_producto=$actualizar_producto->prepare("UPDATE producto SET producto_foto=:foto WHERE producto_id=:id");

    $marcadores = [
        ":foto"=>"",
        ":id"=>$product_id
       
    ];

    //para saber si la consulta se ejecuto con exito o no 
    if($actualizar_producto->execute($marcadores)){
        echo '
        <div class="notification is-info is-light">
            <strong>¡Imagen Eliminada!</strong><br>
            La imagen se elimino exitosamente.

            <p class="hast-text-centered pt-5 pb-5"> 
                <a href="index.php?vista=product_img&product_id_up='.$product_id.'" class="button is-link os-rounded"> Aceptar </a>
            </p>    
        </div>
    ';
    }else{
        echo '
        <div class="notification is-warning is-light">
            <strong>¡Imagen Eliminada!</strong><br>
            Ocurrieron algunos errores, sin embargo la imagen se elimino, pulse aceptar para visualizar los cambios.
            <p class="hast-text-centered pt-5 pb-5"> 
                <a href="index.php?vista=product_img&product_id_up='.$product_id.'" class="button is-link os-rounded"> Aceptar </a>
            </p>    
        </div>
    ';
    }

    $actualizar_producto=null;
