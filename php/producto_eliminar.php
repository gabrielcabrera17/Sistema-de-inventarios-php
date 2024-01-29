<?php
// en product_id_del se almacena el parametro o registro que estamos enviando
//Almacenar en una variable el parametro de tipo get
$product_id_del=limpiar_cadena($_GET['product_id_del']);

 //Verificando si categoria existe
 $check_producto=conexion();
 $check_producto=$check_producto->query("SELECT * FROM producto WHERE
 producto_id='$product_id_del'");

 #comprobando si el producto existe~#
if($check_producto->rowCount()==1){
    //Almacenando todos los datos del producto seleccionado
    $datos=$check_producto->fetch();

    $eliminar_producto=conexion();
    $eliminar_producto=$eliminar_producto->prepare("DELETE FROM producto
    WHERE producto_id=:id");

    $eliminar_producto->execute([":id"=>$product_id_del]);

    if($eliminar_producto->rowCount()==1){
        //primeramente se elimina la imagen si existe, luego los datos
        if(is_file("./img/producto/".$datos['producto_foto'])){
            //Si la imagen existe
            chmod("./img/producto/".$datos['producto_foto'],0777);
                //eliminando un archivo con unlimk
                unlink("./img/producto/".$datos['producto_foto']);
        }
        echo '
        <div class="notification is-info is-light">
            <strong>¡Producto Eliminado!</strong><br>
            Los datos del producto se elimino exitosamente.
        </div>
    ';

    }else{
        echo '
        <div class="notification is-danger is-light">
            <strong>¡Ocurrio un error inesperado!</strong><br>
            No se pudo eliminar el producto, por favor intente nuevamente.
        </div>
    ';

    }
    $eliminar_producto=null;

}else{
    echo '
    <div class="notification is-danger is-light">
        <strong>¡Ocurrio un error inesperado!</strong><br>
        El producto que intenta eliminar, no existe.
    </div>
    ';


}
$check_producto=null;