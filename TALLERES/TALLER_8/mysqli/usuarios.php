<?php
require_once "config.php";
$msg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['action']) && $_POST['action'] == 'add') {
        $sql = "INSERT INTO usuarios (nombre, email, password) VALUES (?, ?, ?)";
        if($stmt = mysqli_prepare($conn, $sql)){
            $pass = password_hash($_POST['password'], PASSWORD_DEFAULT);
            mysqli_stmt_bind_param($stmt, "sss", $_POST['nombre'], $_POST['email'], $pass);
            if(mysqli_stmt_execute($stmt)) $msg = "Usuario agregado.";
            else $msg = "Error: " . mysqli_error($conn);
            mysqli_stmt_close($stmt);
        }
    } elseif (isset($_POST['action']) && $_POST['action'] == 'delete') {
        $sql = "DELETE FROM usuarios WHERE id = ?";
        if($stmt = mysqli_prepare($conn, $sql)){
            mysqli_stmt_bind_param($stmt, "i", $_POST['id']);
            if(mysqli_stmt_execute($stmt)) $msg = "Usuario eliminado.";
            mysqli_stmt_close($stmt);
        }
    }
}

$limit = 5;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $limit;
$search = isset($_GET['q']) ? "%".$_GET['q']."%" : '%%';

$sql = "SELECT * FROM usuarios WHERE nombre LIKE ? OR email LIKE ? LIMIT ?, ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "ssii", $search, $search, $start, $limit);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$sqlCount = "SELECT COUNT(*) as total FROM usuarios WHERE nombre LIKE ? OR email LIKE ?";
$stmtCount = mysqli_prepare($conn, $sqlCount);
mysqli_stmt_bind_param($stmtCount, "ss", $search, $search);
mysqli_stmt_execute($stmtCount);
$countResult = mysqli_stmt_get_result($stmtCount);
$total_rows = mysqli_fetch_assoc($countResult)['total'];
$total_pages = ceil($total_rows / $limit);
?>

<!DOCTYPE html>
<html>
<body>
    <a href="index.php">Volver</a>
    <h2>Usuarios (MySQLi)</h2>
    <p><?= $msg ?></p>
    <form method="GET">
        <input type="text" name="q" value="<?= isset($_GET['q']) ? htmlspecialchars($_GET['q']) : '' ?>">
        <button type="submit">Buscar</button>
    </form>
    <br>
    <form method="POST">
        <input type="hidden" name="action" value="add">
        <input type="text" name="nombre" placeholder="Nombre" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Contraseña" required>
        <button type="submit">Agregar</button>
    </form>
    <br>
    <table border="1">
        <tr><th>ID</th><th>Nombre</th><th>Email</th><th>Acciones</th></tr>
        <?php while($row = mysqli_fetch_assoc($result)): ?>
        <tr>
            <td><?= $row['id'] ?></td>
            <td><?= htmlspecialchars($row['nombre']) ?></td>
            <td><?= htmlspecialchars($row['email']) ?></td>
            <td>
                <form method="POST" style="display:inline;">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" value="<?= $row['id'] ?>">
                    <button onclick="return confirm('Eliminar?')">X</button>
                </form>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
        <a href="?page=<?= $i ?>&q=<?= isset($_GET['q']) ? $_GET['q'] : '' ?>"><?= $i ?></a>
    <?php endfor; ?>
</body>
</html>