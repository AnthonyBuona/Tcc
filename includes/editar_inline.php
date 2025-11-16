<?php
// Arquivo: includes/editar_inline.php (Processa a submissão de campo único via AJAX)

header('Content-Type: application/json');

session_start();

include 'config.php'; // Assumindo que este arquivo define $conexao

// Verifica se os dados necessários foram enviados via POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['id'], $_POST['campo'], $_POST['valor'], $_POST['tabela'], $_POST['pk'])) {
    echo json_encode(['success' => false, 'error' => 'Dados insuficientes ou método inválido.']);
    exit;
}

// 1. Coleta e sanitiza os dados
$id      = $_POST['id'];
$campo   = $_POST['campo'];
$valor   = $_POST['valor'];
$tabela  = $_POST['tabela'];
$pk      = $_POST['pk']; // Chave primária (ex: id_prof)

// 2. Validações de segurança
if (!isset($conexao) || $conexao->connect_error) {
    echo json_encode(['success' => false, 'error' => 'Erro de conexão com o banco de dados.']);
    exit;
}

$tabelas_permitidas = ['professor'];
$campos_permitidos_professor = ['nome', 'cpf', 'areas'];

if (!in_array($tabela, $tabelas_permitidas) || !in_array($campo, $campos_permitidos_professor)) {
    echo json_encode(['success' => false, 'error' => 'Tabela ou campo não permitido para edição.']);
    exit;
}

// 3. Prepara a query (Usando Prepared Statements)
$sql = "UPDATE $tabela SET $campo = ? WHERE $pk = ?";

if ($stmt = $conexao->prepare($sql)) {
    
    // Liga os parâmetros (valor é string 's', ID será tratado como string 's' ou integer 'i')
    $tipo_valor = 's';
    
    // Tentativa de inferir o tipo do ID (ex: se id_prof é sempre um número)
    $tipo_id = 's'; // Default para string (mais seguro em caso de PK mista)
    if (is_numeric($id)) {
        $tipo_id = 'i';
    }

    $stmt->bind_param($tipo_valor . $tipo_id, $valor, $id);
    
    // 4. Executa a query
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode(['success' => true, 'message' => 'Edição salva com sucesso.']);
        } else {
            echo json_encode(['success' => true, 'message' => 'Nenhuma mudança detectada ou ID não encontrado.']);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Erro na execução do UPDATE: ' . $stmt->error]);
    }
    
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'error' => 'Erro na preparação do statement: ' . $conexao->error]);
}

$conexao->close();
?>