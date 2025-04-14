// Variável para controlar se já configuramos os eventos
let eventosConfigurados = false;

// Função para configurar tudo uma única vez
function configurarTudo() {
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
      offcanvas.classList.add("active");
      offcanvasOverlay.classList.add("active");
      document.body.style.overflow = "hidden";
    }

    function fecharOffcanvas() {
      offcanvas.classList.remove("active");
      offcanvasOverlay.classList.remove("active");
      document.body.style.overflow = "";
    }

    function salvarFormulario(e) {
      e.preventDefault();
      if (formServico.checkValidity()) {
        const servico = {
          nome: document.getElementById("nome").value,
          preco: document.getElementById("preco").value,
          duracao: document.getElementById("duration-input").value,
          descricao: document.getElementById("descricao").value,
        };
        console.log("Serviço salvo:", servico);
        formServico.reset();
        fecharOffcanvas();
        alert("Serviço adicionado com sucesso!");
      } else {
        formServico.reportValidity();
      }
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
    btnSalvar.onclick = salvarFormulario;
  }

  // Configura o dropdown
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

    document.onclick = (e) => {
      if (!dropdown.contains(e.target)) {
        dropdownMenu.style.display = "none";
      }
    };
  }

  // Configura o formatador de preço
  const precoInput = document.getElementById("preco");
  if (precoInput) {
    precoInput.oninput = function () {
      let valor = this.value.replace(/\D/g, "");
      valor = valor.replace(/(\d)(\d{2})$/, "$1,$2");
      valor = valor.replace(/(\d)(\d{3})(\d)/, "$1$2.$3");
      this.value = "R$ " + valor;
    };
  }

  eventosConfigurados = true;
}

// Observador mais eficiente
const observer = new MutationObserver((mutations) => {
  // Verifica se os elementos principais foram adicionados
  const elementosAdicionados = mutations.some((mutation) => {
    return Array.from(mutation.addedNodes).some((node) => {
      return (
        node.id === "btnAdicionar" ||
        node.classList?.contains("dropdown") ||
        node.id === "preco"
      );
    });
  });

  if (elementosAdicionados) {
    eventosConfigurados = false;
    configurarTudo();
  }
});

// Configuração inicial quando o DOM estiver pronto
if (document.readyState === "loading") {
  document.addEventListener("DOMContentLoaded", configurarTudo);
} else {
  configurarTudo();
}

// Observa apenas adições/remoções de nós filhos
observer.observe(document.body, {
  childList: true,
  subtree: true,
});

// Resetar ao mudar de página
window.addEventListener("pagehide", () => {
  eventosConfigurados = false;
});
