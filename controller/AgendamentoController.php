<?php
session_start();
require_once '../model/Agendamento.php';

// Configurações iniciais
header_remove();
ob_start();
header('Content-Type: application/json; charset=utf-8');


// Verifica autenticação
if (!isset($_SESSION['usuario_id'])) {
  http_response_code(401);
  echo json_encode(['success' => false, 'error' => 'Usuário não autenticado']);
  exit;
}

try {
  // Verifica método HTTP
  if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    throw new Exception('Método não permitido', 405);
  }

  // Recebe e valida dados
  $json = file_get_contents('php://input');
  $data = json_decode($json, true);

  if (json_last_error() !== JSON_ERROR_NONE) {
    throw new Exception('JSON inválido', 400);
  }

  // Campos obrigatórios
  $requiredFields = ['idServico', 'idProfissional', 'Titulo', 'dataInicio', 'dataFim'];
  foreach ($requiredFields as $field) {
    if (empty($data[$field])) {
      throw new Exception("Campo obrigatório faltando: $field", 400);
    }
  }

  // Dados fixos conforme solicitado
  $agendamentoData = [
    'idUsuario'     => $_SESSION['usuario_id'],
    'idServico'     => (int)$data['idServico'],
    'idProfissional' => (int)$data['idProfissional'],
    'Titulo'        => filter_var($data['Titulo'], FILTER_SANITIZE_STRING),
    'Cor'           => '#3a8d60', // Cor fixa
    'dataInicio'    => date('Y-m-d H:i:s', strtotime($data['dataInicio'])),
    'dataFim'       => date('Y-m-d H:i:s', strtotime($data['dataFim'])),
    'Status'        => 'Confirmado' // Status fixo
  ];

  // Insere no banco
  $agendamentoModel = new AgendamentoModel();

  // Verifica conflitos primeiro
  if ($agendamentoModel->hasConflitoHorario(
    $agendamentoData['idProfissional'],
    $agendamentoData['dataInicio'],
    $agendamentoData['dataFim']
  )) {
    throw new Exception('Profissional já possui agendamento neste horário', 409);
  }

  $idAgendamento = $agendamentoModel->create($agendamentoData);

  // Resposta de sucesso
  http_response_code(201);
  echo json_encode([
    'success' => true,
    'idAgendamento' => $idAgendamento,
    'message' => 'Agendamento confirmado com sucesso'
  ]);
} catch (Exception $e) {
  http_response_code($e->getCode() ?: 500);
  echo json_encode([
    'success' => false,
    'error' => $e->getMessage(),
    'code' => $e->getCode()
  ]);
  error_log('AgendamentoController: ' . $e->getMessage());
} finally {
  ob_end_flush();
}
