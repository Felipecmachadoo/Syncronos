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
    $idServico = $_POST['idServico'] ?? null;
    $nome = $_POST['nome'];
    $descricao = $_POST['descricao'];
    $preco = $_POST['preco'];
    $duracao = $_POST['duracao'];

    if (empty($nome) || empty($preco) || empty($duracao)) {
      return json_encode(['status' => 'error', 'message' => 'Todos os campos são obrigatórios.']);
    }

    // Formata o preço
    $precoFormatado = str_replace(['R$', '.', ' '], '', $preco);
    $precoFormatado = str_replace(',', '.', $precoFormatado);

    try {
      if ($idServico) {
        $stmt = $this->conexao->prepare(
          "UPDATE servicos SET 
                nome = ?, 
                descricao = ?, 
                preco = ?, 
                duracao = ? 
                WHERE idServico = ?"
        );
        $stmt->execute([$nome, $descricao, $precoFormatado, $duracao, $idServico]);

        $mensagem = "Serviço atualizado com sucesso";
      } else {
        $stmt = $this->conexao->prepare(
          "INSERT INTO servicos 
                (nome, descricao, preco, duracao) 
                VALUES (?, ?, ?, ?)"
        );
        $stmt->execute([$nome, $descricao, $precoFormatado, $duracao]);

        $mensagem = "Serviço cadastrado com sucesso";
      }

      return json_encode([
        'status' => 'success',
        'message' => $mensagem
      ]);
    } catch (PDOException $e) {
      return json_encode([
        'status' => 'error',
        'message' => 'Erro ao salvar serviço: ' . $e->getMessage()
      ]);
    }
  }

  public function listarServicos()
  {
    try {
      $stmt = $this->conexao->prepare("SELECT * FROM servicos ORDER BY idServico ASC");
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

      // Verifica se alguma linha foi afetada
      if ($stmt->rowCount() > 0) {
        return json_encode([
          'success' => true,
          'message' => 'Serviço excluído com sucesso'
        ]);
      } else {
        return json_encode([
          'success' => false,
          'message' => 'Nenhum serviço encontrado com este ID'
        ]);
      }
    } catch (PDOException $e) {
      error_log("Erro ao excluir serviço: " . $e->getMessage());

      return json_encode([
        'success' => false,
        'message' => 'Erro ao excluir serviço: ' . $e->getMessage()
      ]);
    }
  }
}
