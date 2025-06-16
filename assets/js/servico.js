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

  console.log("Configurando eventos...");

  // Configura o offcanvas
  const btnAdicionar = document.getElementById("btnAdicionar");
  const offcanvas = document.getElementById("offcanvas");
  const offcanvasOverlay = document.getElementById("offcanvasOverlay");
  const btnCancelar = document.getElementById("btnCancelar");
  const btnSalvar = document.getElementById("btnSalvar");
  const formServico = document.getElementById("formServico");

  if (
    btnAdicionar &&
    offcanvas &&
    offcanvasOverlay &&
    btnCancelar &&
    btnSalvar &&
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

    // Remove eventos antigos
    btnAdicionar.onclick = null;
    offcanvasOverlay.onclick = null;
    btnCancelar.onclick = null;
    btnSalvar.onclick = null;

    // Adiciona novos eventos
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

    // Limpa e recria as opções
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

    // Configura eventos do dropdown
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

  // Configura os botões de editar e excluir
  configurarBotoesAcao();

  eventosConfigurados = true;
}

// Função para limpar o formulário
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

// Função para abrir o offcanvas com dados para edição
async function abrirEdicaoServico(servicoId) {
  try {
    console.log(`Buscando serviço com ID: ${servicoId}`);
    const response = await fetch(
      `../public/ServicoRoutes.php?rota=buscarServico&idServico=${servicoId}`
    );

    if (!response.ok) {
      throw new Error(`Erro HTTP: ${response.status}`);
    }

    const data = await response.json();
    console.log("Resposta completa:", data);

    // Verifica se há um erro na resposta
    if (data.error) {
      throw new Error(data.error);
    }

    // Verifica se os dados do serviço existem
    if (!data || !data.idServico) {
      throw new Error("Dados do serviço inválidos ou não encontrados");
    }

    const servico = data;
    console.log("Dados do serviço recebidos:", servico);

    // Elementos essenciais
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

    // Verificação crítica dos elementos
    if (!elements.formServico) throw new Error("Formulário não encontrado");
    if (!elements.offcanvas) throw new Error("Offcanvas não encontrado");
    if (!elements.offcanvasOverlay)
      throw new Error("Overlay do offcanvas não encontrado");

    // Preenche os campos (ajustado para maiúsculas conforme seu HTML)
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

    // Adiciona/atualiza o campo hidden para o ID
    let idInput = document.getElementById("servico-id");
    if (!idInput) {
      idInput = document.createElement("input");
      idInput.type = "hidden";
      idInput.id = "servico-id";
      idInput.name = "idServico";
      elements.formServico.appendChild(idInput);
    }
    idInput.value = servico.idServico;

    // Atualiza UI para modo edição
    if (elements.btnSalvar) elements.btnSalvar.textContent = "Atualizar";
    if (elements.offcanvasTitle)
      elements.offcanvasTitle.textContent = "Editar Serviço";

    // Abre o offcanvas
    elements.offcanvas.classList.add("active");
    elements.offcanvasOverlay.classList.add("active");
    document.body.style.overflow = "hidden";
  } catch (error) {
    console.error("Erro detalhado:", error);
    notyf.error(error.message || "Erro ao carregar dados do serviço");

    // Debug adicional
    console.log("ID usado na requisição:", servicoId);
    console.log(
      "URL da requisição:",
      `../public/ServicoRoutes.php?rota=buscarServico&idServico=${servicoId}`
    );
  }
}

// Função para excluir serviço
// Função para excluir serviço (MODIFICADA)
async function excluirServico(servicoId) {
  try {
    // Usando o confirm nativo do JavaScript
    const confirmacao = confirm("Tem certeza que deseja excluir este serviço?");

    if (!confirmacao) {
      console.log("Exclusão cancelada pelo usuário");
      return;
    }

    const response = await fetch("../public/ServicoRoutes.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded",
      },
      body: `rota=excluirServico&idServico=${servicoId}`,
    });

    const result = await response.json();

    if (result.success) {
      notyf.success("Serviço excluído com sucesso");
      setTimeout(() => location.reload(), 1500);
    } else {
      notyf.error(result.message || "Erro ao excluir serviço");
    }
  } catch (error) {
    console.error("Erro ao excluir serviço:", error);
    notyf.error("Erro ao comunicar com o servidor");
  }
}

// Configura os botões de ação (editar/excluir)
function configurarBotoesAcao() {
  // Botões de editar (mantido igual)
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

  // Botões de excluir (SIMPLIFICADO - agora só chama a função excluirServico diretamente)
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

// Observador de mutação para elementos dinâmicos
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
    console.log("Novos elementos detectados, reconfigurando...");
    eventosConfigurados = false;
    configurarServicos();
  }
});

// Inicialização
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

// Reset ao sair da página
window.addEventListener("pagehide", () => {
  eventosConfigurados = false;
});
