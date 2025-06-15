<?php

require_once __DIR__ . '/../model/Profissional.php';
require_once __DIR__ . '/../config/conexao.php';

class ProfissionalController
{
  private $conexao;

  public function __construct()
  {
    $db = new Conexao();
    $this->conexao = $db->conectar();
  }

  public function salvarProfissional()
  {
    // Verifica se é edição ou novo cadastro
    $idProfissional = isset($_POST['idProfissional']) ? $_POST['idProfissional'] : null;
    $nome = $_POST['nome'];
    $especialidade = $_POST['especialidade'];
    $celular = $_POST['celular'];

    try {
      if ($idProfissional) {
        // Atualização
        $stmt = $this->conexao->prepare("UPDATE profissional SET nome = ?, especialidade = ?, celular = ? WHERE idProfissional = ?");
        $stmt->execute([$nome, $especialidade, $celular, $idProfissional]);
      } else {
        // Novo cadastro
        $stmt = $this->conexao->prepare("INSERT INTO profissional (nome, especialidade, celular) VALUES (?, ?, ?)");
        $stmt->execute([$nome, $especialidade, $celular]);
      }

      header("Location: ../pages/profissional.php");
      exit;
    } catch (PDOException $e) {
      echo "Erro ao salvar profissional: " . $e->getMessage();
    }
  }

  public function listarProfissionais()
  {
    try {
      $stmt = $this->conexao->prepare("SELECT * FROM profissional");
      $stmt->execute();
      $profissionais = $stmt->fetchAll(PDO::FETCH_ASSOC);
      $stmt->closeCursor();
      return $profissionais;
    } catch (PDOException $e) {
      echo "Erro ao listar profissionais: " . $e->getMessage();
      return [];
    }
  }

  public function excluirProfissional()
  {
    if (!isset($_POST['idProfissional'])) {
      error_log("ID do profissional não fornecido");
      http_response_code(400);
      echo "ID do profissional não fornecido";
      return;
    }

    $idProfissional = $_POST['idProfissional'];

    try {
      $stmt = $this->conexao->prepare("DELETE FROM profissional WHERE idProfissional = ?");
      $stmt->execute([$idProfissional]);

      if ($stmt->rowCount() > 0) {
        // Retorna sucesso
        http_response_code(200);
        echo "Profissional excluído com sucesso";
      } else {
        // Nenhum registro foi afetado (ID não encontrado)
        http_response_code(404);
        echo "Profissional não encontrado";
      }
    } catch (PDOException $e) {
      error_log("Erro ao excluir profissional: " . $e->getMessage());
      http_response_code(500);
      echo "Erro ao excluir profissional: " . $e->getMessage();
    }
  }
}
