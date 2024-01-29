<?php

#Almacenar los datos en variables#
$usuario=limpiar_cadena($_POST['login_usuario']);
$clave=limpiar_cadena($_POST['login_clave']);

//Verificando los campos obligatorios
if($usuario=="" || $clave==""){
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
if(verificar_datos("[a-zA-Z0-9]{4,20}",$usuario)){
    echo '
    <div class="notification is-danger is-light">
        <strong>¡Ocurrio un error inesperado!</strong><br>
        El usuario no coincide con el formato solicitado
    </div>
';

exit();
}

if(verificar_datos("[a-zA-Z0-9$@]{7,100}",$clave)){
    echo '
    <div class="notification is-danger is-light">
        <strong>¡Ocurrio un error inesperado!</strong><br>
        La contraseña no coincide con el formato solicitado
    </div>
';

exit();
}

#Consultas a la base de datos, para verificar que estos datos son los correctos para iniciar la sesión
$check_user=conexion();
$check_user=$check_user->query("SELECT * FROM usuario WHERE 
usuario_usuario ='$usuario'");

//comprobar si se ha seleccionado algun registro en la base de datos

if($check_user->rowCount()==1){
    //Array de datos de la base de datos de todo lo que hemos seleccionado
    $check_user=$check_user->fetch();
    //password_verify sirve para corrobar si un texto coincide con una clave procesada con password_hash
    if($check_user['usuario_usuario'] ==$usuario && password_verify
    ($clave,$check_user['usuario_clave'])){

        $_SESSION['id']=$check_user['usuario_id'];
        $_SESSION['nombre']=$check_user['usuario_nombre'];
        $_SESSION['apellido']=$check_user['usuario_apellido'];
        $_SESSION['usuario']=$check_user['usuario_usuario'];

        // para saber o no si  hemos enviamos encabezados
        if(headers_sent()){
            // si no se envio redirección con js 
            echo "<script> window.location.href='index.php?vista=home'; </script>";
        }else{
            //si se envio redirección con php
            header("Location: index.php?vista=home");
        }


    }else{
        echo '
            <div class="notification is-danger is-light">
                <strong>¡Ocurrio un error inesperado!</strong><br>
                Usuario o clave incorrectos
            </div>
        '; 
    }
}else{
    echo '
    <div class="notification is-danger is-light">
        <strong>¡Ocurrio un error inesperado!</strong><br>
        Usuario o clave incorrectos
    </div>
'; 
}
$check_user=null;