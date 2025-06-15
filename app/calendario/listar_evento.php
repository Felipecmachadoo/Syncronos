<?php
// Incluir o arquivo com a conexão com banco de dados
include_once '../../config/conexao.php';

try {
    // Instanciar a classe de conexão
    $conexao = new Conexao();
    $conn = $conexao->conectar();

    // Criar a QUERY para recuperar os eventos do banco de dados
    $query_events = "SELECT idAgendamento, titulo, cor, dataInicio, dataFim, status FROM agendamentos"; // Corrigido para usar os nomes corretos das colunas

    // Prepara a QUERY
    $result_events = $conn->prepare($query_events);

    // Executar a QUERY
    $result_events->execute();

    // Criar o array que recebe os eventos
    $eventos = [];

    // Percorrer a lista de registros retornado do banco de dados
    while ($row_events = $result_events->fetch(PDO::FETCH_ASSOC)) {
        $eventos[] = [
            'idAgendamento' => $row_events['idAgendamento'],
            'titulo' => $row_events['titulo'],
            'cor' => $row_events['cor'],
            'dataInicio' => $row_events['dataInicio'],
            'dataFim' => $row_events['dataFim'],
            'extendedProps' => [
                'status' => $row_events['status']
            ]
        ];
    }
} catch (PDOException $e) {
    // Em caso de erro, retornar array vazio
    $eventos = [];
}

// Converter o array em objeto e retornar para o JavaScript
echo json_encode($eventos);
