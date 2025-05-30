<?php

// Incluir o arquivo com a conexão com banco de dados
include_once '../../config/conexao.php';

// Receber os dados enviado pelo JavaScript
$dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);

// Validar se todos os campos obrigatórios foram enviados
if (empty($dados['edit_title']) || empty($dados['edit_start']) || empty($dados['edit_end']) || empty($dados['edit_status'])) {
    $retorna = ['status' => false, 'msg' => 'Erro: Todos os campos são obrigatórios!'];
    echo json_encode($retorna);
    exit;
}

// Validar se a data de fim não é anterior à data de início
if (strtotime($dados['edit_end']) < strtotime($dados['edit_start'])) {
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

$cor = isset($cores_status[$dados['edit_status']]) ? $cores_status[$dados['edit_status']] : '#3a8d60';

try {
    // Criar a QUERY editastrar evento no banco de dados
    $query_edit_event = "UPDATE events SET title=:title, color=:color, status=:status, start=:start, end=:end WHERE id=:id";

    // Prepara a QUERY
    $edit_event = $conn->prepare($query_edit_event);

    // Substituir o link pelo valor
    $edit_event->bindParam(':title', $dados['edit_title'], PDO::PARAM_STR);
    $edit_event->bindParam(':color', $cor, PDO::PARAM_STR);
    $edit_event->bindParam(':start', $dados['edit_start'], PDO::PARAM_STR);
    $edit_event->bindParam(':end', $dados['edit_end'], PDO::PARAM_STR);
    $edit_event->bindParam(':status', $dados['edit_status'], PDO::PARAM_STR);
    $edit_event->bindParam(':id', $dados['edit_id'], PDO::PARAM_STR);

    // Verificar se consegui editastrar corretamente
    if ($edit_event->execute()) {
        $retorna = [
            'status' => true,
            'msg' => 'Evento editado com sucesso!',
            'id' => $dados['edit_id'],
            'title' => $dados['edit_title'],
            'color' => $cor,
            'start' => $dados['edit_start'],
            'end' => $dados['edit_end'],
            'extendedProps' => [
                'status' => $dados['edit_status']
            ]
        ];
    } else {
        $retorna = ['status' => false, 'msg' => 'Evento não editado com sucesso!'];
    }
} catch (PDOException $e) {
    $retorna = ['status' => false, 'msg' => 'Erro: ' . $e->getMessage()];
}

// Converter o array em objeto e retornar para o JavaScript
echo json_encode($retorna);
