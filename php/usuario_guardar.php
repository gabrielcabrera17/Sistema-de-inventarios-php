<?php
/*requerir o incluir una unica vez el archivo main.php */
require_once "../php/main.php";

// Almacenar todos los valores que se mandan es decir todos los valores de los inputs en variables
//Almacenando datos
$nombre=limpiar_cadena($_POST['usuario_nombre']);
$apellido=limpiar_cadena($_POST['usuario_apellido']);

$usuario=limpiar_cadena($_POST['usuario_usuario']);
$email=limpiar_cadena($_POST['usuario_email']);

$clave_1=limpiar_cadena($_POST['usuario_clave_1']);
$clave_2=limpiar_cadena($_POST['usuario_clave_2']);


//Segundo filtro, esta vez en el backend
#verificando campos obligatorios
if($nombre =="" || $apellido=="" || $usuario=="" || $clave_1=="" || $clave_2==""){
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
if(verificar_datos("[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{3,40}",$nombre)){
    echo '
    <div class="notification is-danger is-light">
        <strong>¡Ocurrio un error inesperado!</strong><br>
        El nombre no coincide con el formato solicitado
    </div>
';

exit();
}

if(verificar_datos("[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{3,40}",$apellido)){
    echo '
    <div class="notification is-danger is-light">
        <strong>¡Ocurrio un error inesperado!</strong><br>
        El apellido no coincide con el formato solicitado
    </div>
';
exit();
}

if(verificar_datos("[a-zA-Z0-9]{4,20}",$usuario)){
    echo '
    <div class="notification is-danger is-light">
        <strong>¡Ocurrio un error inesperado!</strong><br>
        El usuario no coincide con el formato solicitado
    </div>
';
exit();
}

if(verificar_datos("[a-zA-Z0-9$@.-]{7,100}",$clave_1) || verificar_datos("[a-zA-Z0-9$@.-]{7,100}",$clave_2)){
    echo '
        <div class="notification is-danger is-light">
            <strong>¡Ocurrio un error inesperado!</strong><br>
            Las CLAVES no coinciden con el formato solicitado
        </div>
    ';
    exit();
}


#verificando el email#
if($email!=""){
    if(filter_var($email,FILTER_VALIDATE_EMAIL)){
        // verifica si el correo existe, ya no se podra registrar
        // en check email se tiene la conexión a la base de datos
        $check_email=conexion();
        // Esto se hace para podoer hacer la consulta a la base de datos
        $check_email=$check_email->query("SELECT usuario_email FROM usuario
        WHERE usuario_email='$email'");

        //Devuelve cuantos registros se selecciono 
        if($check_email->rowCount()>0){
            echo '
                <div class="notification is-danger is-light">
                    <strong>¡Ocurrio un error inesperado!</strong><br>
                    El email ingresado ya se encunetra registrado,  por favor 
                    inserte otro email.
                </div>
            ';
            exit();
        }

        //con esto cerramos la conexión y ahorramos espacio en memoria 
        $check_email=null;
    }else{
        echo '
        <div class="notification is-danger is-light">
            <strong>¡Ocurrio un error inesperado!</strong><br>
            El email ingresado no es valido 
        </div>
    ';
    exit();
    }
}
#Verificando Usuario#

    $check_usuario=conexion();
    // Esto se hace para podoer hacer la consulta a la base de datos
    $check_usuario=$check_usuario->query("SELECT usuario_usuario FROM usuario
    WHERE usuario_usuario='$usuario'");

    //Devuelve cuantos registros se selecciono 
    if($check_usuario->rowCount()>0){
        echo '
            <div class="notification is-danger is-light">
                <strong>¡Ocurrio un error inesperado!</strong><br>
                El usuario ingresado ya se encunetra registrado,  por favor 
                inserte otro usuario.
            </div>
        ';
        exit();
    }

    //con esto cerramos la conexión y ahorramos espacio en memoria 
    $check_usuario=null;

    #verificando claves#
    if($clave_1!=$clave_2){
        echo '
        <div class="notification is-danger is-light">
            <strong>¡Ocurrio un error inesperado!</strong><br>
            Las claves que ha ingresado no coinciden.
        </div>
    ';
    exit();  
    }else{
        //encriptando nuestra clave
             /*Generación del hash: Se utiliza la función password_hash() para generar un hash de la contraseña utilizando el algoritmo de hashing bcrypt. El hash resultante se almacena en la variable $clave. La función password_hash() toma dos argumentos: la contraseña original ($clave_1 en este caso) y el algoritmo de hashing que se utilizará (PASSWORD_BCRYPT).

            Almacenamiento del hash: Ahora, el hash resultante ($clave) puede ser almacenado en una base de datos o en cualquier otro lugar seguro para su posterior verificación.
            */
        $clave=password_hash($clave_1,PASSWORD_BCRYPT,["cost"=>10]);
    }

    #Guardando datos#
    //Crear la conexión a la base de datos
    $guardar_usuario=conexion();
    //El método prepare se utiliza en este contexto para preparar una consulta SQL antes de ejecutarla en la base de datos. 
   // Esta práctica es conocida como "sentencias preparadas" y tiene varios beneficios, incluyendo:Seguridad contra inyecciones SQL y Eficiencia en consultas repetidas
    // se usan marcadores en lugar de variables en lugar de $nombre se usa :nombre
    $guardar_usuario=$guardar_usuario->prepare("INSERT INTO usuario(usuario_nombre 
    ,usuario_apellido,usuario_usuario,usuario_clave,usuario_email) VALUES(:nombre,:apellido,:usuario,:clave,:email)");
    //array de marcadores para asignarlas a las variables correspondientes.
    $marcadores = [
        ":nombre"=>$nombre,
        ":apellido"=>$apellido,
        ":usuario"=>$usuario,
        ":clave"=>$clave,
        ":email"=>$email
    ];
    $guardar_usuario->execute($marcadores);
    
    //comprobar si se registro o no los datos
    if($guardar_usuario->rowCount()==1){
        echo '
        <div class="notification is-info is-light">
            <strong>¡Usuario Registrado!</strong><br>
            El usuario se registro correctamente.
        </div>
    ';

    }else{
        echo '
        <div class="notification is-danger is-light">
            <strong>¡Ocurrio un error inesperado!</strong><br>
            No se pudo registrar el usuario, por favor intente nuevamente.
        </div>
    ';
    }
    //cerrar conexiòn
    $guardar_usuario=null;


    