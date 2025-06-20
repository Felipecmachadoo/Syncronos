<?php

require_once __DIR__ . '/../config/conexao.php';
require_once '../controller/ProfissionalController.php';

class Profissional
{
  public function __construct(
    private ?int $idProfissional,
    private string $nome,
    private string $especialidade,
    private string $celular
  ) {}

  // Getters
  public function getIdProfissional(): ?int
  {
    return $this->idProfissional;
  }

  public function getNome(): string
  {
    return $this->nome;
  }

  public function getEspecialidade(): string
  {
    return $this->especialidade;
  }

  public function getCelular(): string
  {
    return $this->celular;
  }

  // Setters
  public function setIdProfissional(int $idProfissional): void
  {
    $this->idProfissional = $idProfissional;
  }

  public function setNome(string $nome): void
  {
    $this->nome = $nome;
  }

  public function setEspecialidade(string $especialidade): void
  {
    $this->especialidade = $especialidade;
  }

  public function setCelular(string $celular): void
  {
    $this->celular = $celular;
  }
}
