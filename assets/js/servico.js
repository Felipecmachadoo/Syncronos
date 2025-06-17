// Variável para controlar se já configuramos os eventos
let eventosConfigurados = false;

const notyf = new Notyf({
  duration: 2000,
  position: {
    x: "right",
    y: "top",
  },
});

// Função para configurar tudo uma única vez
function configurarServicos() {
  if (eventosConfigurados) return;

  // Configura o offcanvas
  const btnAdicionar = document.getElementById("btnAdicionar");
  const offcanvas = document.getElementById("offcanvas");
  const offcanvasOverlay = document.getElementById("offcanvasOverlay");
  const btnCancelar = document.getElementById("btnCancelar");
  const formServico = document.getElementById("formServico");

  if (
    btnAdicionar &&
    offcanvas &&
    offcanvasOverlay &&
    btnCancelar &&
    formServico
  ) {
    function abrirOffcanvas() {
      limparFormulario();
      offcanvas.classList.add("active");
      offcanvasOverlay.classList.add("active");
      document.body.style.overflow = "hidden";
    }

    function fecharOffcanvas() {
      offcanvas.classList.remove("active");
      offcanvasOverlay.classList.remove("active");
      document.body.style.overflow = "";
    }

    btnAdicionar.onclick = abrirOffcanvas;
    offcanvasOverlay.onclick = fecharOffcanvas;
    btnCancelar.onclick = fecharOffcanvas;
  }

  // Configura o dropdown de duração
  const dropdown = document.querySelector(".dropdown");
  if (dropdown) {
    const dropdownToggle = document.getElementById("selected-duration");
    const dropdownMenu = document.querySelector(".dropdown-menu");
    const durationInput = document.getElementById("duration-input");

    dropdownMenu.innerHTML = "";

    const durations = [];
    for (let i = 5; i <= 240; i += 5) {
      durations.push(
        i < 60
          ? `${i} Min`
          : `${Math.floor(i / 60)}h${i % 60 ? ` ${i % 60} Min` : ""}`
      );
    }

    durations.forEach((time) => {
      const option = document.createElement("div");
      option.className = "dropdown-option";
      option.textContent = time;
      option.dataset.value = time;
      dropdownMenu.appendChild(option);
    });

    dropdownToggle.onclick = () => {
      dropdownMenu.style.display =
        dropdownMenu.style.display === "block" ? "none" : "block";
    };

    dropdownMenu.onclick = (e) => {
      if (e.target.classList.contains("dropdown-option")) {
        dropdownToggle.textContent = e.target.textContent;
        durationInput.value = e.target.dataset.value;
        dropdownMenu.style.display = "none";
      }
    };

    document.addEventListener("click", (e) => {
      if (!dropdown.contains(e.target)) {
        dropdownMenu.style.display = "none";
      }
    });
  }

  // Configura o formatador de preço
  const precoInput = document.getElementById("preco");
  if (precoInput) {
    precoInput.addEventListener("input", function () {
      let valor = this.value.replace(/\D/g, "");

      if (valor === "") {
        this.value = "";
        return;
      }
      valor = parseInt(valor).toString();
      valor = valor.padStart(3, "0");

      let reais = valor.slice(0, valor.length - 2);
      let centavos = valor.slice(-2);

      reais = reais.replace(/\B(?=(\d{3})+(?!\d))/g, ".");

      this.value = `R$ ${reais},${centavos}`;
    });
  }

  configurarBotoesAcao();
  configurarSubmitFormulario();

  eventosConfigurados = true;
}

function configurarSubmitFormulario() {
  const formServico = document.getElementById("formServico");
  if (!formServico) return;

  formServico.addEventListener("submit", async function (e) {
    e.preventDefault();

    const btnSalvar = document.getElementById("btnSalvar");
    const isEdicao = btnSalvar && btnSalvar.textContent === "Atualizar";

    try {
      const formData = new FormData(this);

      if (isEdicao) {
        const idInput = document.getElementById("servico-id");
        if (idInput) {
          formData.append("idServico", idInput.value);
        }
      }

      const btnSubmit = this.querySelector('[type="submit"]');
      let originalText = "Salvar";
      if (btnSubmit) {
        originalText = btnSubmit.textContent || "Salvar";
        btnSubmit.textContent = "Processando...";
        btnSubmit.disabled = true;
      }

      const response = await fetch("../public/ServicoRoutes.php", {
        method: "POST",
        body: formData,
      });

      const responseText = await response.text();
      let result = {};

      try {
        result = responseText ? JSON.parse(responseText) : {};
      } catch (e) {
        throw new Error("Resposta inválida do servidor");
      }

      if (btnSubmit) {
        btnSubmit.textContent = originalText;
        btnSubmit.disabled = false;
      }

      if (result.status === "success") {
        notyf.success(result.message || "Operação realizada com sucesso");
        setTimeout(() => location.reload(), 1200);
      } else {
        notyf.error(result.message || "Erro ao processar a requisição");
      }
    } catch (error) {
      notyf.error(error.message || "Erro ao processar a requisição");

      const btnSubmit = formServico.querySelector('[type="submit"]');
      if (btnSubmit) {
        btnSubmit.textContent = isEdicao ? "Atualizar" : "Salvar";
        btnSubmit.disabled = false;
      }
    }
  });
}

function limparFormulario() {
  const form = document.getElementById("formServico");
  if (form) {
    form.reset();
    const durationInput = document.getElementById("duration-input");
    const selectedDuration = document.getElementById("selected-duration");

    if (durationInput) durationInput.value = "5 Min";
    if (selectedDuration) selectedDuration.textContent = "5 Min";

    const idInput = document.getElementById("servico-id");
    if (idInput) {
      idInput.remove();
    }

    const btnSalvar = document.getElementById("btnSalvar");
    if (btnSalvar) {
      btnSalvar.textContent = "Salvar";
    }

    const offcanvasTitle = document.getElementById("offcanvasTitle");
    if (offcanvasTitle) {
      offcanvasTitle.textContent = "Adicionar Novo Serviço";
    }
  }
}

async function abrirEdicaoServico(servicoId) {
  try {
    const response = await fetch(
      `../public/ServicoRoutes.php?rota=buscarServico&idServico=${servicoId}`
    );

    if (!response.ok) {
      throw new Error(`Erro ao buscar serviço`);
    }

    const data = await response.json();

    if (data.error) {
      throw new Error(data.error);
    }

    if (!data || !data.idServico) {
      throw new Error("Serviço não encontrado");
    }

    const servico = data;
    const elements = {
      nome: document.getElementById("nome"),
      descricao: document.getElementById("descricao"),
      preco: document.getElementById("preco"),
      durationInput: document.getElementById("duration-input"),
      selectedDuration: document.getElementById("selected-duration"),
      btnSalvar: document.getElementById("btnSalvar"),
      offcanvasTitle: document.getElementById("offcanvasTitle"),
      formServico: document.getElementById("formServico"),
      offcanvas: document.getElementById("offcanvas"),
      offcanvasOverlay: document.getElementById("offcanvasOverlay"),
    };

    if (
      !elements.formServico ||
      !elements.offcanvas ||
      !elements.offcanvasOverlay
    ) {
      throw new Error("Elementos do formulário não encontrados");
    }

    if (elements.nome) elements.nome.value = servico.Nome || "";
    if (elements.descricao) elements.descricao.value = servico.Descricao || "";

    if (elements.preco) {
      elements.preco.value = servico.Preco
        ? "R$ " + parseFloat(servico.Preco).toFixed(2).replace(".", ",")
        : "R$ 0,00";
    }

    if (elements.durationInput)
      elements.durationInput.value = servico.Duracao || "5 Min";
    if (elements.selectedDuration)
      elements.selectedDuration.textContent = servico.Duracao || "5 Min";

    let idInput = document.getElementById("servico-id");
    if (!idInput) {
      idInput = document.createElement("input");
      idInput.type = "hidden";
      idInput.id = "servico-id";
      idInput.name = "idServico";
      elements.formServico.appendChild(idInput);
    }
    idInput.value = servico.idServico;

    if (elements.btnSalvar) elements.btnSalvar.textContent = "Atualizar";
    if (elements.offcanvasTitle)
      elements.offcanvasTitle.textContent = "Editar Serviço";

    elements.offcanvas.classList.add("active");
    elements.offcanvasOverlay.classList.add("active");
    document.body.style.overflow = "hidden";
  } catch (error) {
    notyf.error(error.message || "Erro ao carregar dados do serviço");
  }
}

async function excluirServico(servicoId) {
  try {
    const confirmacao = confirm("Tem certeza que deseja excluir este serviço?");
    if (!confirmacao) return;

    const response = await fetch("../public/ServicoRoutes.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded",
      },
      body: `rota=excluirServico&idServico=${servicoId}`,
    });

    const text = await response.text();
    let result;

    try {
      result = JSON.parse(text);
    } catch {
      throw new Error("Resposta inválida do servidor");
    }

    if (result.success) {
      notyf.success(result.message || "Serviço excluído com sucesso");
      setTimeout(() => location.reload(), 1200);
    } else {
      notyf.error(result.message || "Erro ao excluir serviço");
    }
  } catch (error) {
    notyf.error("Erro ao processar a exclusão: " + error.message);
  }
}

function configurarBotoesAcao() {
  document.querySelectorAll(".btn-editar").forEach((btn) => {
    btn.onclick = function () {
      const servicoId = this.getAttribute("data-id");
      if (servicoId) {
        abrirEdicaoServico(servicoId);
      } else {
        notyf.error("ID do serviço não encontrado");
      }
    };
  });

  document.querySelectorAll(".btn-excluir").forEach((btn) => {
    btn.onclick = function () {
      const servicoId = this.getAttribute("data-id");
      if (servicoId) {
        excluirServico(servicoId);
      } else {
        notyf.error("ID do serviço não encontrado");
      }
    };
  });
}

const observer = new MutationObserver((mutations) => {
  const elementosRelevantes = [
    "btnAdicionar",
    "dropdown",
    "preco",
    "servico-tabela",
    "btn-editar",
    "btn-excluir",
  ];

  const elementosAdicionados = mutations.some((mutation) => {
    return Array.from(mutation.addedNodes).some((node) => {
      return elementosRelevantes.some(
        (idOrClass) =>
          node.id === idOrClass || node.classList?.contains(idOrClass)
      );
    });
  });

  if (elementosAdicionados) {
    eventosConfigurados = false;
    configurarServicos();
  }
});

if (document.readyState === "loading") {
  document.addEventListener("DOMContentLoaded", () => {
    configurarServicos();
    observer.observe(document.body, {
      childList: true,
      subtree: true,
    });
  });
} else {
  configurarServicos();
  observer.observe(document.body, {
    childList: true,
    subtree: true,
  });
}

window.addEventListener("pagehide", () => {
  eventosConfigurados = false;
});
