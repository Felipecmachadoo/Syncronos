<?php
require_once __DIR__ . '/../model/Expediente.php';
require_once __DIR__ . '/../config/conexao.php';

class ExpedienteController
{
  private $conexao;

  public function __construct()
  {
    $db = new Conexao();
    $this->conexao = $db->conectar();
  }

  public function salvarExpediente()
  {
    header('Content-Type: application/json');

    try {
      if (!isset($_POST['idProfissional'])) {
        throw new Exception("ID do profissional não recebido");
      }

      $idProfissional = $_POST['idProfissional'];
      $dias = $_POST['dias'] ?? [];

      if (empty($dias)) {
        throw new Exception("Nenhum dia de atendimento selecionado");
      }

      $this->conexao->beginTransaction();

      // Verifica dias existentes
      $stmtCheck = $this->conexao->prepare("SELECT diaSemana FROM expediente WHERE idProfissional = ?");
      $stmtCheck->execute([$idProfissional]);
      $diasExistentes = array_column($stmtCheck->fetchAll(PDO::FETCH_ASSOC), 'diaSemana');

      foreach ($dias as $dia) {
        $abertura = $_POST["abertura_{$dia}"] ?? null;
        $fechamento = $_POST["fechamento_{$dia}"] ?? null;
        $inicioIntervalo = $_POST["inicioIntervalo_{$dia}"] ?? null;
        $fimIntervalo = $_POST["fimIntervalo_{$dia}"] ?? null;

        if (!$abertura || !$fechamento) {
          throw new Exception("Horários de abertura e fechamento são obrigatórios para {$dia}");
        }

        if (in_array($dia, $diasExistentes)) {
          $stmt = $this->conexao->prepare("
                        UPDATE expediente SET
                            inicioExpediente = ?,
                            fimExpediente = ?,
                            inicioIntervalo = ?,
                            fimIntervalo = ?
                        WHERE idProfissional = ? AND diaSemana = ?
                    ");
          $stmt->execute([
            $abertura,
            $fechamento,
            $inicioIntervalo,
            $fimIntervalo,
            $idProfissional,
            $dia
          ]);
        } else {
          // Inserção
          $stmt = $this->conexao->prepare("
                        INSERT INTO expediente 
                        (idProfissional, diaSemana, inicioExpediente, fimExpediente, inicioIntervalo, fimIntervalo) 
                        VALUES (?, ?, ?, ?, ?, ?)
                    ");
          $stmt->execute([
            $idProfissional,
            $dia,
            $abertura,
            $fechamento,
            $inicioIntervalo,
            $fimIntervalo
          ]);
        }
      }

      // Remove dias não selecionados
      $diasParaRemover = array_diff($diasExistentes, $dias);
      if (!empty($diasParaRemover)) {
        $placeholders = implode(',', array_fill(0, count($diasParaRemover), '?'));
        $stmtDelete = $this->conexao->prepare("
                    DELETE FROM expediente 
                    WHERE idProfissional = ? 
                    AND diaSemana IN ($placeholders)
                ");
        $params = array_merge([$idProfissional], array_values($diasParaRemover));
        $stmtDelete->execute($params);
      }

      $this->conexao->commit();

      echo json_encode([
        'success' => true,
        'message' => 'Expediente atualizado com sucesso'
      ]);
      exit;
    } catch (Exception $e) {
      $this->conexao->rollBack();
      http_response_code(400);
      echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
      ]);
      exit;
    }
  }

  public function getByProfissional($idProfissional)
  {
    header('Content-Type: application/json');

    try {
      if (empty($idProfissional) || !is_numeric($idProfissional)) {
        throw new Exception("ID do profissional inválido");
      }

      $stmt = $this->conexao->prepare("SELECT * FROM expediente WHERE idProfissional = ?");
      $stmt->execute([$idProfissional]);
      $expedientes = $stmt->fetchAll(PDO::FETCH_ASSOC);

      echo json_encode([
        'success' => true,
        'data' => $expedientes ?: []
      ]);
      exit;
    } catch (Exception $e) {
      http_response_code(500);
      echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
      ]);
      exit;
    }
  }
}

// Ponto de entrada
if (isset($_GET['action']) && $_GET['action'] === 'getByProfissional' && isset($_GET['id'])) {
  $controller = new ExpedienteController();
  $controller->getByProfissional($_GET['id']);
}
