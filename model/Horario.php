<?php

require_once __DIR__ . '/../config/conexao.php';
require_once '../controller/HorarioController.php';

class Horario
{
  public function __construct(
    private int $idHorario,
    private string $horaInicio,
    private string $horaFim,
    private string $diaSemana
  ) {}

  public function getIdHorario(): int
  {
    return $this->idHorario;
  }

  public function getHoraInicio(): string
  {
    return $this->horaInicio;
  }

  public function getHoraFim(): string
  {
    return $this->horaFim;
  }

  public function getDiaSemana(): string
  {
    return $this->diaSemana;
  }

  public function setHoraInicio(string $horaInicio): void
  {
    $this->horaInicio = $horaInicio;
  }

  public function setHoraFim(string $horaFim): void
  {
    $this->horaFim = $horaFim;
  }

  public function setDiaSemana(string $diaSemana): void
  {
    $this->diaSemana = $diaSemana;
  }
}
