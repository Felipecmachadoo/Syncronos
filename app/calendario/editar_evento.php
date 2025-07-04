<?php

include_once '../../config/conexao.php';

// Recebe os dados enviado pelo JavaScript
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

$cores_status = [
    'confirmado' => '#3a8d60',
    'pendente' => '#f39c12',
    'cancelado' => '#e74c3c'
];

$cor = isset($cores_status[$dados['edit_status']]) ? $cores_status[$dados['edit_status']] : '#3a8d60';

try {
    $conexao = new Conexao();
    $conn = $conexao->conectar();

    // Criar a QUERY para editar evento no banco de dados
    $query_edit_event = "UPDATE agendamentos SET titulo=:titulo, cor=:cor, status=:status, dataInicio=:dataInicio, dataFim=:dataFim WHERE idAgendamento=:idAgendamento";

    // Prepara a QUERY
    $edit_event = $conn->prepare($query_edit_event);

    // Substituir os parâmetros pelos valores
    $edit_event->bindParam(':titulo', $dados['edit_title'], PDO::PARAM_STR);
    $edit_event->bindParam(':cor', $cor, PDO::PARAM_STR);
    $edit_event->bindParam(':dataInicio', $dados['edit_start'], PDO::PARAM_STR);
    $edit_event->bindParam(':dataFim', $dados['edit_end'], PDO::PARAM_STR);
    $edit_event->bindParam(':status', $dados['edit_status'], PDO::PARAM_STR);
    $edit_event->bindParam(':idAgendamento', $dados['edit_id'], PDO::PARAM_INT);  // Alterado para PARAM_INT pois ID deve ser numérico

    // Verificar se conseguiu editar corretamente
    if ($edit_event->execute()) {
        $retorna = [
            'status' => true,
            'msg' => 'Evento editado com sucesso!',
            'idAgendamento' => $dados['edit_id'],
            'titulo' => $dados['edit_title'],
            'cor' => $cor,
            'dataInicio' => $dados['edit_start'],
            'dataFim' => $dados['edit_end'],
            'extendedProps' => [
                'status' => $dados['edit_status']
            ]
        ];
    } else {
        $retorna = ['status' => false, 'msg' => 'Erro: Evento não foi editado!'];
    }
} catch (PDOException $e) {
    $retorna = ['status' => false, 'msg' => 'Erro: ' . $e->getMessage()];
}

// Converter o array em objeto e retornar para o JavaScript
echo json_encode($retorna);
