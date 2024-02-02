<?php

require_once "main.php";

//verificando el producto
// este valor se envia desde el formulario
$id=limpiar_cadena($_POST['producto_id']);

 //Verificar si la categoria existe
 $check_producto=conexion();
 // selecciona cuando el input producto_id = $id
 $check_producto=$check_producto->query("SELECT * FROM  producto WHERE producto_id='$id'");

 if($check_producto->rowCount()<=0){
     echo '
             <div class="notification is-danger is-light">
                 <strong>¡Ocurrio un error inesperado!</strong><br>
                 El producto no existe en el sistema.
             </div>
         ';
         exit();
 }else{
     $datos=$check_producto->fetch();
 }
 $check_producto=null;

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

#Verificando Integridad de los datos#
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

#Verificando código#
if($codigo!=$datos['producto_codigo']){
    #Verificando que no se repitan productos ni código de barras#

        $check_codigo=conexion();
        // Esto se hace para podoer hacer la consulta a la base de datos
        $check_codigo=$check_codigo->query("SELECT producto_codigo FROM producto WHERE producto_codigo='$codigo'");

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
}

//si el nombre que enviamos desde el formulario es distinto al de la bd
if($nombre!=$datos['producto_nombre']){
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
    $check_nombre=null;
}

if($categoria!=$datos['categoria_id']){
    #Verificando la cateogria#

    $check_categoria=conexion();
    // Esto se hace para podoer hacer la consulta a la base de datos
    $check_categoria=$check_categoria->query("SELECT categoria_id FROM categoria WHERE categoria_id='$categoria'");

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

}

#Actualizar Datos#
$actualizar_producto=conexion();
//preparando consulta
$actualizar_producto=$actualizar_producto->prepare("UPDATE producto SET producto_codigo=:codigo,producto_nombre=:nombre,producto_precio=:precio, producto_stock=:stock,categoria_id=:categoria WHERE producto_id=:id");

$marcadores = [
    ":codigo"=>$codigo,
    ":nombre"=>$nombre,
    ":precio"=>$precio,
    ":stock"=>$stock,
    ":categoria"=>$categoria,
    ":id"=>$id
   
];

//para saber si la consulta se ejecuto con exito o no 
if($actualizar_producto->execute($marcadores)){
        echo '
        <div class="notification is-info is-light">
            <strong>¡Producto Actualizada!</strong><br>
            El producto se actualizo exitosamente.
        </div>
    ';
    }else{
        echo '
        <div class="notification is-danger is-light">
            <strong>¡Ocurrio un error inesperado!</strong><br>
            No se pudo actualizar el producto, intente nuevamente.
        </div>
    ';
}

$actualizar_producto=null;
