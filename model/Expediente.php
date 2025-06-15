<?php

require_once __DIR__ . '/../config/conexao.php';
require_once '../controller/ExpedienteController.php';

class Expediente
{
  public function __construct(
    private int $idExpediente,
    private int $idProfissional,
    private string $diaSemana,
    private string $inicioExpediente,
    private string $inicioIntervalo,
    private string $fimIntervalo,
    private string $fimExpediente
  ) {}

  // Getters
  public function getIdExpediente(): int
  {
    return $this->idExpediente;
  }

  public function getIdProfissional(): int
  {
    return $this->idProfissional;
  }

  public function getDiaSemana(): string
  {
    return $this->diaSemana;
  }

  public function getInicioExpediente(): string
  {
    return $this->inicioExpediente;
  }

  public function getInicioIntervalo(): string
  {
    return $this->inicioIntervalo;
  }

  public function getFimIntervalo(): string
  {
    return $this->fimIntervalo;
  }

  public function getFimExpediente(): string
  {
    return $this->fimExpediente;
  }

  // Setters
  public function setIdProfissional(int $idProfissional): void
  {
    $this->idProfissional = $idProfissional;
  }

  public function setDiaSemana(string $diaSemana): void
  {
    $this->diaSemana = $diaSemana;
  }

  public function setInicioExpediente(string $inicioExpediente): void
  {
    $this->inicioExpediente = $inicioExpediente;
  }

  public function setInicioIntervalo(string $inicioIntervalo): void
  {
    $this->inicioIntervalo = $inicioIntervalo;
  }

  public function setFimIntervalo(string $fimIntervalo): void
  {
    $this->fimIntervalo = $fimIntervalo;
  }

  public function setFimExpediente(string $fimExpediente): void
  {
    $this->fimExpediente = $fimExpediente;
  }
}
