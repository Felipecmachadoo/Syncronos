// Variável para controlar se já configuramos os eventos
let profissionalConfigurado = false;

const notyf = new Notyf({
  duration: 3000,
  position: {
    x: "right",
    y: "top",
  },
});

// Função para excluir profissional
function excluirProfissional(id) {
  const formData = new FormData();
  formData.append("rota", "excluirProfissional");
  formData.append("idProfissional", id);

  fetch("../public/ProfissionalRoutes.php", {
    method: "POST",
    body: formData,
  })
    .then((response) => {
      if (!response.ok) {
        return response.text().then((text) => {
          throw new Error(text);
        });
      }
      return response.text();
    })
    .then((data) => {
      notyf.success("Profissional excluído com sucesso!");
      document
        .querySelector(`.btn-excluir[data-id="${id}"]`)
        .closest("tr")
        .remove();
    })
    .catch((error) => {
      console.error("Erro:", error);
      notyf.error("Erro ao excluir profissional: " + error.message);
    });
}

// Função principal para configurar tudo
function configurarProfissional() {
  if (profissionalConfigurado) return;

  console.log("Configurando profissional...");

  // ELEMENTOS DO DOM
  const elements = {
    openOffcanvas: document.getElementById("profissional-open-offcanvas"),
    closeOffcanvas: document.getElementById("profissional-close-offcanvas"),
    offcanvas: document.getElementById("profissional-offcanvas"),
    overlay: document.getElementById("profissional-overlay"),
    navCadastro: document.getElementById("profissional-nav-cadastro"),
    navExpediente: document.getElementById("profissional-nav-expediente"),
    cadastroForm: document.getElementById(
      "profissional-cadastro-form-container"
    ),
    expedienteForm: document.getElementById(
      "profissional-expediente-form-container"
    ),
    cancelButton: document.getElementById("profissional-cancel-button"),
    saveButton: document.getElementById("profissional-save-all-button"),
    profissionalForm: document.getElementById("profissional-form"),
    horarioForm: document.getElementById("profissional-horario-form"),
    celular: document.getElementById("profissional-celular"),
    nome: document.getElementById("profissional-nome"),
    profissao: document.getElementById("profissional-profissao"),
  };

  // FUNÇÕES DO OFFCANVAS
  function openOffcanvas(profissionalData = null) {
    elements.offcanvas.classList.add("active");
    elements.overlay.classList.add("active");
    document.body.style.overflow = "hidden";
    showCadastroForm();

    if (profissionalData) {
      fillProfissionalData(profissionalData);
      elements.saveButton.textContent = "Atualizar Profissional";
    }
  }

  function closeOffcanvas() {
    elements.offcanvas.classList.remove("active");
    elements.overlay.classList.remove("active");
    document.body.style.overflow = "";
  }

  // Função para preencher os campos com os dados do profissional
  function fillProfissionalData(profissionalData) {
    elements.nome.value = "";
    elements.celular.value = "";
    elements.profissao.value = "";

    if (profissionalData.nome) elements.nome.value = profissionalData.nome;
    if (profissionalData.celular)
      elements.celular.value = profissionalData.celular;
    if (profissionalData.especialidade)
      elements.profissao.value = profissionalData.especialidade;
  }

  // NAVEGAÇÃO ENTRE FORMULÁRIOS
  function showCadastroForm() {
    elements.cadastroForm.classList.add("active");
    elements.expedienteForm.classList.remove("active");
    elements.navCadastro.classList.add("active");
    elements.navExpediente.classList.remove("active");
    document.querySelector(".profissional-offcanvas-header h2").textContent =
      "Cadastro de Profissional";
  }

  function showExpedienteForm() {
    elements.cadastroForm.classList.remove("active");
    elements.expedienteForm.classList.add("active");
    elements.navCadastro.classList.remove("active");
    elements.navExpediente.classList.add("active");
    document.querySelector(".profissional-offcanvas-header h2").textContent =
      "Expediente";
  }

  function limparFormularioProfissional() {
    const elements = {
      nome: document.getElementById("profissional-nome"),
      celular: document.getElementById("profissional-celular"),
      profissao: document.getElementById("profissional-profissao"),
      saveButton: document.getElementById("profissional-save-all-button"),
    };

    // Limpa os campos
    elements.nome.value = "";
    elements.celular.value = "";
    elements.profissao.value = "";

    // Remove o campo hidden do ID se existir
    const existingIdInput = document.querySelector(
      'input[name="idProfissional"]'
    );
    if (existingIdInput) {
      existingIdInput.remove();
    }

    // Garante que o botão está como "Cadastrar"
    elements.saveButton.textContent = "Cadastrar Profissional";
  }

  // Modifique o event listener do botão de abrir offcanvas para novo cadastro
  if (elements.openOffcanvas) {
    elements.openOffcanvas.addEventListener("click", () => {
      limparFormularioProfissional(); // Limpa o formulário antes de abrir
      openOffcanvas(); // Abre o offcanvas
    });
  }

  // SALVAMENTO DOS DADOS (SEM VALIDAÇÕES)
  function saveData() {
    // Cria um FormData com os dados do formulário principal
    const formData = new FormData(elements.profissionalForm);
    formData.append("rota", "salvarProfissional");

    // Envia os dados
    fetch("../public/ProfissionalRoutes.php", {
      method: "POST",
      body: formData,
    })
      .then((response) => {
        if (!response.ok) {
          return response.text().then((text) => {
            throw new Error(text);
          });
        }
        return response.text();
      })
      .then((data) => {
        notyf.success("Profissional salvo com sucesso!");
        closeOffcanvas();
        window.location.reload(); // Recarrega a página para atualizar a lista
      })
      .catch((error) => {
        console.error("Erro:", error);
        notyf.error("Erro ao salvar profissional: " + error.message);
      });
  }

  // MÁSCARA DE CELULAR
  function applyPhoneMask() {
    if (!elements.celular) return;

    elements.celular.addEventListener("input", function (e) {
      let value = e.target.value.replace(/\D/g, "");
      if (value.length > 11) value = value.slice(0, 11);

      if (value.length > 2) {
        value = `(${value.slice(0, 2)}) ${value.slice(2)}`;
      }
      if (value.length > 10) {
        value = `${value.slice(0, 10)}-${value.slice(10)}`;
      }

      e.target.value = value;
    });
  }

  // EVENT LISTENERS
  if (elements.openOffcanvas) {
    elements.openOffcanvas.addEventListener("click", () => openOffcanvas());
  }
  if (elements.closeOffcanvas) {
    elements.closeOffcanvas.addEventListener("click", closeOffcanvas);
  }
  if (elements.overlay) {
    elements.overlay.addEventListener("click", closeOffcanvas);
  }
  if (elements.cancelButton) {
    elements.cancelButton.addEventListener("click", closeOffcanvas);
  }
  if (elements.navCadastro) {
    elements.navCadastro.addEventListener("click", showCadastroForm);
  }
  if (elements.navExpediente) {
    elements.navExpediente.addEventListener("click", showExpedienteForm);
  }
  if (elements.saveButton) {
    elements.saveButton.addEventListener("click", saveData);
  }

  // Evento para edição de profissional
  document.addEventListener("click", function (e) {
    if (e.target.classList.contains("btn-editar")) {
      e.preventDefault();

      const existingIdInput = document.querySelector(
        'input[name="idProfissional"]'
      );
      if (existingIdInput) {
        existingIdInput.remove();
      }

      const idInput = document.createElement("input");
      idInput.type = "hidden";
      idInput.name = "idProfissional";
      idInput.value = e.target.getAttribute("data-id");
      elements.profissionalForm.appendChild(idInput);

      openOffcanvas({
        nome: e.target.getAttribute("data-nome"),
        celular: e.target.getAttribute("data-celular"),
        especialidade: e.target.getAttribute("data-especialidade"),
      });

      elements.saveButton.textContent = "Atualizar Profissional";
    }

    // Evento para exclusão de profissional
    if (e.target.classList.contains("btn-excluir")) {
      e.preventDefault();
      const idProfissional = e.target.getAttribute("data-id");
      const nomeProfissional = e.target
        .closest("tr")
        .querySelector("td:nth-child(2)").textContent;

      if (confirm(`Tem certeza que deseja excluir ${nomeProfissional}?`)) {
        excluirProfissional(idProfissional);
      }
    }
  });

  // INICIALIZAÇÃO
  applyPhoneMask();
  profissionalConfigurado = true;
}

// Configuração inicial
if (document.readyState === "loading") {
  document.addEventListener("DOMContentLoaded", configurarProfissional);
} else {
  configurarProfissional();
}
