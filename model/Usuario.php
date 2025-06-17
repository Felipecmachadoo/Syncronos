<?php

require_once __DIR__ . '/../config/conexao.php';


class Usuario
{
  public function __construct(

    private int $idUsuario,
    private string $nome,
    private string $cpf,
    private string $telefone,
    private string $email,
    private string $senha,
    private string $tipo
  ) {}

  public static function autenticar($email, $senha)
  {
    $pdo = (new Conexao())->conectar();

    $sql = "SELECT idUsuario, Nome, CPF, Telefone, Email, Senha, Tipo 
            FROM usuarios 
            WHERE Email = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$email]);

    if ($stmt->rowCount() === 1) {
      $row = $stmt->fetch(PDO::FETCH_ASSOC);

      // Comparação da senha (sem hash)
      if ($senha === $row['Senha']) {
        return new Usuario(
          $row['idUsuario'],
          $row['Nome'],
          $row['CPF'],
          $row['Telefone'],
          $row['Email'],
          $row['Senha'],
          $row['Tipo']
        );
      }
    }

    return null;
  }


  public function getIdUsuario(): int
  {
    return $this->idUsuario;
  }

  public function getNome(): string
  {
    return $this->nome;
  }

  public function getCpf(): string
  {
    return $this->cpf;
  }

  public function getTelefone(): string
  {
    return $this->telefone;
  }

  public function getEmail(): string
  {
    return $this->email;
  }

  public function getSenha(): string
  {
    return $this->senha;
  }

  public function getTipo(): string
  {
    return $this->tipo;
  }

  public function setNome(string $nome): void
  {
    $this->nome = $nome;
  }

  public function setCpf(string $cpf): void
  {
    $this->cpf = $cpf;
  }

  public function setTelefone(string $telefone): void
  {
    $this->telefone = $telefone;
  }

  public function setEmail(string $email): void
  {
    $this->email = $email;
  }

  public function setSenha(string $senha): void
  {
    $this->senha = $senha;
  }

  public function setTipo(string $tipo): void
  {
    $this->tipo = $tipo;
  }
}
