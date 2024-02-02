<?php
     require_once "main.php";

     //verificando si el producto existe
     $product_id=limpiar_cadena($_POST['img_up_id']);
 
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
    //Aqui estableceremos la variable para la dirección de la imagne
    $img_dir="../img/producto/";
    //comprobar si se ha enviado o no una imagen
    #comprobar si selecciono una imagen#
    //se le pasa el archivo en el primer corchete
    //En el segundo corchete se especifica que información se quiere obtener de la imagen
    if($_FILES['producto_foto']['name']==""||$_FILES['producto_foto']['size']==0){
        // si esta condición se cumple no se ha seleccionado ninguna imagen valida por el nombre vacio y el tamaño en 0
        echo '
        <div class="notification is-danger is-light">
            <strong>¡Ocurrio un error inesperado!</strong><br>
            No has seleccionado ninguna imagen valida.
        </div>
    ';
    exit();
    }

    //creando el directorio
    if(!file_exists($img_dir)){
        //si el directorio no existe devolvera true e intentaremos crearlo
        //mkdir es para crear un directorio, y el 0777 es el permiso y escritura que se le da
            if(!mkdir($img_dir,0777)){
                echo '
                    <div class="notification is-danger is-light">
                        <strong>¡Ocurrio un error inesperado!</strong><br>
                         No se pudo crear el directorio.
                    </div>
                ';
                exit();
    
            }
     }
    
     chmod($img_dir,0777);

     
    #Verificando formato de imagenes#
    // la función me_content_type verifica el tipo o el formato del archivo
    // temporal name o tmp es la ruta donde esta almacenado temporalmente el archivo, cuando se envia desde el formulario

    if(mime_content_type($_FILES['producto_foto']['tmp_name'])!="image/jpeg" && 
    mime_content_type($_FILES['producto_foto']['tmp_name'])!="image/png"){
        echo '
        <div class="notification is-danger is-light">
            <strong>¡Ocurrio un error inesperado!</strong><br>
             La imagen no coincido con el formato solicitado.
        </div>
    ';
    exit();

    }

    #Verificando el peso de la imagen#
    //Se transforma el peso en kb o kilobytes diviendolo por 1024
    if(($_FILES['producto_foto']['size']/1024)>3072){
        echo '
        <div class="notification is-danger is-light">
            <strong>¡Ocurrio un error inesperado!</strong><br>
             La imagen supero el peso permitido máximo de 3mb.
        </div>
    ';
    exit();
        
    }

    #Extensión de la imagen#
    switch(mime_content_type($_FILES['producto_foto']['tmp_name'])){
        case 'image/jpeg':
            $img_ext=".jpg";
        break;
        case 'image/png':
            $img_ext=".png";
        break;
    }

    //renombrar la imagen utilizando la función creada en main.php
    $img_nombre=renombrar_fotos($datos['producto_nombre']);
    //nombre final de la imagen
    $foto=$img_nombre.$img_ext;

    #Moviendo imagen al directorio #
    //move_uploaded_file se usa para mover la imagen al directorio en donde se especifica entre parentsis
    //primero lleva donde esta almacenado el archivo en el directorio temporal
    //Segundo va el directorio en donde se desea almacenar+el nombre final de la imagen
    //Esta función envia true si no lo mueve y false si lo mueve
    if(!move_uploaded_file($_FILES['producto_foto']['tmp_name'],$img_dir.$foto)){
        echo '
        <div class="notification is-danger is-light">
            <strong>¡Ocurrio un error inesperado!</strong><br>
             No se pudo cargar o subir la imagen al sistema en este momento.
        </div>
        ';
        exit();
    }

    //Eliminando la imagen anterior
    if(is_file($img_dir.$datos['producto_foto']) && $datos['producto_foto'] !=$foto){
        chmod($img_dir.$datos['producto_foto'],0777);
        unlink($img_dir.$datos['producto_foto']);
    }

    //Actualizar la bd para colocar la nueva imagen del producto
    $actualizar_producto=conexion();
    //preparando consulta
    $actualizar_producto=$actualizar_producto->prepare("UPDATE producto SET producto_foto=:foto WHERE producto_id=:id");

    $marcadores = [
        ":foto"=>$foto,
        ":id"=>$product_id
       
    ];

    //para saber si la consulta se ejecuto con exito o no 
    if($actualizar_producto->execute($marcadores)){
        echo '
        <div class="notification is-info is-light">
            <strong>¡Imagen Actualizada!</strong><br>
            La imagen actualizo con exito.

            <p class="hast-text-centered pt-5 pb-5"> 
                <a href="index.php?vista=product_img&product_id_up='.$product_id.'" class="button is-link os-rounded"> Aceptar </a>
            </p>    
        </div>
    ';
    }else{
        if(is_file($img_dir.$foto)){
            chmod($img_dir.$foto,0777);
            unlik($img_dir.$foto);

        }
        echo '
        <div class="notification is-warning is-light">
            <strong>¡Ocurrio un error inesperado!</strong><br>
            No se pudo subir la imagen, intente nuevamente.
        </div>
    ';
    }

    $actualizar_producto=null;
