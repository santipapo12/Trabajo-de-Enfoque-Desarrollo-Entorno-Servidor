<?php
session_start();
require '../../config/db.php';

// Bloquear acceso si no es admin
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'admin') {
    die("Acceso denegado");
}

// Crear producto
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare("INSERT INTO productos (nombre, precio, stock) VALUES (?,?,?)");
    $stmt->execute([$_POST['nombre'], $_POST['precio'], $_POST['stock']]);
    header("Location: productos.php");
    exit;
}

// Eliminar producto
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM productos WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: productos.php");
    exit;
}

// Obtener todos los productos
$productos = $pdo->query("SELECT * FROM productos")->fetchAll();
?>

<h2>Panel de productos</h2>

<form method="POST">
    <input name="nombre" placeholder="Nombre producto" required><br><br>
    <input name="precio" type="number" step="0.01" placeholder="Precio" required><br><br>
    <input name="stock" type="number" placeholder="Stock" required><br><br>
    <button>Crear Producto</button>
</form>

<hr>

<h3>Productos existentes</h3>
<?php foreach ($productos as $p): ?>
    <div>
        <?= $p['nombre'] ?> - <?= $p['precio'] ?> € - Stock: <?= $p['stock'] ?>
        | <a href="productos.php?delete=<?= $p['id'] ?>" onclick="return confirm('¿Seguro que quieres eliminar este producto?')">Eliminar</a>
    </div>
<?php endforeach; ?>

