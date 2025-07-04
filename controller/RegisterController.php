<?php
require_once __DIR__ . '/../config/conexao.php';
require_once __DIR__ . '/../model/Usuario.php';

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

ini_set('display_errors', 0);
error_reporting(E_ALL);

// Função para enviar resposta JSON
function sendResponse($statusCode, $responseData)
{
  http_response_code($statusCode);
  echo json_encode($responseData);
  exit;
}

try {
  // Verificar método HTTP
  if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendResponse(405, ['status' => 'error', 'message' => 'Método não permitido']);
  }

  // Obter dados da requisição
  $input = json_decode(file_get_contents('php://input'), true);

  if (json_last_error() !== JSON_ERROR_NONE) {
    sendResponse(400, ['status' => 'error', 'message' => 'JSON inválido']);
  }

  // Validar campos obrigatórios
  $required = ['nome', 'email', 'cpf', 'senha'];
  foreach ($required as $field) {
    if (empty($input[$field])) {
      sendResponse(400, ['status' => 'error', 'message' => "Campo {$field} é obrigatório"]);
    }
  }

  $database = new Conexao();
  $db = $database->conectar();

  // Verificar se email já existe
  $query = "SELECT idUsuario FROM usuarios WHERE email = ?";
  $stmt = $db->prepare($query);
  $stmt->execute([$input['email']]);

  if ($stmt->rowCount() > 0) {
    sendResponse(409, ['status' => 'error', 'message' => 'E-mail já cadastrado']);
  }

  // Verificar se CPF já existe
  $query = "SELECT idUsuario FROM usuarios WHERE cpf = ?";
  $stmt = $db->prepare($query);
  $stmt->execute([$input['cpf']]);

  if ($stmt->rowCount() > 0) {
    sendResponse(409, ['status' => 'error', 'message' => 'CPF já cadastrado']);
  }

  // Criar novo usuário
  $usuario = new Usuario(
    0,
    $input['nome'],
    $input['cpf'],
    $input['telefone'] ?? '',
    $input['email'],
    $input['senha'],
    'cliente'
  );

  $query = "INSERT INTO usuarios (nome, cpf, telefone, email, senha, tipo) VALUES (?, ?, ?, ?, ?, ?)";
  $stmt = $db->prepare($query);

  $success = $stmt->execute([
    $usuario->getNome(),
    $usuario->getCpf(),
    $usuario->getTelefone(),
    $usuario->getEmail(),
    $usuario->getSenha(),
    $usuario->getTipo()
  ]);

  if (!$success) {
    sendResponse(500, ['status' => 'error', 'message' => 'Erro ao registrar usuário']);
  }

  // Sucesso
  sendResponse(201, [
    'status' => 'success',
    'message' => 'Usuário registrado com sucesso',
    'redirect' => '../auth/login.php',
    'usuario_id' => $db->lastInsertId()
  ]);
} catch (Exception $e) {
  sendResponse(500, ['status' => 'error', 'message' => 'Erro interno no servidor']);
}
