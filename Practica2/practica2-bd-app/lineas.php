<?php
function connect_to_db() {
    session_start();
    $uid = $_SESSION['uid'];
    $pwd = $_SESSION['pwd'];
    $connectionInfo = array();
    if (empty($uid)) {
        // Conectar con Windows Auth
        $serverName = $_SESSION['serverName'];
        $databaseName = $_SESSION['databaseName'];
        $connectionInfo = array("Database" => $databaseName);
    } else {
        // Conectar con sa
        $connectionInfo = array("UID" => $uid, "PWD" => $pwd, "Database" => $databaseName);
    }
    $conn = sqlsrv_connect($serverName, $connectionInfo);
    return $conn;
}
$conn = connect_to_db();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Leer acción
    $action = $_POST['_action'];
    if ($action === 'add') {
        // Obtener valores del formulario
        $codigo = $_POST['codigo'];
        $descripcion = $_POST['descripcion'];
        // Insertar línea en BD
        $tsql = "INSERT INTO Lineas (CodLinea, DescripcionL) VALUES (?, ?)";
        $params = array($codigo, $descripcion);
        $stmt = sqlsrv_query($conn, $tsql, $params);
        if ($stmt === false) {
            die(print_r(sqlsrv_errors(), true));
        }
    } else if ($action === 'update') {
        // Obtener valores del formulario
        $codigo = $_POST['codigo'];
        $descripcion = $_POST['descripcion'];
        // Actualizar línea en BD
        $tsql = "UPDATE Lineas SET DescripcionL = ? WHERE CodLinea = ?";
        $params = array($descripcion, $codigo);
        $stmt = sqlsrv_query($conn, $tsql, $params);
        if ($stmt === false) {
            die(print_r(sqlsrv_errors(), true));
        }
    } else if ($action === 'delete') {
        // Obtener valores del formulario
        $codigo = $_POST['codigo'];
        // Eliminar línea de BD
        $tsql = "DELETE FROM Lineas WHERE CodLinea = ?";
        $params = array($codigo);
        $stmt = sqlsrv_query($conn, $tsql, $params);
        if ($stmt === false) {
            die(print_r(sqlsrv_errors(), true));
        }
    }
}
// Leer Líneas de BD
$tsql = "SELECT * FROM Lineas";
$lineas = sqlsrv_query($conn, $tsql);
if ($lineas === false) {
    die(print_r(sqlsrv_errors(), true));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Líneas de Suministro</title>
</head>
<body>
    <a href="/" class="volver-link"><< Volver a inicio</a>
    <main>
        <h1>Líneas de Suministro</h1>
        <button onClick="openDialog('linea-nueva-dialog')">Agregar Línea</button>
        <dialog id="linea-nueva-dialog">
            <h2>Línea nueva</h2>
            <form action="lineas.php" method="post">
                <label for="codigo">Código de Línea</label>
                <input type="text" name="codigo" id="codigo" required>
                <label for="descripcion">Descripción</label>
                <input type="text" name="descripcion" id="descripcion" required>
                <button type="submit" name="_action" value="add">Agregar</button>
            </form>
        </dialog>
        <table>
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Descripción</th>
                    <th>Opciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = sqlsrv_fetch_array($lineas, SQLSRV_FETCH_ASSOC)) { ?>
                    <tr>
                        <td><?php echo $row['CodLinea'];?></td>
                        <td><?php echo $row['DescripcionL'];?></td>
                        <td>
                            <form action="lineas.php" method="post">
                                <input type="hidden" name="codigo" value="<?php echo $row['CodLinea'];?>">
                                <button class="edit-btn" type="button" onClick="openDialog('<?php echo "update-" . $row['CodLinea']?>')">Editar</button>
                                <button class="delete-btn" type="submit" name="_action" value="delete">Eliminar</button>
                            </form>
                        </td>
                        <td>
                            <dialog id="<?php echo 'update-' . $row['CodLinea']?>">
                                <h2>Actualizar línea</h2>
                                <form action="lineas.php" method="post">
                                    <label for="codigo">Código de Línea</label>
                                    <input type="text" value="<?php echo $row['CodLinea'];?>" name="codigo" id="codigo" readonly>
                                    <label for="descripcion">Descripción</label>
                                    <input type="text" value="<?php echo $row['DescripcionL'];?>" name="descripcion" id="descripcion" required>
                                    <button type="submit" name="_action" value="update">Actualizar</button>
                                </form>
                            </dialog>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </main>
    <script src="funcs.js"></script>
</body>
</html>