<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="../assets/css/login.css" />
  <title>Bem-vindo ao Syncronos!</title>
</head>

<body>
  <div class="container">
    <img src="../assets/images/Syncronos-branco.png" alt="" />
    <h1>Bem-vindo ao Syncronos!</h1>
    <h2>Informe seus dados para continuar</h2>

    <?php if (isset($_SESSION['mensagem_erro'])): ?>
      <div class="mensagem-erro"><?php echo $_SESSION['mensagem_erro'];
                                  unset($_SESSION['mensagem_erro']); ?></div>
    <?php endif; ?>

    <?php if (isset($_SESSION['mensagem_sucesso'])): ?>
      <div class="mensagem-sucesso"><?php echo $_SESSION['mensagem_sucesso'];
                                    unset($_SESSION['mensagem_sucesso']); ?></div>
    <?php endif; ?>

    <div class="form-container">
      <form id="loginForm" action="../controller/LoginController.php" method="POST">
        <input
          type="text"
          name="email"
          id="email-input"
          placeholder="E-mail"
          required />
        <div class="password-container">
          <input
            type="password"
            name="senha"
            id="password-input"
            placeholder="Senha"
            required />
          <img
            id="toggle-password"
            class="eye-icon"
            src="../assets/images/hide.png"
            alt="Mostrar senha" />
        </div>
        <p id="forget-password">
          <a href="/esqueci-senha">Esqueceu sua senha?</a>
        </p>
        <p id="create-account">
          <span class="text-white">Não tem uma conta?</span>
          <span class="text-orange"><a href="../auth/register.php">Cadastre-se</a></span>
        </p>
        <button class="continue" type="submit">Continuar</button>
      </form>
    </div>
  </div>

  <script src="../assets/js/login.js"></script>
</body>

</html>