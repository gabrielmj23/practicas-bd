<?php
function leer_clientes($conn) {
    // Leer Líneas de BD
    $tsql = "SELECT * FROM Clientes";
    $clientes = sqlsrv_query($conn, $tsql);
    if ($clientes === false) {
        die(print_r(sqlsrv_errors(), true));
    }
    return $clientes;
}
function get_tipo_pago($tipo) {
    if ($tipo === 'E') {
        return 'Efectivo';
    } else if ($tipo === 'T') {
        return 'Transferencia';
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
        $numFactura = $_POST['numFactura'];
        $rifCliente = $_POST['rifCliente'];
        $fechaEmision = date('Y-m-d h:i:s a', time());
        $tipoPago = $_POST['tipoPago'];
        $tipoMoneda = $_POST['tipoMoneda'];
        // Insertar artículo en BD
        $tsql = "INSERT INTO Facturas (NumFactura, RIFCliente, FechaEmision, TipoPago, TipoMoneda) VALUES (?, ?, ?, ?, ?)";
        $params = array($numFactura, $rifCliente, $fechaEmision, $tipoPago, $tipoMoneda);
        $stmt = sqlsrv_query($conn, $tsql, $params);
        if ($stmt === false) {
            die(print_r(sqlsrv_errors(), true));
        }
    } else if ($action === 'update') {
        // Obtener valores del formulario
        $numFactura = $_POST['numFactura'];
        $fechaEmision = $_POST['fechaEmision'];
        $tipoPago = $_POST['tipoPago'];
        $tipoMoneda = $_POST['tipoMoneda'];
        // Actualizar artículo en BD
        $tsql = "UPDATE Facturas SET FechaEmision = ?, TipoPago = ?, TipoMoneda = ? WHERE NumFactura = ?";
        $params = array($fechaEmision, $tipoPago, $tipoMoneda, $numFactura);
        $stmt = sqlsrv_query($conn, $tsql, $params);
        if ($stmt === false) {
            die(print_r(sqlsrv_errors(), true));
        }
    } else if ($action === 'delete') {
        // Obtener valores del formulario
        $numFactura = $_POST['numFactura'];
        // Eliminar línea de BD
        $tsql = "DELETE FROM Facturas WHERE NumFactura = ?";
        $params = array($numFactura);
        $stmt = sqlsrv_query($conn, $tsql, $params);
        if ($stmt === false) {
            die(print_r(sqlsrv_errors(), true));
        }
    }
}
// Leer Artículos de la BD
$tsql = "SELECT * FROM Facturas";
$facturas = sqlsrv_query($conn, $tsql);
if ($facturas === false) {
    die(print_r(sqlsrv_errors(), true));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Facturas</title>
</head>
<body>
    <a href="/" class="volver-link"><< Volver a inicio</a>
    <main>
        <h1>Facturas</h1>
        <button onClick="openDialog('factura-nueva-dialog')">Agregar Factura</button>
        <dialog id="factura-nueva-dialog">
            <h2>Factura nueva</h2>
            <form action="facturas.php" method="post">
                <label for="numFactura">Número de Factura (F####)</label>
                <input type="text" name="numFactura" id="numFactura" required>
                <label for="rifCliente">Cliente</label>
                <select name="rifCliente" id="rifCliente">
                    <?php $clientes = leer_clientes($conn); ?>
                    <?php while($row = sqlsrv_fetch_array($clientes, SQLSRV_FETCH_ASSOC)) { ?>
                        <option value="<?php echo $row['RIFCliente'];?>"><?php echo $row['Cedula'] . ' - ' . $row['NombreC'];?></option>
                    <?php } ?>
                </select>
                <label for="status-box">Tipo de Pago</label>
                <div id="status-box">
                    <div>
                        <input type="radio" name="tipoPago" id="efectivo" value="E">
                        <label for="efectivo">Efectivo</label>
                    </div>
                    <div>
                        <input type="radio" name="tipoPago" id="transferencia" value="T">
                        <label for="transferencia">Transferencia</label>
                    </div>
                </div>
                <label for="status-box">Tipo de Moneda</label>
                <div id="status-box">
                    <div>
                        <input type="radio" name="tipoMoneda" id="bolivares" value="Bolivares">
                        <label for="bolivares">Bolívares</label>
                    </div>
                    <div>
                        <input type="radio" name="tipoMoneda" id="divisas" value="Divisas">
                        <label for="divisas">Divisas</label>
                    </div>
                    <div>
                        <input type="radio" name="tipoMoneda" id="petro" value="Petro">
                        <label for="petro">Petro</label>
                    </div>
                </div>
                <button type="submit" name="_action" value="add">Agregar</button>
            </form>
        </dialog>
        <table>
            <thead>
                <tr>
                    <th>Número</th>
                    <th>RIF del Cliente</th>
                    <th>Fecha de Emisión</th>
                    <th>Tipo de Pago</th>
                    <th>Tipo de Moneda</th>
                    <th>Opciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = sqlsrv_fetch_array($facturas, SQLSRV_FETCH_ASSOC)) { ?>
                    <tr>
                        <td><?php echo $row['NumFactura'];?></td>
                        <td><?php echo $row['RIFCliente'];?></td>
                        <td><?php echo $row['FechaEmision']->format('d-m-Y');?></td>
                        <td><?php echo get_tipo_pago($row['TipoPago']);?></td>
                        <td><?php echo $row['TipoMoneda'];?></td>
                        <td>
                            <form action="articulos.php" method="post">
                                <input type="hidden" name="numFactura" value="<?php echo $row['NumFactura'];?>">
                                <button class="edit-btn" type="button" onClick="openDialog('<?php echo "update-" . $row['NumFactura']?>')">Editar</button>
                                <button class="delete-btn" type="submit" name="_action" value="delete">Eliminar</button>
                            </form>
                        </td>
                        <td>
                            <dialog id="<?php echo 'update-' . $row['NumFactura']?>">
                                <h2>Actualizar factura</h2>
                                <form action="facturas.php" method="post">
                                    <label for="numFactura">Código de Artículo</label>
                                    <input type="text" value="<?php echo $row['NumFactura'];?>" name="numFactura" id="numFactura" readonly>
                                    <label for="RIFCliente">RIF del Cliente</label>
                                    <input type="text" value="<?php echo $row['RIFCliente'];?>" name="rifCliente" id="rifCliente" readonly>
                                    <label for="fechaEmision">Fecha de Emisión</label>
                                    <input type="date" value="<?php echo $row['FechaEmision']->format('Y-m-d');?>" name="fechaEmision" id="fechaEmision" required>
                                    <label for="statusBox">Tipo de Pago</label>
                                    <div id="status-box">
                                        <div>
                                            <input type="radio" <?php echo ($row['TipoPago'] === 'E' ? 'checked' : '');?> name="tipoPago" id="efectivo" value="E">
                                            <label for="efectivo">Efectivo</label>
                                        </div>
                                        <div>
                                            <input type="radio" <?php echo ($row['TipoPago'] === 'T' ? 'checked' : '') ;?> name="tipoPago" id="transferencia" value="T">
                                            <label for="transferencia">Transferencia</label>
                                        </div>
                                    </div>
                                    <label for="statusBox">Tipo de Moneda</label>
                                    <div id="status-box">
                                        <div>
                                            <input type="radio" <?php echo ($row['TipoMoneda'] === 'Bolivares' ? 'checked' : '');?> name="tipoMoneda" id="bolivares" value="Bolivares">
                                            <label for="bolivares">Bolívares</label>
                                        </div>
                                        <div>
                                            <input type="radio" <?php echo ($row['TipoMoneda'] === 'Divisas' ? 'checked' : '') ;?> name="tipoMoneda" id="divisas" value="Divisas">
                                            <label for="divisas">Divisas</label>
                                        </div>
                                        <div>
                                            <input type="radio" <?php echo ($row['TipoMoneda'] === 'Petros' ? 'checked' : '') ;?> name="tipoMoneda" id="petro" value="Petro">
                                            <label for="petro">Petro</label>
                                        </div>
                                    </div>
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