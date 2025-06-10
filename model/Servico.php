<?php

require_once __DIR__ . '/../config/conexao.php';
require_once __DIR__ . '../../controller/ServicoController.php';

class Servico
{
  public function __construct(

    private int $idServico,
    private string $nome,
    private string $descricao,
    private string $preco,
    private string $duracao,
  ) {}


  public function getIdServico(): int
  {
    return $this->idServico;
  }

  public function getNome(): string
  {
    return $this->nome;
  }

  public function getDescricao(): string
  {
    return $this->descricao;
  }

  public function getPreco(): string
  {
    return $this->preco;
  }

  public function getDuracao(): string
  {
    return $this->duracao;
  }

  public function setNome(string $nome): void
  {
    $this->nome = $nome;
  }

  public function setDescricao(string $descricao): void
  {
    $this->descricao = $descricao;
  }

  public function setPreco(string $preco): void
  {
    $this->preco = $preco;
  }

  public function setDuracao(string $duracao): void
  {
    $this->duracao = $duracao;
  }
}
