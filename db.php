<?php
// Datos de conexión para Supabase (PostgreSQL)
$host = "aws-0-us-east-2.pooler.supabase.com";
$port = "6543";
$dbname = "postgres";
$username = "postgres.oajnnwxvdltdebleftil";
$password = "gucu100905"; // Reemplaza esto con tu contraseña real de Supabase

// Cadena de conexión para PostgreSQL
$connection_string = "host=$host port=$port dbname=$dbname user=$username password=$password";

// Intentar conectar usando la extensión pg_connect de PostgreSQL
$conn = pg_connect($connection_string);

// Verificar la conexión
if (!$conn) {
    die("Conexión fallida: " . pg_last_error());
}

// Función auxiliar para simular comportamiento similar a mysqli
function pg_query_params_safe($conn, $query, $params = []) {
    if (empty($params)) {
        return pg_query($conn, $query);
    } else {
        return pg_query_params($conn, $query, $params);
    }
}
?>
