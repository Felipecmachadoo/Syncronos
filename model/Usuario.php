<?php
require_once './config/conexao.php';

class Usuario
{
  private $conn;

  public function __construct()
  {
    $this->conn = Conexao::conectar();
  }

  public function cadastrar($nome, $cpf, $telefone, $email, $senha, $tipo)
  {
    $sql = "INSERT INTO Usuarios (Nome, CPF, Telefone, Email, Senha, Tipo) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $this->conn->prepare($sql);
    return $stmt->execute([$nome, $cpf, $telefone, $email, $senha, $tipo]);
  }

  public function listar()
  {
    $sql = "SELECT * FROM Usuarios";
    return $this->conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
  }
}
