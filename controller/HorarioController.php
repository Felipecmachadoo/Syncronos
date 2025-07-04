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

    try {
      // Primeiro processa os dias desmarcados (se houver)
      if (!empty($_POST['diasDesmarcados'])) {
        $diasDesmarcados = json_decode($_POST['diasDesmarcados'], true);

        foreach ($diasDesmarcados as $dia) {
          $this->removerHorario($dia);
        }
      }

      // Depois processa os dias marcados (se houver)
      if (isset($_POST['dias']) && is_array($_POST['dias'])) {
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
      } else {
        // Caso não haja dias marcados, mas há dias desmarcados
        if (!empty($diasDesmarcados)) {
          $_SESSION['sucesso'] = 'Dias desmarcados removidos com sucesso!';
        } else {
          $_SESSION['erro'] = 'Selecione pelo menos um dia de funcionamento.';
          header("Location: " . $_SERVER['HTTP_REFERER']);
          exit;
        }
      }

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

  public function buscarHorarios()
  {
    try {
      return $this->obterHorarios(); // Retorna array associativo com os horários
    } catch (Exception $e) {
      // Retorna array vazio em caso de erro (ou você pode lançar a exceção se preferir)
      return [];
    }
  }

  private function obterHorarios(): array
  {
    $stmt = $this->conexao->query("SELECT * FROM horarios ORDER BY FIELD(diaSemana, 'segunda', 'terca', 'quarta', 'quinta', 'sexta', 'sabado', 'domingo')");
    $horarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt->closeCursor();
    return $horarios;
  }

  private function removerHorario(string $diaSemana): void
  {
    $stmt = $this->conexao->prepare(
      "DELETE FROM horarios WHERE diaSemana = :diaSemana"
    );
    $stmt->bindParam(':diaSemana', $diaSemana);
    $stmt->execute();
    $stmt->closeCursor();
  }
}
