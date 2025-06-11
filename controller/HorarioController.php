<?php

require_once __DIR__ . '/../model/Horario.php';
require_once __DIR__ . '/../config/conexao.php';

class HorarioController
{
  private $conexao;

  public function __construct()
  {
    $db = new Conexao();
    $this->conexao = $db->conectar();
  }

  public function salvarHorarios()
  {
    if (session_status() === PHP_SESSION_NONE) {
      session_start();
    }

    if (!isset($_POST['dias']) || !is_array($_POST['dias'])) {
      $_SESSION['erro'] = 'Selecione pelo menos um dia de funcionamento.';
      header("Location: " . $_SERVER['HTTP_REFERER']);
      exit;
    }

    try {
      foreach ($_POST['dias'] as $dia) {
        $nomeAbertura = 'abertura_' . $dia;
        $nomeFechamento = 'fechamento_' . $dia;

        $horaInicio = $_POST[$nomeAbertura] ?? null;
        $horaFim = $_POST[$nomeFechamento] ?? null;

        if (empty($horaInicio) || empty($horaFim)) {
          $_SESSION['erro'] = "Preencha os horários de abertura e fechamento para {$dia}.";
          header("Location: " . $_SERVER['HTTP_REFERER']);
          exit;
        }

        if (!$this->validarHorarios($horaInicio, $horaFim)) {
          $_SESSION['erro'] = "O horário de fechamento deve ser posterior ao de abertura para {$dia}.";
          header("Location: " . $_SERVER['HTTP_REFERER']);
          exit;
        }

        $horario = new Horario(
          idHorario: 0,
          horaInicio: $horaInicio,
          horaFim: $horaFim,
          diaSemana: $dia
        );

        // Verifica se já existe horário para este dia
        if ($this->horarioExiste($dia)) {
          $this->atualizar($horario);
        } else {
          $this->inserir($horario);
        }
      }

      $_SESSION['sucesso'] = 'Horários atualizados com sucesso!';
      header("Location: ../pages/horario.php");
      exit;
    } catch (PDOException $e) {
      $_SESSION['erro'] = 'Erro ao salvar horários: ' . $e->getMessage();
      header("Location: " . $_SERVER['HTTP_REFERER']);
      exit;
    }
  }

  private function horarioExiste(string $diaSemana): bool
  {
    $stmt = $this->conexao->prepare(
      "SELECT COUNT(*) FROM horarios WHERE diaSemana = :diaSemana"
    );
    $stmt->bindParam(':diaSemana', $diaSemana);
    $stmt->execute();

    $count = $stmt->fetchColumn();
    $stmt->closeCursor();

    return $count > 0;
  }

  private function atualizar(Horario $horario): void
  {
    $stmt = $this->conexao->prepare(
      "UPDATE horarios 
            SET horaInicio = :horaInicio, horaFim = :horaFim 
            WHERE diaSemana = :diaSemana"
    );

    $diaSemana = $horario->getDiaSemana();
    $horaInicio = $horario->getHoraInicio();
    $horaFim = $horario->getHoraFim();

    $stmt->bindParam(':diaSemana', $diaSemana);
    $stmt->bindParam(':horaInicio', $horaInicio);
    $stmt->bindParam(':horaFim', $horaFim);

    $stmt->execute();
    $stmt->closeCursor();
  }

  private function inserir(Horario $horario): void
  {
    $stmt = $this->conexao->prepare(
      "INSERT INTO horarios (diaSemana, horaInicio, horaFim) 
            VALUES (:diaSemana, :horaInicio, :horaFim)"
    );

    $diaSemana = $horario->getDiaSemana();
    $horaInicio = $horario->getHoraInicio();
    $horaFim = $horario->getHoraFim();

    $stmt->bindParam(':diaSemana', $diaSemana);
    $stmt->bindParam(':horaInicio', $horaInicio);
    $stmt->bindParam(':horaFim', $horaFim);

    $stmt->execute();
    $stmt->closeCursor();
  }

  private function validarHorarios($horaInicio, $horaFim): bool
  {
    $horaInicio = str_replace('h', '', $horaInicio);
    $horaFim = str_replace('h', '', $horaFim);

    $inicio = DateTime::createFromFormat('H:i', $horaInicio);
    $fim = DateTime::createFromFormat('H:i', $horaFim);

    return $fim > $inicio;
  }

  public function buscarHorarios(): array
  {
    $stmt = $this->conexao->prepare("SELECT diaSemana, horaInicio, horaFim FROM horarios");
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt->closeCursor();

    $horarios = [];
    foreach ($result as $row) {
      $dia = $row['diaSemana'];
      $horarios[$dia] = [
        'abertura' => $row['horaInicio'],
        'fechamento' => $row['horaFim']
      ];
    }

    return $horarios;
  }
}
