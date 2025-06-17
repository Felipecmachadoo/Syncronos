<?php
require_once __DIR__ . '/../config/Conexao.php';

class AgendamentoModel
{
  private $conn;

  public function __construct()
  {
    $conexao = new Conexao();
    $this->conn = $conexao->conectar();

    if ($this->conn === null) {
      throw new Exception("Não foi possível estabelecer conexão com o banco de dados");
    }
  }

  public function create(array $data)
  {
    $query = "INSERT INTO agendamentos 
                 (idUsuario, idServico, idProfissional, Titulo, Cor, dataInicio, dataFim, Status) 
                 VALUES (:idUsuario, :idServico, :idProfissional, :titulo, :cor, :dataInicio, :dataFim, :status)";

    $stmt = $this->conn->prepare($query);

    try {
      $stmt->execute([
        ':idUsuario' => $data['idUsuario'],
        ':idServico' => $data['idServico'],
        ':idProfissional' => $data['idProfissional'],
        ':titulo' => $data['Titulo'],
        ':cor' => $data['Cor'],
        ':dataInicio' => $data['dataInicio'],
        ':dataFim' => $data['dataFim'],
        ':status' => $data['Status']
      ]);

      return $this->conn->lastInsertId();
    } catch (PDOException $e) {
      error_log("Erro ao criar agendamento: " . $e->getMessage());
      throw new Exception("Erro ao salvar agendamento no banco de dados");
    }
  }

  public function hasConflitoHorario($idProfissional, $dataInicio, $dataFim)
  {
    $query = "SELECT COUNT(*) 
                 FROM agendamentos 
                 WHERE idProfissional = :idProfissional
                 AND (
                    (:dataInicio BETWEEN dataInicio AND dataFim)
                    OR (:dataFim BETWEEN dataInicio AND dataFim)
                    OR (dataInicio BETWEEN :dataInicio AND :dataFim)
                 )
                 AND Status NOT IN ('Cancelado', 'Rejeitado')";

    $stmt = $this->conn->prepare($query);

    try {
      $stmt->execute([
        ':idProfissional' => $idProfissional,
        ':dataInicio' => $dataInicio,
        ':dataFim' => $dataFim
      ]);

      return $stmt->fetchColumn() > 0;
    } catch (PDOException $e) {
      error_log("Erro ao verificar conflito de horário: " . $e->getMessage());
      throw new Exception("Erro ao verificar disponibilidade do profissional");
    }
  }
}
