<?php
require_once '../controller/HorarioController.php';
require_once '../config/conexao.php';

$controller = new HorarioController();
$rota = $_POST['rota'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  switch ($rota) {
    case 'salvarHorario':
      $controller->salvarHorarios($_POST);
      break;
  }
}
