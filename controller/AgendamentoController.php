<?php
session_start();
require_once __DIR__ . '/../model/Agendamento.php';

// Configurações iniciais
error_reporting(E_ALL);
ini_set('display_errors', 0); // Não exibe erros na resposta
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../../logs/php_errors.log');
header('Content-Type: application/json; charset=utf-8');
date_default_timezone_set('America/Sao_Paulo');

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
  if ($json === false) {
    throw new Exception('Não foi possível ler os dados da requisição', 400);
  }

  $data = json_decode($json, true);
  if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
    throw new Exception('JSON inválido: ' . json_last_error_msg(), 400);
  }

  // Log dos dados recebidos (apenas para debug)
  error_log('Dados recebidos: ' . print_r($data, true));

  // Campos obrigatórios
  $requiredFields = ['idServico', 'idProfissional', 'Titulo', 'dataInicio', 'dataFim'];
  foreach ($requiredFields as $field) {
    if (empty($data[$field])) {
      throw new Exception("Campo obrigatório faltando: $field", 400);
    }
  }

  // Processamento das datas - considera o horário local (America/Sao_Paulo)
  try {
    $dataInicio = DateTime::createFromFormat(
      'Y-m-d H:i:s',
      $data['dataInicio'],
      new DateTimeZone('America/Sao_Paulo')
    );

    $dataFim = DateTime::createFromFormat(
      'Y-m-d H:i:s',
      $data['dataFim'],
      new DateTimeZone('America/Sao_Paulo')
    );

    if (!$dataInicio || !$dataFim) {
      throw new Exception("Formato de data inválido", 400);
    }

    // Garante que as datas estão no formato correto
    $dataInicioStr = $dataInicio->format('Y-m-d H:i:s');
    $dataFimStr = $dataFim->format('Y-m-d H:i:s');
  } catch (Exception $e) {
    throw new Exception("Erro ao processar datas: " . $e->getMessage(), 400);
  }

  // Prepara dados do agendamento
  $agendamentoData = [
    'idUsuario' => $_SESSION['usuario_id'],
    'idServico' => (int)$data['idServico'],
    'idProfissional' => (int)$data['idProfissional'],
    'Titulo' => filter_var($data['Titulo'], FILTER_SANITIZE_STRING),
    'Cor' => '#3a8d60', // Cor fixa
    'dataInicio' => $dataInicioStr,
    'dataFim' => $dataFimStr,
    'Status' => 'Confirmado'
  ];

  // Log dos dados processados (apenas para debug)
  error_log('Dados do agendamento: ' . print_r($agendamentoData, true));

  // Instancia o modelo
  $agendamentoModel = new AgendamentoModel();

  // Verifica conflitos de horário
  if ($agendamentoModel->hasConflitoHorario(
    $agendamentoData['idProfissional'],
    $agendamentoData['dataInicio'],
    $agendamentoData['dataFim']
  )) {
    throw new Exception('Profissional já possui agendamento neste horário', 409);
  }

  // Cria o agendamento
  $idAgendamento = $agendamentoModel->create($agendamentoData);

  if (!$idAgendamento) {
    throw new Exception('Falha ao criar agendamento no banco de dados', 500);
  }

  // Resposta de sucesso
  http_response_code(201);
  echo json_encode([
    'success' => true,
    'idAgendamento' => $idAgendamento,
    'message' => 'Agendamento confirmado com sucesso',
    'data' => [
      'inicio' => $agendamentoData['dataInicio'],
      'fim' => $agendamentoData['dataFim']
    ]
  ]);
} catch (Exception $e) {
  // Log do erro completo
  error_log('Erro no AgendamentoController: ' . $e->getMessage() .
    ' na linha ' . $e->getLine() .
    ' do arquivo ' . $e->getFile());

  // Resposta de erro
  http_response_code($e->getCode() ?: 500);
  echo json_encode([
    'success' => false,
    'error' => $e->getMessage(),
    'code' => $e->getCode()
  ]);
}
