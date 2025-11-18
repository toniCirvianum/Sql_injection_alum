<?php




include('credentials.php');
include('./vendor/autoload.php');

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();


function getConnection()
{
    //parametres de connexio a la BBDD
    $db_host = $_ENV['DB_HOST'];
    $db_name = $_ENV['DB_NAME'];
    $db_user = $_ENV['DB_USER'];
    $db_password = $_ENV['DB_PASSWORD'];

    // Connexió bàsica
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ];

    try {
        $pdo = new PDO("mysql:host=" . $db_host . ";dbname=" . $db_name, $db_user, $db_password, $options);
        $pdo->exec('SET CHARACTER SET UTF8');
        echo "Connexio feta a la base de dades";
        return $pdo;
    } catch (PDOException $error) {
        echo "Error al connectar a la base de dades: ". $error->getMessage();
    }
}

function closeCoonection($connection) {
    $connection=null;
}


function executQyuery($sql, $params=null, $returnLastInsertId=false) {
    //executa al sentencia que passem a la variable sql
    //Amb una sola funció getionem SELECt, INSERT, UPDATE i DELETE
    try {
        $connection = getConnection(); //conectem a la BBDD
        $mySql = $connection->prepare($sql); //Preparem la sentencia sql

        if ($params!=null) {
            //si tenim parametres la sentencia s'executa amb aquests parametres
            $result = $mySql->execute($params);
        } else {
            //si no l'executem sense paramtres
            $result = $mySql->execute();
        }

        if ($returnLastInsertId) {
            //si estem inserint o fent un update ens retorna el ultim id
            $result = $connection ->lastInsertId();
        } else {
            //sino, mirem si s'ha modificat alguna fila de la BBDD
            $hasModofifiedRow = $mySql->rowCount() >0;
            if ($hasModofifiedRow) {
                $result = $mySql;
            } else {
                $result = null;
            }

        }
        return $result;

        
    } catch (PDOException $error) {
        echo "Error al llançar el SQL: ".$error->getMessage();
        return null;
    } finally {
        //tanquem la connexio a la BBDD
        if ($connection!=null) {
            closeCoonection($connection);
        }
    }
}






// Recollim dades del formulari
$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

//Codi vulnerable a SQL INJECTION
// $sql = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
// $result = $pdo->query($sql);
// $hasResult = $result->rowCount() > 0;

$sql ="SELECT * FROM users WHERE username=:username AND password=:password";
$params =[
    ':username'=>$username,
    ':password'=>$password
];

$result=executQyuery($sql,$params);

// Comprovació resultat
if ($result) {
    echo "<h3 style='color:green'>Login correcte! </h3>";
} else {
    echo "<h3 style='color:red'>Credencials incorrectes</h3>";
}

// Mostrem la consulta per entendre què està passant
echo "<p><b>Consulta executada:</b> $sql</p>";
