<?php
require '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("INSERT INTO usuarios (nombre,email,password,rol) VALUES (?,?,?, 'cliente')");
    $stmt->execute([$nombre, $email, $password]);
    header("Location: login.php");
}
?>

<h2>Registro</h2>

<form method="POST">
    <input name="nombre" placeholder="Nombre" required><br><br>
    <input type="email" name="email" placeholder="Email" required><br><br>
    <input type="password" name="password" placeholder="Contraseña" required><br><br>
    <button>Registrarse</button>
</form>
