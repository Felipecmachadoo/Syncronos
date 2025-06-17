<?php
include_once '../../config/conexao.php';

try {
    $conexao = new Conexao();
    $conn = $conexao->conectar();

    // Agora buscamos também o nome do usuário
    $query_events = "
        SELECT 
            ag.idAgendamento, 
            ag.idUsuario, 
            u.nome AS nomeUsuario, 
            ag.titulo, 
            ag.cor, 
            ag.dataInicio, 
            ag.dataFim, 
            ag.status 
        FROM agendamentos ag
        LEFT JOIN usuarios u ON ag.idUsuario = u.idUsuario
    ";

    $result_events = $conn->prepare($query_events);
    $result_events->execute();

    $eventos = [];

    while ($row_events = $result_events->fetch(PDO::FETCH_ASSOC)) {
        $eventos[] = [
            'idAgendamento' => $row_events['idAgendamento'],
            'titulo' => $row_events['titulo'],
            'cor' => $row_events['cor'],
            'dataInicio' => $row_events['dataInicio'],
            'dataFim' => $row_events['dataFim'],
            'extendedProps' => [
                'status' => $row_events['status'],
                'idUsuario' => $row_events['idUsuario'],
                'nomeUsuario' => $row_events['nomeUsuario'], // Agora temos o nome!
            ]
        ];
    }
} catch (PDOException $e) {
    $eventos = [];
}

echo json_encode($eventos);
