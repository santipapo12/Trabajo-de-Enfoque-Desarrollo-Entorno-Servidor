<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['usuario'])) {
    die("Debes iniciar sesión para ver el carrito");
}

// Inicializar carrito si no existe
if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}

// Añadir producto al carrito
if (isset($_GET['add'])) {
    $id = $_GET['add'];
    if (!isset($_SESSION['carrito'][$id])) {
        $_SESSION['carrito'][$id] = 1;
    } else {
        $_SESSION['carrito'][$id]++;
    }
    header("Location: carrito.php");
    exit;
}

// Eliminar producto del carrito
if (isset($_GET['remove'])) {
    $id = $_GET['remove'];
    if (isset($_SESSION['carrito'][$id])) {
        unset($_SESSION['carrito'][$id]);
    }
    header("Location: carrito.php");
    exit;
}

// Confirmar pedido
if (isset($_POST['confirmar'])) {
    $usuario_id = $_SESSION['usuario']['id'];
    $stmt = $pdo->prepare("INSERT INTO pedidos (usuario_id, fecha) VALUES (?, NOW())");
    $stmt->execute([$usuario_id]);
    $pedido_id = $pdo->lastInsertId();

    foreach ($_SESSION['carrito'] as $producto_id => $cantidad) {
        $stmt2 = $pdo->prepare("INSERT INTO pedidos_detalle (pedido_id, producto_id, cantidad) VALUES (?,?,?)");
        $stmt2->execute([$pedido_id, $producto_id, $cantidad]);
    }

    // Vaciar carrito
    $_SESSION['carrito'] = [];
    $mensaje = "Pedido confirmado con éxito!";
}

// Traer datos de los productos del carrito
$productos = [];
if (!empty($_SESSION['carrito'])) {
    $ids = implode(',', array_keys($_SESSION['carrito']));
    $stmt = $pdo->query("SELECT * FROM productos WHERE id IN ($ids)");
    $productos = $stmt->fetchAll();
}

?>

<h2>Carrito de compras</h2>

<?php if(isset($mensaje)) echo "<p style='color:green;'>$mensaje</p>"; ?>

<?php if(empty($productos)): ?>
    <p>El carrito está vacío.</p>
<?php else: ?>
    <table border="1" cellpadding="5">
        <tr>
            <th>Producto</th>
            <th>Precio</th>
            <th>Cantidad</th>
            <th>Total</th>
            <th>Acción</th>
        </tr>
        <?php 
        $total = 0;
        foreach($productos as $p):
            $cantidad = $_SESSION['carrito'][$p['id']];
            $subtotal = $p['precio'] * $cantidad;
            $total += $subtotal;
        ?>
        <tr>
            <td><?= $p['nombre'] ?></td>
            <td><?= number_format($p['precio'],2) ?> €</td>
            <td><?= $cantidad ?></td>
            <td><?= number_format($subtotal,2) ?> €</td>
            <td><a href="carrito.php?remove=<?= $p['id'] ?>">Eliminar</a></td>
        </tr>
        <?php endforeach; ?>
        <tr>
            <td colspan="3"><strong>Total</strong></td>
            <td colspan="2"><?= number_format($total,2) ?> €</td>
        </tr>
    </table>
    <form method="POST">
        <button name="confirmar">Confirmar Pedido</button>
    </form>
<?php endif; ?>
