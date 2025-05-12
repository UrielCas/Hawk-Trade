<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Iniciar Sesión</title>
  <link rel="stylesheet" href="estilos.css">
</head>
<body>

  <h2>Iniciar Sesión</h2>

<form method="POST" action="procesar_login.php">
  <input type="text" name="matricula" placeholder="Z + la matricula, ej.ZS23123456" required 
         pattern="ZS[0-9]{8}" 
         title="La matrícula debe iniciar con 'ZS' seguido de 8 números">
  
  <input type="password" name="contrasena" placeholder="Contraseña" required>
  
  <button type="submit">Iniciar sesión</button>
</form>


</body>
</html>
