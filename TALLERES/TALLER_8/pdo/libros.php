<?php
require_once "config.php";

$msg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        if (isset($_POST['action']) && $_POST['action'] == 'add') {
            $sql = "INSERT INTO libros (titulo, autor, isbn, anio, cantidad, disponible) VALUES (:t, :a, :i, :y, :c, :c)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':t' => $_POST['titulo'], ':a' => $_POST['autor'],
                ':i' => $_POST['isbn'], ':y' => $_POST['anio'], ':c' => $_POST['cantidad']
            ]);
            $msg = "Libro agregado.";
        } elseif (isset($_POST['action']) && $_POST['action'] == 'update') {
            $sql = "UPDATE libros SET titulo=:t, autor=:a, isbn=:i, anio=:y WHERE id=:id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':t' => $_POST['titulo'], ':a' => $_POST['autor'],
                ':i' => $_POST['isbn'], ':y' => $_POST['anio'], ':id' => $_POST['id']
            ]);
            $msg = "Libro actualizado.";
        } elseif (isset($_POST['action']) && $_POST['action'] == 'delete') {
            $stmt = $pdo->prepare("DELETE FROM libros WHERE id = :id");
            $stmt->execute([':id' => $_POST['id']]);
            $msg = "Libro eliminado.";
        }
    } catch (PDOException $e) {
        $msg = "Error: " . $e->getMessage();
    }
}

$limit = 5;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $limit;
$search = isset($_GET['q']) ? $_GET['q'] : '';

$sql = "SELECT * FROM libros WHERE titulo LIKE :q OR autor LIKE :q LIMIT :start, :limit";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':q', "%$search%", PDO::PARAM_STR);
$stmt->bindValue(':start', $start, PDO::PARAM_INT);
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->execute();
$libros = $stmt->fetchAll();

$stmtCount = $pdo->prepare("SELECT COUNT(*) FROM libros WHERE titulo LIKE :q OR autor LIKE :q");
$stmtCount->execute([':q' => "%$search%"]);
$total_pages = ceil($stmtCount->fetchColumn() / $limit);
?>

<!DOCTYPE html>
<html>
<body>
    <a href="index.php">Volver</a>
    <h2>Libros (PDO)</h2>
    <p><?= $msg ?></p>
    
    <form method="GET">
        <input type="text" name="q" value="<?= htmlspecialchars($search) ?>">
        <button type="submit">Buscar</button>
    </form>
    <br>
    <form method="POST">
        <input type="hidden" name="action" value="add">
        <input type="text" name="titulo" placeholder="Título" required>
        <input type="text" name="autor" placeholder="Autor" required>
        <input type="text" name="isbn" placeholder="ISBN" required>
        <input type="number" name="anio" placeholder="Año">
        <input type="number" name="cantidad" placeholder="Cantidad" required>
        <button type="submit">Agregar</button>
    </form>
    <br>
    <table border="1">
        <tr><th>ID</th><th>Título</th><th>Autor</th><th>ISBN</th><th>Disp.</th><th>Acciones</th></tr>
        <?php foreach ($libros as $l): ?>
        <tr>
            <td><?= $l['id'] ?></td>
            <td><?= htmlspecialchars($l['titulo']) ?></td>
            <td><?= htmlspecialchars($l['autor']) ?></td>
            <td><?= htmlspecialchars($l['isbn']) ?></td>
            <td><?= $l['disponible'] ?></td>
            <td>
                <form method="POST" style="display:inline;">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" value="<?= $l['id'] ?>">
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