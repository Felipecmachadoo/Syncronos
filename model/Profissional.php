<?php
require_once './config/conexao.php';

class Profissional
{
  private $conn;

  public function __construct()
  {
    $this->conn = Conexao::conectar();
  }

  public function cadastrar($nome, $especialidade, $usuarioId)
  {
    $sql = "INSERT INTO Profissionais (Nome, Especialidade, UsuarioID) VALUES (?, ?, ?)";
    $stmt = $this->conn->prepare($sql);
    return $stmt->execute([$nome, $especialidade, $usuarioId]);
  }

  public function listar()
  {
    $sql = "SELECT * FROM Profissionais";
    return $this->conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
  }
}
