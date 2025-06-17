<?php
session_start();
require_once '../model/Usuario.php';

header('Content-Type: application/json');

$email = $_POST['email'];
$senha = $_POST['senha'];

$usuario = Usuario::autenticar($email, $senha);

if ($usuario) {
  $_SESSION['usuario_id'] = $usuario->getIdUsuario();
  $_SESSION['Tipo'] = $usuario->getTipo();
  $_SESSION['usuario_nome'] = $usuario->getNome(); // 👉 adiciona isso

  if ($usuario->getTipo() === 'administrador') {
    echo json_encode([
      'status' => 'success',
      'redirect' => '../pages/calendario.php'
    ]);
  } else {
    echo json_encode([
      'status' => 'success',
      'redirect' => '../pages/barbearia.php'
    ]);
  }
} else {
  echo json_encode([
    'status' => 'error',
    'message' => 'Credenciais inválidas'
  ]);
}
