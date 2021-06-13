<?php

require_once('database.php');

/* utilidades */
function mempty() { // multiple empty
    foreach(func_get_args() as $arg) {
        if(empty($arg))
            return true;
    }
    return false;
}


/* mensajes al usuario */

function crearMensaje($mensaje, $tipo) {
    $_SESSION['mensaje'] = $mensaje;
    $_SESSION['tipo-mensaje'] = $tipo; //1-exito, 2-info, 3-error
}

function mostrarMensaje() {
    global $_SESSION;

    if (!isset($_SESSION["mensaje"], $_SESSION["tipo-mensaje"])) {
        // si no hay mensaje que mostrar
        return false;
    }

    $mensaje = $_SESSION["mensaje"];
    $tipoMensaje = $_SESSION["tipo-mensaje"];
    switch ($tipoMensaje) {
        case 1:
            $class = 'mensaje-exito';
            break;
        case 2:
            $class = 'mensaje-info';
            break;
        case 3:
            $class = 'mensaje-error';
            break;
        default:
            $class = 'mensaje-generico';
            break;
    }

    //include_once ('includes/mensaje_info.php');

    unset($_SESSION["mensaje"]);
    unset($_SESSION["tipo-mensaje"]);

    return array($mensaje, $class);
}


/*BDD*/
function crearConexion() {
    if (!isset($db) || $db === NULL) {
        $db = new Database();
    }
    return $db;
}

function selectModelo($campos, $tabla, $id_modelo, $categoria) {
    $con = crearConexion();
    //select
    $sql = "SELECT";
    foreach ($campos as $clave=>$valor) {
        if ($clave === count($campos) - 1) {
            $sql .= " $valor "; //última sin coma
        } else
            $sql .= " $valor, ";
    }
    /*if (count($campos) == 4) {
        $sql = "SELECT $campos[0], $campos[1], $campos[2], $campos[3]";
    }
    if (count($campos) == 3) {
        $sql = "SELECT $campos[0], $campos[1], $campos[2]";
    } else {
        $sql = "SELECT $campos[0], $campos[1]";
    }*/
    $sql .= " FROM $tabla ";
    //where
    if ($id_modelo !== null) {
        $sql .= " WHERE id_modelo = $id_modelo";
    }
    if ($categoria !== null) {
        $sql .= " WHERE categoria = $categoria";
    }
    $sql .= ";";
    //consulta
    $con->hacerConsulta($sql);
    $filas = $con->getRows();
    $con->disconnect();//quitar esto?
    return $filas;
}

function selectUsuarios($usuario = null) {
    $con = crearConexion();

    //sql
    $sql = "SELECT *
            FROM usuario";
    if (!is_null($usuario)) {
        if (is_numeric($usuario)) {
            $sql .= " WHERE id_usuario = '$usuario'";
        } elseif (is_string($usuario)) {
            $sql .= " WHERE nombre_usuario = '$usuario'";
        }
    }
    $sql .= ";";
    //consulta
    $con->hacerConsulta($sql);
    if ($con->getNumRows() === 0) {
        return false;
    }
    $filas = $con->getRows();

    /* si es solo una fila */
    if ($con->getNumRows() < 2) {
        $fila = $filas[0];
        return $fila;
    }
    $con->disconnect();
    return $filas;
}


function insertModelo($titulo, $ruta, $miniatura, $categ, $usuario)  {
    $con = crearConexion();
    $sql = "INSERT INTO modelo (titulo, ruta, miniatura, categoria, usuario)
            VALUES ('$titulo', '$ruta', '$miniatura', $categ, $usuario);";
    $con->hacerConsulta($sql);
    $con->disconnect();
    return $con->getNumRows();
}
function insertUsuario($nombre_usuario, $pass)  {
    $con = crearConexion();
    $sql = "INSERT INTO usuario (nombre_usuario, "."pass".")
            VALUES ('$nombre_usuario', '$pass');";
    $con->hacerConsulta($sql);
    $con->disconnect();
    return;
}



//delete

function deleteModelo($idModelo)  {
    $con = crearConexion();
    $sql = "DELETE FROM modelo
            WHERE id_modelo = {$idModelo};";
    $con->hacerConsulta($sql);
    $con->disconnect();
    return $con->getNumRows();
}

function deleteUsuario($usuario) {

}





/* sesiones */
/* usar en upload, etc. */
function checkRedireccionarLogin() {
    if (!isset($_SESSION['user_id'])) {
        //header("Location:".$_SERVER['PHP_SELF']);
        header('Location:login.php');
    }
//header('Location:login.php');

}

function checkSesion() {
    if (!isset($_SESSION['user_id'])) {
        // si no se ha iniciado sesión, regresa a la página
        if (isset($_SERVER['HTTP_REFERER'])) {
            header('Location:'.$_SERVER['HTTP_REFERER']);
            // o vuelve al index
        } header('Location:login.php');
    }
}

/* END BDD*/



/* filtrar inputs */

function textoCorrecto($input) {
    // longitud
    $longitudTexto = strlen($input);
    if ($longitudTexto < 2 || $longitudTexto > 30 ) {
        return false;
    }
    // comience por número
    if (preg_match('/^[0-9]/', $input)) {
        return false;
    }
    // contenga caracteres especiales
    if (preg_match('/[$€&+,:;=?@#|\'<>.^*()%!-]/', $input)) {
        return false;
    }

}

/*$db = new Database();
$db->mi_query("SELECT * FROM pelicula");
if($db->get_num_rows() == 0){
    echo "No hay películas<br/>";
}
else{
    $mis_pelis = $db->rows();
    foreach($mis_pelis as $peli){
        echo $peli['id_pelicula']." - <strong>".$peli['titulo']."</strong> - ".$peli['sinopsis'].
            " - ".$peli['director']."<br/>";
    }
}
$db->disconnect();*/

?>