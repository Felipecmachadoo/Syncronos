// Função para configurar eventos do Offcanvas
function configurarOffcanvas() {
  const btnAdicionar = document.getElementById("btnAdicionar");
  const offcanvas = document.getElementById("offcanvas");
  const offcanvasOverlay = document.getElementById("offcanvasOverlay");
  const btnCancelar = document.getElementById("btnCancelar");
  const btnSalvar = document.getElementById("btnSalvar");
  const formServico = document.getElementById("formServico");

  if (!btnAdicionar) return; // Evita erro se estiver em outra aba

  // Remove eventos antigos antes de adicionar novos
  btnAdicionar.removeEventListener("click", abrirOffcanvas);
  offcanvasOverlay.removeEventListener("click", fecharOffcanvas);
  btnCancelar.removeEventListener("click", fecharOffcanvas);
  btnSalvar.removeEventListener("click", salvarFormulario);

  // Função para abrir o offcanvas
  function abrirOffcanvas() {
    offcanvas.classList.add("active");
    offcanvasOverlay.classList.add("active");
    document.body.style.overflow = "hidden"; // Impede rolagem do body
  }

  // Função para fechar o offcanvas
  function fecharOffcanvas() {
    offcanvas.classList.remove("active");
    offcanvasOverlay.classList.remove("active");
    document.body.style.overflow = ""; // Restaura rolagem do body
  }

  // Função para salvar o formulário
  function salvarFormulario() {
    if (formServico.checkValidity()) {
      const servico = {
        nome: document.getElementById("nome").value,
        preco: document.getElementById("preco").value,
        duracao: document.getElementById("duration-input").value,
        descricao: document.getElementById("descricao").value,
      };

      console.log("Serviço a ser salvo:", servico);

      formServico.reset();
      fecharOffcanvas();
      alert("Serviço adicionado com sucesso!");
    } else {
      formServico.reportValidity();
    }
  }

  // Adiciona os eventos novamente
  btnAdicionar.addEventListener("click", abrirOffcanvas);
  offcanvasOverlay.addEventListener("click", fecharOffcanvas);
  btnCancelar.addEventListener("click", fecharOffcanvas);
  btnSalvar.addEventListener("click", salvarFormulario);
}

// Monitorar mudanças de página na sidebar e reconfigurar os eventos
const observer = new MutationObserver(() => {
  if (document.getElementById("btnAdicionar")) {
    configurarOffcanvas();
  }
});

// Observa mudanças no conteúdo da página
observer.observe(document.body, { childList: true, subtree: true });

// Configuração inicial
configurarOffcanvas();

// Função para formatar o valor do preço
function formatPreco(input) {
  // Remove todos os caracteres que não sejam números
  let valor = input.value.replace(/\D/g, "");

  // Adiciona a vírgula para separar os centavos (caso necessário)
  valor = valor.replace(/(\d)(\d{2})$/, "$1,$2");

  // Adiciona as vírgulas a cada 3 dígitos
  valor = valor.replace(/(\d)(\d{3})(\d)/, "$1$2.$3");

  // Adiciona o "R$" no início
  input.value = "R$ " + valor;
}

// Aplica a função no campo de preço enquanto o usuário digita
document.getElementById("preco").addEventListener("input", function () {
  formatPreco(this);
});

// Script para as opções de duração do dropdown

// Função para gerar as opções de duração
const durations = [];
for (let i = 5; i <= 240; i += 5) {
  if (i < 60) {
    durations.push(`${i} Min`);
  } else {
    let hours = Math.floor(i / 60);
    let minutes = i % 60;
    durations.push(minutes === 0 ? `${hours}h` : `${hours}h ${minutes} Min`);
  }
}

const dropdown = document.querySelector(".dropdown");
const dropdownToggle = document.getElementById("selected-duration");
const dropdownMenu = document.querySelector(".dropdown-menu");
const durationInput = document.getElementById("duration-input");

// Adiciona as opções geradas ao dropdown
durations.forEach((time) => {
  const option = document.createElement("div");
  option.classList.add("dropdown-option");
  option.textContent = time;
  option.dataset.value = time; // Armazena o valor da opção
  dropdownMenu.appendChild(option);
});

// Mostra ou esconde o dropdown ao clicar
dropdownToggle.addEventListener("click", () => {
  dropdownMenu.style.display =
    dropdownMenu.style.display === "block" ? "none" : "block";
});

// Captura o clique em uma opção e fecha o menu
dropdownMenu.addEventListener("click", (event) => {
  if (event.target.classList.contains("dropdown-option")) {
    dropdownToggle.textContent = event.target.textContent;
    durationInput.value = event.target.dataset.value; // Atualiza o valor selecionado

    console.log("Duração selecionada:", durationInput.value); // Agora pega o valor certo

    dropdownMenu.style.display = "none"; // Fecha o dropdown
  }
});

// Fecha o dropdown ao clicar fora dele
document.addEventListener("click", (event) => {
  if (!dropdown.contains(event.target)) {
    dropdownMenu.style.display = "none";
  }
});
