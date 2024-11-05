<?php
// Configurações de conexão
$host = 'mysql';
$db = 'shibakita';
$user = 'toshiro';
$pass = trim(file_get_contents('/run/secrets/mysql_password'));
$charset = 'utf8mb4';

// Configuração DSN
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    // Estabelece a conexão
    $pdo = new PDO($dsn, $user, $pass, $options);
    echo "Conexão com banco de dados estabelecida com sucesso!<br>";

    // Cria a tabela se não existir
    $sql_create = "CREATE TABLE IF NOT EXISTS dados (
        AlunoID INT PRIMARY KEY,
        Nome VARCHAR(255),
        Sobrenome VARCHAR(255),
        Endereco VARCHAR(255),
        Cidade VARCHAR(255),
        Host VARCHAR(255),
        DataCriacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";

    $pdo->exec($sql_create);

    // Gera valores aleatórios
    $valor_rand1 = rand(1, 999);
    $valor_rand2 = strtoupper(substr(bin2hex(random_bytes(4)), 1));
    $host_name = gethostname();

    // Prepara e executa a inserção usando prepared statement
    $sql_insert = "INSERT INTO dados (AlunoID, Nome, Sobrenome, Endereco, Cidade, Host) 
                   VALUES (:alunoId, :nome, :sobrenome, :endereco, :cidade, :host)";

    $stmt = $pdo->prepare($sql_insert);

    $resultado = $stmt->execute([
        ':alunoId' => $valor_rand1,
        ':nome' => $valor_rand2,
        ':sobrenome' => $valor_rand2,
        ':endereco' => $valor_rand2,
        ':cidade' => $valor_rand2,
        ':host' => $host_name
    ]);

    if ($resultado) {
        echo "<br>Novo registro criado com sucesso!<br>";
        echo "ID do Aluno: $valor_rand1<br>";
        echo "Valor Aleatório: $valor_rand2<br>";
        echo "Hostname: $host_name<br>";
    }

    // Exibe os últimos 5 registros
    echo "<br><h3>Últimos 5 registros:</h3>";
    $stmt = $pdo->query("SELECT * FROM dados ORDER BY DataCriacao DESC LIMIT 5");
    while ($row = $stmt->fetch()) {
        echo "ID: " . $row['AlunoID'] .
            " | Nome: " . $row['Nome'] .
            " | Host: " . $row['Host'] .
            " | Data: " . $row['DataCriacao'] . "<br>";
    }

} catch (PDOException $e) {
    echo "Erro na conexão ou operação: " . $e->getMessage();
}