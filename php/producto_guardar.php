<?php
    /*requerir o incluir una unica vez el archivo main.php */
require_once "main.php";
require_once "../inc/session_start.php";

// Almacenar todos los valores que se mandan es decir todos los valores de los inputs en variables
//Almacenando datos
$codigo=limpiar_cadena($_POST['producto_codigo']);
$nombre=limpiar_cadena($_POST['producto_nombre']);

$precio=limpiar_cadena($_POST['producto_precio']);
$stock=limpiar_cadena($_POST['producto_stock']);
$categoria=limpiar_cadena($_POST['producto_categoria']);


#verificando campos obligatorios
if($codigo =="" || $nombre=="" || $precio=="" || $stock=="" || $categoria==""){
    echo '
        <div class="notification is-danger is-light">
            <strong>¡Ocurrio un error inesperado!</strong><br>
            No has llenado todos los campos que son obligatorios
        </div>
    ';
    //con la función exit se detiene  la ejecución de nuestro programa por no haber cumplido con la condicional
    exit();
}

#Verificando Integridad de los datos
if(verificar_datos("[a-zA-Z0-9- ]{1,70}",$codigo)){
    echo '
    <div class="notification is-danger is-light">
        <strong>¡Ocurrio un error inesperado!</strong><br>
        El código del producto no coincide con el formato solicitado.
    </div>
';

exit();
}


if(verificar_datos("[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ().,$#\-\/ ]{1,70}",$nombre)){
    echo '
    <div class="notification is-danger is-light">
        <strong>¡Ocurrio un error inesperado!</strong><br>
        El nombre del producto no coincide con el formato solicitado.
    </div>
';

exit();
}

if(verificar_datos("[0-9.]{1,25}",$precio)){
    echo '
    <div class="notification is-danger is-light">
        <strong>¡Ocurrio un error inesperado!</strong><br>
        El precio del producto no coincide con el formato solicitado.
    </div>
';

exit();
}

if(verificar_datos("[0-9]{1,25}",$stock)){
    echo '
    <div class="notification is-danger is-light">
        <strong>¡Ocurrio un error inesperado!</strong><br>
        El stock del producto no coincide con el formato solicitado.
    </div>
';

exit();
}

#Verificando que no se repitan productos ni código de barras#

$check_codigo=conexion();
// Esto se hace para podoer hacer la consulta a la base de datos
$check_codigo=$check_codigo->query("SELECT producto_codigo FROM producto
WHERE producto_codigo='$codigo'");

//Devuelve cuantos registros se selecciono 
if($check_codigo->rowCount()>0){
    echo '
        <div class="notification is-danger is-light">
            <strong>¡Ocurrio un error inesperado!</strong><br>
            El código de barras ingresado ya se encunetra registrado,  por favor 
            inserte otro código.
        </div>
    ';
    exit();
}

//con esto cerramos la conexión y ahorramos espacio en memoria 
$check_codigo=null;

#Verificando que el nombre no se repita#

$check_nombre=conexion();
// Esto se hace para podoer hacer la consulta a la base de datos
$check_nombre=$check_nombre->query("SELECT producto_nombre FROM producto
WHERE producto_nombre='$nombre'");

//Devuelve cuantos registros se selecciono 
if($check_nombre->rowCount()>0){
    echo '
        <div class="notification is-danger is-light">
            <strong>¡Ocurrio un error inesperado!</strong><br>
            El nombre de producto ingresado ya se encuentra registrado,  por favor 
            inserte otro nombre.
        </div>
    ';
    exit();
}

//con esto cerramos la conexión y ahorramos espacio en memoria 
$check_nombre=null;


#Verificando la cateogria#

$check_categoria=conexion();
// Esto se hace para podoer hacer la consulta a la base de datos
$check_categoria=$check_categoria->query("SELECT categoria_id FROM categoria
WHERE categoria_id='$categoria'");

//Devuelve cuantos registros se selecciono 
if($check_categoria->rowCount()<=0){
    echo '
        <div class="notification is-danger is-light">
            <strong>¡Ocurrio un error inesperado!</strong><br>
            La categoria ingresada ya se encuentra registrado,  por favor 
            inserte otra categoria.
        </div>
    ';
    exit();
}

//En generar cargaremos la imagen en el servidor
//con esto cerramos la conexión y ahorramos espacio en memoria 
$check_categoria=null;

#Directorio de imagenes#
//Aqui estableceremos la variable para la dirección de la imagne
$img_dir="../img/producto/";


//comprobar si se ha enviado o no una imagen
#comprobar si selecciono una imagen#
//se le pasa el archivo en el primer corchete
//En el segundo corchete se especifica que información se quiere obtener de la imagen
if($_FILES['producto_foto']['name']!=""&&$_FILES['producto_foto']['size']>0){
// en $_Files viene almacenada la información de la imagen que se envia con el formulario

    #Verificando si existe o no el directorio#
    //Esta función file_exists devuelve si existe o no el directorio especificado
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

    chmod($img_dir,0777);

    //renombrar la imagen utilizando la función creada en main.php
    $img_nombre=renombrar_fotos($nombre);
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
}else{
    //
    $foto="";
}

#Guardando Datos#
$guardar_producto=conexion();
//El método prepare se utiliza en este contexto para preparar una consulta SQL antes de ejecutarla en la base de datos. 
// Esta práctica es conocida como "sentencias preparadas" y tiene varios beneficios, incluyendo:Seguridad contra inyecciones SQL y Eficiencia en consultas repetidas
 // se usan marcadores en lugar de variables en lugar de $nombre se usa :nombre
$guardar_producto=$guardar_producto->prepare("INSERT INTO producto(producto_codigo,
producto_nombre,producto_precio,producto_stock,producto_foto,categoria_id,usuario_id) VALUES(:codigo,:nombre,:precio,:stock,:foto,:categoria,:usuario)");

$marcadores = [
    ":codigo"=>$codigo,
    ":nombre"=>$nombre,
    ":precio"=>$precio,
    ":stock"=>$stock,
    ":categoria"=>$categoria,
    ":foto"=>$foto,
    ":usuario"=>$_SESSION['id']
   
];

$guardar_producto->execute($marcadores);

//comprobar si se registro o no los datos
if($guardar_producto->rowCount()==1){
    echo '
    <div class="notification is-info is-light">
        <strong>¡Producto Registrado!</strong><br>
        El Producto se registro correctamente.
    </div>
';

}else{
    //Trataremos de eliminar la imagen movida del producto no registrado
    //para ello comprobamos si la imagen existe o no en el directorio
    if(is_file($img_dir.$foto)){
        //Le damos permiso de lectura y escritura
        chmod($img_dir.$foto,0777);
        //eliminamos el archivo utilizando unlik
        unlink($img_dir.$foto);


    }
    echo '
    <div class="notification is-danger is-light">
        <strong>¡Ocurrio un error inesperado!</strong><br>
        No se pudo registrar el producto, por favor intente nuevamente.
    </div>
';
}
//cerrar conexiòn
$guardar_producto=null;
