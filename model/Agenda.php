<?php
require_once './config/conexao.php';

class Horario
{
  private $conn;

  public function __construct()
  {
    $this->conn = Conexao::conectar();
  }

  public function cadastrar($diaSemana, $horaAbertura, $horaFechamento)
  {
    $sql = "INSERT INTO Horarios (DiaSemana, HoraAbertura, HoraFechamento) VALUES (?, ?, ?)";
    $stmt = $this->conn->prepare($sql);
    return $stmt->execute([$diaSemana, $horaAbertura, $horaFechamento]);
  }

  public function listar()
  {
    $sql = "SELECT * FROM Horarios";
    return $this->conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
  }
}
