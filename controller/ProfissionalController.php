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
    header('Content-Type: application/json');

    try {
      $idProfissional = isset($_POST['idProfissional']) ? $_POST['idProfissional'] : null;
      $nome = $_POST['nome'];
      $especialidade = $_POST['especialidade'];
      $celular = $_POST['celular'];

      if ($idProfissional) {
        $stmt = $this->conexao->prepare("UPDATE profissional SET nome = ?, especialidade = ?, celular = ? WHERE idProfissional = ?");
        $stmt->execute([$nome, $especialidade, $celular, $idProfissional]);
      } else {
        $stmt = $this->conexao->prepare("INSERT INTO profissional (nome, especialidade, celular) VALUES (?, ?, ?)");
        $stmt->execute([$nome, $especialidade, $celular]);
        $idProfissional = $this->conexao->lastInsertId();
      }

      // Retorne sempre JSON
      echo json_encode([
        'success' => true,
        'id' => $idProfissional,
        'message' => 'Profissional salvo com sucesso'
      ]);
      exit;
    } catch (PDOException $e) {
      // Retorne erros também como JSON
      http_response_code(500);
      echo json_encode([
        'success' => false,
        'error' => 'Erro ao salvar profissional: ' . $e->getMessage()
      ]);
      exit;
    }
  }

  public function salvarExpediente()
  {
    if (!isset($_POST['profissional-dias'])) {
      header("Location: ../pages/profissional.php?error=Selecione pelo menos um dia");
      exit;
    }

    $diasSelecionados = $_POST['profissional-dias'];
    $idProfissional = $_POST['idProfissional'];

    try {
      $this->conexao->beginTransaction();

      // Primeiro remove os horários antigos
      $stmtDelete = $this->conexao->prepare("DELETE FROM expediente WHERE profissional_id = ?");
      $stmtDelete->execute([$idProfissional]);

      // Prepara a inserção
      $stmtInsert = $this->conexao->prepare("
            INSERT INTO expediente 
            (profissional_id, dia_semana, abertura, fechamento, inicio_intervalo, fim_intervalo) 
            VALUES (?, ?, ?, ?, ?, ?)
        ");

      // Para cada dia selecionado, insere os horários
      foreach ($diasSelecionados as $dia) {
        $abertura = $_POST["profissional-abertura_$dia"] ?? null;
        $fechamento = $_POST["profissional-fechamento_$dia"] ?? null;
        $inicioIntervalo = $_POST["profissional-inicio_intervalo_$dia"] ?? null;
        $fimIntervalo = $_POST["profissional-fim_intervalo_$dia"] ?? null;

        // Validação básica
        if (empty($abertura) || empty($fechamento)) {
          continue;
        }

        $stmtInsert->execute([
          $idProfissional,
          $dia,
          $abertura,
          $fechamento,
          $inicioIntervalo,
          $fimIntervalo
        ]);
      }

      $this->conexao->commit();
      header("Location: ../pages/profissional.php?success=Horários atualizados com sucesso");
      exit;
    } catch (PDOException $e) {
      $this->conexao->rollBack();
      header("Location: ../pages/profissional.php?error=Erro ao salvar horários");
      exit;
    }
  }

  public function buscarExpediente($idProfissional)
  {
    try {
      $stmt = $this->conexao->prepare("SELECT * FROM expediente WHERE profissional_id = ?");
      $stmt->execute([$idProfissional]);
      return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
      error_log("Erro ao buscar expediente: " . $e->getMessage());
      return [];
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
    header('Content-Type: application/json');

    try {
      if (!isset($_POST['idProfissional'])) {
        throw new Exception("ID do profissional não fornecido");
      }

      $id = $_POST['idProfissional'];
      $this->conexao->beginTransaction();

      // 1. Primeiro exclui o expediente
      $stmtExpediente = $this->conexao->prepare("DELETE FROM expediente WHERE idProfissional = ?");
      $stmtExpediente->execute([$id]);

      // 2. Depois exclui o profissional
      $stmtProfissional = $this->conexao->prepare("DELETE FROM profissional WHERE idProfissional = ?");
      $stmtProfissional->execute([$id]);

      $this->conexao->commit();

      echo json_encode([
        'success' => true,
        'message' => 'Profissional e horários excluídos com sucesso'
      ]);
      exit;
    } catch (Exception $e) {
      $this->conexao->rollBack();
      http_response_code(500);
      echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
      ]);
      exit;
    }
  }
}
