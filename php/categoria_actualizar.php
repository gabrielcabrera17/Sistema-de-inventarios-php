<?php
    
    require_once "main.php";

    //verificando la categoria

    $id=limpiar_cadena($_POST['categoria_id']);

    //Verificar si la categoria existe
    $check_categoria=conexion();
    $check_categoria=$check_categoria->query("SELECT * FROM  categoria WHERE 
    categoria_id='$id'");

    if($check_categoria->rowCount()<=0){
        echo '
                <div class="notification is-danger is-light">
                    <strong>¡Ocurrio un error inesperado!</strong><br>
                    La categoria no existe en el sistema.
                </div>
            ';
            exit();
    }else{
        $datos=$check_categoria->fetch();
    }
    $check_categoria=null;

    //Almacenando los datos enviados desde el formulario cateogrynew
    $nombre=limpiar_cadena($_POST['categoria_nombre']);
    $ubicacion=limpiar_cadena($_POST['categoria_ubicacion']);

    #verificando campos obligatorios
    if($nombre =="" ){
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
     if(verificar_datos("[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ ]{4,50}",$nombre)){
        echo '
        <div class="notification is-danger is-light">
            <strong>¡Ocurrio un error inesperado!</strong><br>
            El nombre no coincide con el formato solicitado
        </div>
    ';

    exit();
    }

    if($ubicacion!=""){
        #Verificando Integridad de los datos
        if(verificar_datos("[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ ]{5,150}",$ubicacion)){
            echo '
            <div class="notification is-danger is-light">
                <strong>¡Ocurrio un error inesperado!</strong><br>
                La ubicación coincide con el formato solicitado
            </div>
        ';

        exit();
        }
    }

    #Verificando que no se repitan el nombre de la categoria cuando sea distinto que al que se tiene en la bd#
    if($nombre!=$datos['categoria_nombre']){
        $check_nombre=conexion();
        // Esto se hace para poder hacer la consulta a la base de datos
        $check_nombre=$check_nombre->query("SELECT categoria_nombre FROM categoria
        WHERE categoria_nombre='$nombre'");

        //Devuelve cuantos registros se selecciono 
        if($check_nombre->rowCount()>0){
            echo '
                <div class="notification is-danger is-light">
                    <strong>¡Ocurrio un error inesperado!</strong><br>
                    El nombre ingresado ya se encunetra registrado,  por favor 
                    inserte otra.
                </div>
            ';
            exit();
        }
        //con esto cerramos la conexión y ahorramos espacio en memoria 
            $check_nombre=null;
    }

#Actualizar Datos#
$actualizar_categoria=conexion();
//preparando consulta
$actualizar_categoria=$actualizar_categoria->prepare("UPDATE categoria SET 
categoria_nombre=:nombre,categoria_ubicacion=:ubicacion WHERE categoria_id=:id");

$marcadores = [
    ":nombre"=>$nombre,
    ":ubicacion"=>$ubicacion,
    ":id"=>$id
];

//para saber si la consulta se ejecuto con exito o no 
if($actualizar_categoria->execute($marcadores)){
    echo '
    <div class="notification is-info is-light">
        <strong>¡Categoria Actualizada!</strong><br>
        La categoria se actualizo exitosamente.
    </div>
';
}else{
    echo '
    <div class="notification is-danger is-light">
        <strong>¡Ocurrio un error inesperado!</strong><br>
        No se pudo actualizar la categoria, intente nuevamente.
    </div>
';
}

$actualizar_categoria=null;
    
    
