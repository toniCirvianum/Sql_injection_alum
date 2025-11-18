<?php
//parametres de connexio a la BBDD
// $db_host = $_ENV['DB_HOST'];
// $db_name = $_ENV['DB_NAME'];
// $db_user = $_ENV['DB_USER'];
// $db_password = $_ENV['DB_PASSWORD'];

include('credentials.php');


// Connexió bàsica
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
];
$pdo = new PDO("mysql:host=" . DB_HOST. ";dbname=" . DB_NAME,DB_USER,DB_PASSWORD, $options);

// Recollim dades del formulari
$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

//Codi vulnerable a SQL INJECTION
$sql = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
$result = $pdo->query($sql);
$hasResult = $result->rowCount() > 0;

// Comprovació resultat
if ($hasResult) {
    echo "<h3 style='color:green'>Login correcte! </h3>";
} else {
    echo "<h3 style='color:red'>Credencials incorrectes</h3>";
}

// Mostrem la consulta per entendre què està passant
echo "<p><b>Consulta executada:</b> $sql</p>";
