<?php
function get_status($status) {
    if ($status === 'A') {
        return 'Activo';
    } else if ($status === 'D') {
        return 'Desincorporado';
    } else if ($status === 'R') {
        return 'En revisión';
    }
}
function leer_lineas($conn) {
    // Leer Líneas de BD
    $tsql = "SELECT * FROM Lineas";
    $lineas = sqlsrv_query($conn, $tsql);
    if ($lineas === false) {
        die(print_r(sqlsrv_errors(), true));
    }
    return $lineas;
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
        $codigo = $_POST['codigo'];
        $descripcion = $_POST['descripcion'];
        $linea = $_POST['linea'];
        $precio = $_POST['precio'];
        $existencia = $_POST['existencia'];
        $maximo = $_POST['maximo'];
        $minimo = $_POST['minimo'];
        $status = $_POST['status'];
        $fechaDesincorporacion = $_POST['fechaDesincorporacion'];
        // Insertar artículo en BD
        $tsql = "INSERT INTO Articulos (CodArticulo, Descripcion, CodLinea, Precio, Existencia, Maximo, Minimo, StatusA, FechaDesincorporacion) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $params = array($codigo, $descripcion, $linea, $precio, $existencia, $maximo, $minimo, $status, $fechaDesincorporacion);
        $stmt = sqlsrv_query($conn, $tsql, $params);
        if ($stmt === false) {
            die(print_r(sqlsrv_errors(), true));
        }
    } else if ($action === 'update') {
        // Obtener valores del formulario
        $codigo = $_POST['codigo'];
        $descripcion = $_POST['descripcion'];
        $linea = $_POST['linea'];
        $precio = $_POST['precio'];
        $existencia = $_POST['existencia'];
        $maximo = $_POST['maximo'];
        $minimo = $_POST['minimo'];
        $status = $_POST['status'];
        $fechaDesincorporacion = $_POST['fechaDesincorporacion'];
        // Actualizar artículo en BD
        $tsql = "UPDATE Articulos SET Descripcion = ?, CodLinea = ?, Precio = ?, Existencia = ?, Maximo = ?, Minimo = ?, StatusA = ?, FechaDesincorporacion = ? WHERE CodArticulo = ?";
        $params = array($descripcion, $linea, $precio, $existencia, $maximo, $minimo, $status, $fechaDesincorporacion, $codigo);
        $stmt = sqlsrv_query($conn, $tsql, $params);
        if ($stmt === false) {
            die(print_r(sqlsrv_errors(), true));
        }
    } else if ($action === 'delete') {
        // Obtener valores del formulario
        $codigo = $_POST['codigo'];
        // Eliminar línea de BD
        $tsql = "DELETE FROM Articulos WHERE CodArticulo = ?";
        $params = array($codigo);
        $stmt = sqlsrv_query($conn, $tsql, $params);
        if ($stmt === false) {
            die(print_r(sqlsrv_errors(), true));
        }
    }
}
// Leer Artículos de la BD
$tsql = "SELECT * FROM Articulos";
$articulos = sqlsrv_query($conn, $tsql);
if ($articulos === false) {
    die(print_r(sqlsrv_errors(), true));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Artículos</title>
</head>
<body>
    <a href="/" class="volver-link"><< Volver a inicio</a>
    <main>
        <h1>Artículos</h1>
        <button onClick="openDialog('articulo-nuevo-dialog')">Agregar Artículo</button>
        <dialog id="articulo-nuevo-dialog">
            <h2>Artículo nuevo</h2>
            <form action="articulos.php" method="post">
                <label for="codigo">Código de Artículo (A####)</label>
                <input type="text" name="codigo" id="codigo" required>
                <label for="descripcion">Descripción</label>
                <input type="text" name="descripcion" id="descripcion" required>
                <label for="linea">Línea de Suministro</label>
                <select name="linea" id="linea">
                    <?php $lineas = leer_lineas($conn); ?>
                    <?php while($row = sqlsrv_fetch_array($lineas, SQLSRV_FETCH_ASSOC)) { ?>
                        <option value="<?php echo $row['CodLinea'];?>"><?php echo $row['DescripcionL'];?></option>
                    <?php } ?>
                </select>
                <label for="precio">Precio</label>
                <input type="number" min="1" step="0.01" name="precio" id="precio" required>
                <label for="existencia">Existencia</label>
                <input type="number" min="0" name="existencia" id="existencia" required>
                <label for="maximo">Máximo</label>
                <input type="number" min="0" name="maximo" id="maximo" required>
                <label for="minimo">Mínimo</label>
                <input type="number" min="0" name="minimo" id="minimo" required>
                <label for="status-box">Estado</label>
                <div id="status-box">
                    <div>
                        <input type="radio" name="status" id="activo" value="A">
                        <label for="activo">Activo</label>
                    </div>
                    <div>
                        <input type="radio" name="status" id="desincorporado" value="D">
                        <label for="desincorporado">Desincorporado</label>
                    </div>
                    <div>
                        <input type="radio" name="status" id="revision" value="R">
                        <label for="revision">En revisión</label>
                    </div>
                </div>
                <label for="fechaDesincorporacion">Fecha de desincorporación</label>
                <input type="date" name="fechaDesincorporacion" id="fechaDesincorporacion">
                <button type="submit" name="_action" value="add">Agregar</button>
            </form>
        </dialog>
        <table>
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Descripción</th>
                    <th>Código de Línea</th>
                    <th>Precio</th>
                    <th>Existencia</th>
                    <th>Máximo</th>
                    <th>Mínimo</th>
                    <th>Estado</th>
                    <th>Fecha de Desincorporación</th>
                    <th>Opciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = sqlsrv_fetch_array($articulos, SQLSRV_FETCH_ASSOC)) { ?>
                    <tr>
                        <td><?php echo $row['CodArticulo'];?></td>
                        <td><?php echo $row['Descripcion'];?></td>
                        <td><?php echo $row['CodLinea'];?></td>
                        <td><?php echo $row['Precio'];?></td>
                        <td><?php echo $row['Existencia'];?></td>
                        <td><?php echo $row['Maximo'];?></td>
                        <td><?php echo $row['Minimo'];?></td>
                        <td><?php echo get_status($row['StatusA']);?></td>
                        <td><?php if (!empty($row['FechaDesincorporacion'])) echo $row['FechaDesincorporacion']->format('d-m-Y');?></td>
                        <td>
                            <form action="articulos.php" method="post">
                                <input type="hidden" name="codigo" value="<?php echo $row['CodArticulo'];?>">
                                <button class="edit-btn" type="button" onClick="openDialog('<?php echo "update-" . $row['CodArticulo']?>')">Editar</button>
                                <button class="delete-btn" type="submit" name="_action" value="delete">Eliminar</button>
                            </form>
                        </td>
                        <td>
                            <dialog id="<?php echo 'update-' . $row['CodArticulo']?>">
                                <h2>Actualizar artículo</h2>
                                <form action="articulos.php" method="post">
                                    <label for="codigo">Código de Artículo</label>
                                    <input type="text" value="<?php echo $row['CodArticulo'];?>" name="codigo" id="codigo" readonly>
                                    <label for="descripcion">Descripción</label>
                                    <input type="text" value="<?php echo $row['Descripcion'];?>" name="descripcion" id="descripcion" required>
                                    <label for="linea">Línea de Suministro</label>
                                    <select name="linea" id="linea">
                                        <?php $lineas = leer_lineas($conn); ?>
                                        <?php while($linea = sqlsrv_fetch_array($lineas, SQLSRV_FETCH_ASSOC)) { ?>
                                            <option value="<?php echo $linea['CodLinea'];?>" <?php echo ($linea['CodLinea'] === $row['CodLinea'] ? 'selected' : '');?>><?php echo $linea['DescripcionL'];?></option>
                                        <?php } ?>
                                    </select>
                                    <label for="precio">Precio</label>
                                    <input type="number" min="1" step="0.01" value="<?php echo $row['Precio'];?>" name="precio" id="precio" required>
                                    <label for="existencia">Existencia</label>
                                    <input type="number" min="0" value="<?php echo $row['Existencia'];?>" name="existencia" id="existencia" required>
                                    <label for="maximo">Máximo</label>
                                    <input type="number" min="0" value="<?php echo $row['Maximo'];?>" name="maximo" id="maximo" required>
                                    <label for="minimo">Mínimo</label>
                                    <input type="number" min="0" value="<?php echo $row['Minimo'];?>" name="minimo" id="minimo" required>
                                    <label for="statusBox">Estatus</label>
                                    <div id="status-box">
                                        <div>
                                            <input type="radio" <?php echo ($row['StatusA'] === 'A' ? 'checked' : '');?> name="status" id="activo" value="A">
                                            <label for="activo">Activo</label>
                                        </div>
                                        <div>
                                            <input type="radio" <?php echo ($row['StatusA'] === 'D' ? 'checked' : '') ;?> name="status" id="suspendido" value="D">
                                            <label for="suspendido">Desincorporado</label>
                                        </div>
                                        <div>
                                            <input type="radio" <?php echo ($row['StatusA'] === 'R' ? 'checked' : '') ;?> name="status" id="revision" value="R">
                                            <label for="revision">En revisión</label>
                                        </div>
                                    </div>
                                    <label for="fechaDesincorporacion">Fecha de desincorporación</label>
                                    <input type="date" value="<?php if (!empty($row['FechaDesincorporacion'])) echo $row['FechaDesincorporacion']->format('Y-m-d');?>" name="fechaDesincorporacion" id="fechaDesincorporacion">
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