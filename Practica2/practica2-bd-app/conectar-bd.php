<?php 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener valores del formulario
    $serverName = $_POST['serverName'];
    $uid = $_POST['uid'];
    $pwd = $_POST['pwd'];
    $databaseName = $_POST['databaseName'];
    // Intentar conexión
    $connectionInfo = array();
    if (empty($uid) || empty($uid)) {
        $connectionInfo = array("Database" => $databaseName);
    } else {
        $connectionInfo = array("UID" => $uid, "PWD" => $pwd, "Database" => $databaseName);
    }
    // Hacer conexión global
    if (!session_id()) session_start();
    $conn = sqlsrv_connect($serverName, $connectionInfo);
    if (!empty($conn)) {
        $_SESSION['serverName'] = $serverName;
        $_SESSION['uid'] = $uid;
        $_SESSION['pwd'] = $pwd;
        $_SESSION['databaseName'] = $databaseName;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Conectarse a la BD</title>
</head>
<body>
    <a href="/" class="volver-link"><< Volver a inicio</a>
    <main>
        <h1>Conectarse a la base de datos</h1>
        <form action="conectar-bd.php" method="POST">
            <label for="serverName">Nombre del servidor:</label>
            <input type="text" name="serverName" id="serverName" required>
            <label for="uid">Usuario (si no usa Windows Authentication):</label>
            <input type="text" name="uid" id="uid">
            <label for="pwd">Contraseña (si no usa Windows Authentication):</label>
            <input type="password" name="pwd" id="pwd">
            <label for="databaseName">Nombre de la base de datos:</label>
            <input type="text" name="databaseName" id="databaseName" value="ComprasBD" required>
            <button type="submit">Conectar</button>
        </form>
        <p>
            <?php
            if (empty($conn)) {
                echo 'Estado: Sin datos de conexión';
            } else {
                echo 'Estado: Se ha guardado su información de conexión';
            }
            ?>
        </p>
    </main>
</body>
</html>