// Função para carregar páginas HTML dinamicamente e atualizar o título
function loadPage(page, title) {
  fetch(`./pages/${page}.html`)
    .then((response) => {
      if (!response.ok) throw new Error("Página não encontrada.");
      return response.text();
    })
    .then((html) => {
      document.getElementById("home-content").innerHTML = html;
      document.title = title; // Atualiza o título da página

      // Carregar o script específico para a página (se existir)
      loadPageScript(page);
    })
    .catch((error) => {
      document.getElementById(
        "home-content"
      ).innerHTML = `<p>Erro ao carregar página: ${error.message}</p>`;
      document.title = "Erro - Syncronos"; // Define um título padrão em caso de erro
    });
}

// Função para carregar o script específico da página
function loadPageScript(page) {
  const script = document.createElement("script");

  // Verifica qual página está sendo carregada e atribui o script correto
  switch (page) {
    case "servico":
      script.src = "../assets/js/servico.js"; // Caminho para o script da página de serviços
      break;
    case "calendario":
      script.src = "../assets/js/calendario.js"; // Caso você tenha um script específico para o calendário
      break;
    case "horario":
      script.src = "../assets/js/horario.js"; // Caso você tenha um script específico para o horário
      break;
    case "profissional":
      script.src = "../assets/js/profissional.js"; // Caso você tenha um script específico para os profissionais
      break;
    default:
      return;
  }

  // Após o script ser carregado, ele é adicionado ao documento
  script.onload = function () {
    console.log(`Script para a página ${page} carregado com sucesso.`);
  };

  // Adiciona o script ao body da página
  document.body.appendChild(script);
}

// Configuração das rotas do Mithril com atualização do título
m.route(document.getElementById("home-content"), "/", {
  "/": {
    onmatch: () => loadPage("dashboard", "Dashboard - Syncronos"),
    render: () => m("div"),
  },
  "/calendario": {
    onmatch: () => loadPage("calendario", "Calendário - Syncronos"),
    render: () => m("div"),
  },
  "/horario": {
    onmatch: () => loadPage("horario", "Horários - Syncronos"),
    render: () => m("div"),
  },
  "/servico": {
    onmatch: () => loadPage("servico", "Serviços - Syncronos"),
    render: () => m("div"),
  },
  "/profissional": {
    onmatch: () => loadPage("profissional", "Profissionais - Syncronos"),
    render: () => m("div"),
  },
});
