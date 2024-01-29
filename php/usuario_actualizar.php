<?php
    require_once "../inc/session_start.php";

    require_once "main.php";
    $id=limpiar_cadena($_POST['usuario_id']);

    //Verificar si el usuario existe
    $check_usuairo=conexion();
    $check_usuairo=$check_usuairo->query("SELECT * FROM  usuario WHERE 
    usuario_id='$id'");

    if($check_usuairo->rowCount()<=0){
        echo '
                <div class="notification is-danger is-light">
                    <strong>¡Ocurrio un error inesperado!</strong><br>
                    El usuario no existe en el sistema.
                </div>
            ';
            exit();
    }else{
        $datos=$check_usuairo->fetch();
    }
    $check_usuairo=null;

    //Recibir el usuario y clave del que quiere actualizar sus datos
    //Almacenamos esos datos en las siguientes variables 
    $admin_usuario=limpiar_cadena($_POST['administrador_usuario']);
    $admin_clave=limpiar_cadena($_POST['administrador_clave']);
    
    #verificando campos obligatorios
    if($admin_usuario =="" || $admin_clave ==""){
        echo '
            <div class="notification is-danger is-light">
                <strong>¡Ocurrio un error inesperado!</strong><br>
                No has llenado todos los campos que son obligatorios, que 
                corresponden a su usuario y clave;
            </div>
        ';
        //con la función exit se detiene  la ejecución de nuestro programa por no haber cumplido con la condicional
        exit();
    }

    #Verificando Integridad de los datos
    if(verificar_datos("[a-zA-Z0-9]{4,20}",$admin_usuario)){
        echo '
        <div class="notification is-danger is-light">
            <strong>¡Ocurrio un error inesperado!</strong><br>
            Su usuario no coincide con el formato solicitado.
        </div>
    ';

    exit();
    }

    #Verificando Integridad de los datos
    if(verificar_datos("[a-zA-Z0-9$@.-]{7,100}",$admin_clave)){
        echo '
        <div class="notification is-danger is-light">
            <strong>¡Ocurrio un error inesperado!</strong><br>
            La clave no coincide con el formato solicitado.
        </div>
    ';

    exit();
    }

    //Verificar que existen en la base de datos el admin
    $check_admin=conexion();
    $check_admin=$check_admin->query("SELECT usuario_usuario,usuario_clave 
     FROM usuario WHERE usuario_usuario='$admin_usuario' AND usuario_id='".$_SESSION['id']."'");

    if($check_admin->rowCount()==1){
        $check_admin=$check_admin->fetch();
        if($check_admin['usuario_usuario']!=$admin_usuario || !password_verify($admin_clave,$check_admin['usuario_clave'])){
            echo '
            <div class="notification is-danger is-light">
                <strong>¡Ocurrio un error inesperado!</strong><br>
                 Usuario o clave de administrador incorrecto.
            </div>
            ';
    
            exit();

        }

    }else{
        echo '
        <div class="notification is-danger is-light">
            <strong>¡Ocurrio un error inesperado!</strong><br>
             Usuario o clave de administrador incorrecto.
        </div>
        ';

        exit();

    }
    $check_admin=null;

    //Almacenando datos
    $nombre=limpiar_cadena($_POST['usuario_nombre']);
    $apellido=limpiar_cadena($_POST['usuario_apellido']);

    $usuario=limpiar_cadena($_POST['usuario_usuario']);
    $email=limpiar_cadena($_POST['usuario_email']);

    $clave_1=limpiar_cadena($_POST['usuario_clave_1']);
    $clave_2=limpiar_cadena($_POST['usuario_clave_2']);


    #verificando campos obligatorios
    if($nombre =="" || $apellido=="" || $usuario=="" ){
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

#verificando el email#
if($email!="" && $email!=$datos['usuario_email']){
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
if($usuario!=$datos['usuario_usuario']){
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

}

 #verificando claves#
if($clave_1!="" || $clave_2!=""){
    if(verificar_datos("[a-zA-Z0-9$@.-]{7,100}",$clave_1) || verificar_datos("[a-zA-Z0-9$@.-]{7,100}",$clave_2)){
        echo '
            <div class="notification is-danger is-light">
                <strong>¡Ocurrio un error inesperado!</strong><br>
                Las CLAVES no coinciden con el formato solicitado
            </div>
        ';
        exit();
    }else{
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

    }
    
}else{
    $clave=$datos['usuario_usuario'];
}

#Actualizar Datos#
$actualizar_usuario=conexion();
//preparando consulta
$actualizar_usuario=$actualizar_usuario->prepare("UPDATE usuario SET 
usuario_nombre=:nombre,usuario_apellido=:apellido,usuario_usuario=:usuario,
usuario_clave=:clave,usuario_email=:email WHERE usuario_id=:id");

$marcadores = [
    ":nombre"=>$nombre,
    ":apellido"=>$apellido,
    ":usuario"=>$usuario,
    ":clave"=>$clave,
    ":email"=>$email,
    ":id"=>$id
];

//para saber si la consulta se ejecuto con exito o no 
if($actualizar_usuario->execute($marcadores)){
    echo '
    <div class="notification is-info is-light">
        <strong>¡Usuario Actualizado!</strong><br>
        El usuario se actualizo exitosamente.
    </div>
';
}else{
    echo '
    <div class="notification is-danger is-light">
        <strong>¡Ocurrio un error inesperado!</strong><br>
        No se pudo actualizar el usuario, intente nuevamente.
    </div>
';
}

$actualizar_usuario=null;

