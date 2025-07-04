<?php

include_once '../../config/conexao.php';

$id = filter_input(INPUT_GET, 'idAgendamento', FILTER_SANITIZE_NUMBER_INT);

// Acessa o IF quando existe o id do evento
if (!empty($id)) {
    try {
        $conexao = new Conexao();
        $conn = $conexao->conectar();

        // Criar a QUERY apagar evento no banco de dados
        $query_apagar_event = "DELETE FROM agendamentos WHERE idAgendamento=:idAgendamento";

        // Prepara a QUERY
        $apagar_event = $conn->prepare($query_apagar_event);

        // Substituir o link pelo valor
        $apagar_event->bindParam(':idAgendamento', $id, PDO::PARAM_INT);

        // Verificar se conseguiu apagar corretamente
        if ($apagar_event->execute()) {
            $retorna = ['status' => true, 'msg' => 'Evento apagado com sucesso!'];
        } else {
            $retorna = ['status' => false, 'msg' => 'Erro: Evento não apagado!'];
        }
    } catch (PDOException $e) {
        $retorna = ['status' => false, 'msg' => 'Erro: ' . $e->getMessage()];
    }
} else { // Acessa o ELSE quando o id está vazio
    $retorna = ['status' => false, 'msg' => 'Erro: Necessário enviar o id do evento!'];
}

// Converter o array em objeto e retornar para o JavaScript
echo json_encode($retorna);
