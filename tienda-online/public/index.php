<?php
session_start();
require '../config/db.php';

$productos = $pdo->query("SELECT * FROM productos")->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Tienda Online</title>
</head>
<body>

<h1>Tienda Online</h1>

<?php if (isset($_SESSION['usuario'])): ?>
    <p>Usuario conectado</p>
<?php else: ?>
    <p>No has iniciado sesión</p>
<?php endif; ?>

<hr>

<?php if (count($productos) === 0): ?>
    <p>No hay productos todavía</p>
<?php endif; ?>

<?php foreach ($productos as $p): ?>
    <div style="border:1px solid #000; padding:10px; margin:10px;">
        <h3><?= $p['nombre'] ?></h3>
        <p>Precio: <?= $p['precio'] ?> €</p>
        <a href="carrito.php?add=<?= $p['id'] ?>">Añadir al carrito</a>
    </div>
<?php endforeach; ?>

</body>
</html>
