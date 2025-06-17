<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="../assets/css/register.css" />
  <title>Bem-vindo ao Syncronos!</title>
</head>

<body>
  <div class="container">
    <img src="../assets/images/Syncronos-branco.png" alt="" />
    <h1>Crie sua conta</h1>
    <h2>Para começar agora!</h2>

    <?php if (isset($_SESSION['mensagem_erro'])): ?>
      <div class="mensagem-erro"><?php echo $_SESSION['mensagem_erro'];
                                  unset($_SESSION['mensagem_erro']); ?></div>
    <?php endif; ?>

    <div class="form-container">
      <form id="registerForm" action="../controller/RegisterController.php" method="POST">
        <input type="hidden" name="rota" value="registrarUsuario">
        <div class="email-container">
          <input
            type="text"
            name="email"
            id="email-input"
            placeholder="E-mail" />
        </div>
        <div class="phone-container">
          <input
            type="text"
            name="telefone"
            id="phone-input"
            placeholder="Celular" />
        </div>
        <div class="name-container">
          <input
            type="text"
            name="nome"
            id="name-input"
            placeholder="Nome conforme o documento" />
        </div>
        <div class="cpf-container">
          <input type="text" name="cpf" id="cpf-input" placeholder="Cpf" />
        </div>
        <div class="password-container">
          <input
            type="password"
            name="senha"
            id="password-input"
            placeholder="Senha" />
          <img
            id="toggle-password"
            class="eye-icon"
            src="../assets/images/hide.png"
            alt="Mostrar senha" />
        </div>
        <button class="button" type="submit">Continuar</button>
        <hr class="line" />
        <p id="have-account">
          <span class="text-white">Já tem uma conta?</span>
          <span class="text-orange"><a href="../auth/login.html">Faça o seu login!</a></span>
        </p>
      </form>
    </div>
  </div>

  <script src="../assets/js/register.js"></script>
</body>

</html>