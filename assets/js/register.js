document.addEventListener("DOMContentLoaded", function () {
  const registerForm = document.getElementById("registerForm");

  if (registerForm) {
    registerForm.addEventListener("submit", async function (e) {
      e.preventDefault();

      const submitBtn = registerForm.querySelector('button[type="submit"]');
      submitBtn.disabled = true;
      submitBtn.textContent = "Cadastrando...";

      try {
        // Coletar dados do formulário
        const formData = {
          nome: registerForm.nome.value,
          email: registerForm.email.value,
          cpf: registerForm.cpf.value,
          telefone: registerForm.telefone?.value || "",
          senha: registerForm.senha.value,
        };

        console.log("Dados sendo enviados:", formData);

        const response = await fetch("../controller/RegisterController.php", {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
          },
          body: JSON.stringify(formData),
        });

        // Verificar se a resposta é JSON
        const contentType = response.headers.get("content-type");
        if (!contentType || !contentType.includes("application/json")) {
          const text = await response.text();
          console.error("Resposta não-JSON:", text);
          throw new Error("Resposta inválida do servidor");
        }

        const result = await response.json();

        if (!response.ok) {
          throw new Error(result.message || "Erro no cadastro");
        }

        if (result.status === "success") {
          alert(result.message);
          if (result.redirect) {
            window.location.href = result.redirect;
          }
        } else {
          throw new Error(result.message || "Erro desconhecido");
        }
      } catch (error) {
        console.error("Erro no cadastro:", error);
        alert(error.message || "Erro ao processar cadastro. Tente novamente.");
      } finally {
        submitBtn.disabled = false;
        submitBtn.textContent = "Cadastrar";
      }
    });
  }

  // Toggle de senha (opcional)
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
