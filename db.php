<?php
// Archivo db.php actualizado con los parámetros exactos de Supabase

// Parámetros de conexión proporcionados por Supabase
$host = "aws-0-us-east-2.pooler.supabase.com";
$port = "6543";
$dbname = "postgres";
$username = "postgres.oajnnwxvdltdebleftil";
$password = "gucu100905"; // IMPORTANTE: Reemplaza con tu contraseña real
$pool_mode = "transaction";

// Construir cadena de conexión
$conn_string = "host=$host port=$port dbname=$dbname user=$username password=$password";
// Añadir opciones de pool si es necesario
if ($pool_mode) {
    $conn_string .= " options='-c pool_mode=$pool_mode'";
}

try {
    // Intentar establecer la conexión
    $pg_conn = pg_connect($conn_string);
    
    // Verificar si la conexión fue exitosa
    if (!$pg_conn) {
        throw new Exception("No se pudo conectar a PostgreSQL: " . pg_last_error());
    }
    
    // Clase adaptadora para mantener la compatibilidad con el código mysqli existente
    class PgAdapter {
        private $pg_conn;
        
        public function __construct($pg_connection) {
            $this->pg_conn = $pg_connection;
        }
        
        // Método para ejecutar consultas SQL
        public function query($sql) {
            // Ejecutar la consulta
            $result = pg_query($this->pg_conn, $sql);
            
            if (!$result) {
                // Si hay un error en la consulta
                echo "Error en la consulta: " . pg_last_error($this->pg_conn);
                return false;
            }
            
            // Devolver un objeto resultado compatible con mysqli
            return new PgResult($result);
        }
        
        // Método para escapar cadenas (prevenir inyección SQL)
        public function real_escape_string($string) {
            return pg_escape_string($this->pg_conn, $string);
        }
        
        // Cerrar la conexión
        public function close() {
            return pg_close($this->pg_conn);
        }
    }
    
    // Clase para resultados, compatible con mysqli_result
    class PgResult {
        private $pg_result;
        public $num_rows;
        
        public function __construct($pg_result) {
            $this->pg_result = $pg_result;
            $this->num_rows = pg_num_rows($pg_result);
        }
        
        // Obtener una fila como array asociativo
        public function fetch_assoc() {
            return pg_fetch_assoc($this->pg_result);
        }
        
        // Liberar el resultado
        public function free() {
            return pg_free_result($this->pg_result);
        }
    }
    
    // Crear el objeto conn compatible con el resto del código
    $conn = new PgAdapter($pg_conn);
    
} catch (Exception $e) {
    // Mostrar error y terminar
    die("Error de conexión: " . $e->getMessage());
}
?>
