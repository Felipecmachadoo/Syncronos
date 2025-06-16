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

  public function listarServicos()
  {
    try {
      $stmt = $this->conexao->prepare("SELECT * FROM servicos ORDER BY nome");
      $stmt->execute();
      return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
      echo "Erro ao listar serviços: " . $e->getMessage();
      return [];
    }
  }

  public function buscarServicoPorId($idServico)
  {
    try {
      $stmt = $this->conexao->prepare("SELECT * FROM servicos WHERE idServico = ?");
      $stmt->execute([$idServico]);
      $servico = $stmt->fetch(PDO::FETCH_ASSOC);

      // Garanta que está retornando os dados corretamente
      if ($servico) {
        header('Content-Type: application/json');
        echo json_encode($servico);
        exit;
      } else {
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Serviço não encontrado']);
        exit;
      }
    } catch (PDOException $e) {
      header('Content-Type: application/json');
      echo json_encode(['error' => 'Erro ao buscar serviço: ' . $e->getMessage()]);
      exit;
    }
  }

  public function excluirServico($idServico)
  {
    try {
      $stmt = $this->conexao->prepare("DELETE FROM servicos WHERE idServico = ?");
      $stmt->execute([$idServico]);
      return true;
    } catch (PDOException $e) {
      echo "Erro ao excluir serviço: " . $e->getMessage();
      return false;
    }
  }
}
