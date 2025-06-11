<?php

require_once __DIR__ . '/../model/Servico.php';
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
    $descricao = $_POST['descricao'];
    $preco = $_POST['preco'];
    $duracao = $_POST['duracao'];

    if (empty($nome) || empty($preco) || empty($duracao)) {
      return json_encode(['status' => 'error', 'message' => 'Todos os campos são obrigatórios.']);
    }

    // Remove a formatação do preço antes de salvar
    $precoFormatado = str_replace(['R$', '.', ' '], '', $preco);
    $precoFormatado = str_replace(',', '.', $precoFormatado);

    try {
      $stmtServico = $this->conexao->prepare("INSERT INTO servicos (nome, descricao, preco, duracao) VALUES (?, ?, ?, ?)");
      $stmtServico->execute([$nome, $descricao, $precoFormatado, $duracao]);

      header("Location: ../pages/servico.php");
      exit;
    } catch (PDOException $e) {
      echo "Erro ao cadastrar serviço: " . $e->getMessage();
    }
  }
}
