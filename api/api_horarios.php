<?php
require_once __DIR__ . '/../controller/HorarioController.php';

$horarioController = new HorarioController();
$horarioController->buscarHorarios(); // Só isso. O método já imprime o JSON e faz exit
