<?php
// Datos de conexión para Supabase (PostgreSQL)
$host = "aws-0-us-east-2.pooler.supabase.com";
$port = "6543";
$dbname = "postgres";
$username = "postgres.oajnnwxvdltdebleftil";
$password = "TU-CONTRASEÑA-AQUÍ"; // Reemplaza esto con tu contraseña real de Supabase

// Intentar conectar usando la extensión pg_connect de PostgreSQL
$conn = pg_connect("host=$host port=$port dbname=$dbname user=$username password=$password");

// Verificar la conexión
if (!$conn) {
    die("Conexión fallida: " . pg_last_error());
}

// Clase para simular comportamiento de mysqli en PostgreSQL
// Esto facilita la migración sin cambiar mucho código
class PgSQLAdapter {
    private $conn;
    
    public function __construct($pgconn) {
        $this->conn = $pgconn;
    }
    
    public function query($sql) {
        $result = pg_query($this->conn, $sql);
        if (!$result) {
            return false;
        }
        return new PgSQLResult($result);
    }
    
    public function close() {
        return pg_close($this->conn);
    }
    
    public function real_escape_string($string) {
        return pg_escape_string($this->conn, $string);
    }
}

// Clase para simular el resultado de mysqli en PostgreSQL
class PgSQLResult {
    private $result;
    public $num_rows;
    
    public function __construct($pgresult) {
        $this->result = $pgresult;
        $this->num_rows = pg_num_rows($pgresult);
    }
    
    public function fetch_assoc() {
        return pg_fetch_assoc($this->result);
    }
    
    public function free() {
        return pg_free_result($this->result);
    }
}

// Crear el adaptador que simula mysqli
$conn = new PgSQLAdapter($conn);
?>

