<?php
require_once '../controller/ProfissionalController.php';
$controller = new ProfissionalController();
$dados = $controller->listarProfissionais();
header('Content-Type: application/json');
echo json_encode($dados);
