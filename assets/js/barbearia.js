// Funções do dropdown (fora do DOMContentLoaded para serem globais)
function toggleHours() {
  const hoursList = document.getElementById("hours-list");
  const toggleButton = document.querySelector(".hours-toggle");

  if (hoursList.style.display === "none") {
    hoursList.style.display = "block";
    toggleButton.innerHTML = "Horários de funcionamento ▲";
    if (!hoursList.hasAttribute("data-loaded")) {
      loadBusinessHours();
    }
  } else {
    hoursList.style.display = "none";
    toggleButton.innerHTML = "Horários de funcionamento ▼";
  }
}

async function loadBusinessHours() {
  try {
    const response = await fetch("../api/api_horarios.php");

    const horarios = await response.json();

    if (!Array.isArray(horarios) || horarios.length === 0) {
      throw new Error("Resposta vazia ou formato inválido");
    }

    // Mapeamento dos dias da semana para números (FullCalendar usa 0 = domingo, 1 = segunda, etc)
    const diasMap = {
      domingo: 0,
      segunda: 1,
      terca: 2,
      quarta: 3,
      quinta: 4,
      sexta: 5,
      sabado: 6,
    };

    const businessHours = horarios.map((h) => ({
      daysOfWeek: [diasMap[h.diaSemana.toLowerCase()]],
      startTime: h.horaInicio.slice(0, 5),
      endTime: h.horaFim.slice(0, 5),
    }));

    console.log("Horários formatados:", businessHours);

    return businessHours;
  } catch (error) {
    console.error("Erro ao carregar horários:", error);
    return [];
  }
}

function formatBusinessHours(horarios) {
  const diasOrdenados = [
    "segunda",
    "terca",
    "quarta",
    "quinta",
    "sexta",
    "sabado",
    "domingo",
  ];
  const diasNomes = {
    segunda: "Segunda-feira",
    terca: "Terça-feira",
    quarta: "Quarta-feira",
    quinta: "Quinta-feira",
    sexta: "Sexta-feira",
    sabado: "Sábado",
    domingo: "Domingo",
  };

  let html = '<ul style="list-style-type:none; padding-left:0;">';
  let hasHours = false;

  diasOrdenados.forEach((dia) => {
    if (horarios[dia] && horarios[dia].abertura && horarios[dia].fechamento) {
      hasHours = true;
      html += `
        <li style="margin-bottom:8px;">
          <strong>${diasNomes[dia]}:</strong> 
          ${horarios[dia].abertura} - ${horarios[dia].fechamento}
        </li>
      `;
    }
  });

  html += "</ul>";
  return hasHours
    ? html
    : "<p>Não há horários de funcionamento cadastrados</p>";
}

document.addEventListener("DOMContentLoaded", () => {
  // Elementos do DOM
  const agendarModalEl = document.getElementById("agendarModal");
  const agendarModal = new bootstrap.Modal(agendarModalEl);
  const profissionaisSection = document.getElementById("profissionais-section");
  const datasSection = document.getElementById("datas-section");
  const horariosSection = document.getElementById("horarios-disponiveis");
  const dataTitle = document.getElementById("data-title");
  const btnVoltarProfissionais = document.getElementById(
    "btn-voltar-profissionais"
  );

  // Variáveis globais
  let profissionais = [];
  let profissionalSelecionado = null;
  let dataSelecionada = null;
  let servicoSelecionado = null;

  // --- Funções Auxiliares ---
  function toggleUserDropdown() {
    const dropdownMenu = document.getElementById("userDropdownMenu");
    const dropdown = document.querySelector(".user-dropdown");

    if (dropdownMenu.style.display === "block") {
      dropdownMenu.style.display = "none";
      dropdown.classList.remove("active");
    } else {
      dropdownMenu.style.display = "block";
      dropdown.classList.add("active");
    }
  }

  // Fechar dropdown ao clicar fora
  document.addEventListener("click", function (event) {
    const dropdown = document.querySelector(".user-dropdown");
    const dropdownMenu = document.getElementById("userDropdownMenu");
    const dropdownToggle = document.querySelector(".user-dropdown-toggle");

    if (!dropdown.contains(event.target) && event.target !== dropdownToggle) {
      dropdownMenu.style.display = "none";
      dropdown.classList.remove("active");
    }
  });

  window.toggleUserDropdown = toggleUserDropdown;

  document.addEventListener("click", function (event) {
    const dropdown = document.querySelector(".user-dropdown");
    if (!dropdown.contains(event.target)) {
      document.getElementById("userDropdownMenu").style.display = "none";
    }
  });

  function gerarDatas() {
    const datas = [];
    const hoje = new Date();
    for (let i = 0; i <= 30; i++) {
      const novaData = new Date(hoje);
      novaData.setDate(hoje.getDate() + i);
      datas.push(novaData);
    }
    return datas;
  }

  function formatarData(date) {
    const diasSemana = ["DOM", "SEG", "TER", "QUA", "QUI", "SEX", "SÁB"];
    const dia = String(date.getDate()).padStart(2, "0");
    const mes = String(date.getMonth() + 1).padStart(2, "0");
    const diaSemana = diasSemana[date.getDay()];
    return `${dia}/${mes} ${diaSemana}`;
  }

  function timeToDate(timeStr) {
    const [h, m] = timeStr.split(":").map(Number);
    const d = new Date();
    d.setHours(h, m, 0, 0);
    return d;
  }

  // --- Funções Principais ---
  async function carregarHorarios() {
    horariosSection.innerHTML = "<p>Carregando horários...</p>";

    try {
      const res = await fetch(
        `../controller/ExpedienteController.php?action=getByProfissional&id=${profissionalSelecionado.idProfissional}`
      );
      if (!res.ok) throw new Error("Erro ao buscar expediente");

      const json = await res.json();
      if (!json.success) throw new Error(json.error || "Erro no servidor");

      const expediente = json.data;
      const diasSemanaAPI = [
        "domingo",
        "segunda",
        "terca",
        "quarta",
        "quinta",
        "sexta",
        "sabado",
      ];
      const diaSemanaNome = diasSemanaAPI[dataSelecionada.getDay()];

      const expedienteDia = expediente.find(
        (e) => e.diaSemana.toLowerCase() === diaSemanaNome
      );

      if (!expedienteDia) {
        horariosSection.innerHTML = "<p>Profissional não atende neste dia.</p>";
        return;
      }

      const horariosDisponiveis = gerarHorariosComIntervalos(expedienteDia, 30);

      if (horariosDisponiveis.length === 0) {
        horariosSection.innerHTML =
          "<p>Sem horários disponíveis para esta data.</p>";
        return;
      }

      exibirHorarios(horariosDisponiveis);
    } catch (e) {
      horariosSection.innerHTML = `<p style="color:red;">${e.message}</p>`;
    }
  }

  function gerarHorariosComIntervalos(expedienteDia, intervaloMinutos) {
    const horarios = [];
    const inicio = timeToDate(expedienteDia.inicioExpediente);
    const fim = timeToDate(expedienteDia.fimExpediente);
    const intInicio = timeToDate(expedienteDia.inicioIntervalo);
    const intFim = timeToDate(expedienteDia.fimIntervalo);

    let horarioAtual = new Date(inicio);

    while (horarioAtual < fim) {
      if (!(horarioAtual >= intInicio && horarioAtual < intFim)) {
        const h = String(horarioAtual.getHours()).padStart(2, "0");
        const m = String(horarioAtual.getMinutes()).padStart(2, "0");
        horarios.push(`${h}:${m}`);
      }
      horarioAtual.setMinutes(horarioAtual.getMinutes() + intervaloMinutos);
    }

    return horarios;
  }

  function exibirHorarios(horarios) {
    horariosSection.innerHTML = `
      <h6>Horários disponíveis:</h6>
      <div class="d-flex flex-wrap gap-2">
        ${horarios
          .map(
            (hora) => `
          <button type="button" class="btn btn-outline-primary horario-btn" data-hora="${hora}">
            ${hora}
          </button>
        `
          )
          .join("")}
      </div>
    `;

    document.querySelectorAll(".horario-btn").forEach((btn) => {
      btn.addEventListener("click", async () => {
        const confirmacao = confirm(
          `Deseja agendar ${servicoSelecionado.Nome} com ${
            profissionalSelecionado.Nome
          } no dia ${formatarData(dataSelecionada)} às ${btn.dataset.hora}?`
        );

        if (confirmacao) {
          await registrarAgendamento(btn.dataset.hora);
        }
      });
    });
  }

  async function registrarAgendamento(hora) {
    try {
      const userId = document.body.dataset.userId;

      const dataInicio = new Date(dataSelecionada);
      const [h, m] = hora.split(":").map(Number);
      dataInicio.setHours(h, m, 0, 0);

      const dataFim = new Date(dataInicio);
      dataFim.setMinutes(dataInicio.getMinutes() + servicoSelecionado.duracao);

      const formatarParaServidor = (date) => {
        const pad = (num) => num.toString().padStart(2, "0");
        return (
          `${date.getFullYear()}-${pad(date.getMonth() + 1)}-${pad(
            date.getDate()
          )} ` + `${pad(date.getHours())}:${pad(date.getMinutes())}:00`
        );
      };

      const agendamento = {
        idUsuario: userId,
        idServico: servicoSelecionado.idServico,
        idProfissional: profissionalSelecionado.idProfissional,
        Titulo: `${servicoSelecionado.Nome}`,
        Cor: "#3788d8",
        dataInicio: formatarParaServidor(dataInicio),
        dataFim: formatarParaServidor(dataFim),
        Status: "Agendado",
      };

      console.log("Enviando:", agendamento);

      const response = await fetch(
        "../controller/AgendamentoController.php?action=create",
        {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify(agendamento),
        }
      );

      const text = await response.text();
      let result;

      try {
        result = JSON.parse(text);
      } catch (e) {
        console.error("Resposta não é JSON:", text);
        throw new Error("Resposta inválida do servidor");
      }

      if (!result.success) {
        throw new Error(result.error || "Erro ao agendar");
      }

      alert("Agendamento realizado com sucesso!");
      agendarModal.hide();
    } catch (error) {
      console.error("Erro no agendamento:", error);
      alert(`Erro: ${error.message}`);
    }
  }

  function mostrarDatas() {
    profissionaisSection.style.display = "none";
    datasSection.style.display = "block";
    horariosSection.innerHTML = "";

    dataTitle.textContent = `Selecione uma data para ${profissionalSelecionado.Nome}:`;

    const dateCarousel = datasSection.querySelector(".date-carousel");
    dateCarousel.innerHTML = "";

    const datas = gerarDatas();

    datas.forEach((data, i) => {
      const dataItem = document.createElement("div");
      dataItem.classList.add("data-item");
      dataItem.dataset.date = data.toISOString();

      const diaSemana = document.createElement("div");
      diaSemana.textContent = formatarData(data).split(" ")[1];
      diaSemana.style.fontSize = "0.8rem";
      diaSemana.style.fontWeight = "bold";

      const diaMes = document.createElement("div");
      diaMes.textContent = formatarData(data).split(" ")[0];

      dataItem.appendChild(diaSemana);
      dataItem.appendChild(diaMes);

      if (i === 0) {
        dataItem.classList.add("selected");
        dataSelecionada = data;
        carregarHorarios();
      }

      dataItem.addEventListener("click", () => {
        datasSection
          .querySelectorAll(".data-item")
          .forEach((el) => el.classList.remove("selected"));
        dataItem.classList.add("selected");
        dataSelecionada = new Date(dataItem.dataset.date);
        carregarHorarios();
      });

      dateCarousel.appendChild(dataItem);
    });

    const container = datasSection.querySelector(".date-carousel-container");
    const prevBtn = datasSection.querySelector(".carousel-prev");
    const nextBtn = datasSection.querySelector(".carousel-next");

    prevBtn.addEventListener("click", () =>
      container.scrollBy({ left: -150, behavior: "smooth" })
    );
    nextBtn.addEventListener("click", () =>
      container.scrollBy({ left: 150, behavior: "smooth" })
    );
  }

  btnVoltarProfissionais.addEventListener("click", () => {
    profissionalSelecionado = null;
    datasSection.style.display = "none";
    profissionaisSection.style.display = "block";
  });

  async function carregarProfissionais() {
    profissionaisSection.innerHTML = "<p>Carregando profissionais...</p>";

    try {
      const res = await fetch("../api/api_profissionais.php");
      if (!res.ok) throw new Error("Erro ao buscar profissionais");

      profissionais = await res.json();

      if (!profissionais.length) {
        profissionaisSection.innerHTML =
          "<p>Nenhum profissional encontrado.</p>";
        return;
      }

      profissionaisSection.innerHTML = profissionais
        .map(
          (p) => `
        <div class="profissional-card" data-id="${p.idProfissional}" 
             style="cursor:pointer; border:1px solid #ccc; padding:10px; margin-bottom:10px; border-radius:4px;">
          <strong>${p.Nome}</strong>
        </div>
      `
        )
        .join("");

      document.querySelectorAll(".profissional-card").forEach((el) => {
        el.addEventListener("click", () => {
          const id = el.getAttribute("data-id");
          profissionalSelecionado = profissionais.find(
            (p) => p.idProfissional == id
          );
          mostrarDatas();
        });
      });
    } catch (e) {
      profissionaisSection.innerHTML = `<p style="color:red;">${e.message}</p>`;
    }
  }

  document.querySelectorAll(".btn-agendar").forEach((btn) => {
    btn.addEventListener("click", (e) => {
      e.preventDefault();

      const card = btn.closest(".service-card");
      servicoSelecionado = {
        idServico: card.dataset.servicoId,
        Nome: card.querySelector("h3").textContent,
        duracao: 30,
      };

      profissionalSelecionado = null;
      dataSelecionada = null;
      profissionaisSection.style.display = "block";
      datasSection.style.display = "none";

      carregarProfissionais();
      agendarModal.show();
    });
  });

  agendarModalEl.addEventListener("hidden.bs.modal", () => {
    document.activeElement.blur();
  });

  // Inicialização do dropdown de horários
  const hoursToggleBtn = document.querySelector(".hours-toggle");
  if (hoursToggleBtn) {
    hoursToggleBtn.addEventListener("click", toggleHours);
  }
});
