<?php
require_once './config/conexao.php';

class Expediente
{
  private $conn;

  public function __construct()
  {
    $this->conn = Conexao::conectar();
  }

  public function cadastrar($profissionalId, $diaSemana, $horaInicio, $horaFim)
  {
    $sql = "INSERT INTO Expediente (ProfissionalID, DiaSemana, HoraInicio, HoraFim) VALUES (?, ?, ?, ?)";
    $stmt = $this->conn->prepare($sql);
    return $stmt->execute([$profissionalId, $diaSemana, $horaInicio, $horaFim]);
  }

  public function listar()
  {
    $sql = "SELECT * FROM Expediente";
    return $this->conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
  }
}
