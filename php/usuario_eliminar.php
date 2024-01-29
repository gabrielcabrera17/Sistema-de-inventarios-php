<?php
    $user_id_del=limpiar_cadena($_GET['user_id_del']);

    //Verificando si el usuario existe
    $check_usuairo=conexion();
    $check_usuairo=$check_usuairo->query("SELECT usuario_id FROM usuario WHERE
    usuario_id='$user_id_del'");
    if($check_usuairo->rowCount()==1){

    //Verificando si ya existen productos asociados a este usuario o si ya ha regristraod un producto

    $check_productos=conexion();
    $check_productos=$check_productos->query("SELECT usuario_id FROM producto WHERE
    usuario_id='$user_id_del' LIMIT 1");
    if($check_productos->rowCount()<=0){

    $eliminar_usuario=conexion();
    $eliminar_usuario=$eliminar_usuario->prepare("DELETE FROM usuario
    WHERE usuario_id=:id");

    $eliminar_usuario->execute([":id"=>$user_id_del]);

    if($eliminar_usuario->rowCount()==1){
        echo '
        <div class="notification is-info is-light">
            <strong>¡Usuario Eliminado!</strong><br>
            El usuario se elimino exitosamente.
        </div>
    ';

    }else{
        echo '
        <div class="notification is-danger is-light">
            <strong>¡Ocurrio un error inesperado!</strong><br>
            No se pudo eliminar el usuario, por favor intente nuevamente.
        </div>
    ';

    }
    $eliminar_usuario=null;

    }else{
        echo '
                <div class="notification is-danger is-light">
                    <strong>¡Ocurrio un error inesperado!</strong><br>
                    No podemos eliminar el usuario, ya que tiene productos registrados.
                </div>
            ';
    }
    $check_productos=null;
    }else{
        echo '
                <div class="notification is-danger is-light">
                    <strong>¡Ocurrio un error inesperado!</strong><br>
                    El usuario que intenta eliminar no existe.
                </div>
            ';
    }
    $check_usuairo=null;