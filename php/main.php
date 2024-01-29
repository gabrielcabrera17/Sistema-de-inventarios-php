 

<?php
/*Archivo principal que contendra funciones que utilizaremos 
en todo el sistema, como por ejemplo conexión a la bd o verificación de los datos dle formulario o inyección sql, en sintesis lo que se usa varias veces en el sistema */

/* conexíon bd mediante clase pdo*/
function conexion(){
    $pdo = new PDO('mysql:host=localhost;dbname=inventario','root','5.510.514AyG');
    return $pdo;
}
#verificar datos#
function verificar_datos($filtro,$cadena){

    if(preg_match("/^".$filtro."$/",$cadena)){
        return false;
    }else{
        return true;    
    }
}

# servira para limpiar cadenas de texto#
function limpiar_cadena($cadena){
    //trim sirve para eliminar espacios en blanco en la cadena
    $cadena=trim($cadena);

    //Para quitar las barras de un string
    $cadena=stripcslashes($cadena);
    //Sirve para reemplazar un texto por busqueda
    // y evitar que se escriba todo esto en nuestro fomrulario
    $cadena=str_ireplace("<script>","",$cadena); // sirve para evitar ataques de tipo inyección sql
    $cadena=str_ireplace("</script>","",$cadena);
    $cadena=str_ireplace("<script src>","",$cadena);
    $cadena=str_ireplace("<script type=>","",$cadena);
    $cadena=str_ireplace("SELECT * FROM","",$cadena);
    $cadena=str_ireplace("DELETE FROM","",$cadena);
    $cadena=str_ireplace("INSERT INTO","",$cadena);
    $cadena=str_ireplace("DROP TABLE","",$cadena);
    $cadena=str_ireplace("DROP DATABASE","",$cadena);
    $cadena=str_ireplace("TRUNCATE TABLE","",$cadena);
    $cadena=str_ireplace("SHOW TABLES","",$cadena);
    $cadena=str_ireplace("SHOW DATABASES","",$cadena);
    $cadena=str_ireplace("<?php","",$cadena);
    $cadena=str_ireplace("?>","",$cadena);
    $cadena=str_ireplace("--","",$cadena);
    $cadena=str_ireplace("^","",$cadena);
    $cadena=str_ireplace("<","",$cadena);
    $cadena=str_ireplace("[","",$cadena);
    $cadena=str_ireplace("]","",$cadena);
    $cadena=str_ireplace("==","",$cadena);
    $cadena=str_ireplace(";","",$cadena);
    $cadena=str_ireplace("::","",$cadena);
    $cadena=trim($cadena);
    $cadena=stripcslashes($cadena);
    return $cadena;
} 


// función para renombrar fotos
function renombrar_fotos($nombre ){
    // si hay un vacio lo cambiamos por y _ y asi sucesivamente.
    $nombre=str_ireplace(" ","_",$nombre);
    $nombre=str_ireplace("/","_",$nombre);
    $nombre=str_ireplace("#","_",$nombre);
    $nombre=str_ireplace("-","_",$nombre);
    $nombre=str_ireplace("$","_",$nombre);
    $nombre=str_ireplace(".","_",$nombre);
    $nombre=str_ireplace(",","_",$nombre);

    // ran selecciona un nùmero aleatorio entre un minimo y máximo asignado
    //concatena con _ las palabras
    $nombre=$nombre."_".rand(0,100);
    return $nombre;

}
//query es para realizar una petición o consulta a la base de datos directamente

//Función paginador de tablas
//el parametro o variable pagina nos indica en que página nos encontramos
function paginador_tablas($pagina,$Npaginas,$url,$botones){
    $tabla='<nav class="pagination is-centered is-rounded" role="navigation" aria-label="pagination">';



    if($pagina<=1){
        $tabla.='
        <a class="pagination-previous is-disabled" disabled>Anterior</a>
        <ul class="pagination-list">
        ';

    }else{
        $tabla.='
        <a class="pagination-previous" href="'.$url.($pagina-1).
        '">Anterior</a>
        <ul class="pagination-list">
            <li><a class="pagination-link" href="'.$url.'1">1</a></li>
            <li><span class="pagination-ellipsis">&hellip;</span></li>
        ';

    }

    //boton siguiente

    //contador
    $ci=0;
    for($i = $pagina; $i<=$Npaginas; $i++){
    if($ci>=$botones){
        break;
    }
        if($pagina==$i){
            $tabla.=' <li><a class="pagination-link is-current"  href="'.$url.$i.'">'.$i.'</a></li> ';

        }else{
            $tabla.=' <li><a class="pagination-link"  href="'.$url.$i.'">'.$i.'</a></li> ';

        }
        $ci++;
    }

    if($pagina==$Npaginas){
        $tabla.='
        </ul>
        <a class="pagination-next is-disabled" disabled>Siguiente</a>
        ';

    }else{
        $tabla.='
             <li><span class="pagination-ellipsis">&hellip;</span></li>
             <li><a class="pagination-link"  href="'.$url.$Npaginas.'">'.$Npaginas.'</a></li>
        </ul>
        <a class="pagination-next"href="'.$url.($pagina+1).
        '">Siguiente</a>
    
        ';

    }
    //concatenar a la tabla el final de nav
    $tabla.='</nav>';
    return $tabla;
}


?>
