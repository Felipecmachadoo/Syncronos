<?php
// Incluir o arquivo com a conexão com banco de dados
include_once '../../config/conexao.php';

try {
    // Criar a QUERY para recuperar os eventos do banco de dados
    $query_events = "SELECT id, title, color, start, end, status FROM events";

    // Prepara a QUERY
    $result_events = $conn->prepare($query_events);

    // Executar a QUERY
    $result_events->execute();

    // Criar o array que recebe os eventos
    $eventos = [];

    // Percorrer a lista de registros retornado do banco de dados
    while ($row_events = $result_events->fetch(PDO::FETCH_ASSOC)) {
        $eventos[] = [
            'id' => $row_events['id'],
            'title' => $row_events['title'],
            'color' => $row_events['color'],
            'start' => $row_events['start'],
            'end' => $row_events['end'],
            'extendedProps' => [
                'status' => $row_events['status'] // ← Esta linha é a correção principal
            ]
        ];
    }
} catch (PDOException $e) {
    // Em caso de erro, retornar array vazio
    $eventos = [];
}

// Converter o array em objeto e retornar para o JavaScript
echo json_encode($eventos);
