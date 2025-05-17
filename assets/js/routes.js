function loadPage(page, title) {
  const container = document.getElementById("home-content");
  if (!container) {
    console.error("#home-content não encontrado!");
    return;
  }

  // Atualiza o título
  document.title = title;

  // Mostra o loader centralizado
  container.innerHTML = `
    <div class="loader-container">
      <div class="lds-spinner">
        <div></div><div></div><div></div><div></div><div></div><div></div>
        <div></div><div></div><div></div><div></div><div></div><div></div>
      </div>
    </div>
  `;

  // Cria uma promise que resolve após 1 segundo (tempo mínimo)
  const minimumLoadTime = new Promise((resolve) => setTimeout(resolve, 400));

  // Remove CSS anterior se existir
  const oldCss = document.getElementById("dynamic-css");
  if (oldCss) oldCss.remove();

  // Cria novo link para CSS
  const cssLink = document.createElement("link");
  cssLink.rel = "stylesheet";
  cssLink.id = "dynamic-css";
  cssLink.href = `../assets/css/${page}.css`;

  // Combina o carregamento com o tempo mínimo
  Promise.all([
    minimumLoadTime,
    new Promise((resolve) => {
      cssLink.onload = resolve;
      cssLink.onerror = resolve; // Continua mesmo se o CSS falhar
      document.head.appendChild(cssLink);
    }),
    fetch(`./pages/${page}.html`).then((res) =>
      res.ok ? res.text() : Promise.reject("Erro ao carregar HTML")
    ),
  ])
    .then(([_, __, html]) => {
      container.innerHTML = html;
      loadPageScript(page);
    })
    .catch((err) => {
      console.error(err);
      container.innerHTML = `<p>Erro ao carregar conteúdo: ${
        err.message || err
      }</p>`;
    });
}

// Função para carregar o script específico da página
function loadPageScript(page) {
  let scriptSrc;

  switch (page) {
    case "servico":
      scriptSrc = "../assets/js/servico.js";
      break;
    case "calendario":
      scriptSrc = "../assets/js/calendario.js";
      break;
    case "horario":
      scriptSrc = "../assets/js/horario.js";
      break;
    case "profissional":
      scriptSrc = "../assets/js/profissional.js";
      break;
    default:
      return; // Nenhum script para essa página
  }

  // Evita carregar duplicado
  if (document.querySelector(`script[src="${scriptSrc}"]`)) {
    console.log(`Script da página ${page} já carregado.`);
    return;
  }

  const script = document.createElement("script");
  script.src = scriptSrc;
  script.defer = true;

  script.onload = () => {
    console.log(`Script da página ${page} carregado com sucesso.`);
  };

  document.body.appendChild(script);
}

function loadPageCSS(page) {
  // Remove o CSS dinâmico anterior, se houver
  const existingLink = document.getElementById("dynamic-css");
  if (existingLink) existingLink.remove();

  // Cria e insere um novo link de CSS
  const link = document.createElement("link");
  link.id = "dynamic-css";
  link.rel = "stylesheet";
  link.href = `../assets/css/${page}.css`; // Ex: servico.css, calendario.css...
  document.head.appendChild(link);
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
