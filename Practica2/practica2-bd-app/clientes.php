<?php
function get_status($status) {
    switch ($status) {
        case 'A':
            return 'Activo';
        case 'S':
            return 'Suspendido';
        case 'M':
            return 'Moroso';
        default:
            return '';
    }
}
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
        $rif = $_POST['rif'];
        $cedula = $_POST['cedula'];
        $nombre = $_POST['nombre'];
        $email = $_POST['email'];
        $direccion = $_POST['direccion'];
        $telefono = $_POST['telefono'];
        $status = $_POST['status'];
        $fechaAfiliacion = $_POST['fechaAfiliacion'];
        $fechaDesafiliacion = $_POST['fechaDesafiliacion'];
        // Insertar cliente en BD
        $tsql = "INSERT INTO Clientes (RIFCliente, Cedula, NombreC, email, DireccionC, TelefonoC, StatusC, FechaAfiliacion, FechaDesafiliacion) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $params = array($rif, $cedula, $nombre, $email, $direccion, $telefono, $status, $fechaAfiliacion, $fechaDesafiliacion);
        $stmt = sqlsrv_query($conn, $tsql, $params);
        if ($stmt === false) {
            die(print_r(sqlsrv_errors(), true));
        }
    } else if ($action === 'update') {
        // Obtener valores del formulario
        $rif = $_POST['rif'];
        $cedula = $_POST['cedula'];
        $nombre = $_POST['nombre'];
        $email = $_POST['email'];
        $direccion = $_POST['direccion'];
        $telefono = $_POST['telefono'];
        $status = $_POST['status'];
        $fechaAfiliacion = $_POST['fechaAfiliacion'];
        $fechaDesafiliacion = $_POST['fechaDesafiliacion'];
        // Actualizar cliente en BD
        $tsql = "UPDATE Clientes SET Cedula = ?, NombreC = ?, email = ?, DireccionC = ?, TelefonoC = ?, StatusC = ?, FechaAfiliacion = ?, FechaDesafiliacion = ? WHERE RIFCliente = ?";
        $params = array($cedula, $nombre, $email, $direccion, $telefono, $status, $fechaAfiliacion, $fechaDesafiliacion, $rif);
        $stmt = sqlsrv_query($conn, $tsql, $params);
        if ($stmt === false) {
            die(print_r(sqlsrv_errors(), true));
        }
    } else if ($action === 'delete') {
        // Obtener valores del formulario
        $rif = $_POST['rif'];
        // Eliminar cliente de BD
        $tsql = "DELETE FROM Clientes WHERE RIFCliente = ?";
        $params = array($rif);
        $stmt = sqlsrv_query($conn, $tsql, $params);
        if ($stmt === false) {
            die(print_r(sqlsrv_errors(), true));
        }
    }
}
// Leer clientes de BD
$tsql = "SELECT * FROM Clientes";
$clientes = sqlsrv_query($conn, $tsql);
if ($clientes === false) {
    die(print_r(sqlsrv_errors(), true));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Clientes</title>
</head>
<body>
    <a href="/" class="volver-link"><< Volver a inicio</a>
    <main>
        <h1>Clientes</h1>
        <button onClick="openDialog('cliente-nuevo-dialog')">Agregar cliente</button>
        <dialog id="cliente-nuevo-dialog">
            <h2>Cliente nuevo</h2>
            <form action="clientes.php" method="post">
                <label for="rif">RIF</label>
                <input autofocus type="text" name="rif" id="rif" required>
                <label for="cedula">Cédula</label>
                <input type="text" name="cedula" id="cedula" required>
                <label for="nombre">Nombre</label>
                <input type="text" name="nombre" id="nombre" required>
                <label for="email">Email</label>
                <input type="text" name="email" id="email" required>
                <label for="direccion">Dirección</label>
                <input type="text" name="direccion" id="direccion" required>
                <label for="telefono">Teléfono</label>
                <input type="text" name="telefono" id="telefono">
                <label for="statusBox">Estatus</label>
                <div id="status-box">
                    <div>
                        <input type="radio" name="status" id="activo" value="A">
                        <label for="activo">Activo</label>
                    </div>
                    <div>
                        <input type="radio" name="status" id="suspendido" value="S">
                        <label for="suspendido">Suspendido</label>
                    </div>
                    <div>
                        <input type="radio" name="status" id="moroso" value="M">
                        <label for="moroso">Moroso</label>
                    </div>
                </div>
                <label for="fechaAfiliacion">Fecha de Afiliación</label>
                <input type="date" name="fechaAfiliacion" id="fechaAfiliacion" required>
                <label for="fechaDesafiliacion">Fecha de Desafiliación</label>
                <input type="date" name="fechaDesafiliacion" id="fechaDesafiliacion">
                <button type="submit" name="_action" value="add">Agregar</button>
            </form>
        </dialog>
        <table>
            <thead>
                <tr>
                    <th>RIF</th>
                    <th>Cédula</th>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Dirección</th>
                    <th>Teléfono</th>
                    <th>Estatus</th>
                    <th>Fecha de afiliación</th>
                    <th>Fecha de desafiliación</th>
                    <th>Opciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = sqlsrv_fetch_array($clientes, SQLSRV_FETCH_ASSOC)) { ?>
                    <tr>
                        <td><?php echo $row['RIFCliente'];?></td>
                        <td><?php echo $row['Cedula'];?></td>
                        <td><?php echo $row['NombreC'];?></td>
                        <td><?php echo $row['email'];?></td>
                        <td><?php echo $row['DireccionC'];?></td>
                        <td><?php echo $row['TelefonoC'];?></td>
                        <td><?php echo get_status($row['StatusC']);?></td>
                        <td><?php echo $row['FechaAfiliacion']->format('d-m-Y');?></td>
                        <td><?php echo $row['FechaDesafiliacion']->format('d-m-Y');?></td>
                        <td>
                            <form action="clientes.php" method="post">
                                <input type="hidden" name="rif" value="<?php echo $row['RIFCliente'];?>">
                                <button class="edit-btn" type="button" onClick="openDialog('<?php echo "update-" . $row['RIFCliente']?>')">Editar</button>
                                <button class="delete-btn" type="submit" name="_action" value="delete">Eliminar</button>
                            </form>
                        </td>
                        <td>
                            <dialog id="<?php echo 'update-' . $row['RIFCliente']?>">
                                <h2>Actualizar cliente</h2>
                                <form action="clientes.php" method="post">
                                    <label for="cedula">Cédula</label>
                                    <input type="text" value="<?php echo $row['Cedula'];?>" name="cedula" id="cedula" required>
                                    <label for="nombre">Nombre</label>
                                    <input type="text" value="<?php echo $row['NombreC'];?>" name="nombre" id="nombre" required>
                                    <label for="email">Email</label>
                                    <input type="text" value="<?php echo $row['email'];?>" name="email" id="email" required>
                                    <label for="direccion">Dirección</label>
                                    <input type="text" value="<?php echo $row['DireccionC'];?>" name="direccion" id="direccion" required>
                                    <label for="telefono">Teléfono</label>
                                    <input type="text" value="<?php echo $row['TelefonoC'];?>" name="telefono" id="telefono">
                                    <label for="statusBox">Estatus</label>
                                    <div id="status-box">
                                        <div>
                                            <input type="radio" <?php echo ($row['StatusC'] === 'A' ? 'checked' : '');?> name="status" id="activo" value="A">
                                            <label for="activo">Activo</label>
                                        </div>
                                        <div>
                                            <input type="radio" <?php echo ($row['StatusC'] === 'S' ? 'checked' : '') ;?> name="status" id="suspendido" value="S">
                                            <label for="activo">Suspendido</label>
                                        </div>
                                        <div>
                                            <input type="radio" <?php echo ($row['StatusC'] === 'M' ? 'checked' : '') ;?> name="status" id="moroso" value="M">
                                            <label for="activo">Moroso</label>
                                        </div>
                                    </div>
                                    <label for="fechaAfiliacion">Fecha de Afiliación</label>
                                    <input type="date" value="<?php echo $row['FechaAfiliacion']->format('Y-m-d');?>" name="fechaAfiliacion" id="fechaAfiliacion" required>
                                    <label for="fechaDesafiliacion">Fecha de Desafiliación</label>
                                    <input type="date" value="<?php if (!empty($row['FechaDesafiliacion'])) echo $row['FechaDesafiliacion']->format('Y-m-d');?>" name="fechaDesafiliacion" id="fechaDesafiliacion">
                                    <input type="hidden" name="rif" value="<?php echo $row['RIFCliente'];?>">
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