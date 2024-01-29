<?php
session_destroy();
 // para saber o no si  hemos enviamos encabezados
 if(headers_sent()){
    // si no se envio redirección con js 
    echo "<script> window.location.href='index.php?vista=home'; </script>";
}else{
    //si se envio redirección con php
    header("Location: index.php?vista=login");
}