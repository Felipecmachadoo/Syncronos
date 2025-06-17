document.addEventListener("DOMContentLoaded", function () {
  const loginForm = document.getElementById("loginForm");

  if (loginForm) {
    const submitBtn = loginForm.querySelector('button[type="submit"]');

    loginForm.addEventListener("submit", async function (e) {
      e.preventDefault();

      try {
        // Mostrar loading
        submitBtn.disabled = true;
        submitBtn.textContent = "Autenticando...";

        const formData = new FormData(loginForm);
        const response = await fetch(
          "http://127.0.0.1/Syncronos/controller/LoginController.php",
          {
            method: "POST",
            body: formData,
          }
        );

        // Verifica se a resposta é JSON
        const contentType = response.headers.get("content-type");
        if (!contentType || !contentType.includes("application/json")) {
          const text = await response.text();
          console.error("Resposta não-JSON:", text);
          throw new Error("Resposta inválida do servidor");
        }

        const result = await response.json();

        if (result.status === "success") {
          window.location.href = result.redirect || "/pages/barbearia.php";
        } else {
          throw new Error(result.message || "Erro ao fazer login");
        }
      } catch (error) {
        console.error("Erro no login:", error);
        alert(
          error.message.includes("Credenciais inválidas")
            ? "E-mail ou senha incorretos. Por favor, verifique seus dados."
            : "Erro ao processar login. Tente novamente mais tarde."
        );
      } finally {
        // Restaurar botão
        submitBtn.disabled = false;
        submitBtn.textContent = "Continuar";
      }
    });
  }

  // Toggle de senha (mantido igual)
  const togglePassword = document.getElementById("toggle-password");
  const passwordField = document.getElementById("password-input");

  if (togglePassword && passwordField) {
    togglePassword.addEventListener("click", () => {
      const type = passwordField.type === "password" ? "text" : "password";
      passwordField.type = type;
      togglePassword.src =
        type === "password"
          ? "../assets/images/hide.png"
          : "../assets/images/witness.png";
    });
  }
});
