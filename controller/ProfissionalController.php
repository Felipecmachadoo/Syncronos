<?php

require_once __DIR__ . '/../model/Profissional.php';
require_once __DIR__ . '/../config/conexao.php';

class ServicoController
{
  private $conexao;

  public function __construct()
  {
    $db = new Conexao();
    $this->conexao = $db->conectar();
  }

  public function salvarServico()
  {
    $nome = $_POST['nome'];
    $especialidade = $_POST['especialidade'];
    $celular = $_POST['celular'];

    if (empty($nome) || empty($especialidade)) {
      return json_encode(['status' => 'error', 'message' => 'Todos os campos são obrigatórios.']);
    }

    try {
      $stmtServico = $this->conexao->prepare("INSERT INTO profissional (nome, especialidade, celular) VALUES (?, ?, ?, )");
      $stmtServico->execute([$nome, $especialidade, $celular]);

      header("Location: ../dashboard/pages/profissional.php");
      exit;
    } catch (PDOException $e) {
      echo "Erro ao cadastrar serviço: " . $e->getMessage();
    }
  }
}
