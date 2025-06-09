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
    configurarServicos();
  }
});

// Configuração inicial quando o DOM estiver pronto
if (document.readyState === "loading") {
  document.addEventListener("DOMContentLoaded", configurarServicos);
} else {
  configurarServicos();
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
