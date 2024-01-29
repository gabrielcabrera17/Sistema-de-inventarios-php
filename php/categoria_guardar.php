<?php
    require_once "main.php";

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
                El nombre no coincide con el formato solicitado
            </div>
        ';

        exit();
        }
    }

    #Verificando que no se repitan las categorias#

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


    
    #Guardando datos#
    //Crear la conexión a la base de datos
    $guardar_categoria=conexion();
    //El método prepare se utiliza en este contexto para preparar una consulta SQL antes de ejecutarla en la base de datos. 
   // Esta práctica es conocida como "sentencias preparadas" y tiene varios beneficios, incluyendo:Seguridad contra inyecciones SQL y Eficiencia en consultas repetidas
    // se usan marcadores en lugar de variables en lugar de $nombre se usa :nombre
    $guardar_categoria=$guardar_categoria->prepare("INSERT INTO categoria(categoria_nombre 
    ,categoria_ubicacion) VALUES(:nombre,:ubicacion)");
    //array de marcadores para asignarlas a las variables correspondientes.

    $marcadores = [
        ":nombre"=>$nombre,
        ":ubicacion"=>$ubicacion,
    ];
    $guardar_categoria->execute($marcadores);

    if( $guardar_categoria->rowCount()==1){
        echo '
        <div class="notification is-ifno is-light">
            <strong>¡Categoria registrada!</strong><br>
            La categoria se registro correctamente.
        </div>
    ';

    }else{
        echo '
        <div class="notification is-danger is-light">
            <strong>¡Ocurrio un error inesperado!</strong><br>
            No se pudo registrar la categoría, por favor intente nuevamente.
        </div>
    ';
    }
    $guardar_categoria=null;

            