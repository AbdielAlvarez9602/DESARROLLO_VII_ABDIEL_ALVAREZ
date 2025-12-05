<?php
require_once "config.php";

$msg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        if (isset($_POST['action']) && $_POST['action'] == 'add') {
            $sql = "INSERT INTO usuarios (nombre, email, password) VALUES (:n, :e, :p)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':n' => $_POST['nombre'], ':e' => $_POST['email'],
                ':p' => password_hash($_POST['password'], PASSWORD_DEFAULT)
            ]);
            $msg = "Usuario agregado.";
        } elseif (isset($_POST['action']) && $_POST['action'] == 'update') {
            $sql = "UPDATE usuarios SET nombre=:n, email=:e WHERE id=:id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':n' => $_POST['nombre'], ':e' => $_POST['email'], ':id' => $_POST['id']]);
            $msg = "Usuario actualizado.";
        } elseif (isset($_POST['action']) && $_POST['action'] == 'delete') {
            $stmt = $pdo->prepare("DELETE FROM usuarios WHERE id = :id");
            $stmt->execute([':id' => $_POST['id']]);
            $msg = "Usuario eliminado.";
        }
    } catch (PDOException $e) {
        $msg = "Error: " . $e->getMessage();
    }
}

$limit = 5;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $limit;
$search = isset($_GET['q']) ? $_GET['q'] : '';

$sql = "SELECT * FROM usuarios WHERE nombre LIKE :q OR email LIKE :q LIMIT :start, :limit";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':q', "%$search%", PDO::PARAM_STR);
$stmt->bindValue(':start', $start, PDO::PARAM_INT);
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->execute();
$usuarios = $stmt->fetchAll();

$stmtCount = $pdo->prepare("SELECT COUNT(*) FROM usuarios WHERE nombre LIKE :q OR email LIKE :q");
$stmtCount->execute([':q' => "%$search%"]);
$total_pages = ceil($stmtCount->fetchColumn() / $limit);
?>

<!DOCTYPE html>
<html>
<body>
    <a href="index.php">Volver</a>
    <h2>Usuarios (PDO)</h2>
    <p><?= $msg ?></p>
    
    <form method="GET">
        <input type="text" name="q" value="<?= htmlspecialchars($search) ?>">
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
        <?php foreach ($usuarios as $u): ?>
        <tr>
            <td><?= $u['id'] ?></td>
            <td><?= htmlspecialchars($u['nombre']) ?></td>
            <td><?= htmlspecialchars($u['email']) ?></td>
            <td>
                <form method="POST" style="display:inline;">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" value="<?= $u['id'] ?>">
                    <button onclick="return confirm('Eliminar?')">X</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
        <a href="?page=<?= $i ?>&q=<?= $search ?>"><?= $i ?></a>
    <?php endfor; ?>
</body>
</html>