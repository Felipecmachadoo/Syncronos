<?php

// Incluir o arquivo com a conexão com banco de dados
include_once '../../config/conexao.php';

// Receber os dados enviado pelo JavaScript
$dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);

// Validar se todos os campos obrigatórios foram enviados
if (empty($dados['cad_title']) || empty($dados['cad_start']) || empty($dados['cad_end']) || empty($dados['cad_status'])) {
    $retorna = ['status' => false, 'msg' => 'Erro: Todos os campos são obrigatórios!'];
    echo json_encode($retorna);
    exit;
}

// Validar se a data de fim não é anterior à data de início
if (strtotime($dados['cad_end']) < strtotime($dados['cad_start'])) {
    $retorna = ['status' => false, 'msg' => 'Erro: A data de fim não pode ser anterior à data de início!'];
    echo json_encode($retorna);
    exit;
}

// Definir cor baseada no status
$cores_status = [
    'confirmado' => '#3a8d60',
    'pendente' => '#f39c12',
    'cancelado' => '#e74c3c'
];

$cor = isset($cores_status[$dados['cad_status']]) ? $cores_status[$dados['cad_status']] : '#3a8d60';

try {
    // Criar a QUERY cadastrar evento no banco de dados
    $query_cad_event = "INSERT INTO events (title, color, start, end, status) VALUES (:title, :color, :start, :end, :status)";

    // Prepara a QUERY
    $cad_event = $conn->prepare($query_cad_event);

    // Substituir o link pelo valor
    $cad_event->bindParam(':title', $dados['cad_title'], PDO::PARAM_STR);
    $cad_event->bindParam(':color', $cor, PDO::PARAM_STR);
    $cad_event->bindParam(':start', $dados['cad_start'], PDO::PARAM_STR);
    $cad_event->bindParam(':end', $dados['cad_end'], PDO::PARAM_STR);
    $cad_event->bindParam(':status', $dados['cad_status'], PDO::PARAM_STR);

    // Verificar se consegui cadastrar corretamente
    if ($cad_event->execute()) {
        $retorna = [
            'status' => true,
            'msg' => 'Evento cadastrado com sucesso!',
            'id' => $conn->lastInsertId(),
            'title' => $dados['cad_title'],
            'color' => $cor,
            'start' => $dados['cad_start'],
            'end' => $dados['cad_end'],
            'extendedProps' => [
                'status' => $dados['cad_status']
            ]
        ];
    } else {
        $retorna = ['status' => false, 'msg' => 'Erro: Evento não cadastrado!'];
    }
} catch (PDOException $e) {
    $retorna = ['status' => false, 'msg' => 'Erro: ' . $e->getMessage()];
}

// Converter o array em objeto e retornar para o JavaScript
echo json_encode($retorna);
