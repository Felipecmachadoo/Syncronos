<?php
require_once __DIR__ . '/../controller/HorarioController.php';

$horarioController = new HorarioController();
$horarios = $horarioController->buscarHorarios();

header('Content-Type: application/json');
echo json_encode($horarios);
