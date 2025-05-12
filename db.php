<?php
<?php
$dsn = "pgsql:host=aws-0-us-east-2.pooler.supabase.com;port=6543;dbname=postgres";
$username = "postgres.oajnnwxvdltdebleftil";
$password = "[gucu100905]"; // Reemplaza [YOUR-PASSWORD] con tu contraseña real

try {
    $conn = new PDO($dsn, $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}
?>
