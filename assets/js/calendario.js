// Executar quando o documento HTML for completamente carregado
document.addEventListener("DOMContentLoaded", function () {
  // Receber o SELETOR calendar do atributo id
  var calendarEl = document.getElementById("calendar");

  // Receber os seletores dos modais
  const visualizarModal = document.getElementById("visualizarModal");
  const formularioModal = document.getElementById("formularioModal");
  const modalOverlay = document.getElementById("modalOverlay");

  // Receber o SELETOR "msgViewEvento"
  const msgViewEvento = document.getElementById("msgViewEvento");

  // Instanciar FullCalendar.Calendar e atribuir a variável calendar
  var calendar = new FullCalendar.Calendar(calendarEl, {
    // Criar o cabeçalho do calendário
    headerToolbar: {
      left: "prev,next today",
      center: "title",
      right: "dayGridMonth,timeGridWeek,timeGridDay",
    },

    // Definir o idioma usado no calendário
    locale: "pt-br",

    // Permitir clicar nos nomes dos dias da semana
    navLinks: true,

    // Permitir clicar e arrastar o mouse sobre um ou vários dias no calendário
    selectable: true,

    // Indicar visualmente a área que será selecionada antes que o usuário solte o botão do mouse para confirmar a seleção
    selectMirror: true,

    // Permitir arrastar e redimensionar os eventos diretamente no calendário.
    editable: true,

    // Número máximo de eventos em um determinado dia, se for true, o número de eventos será limitado à altura da célula do dia
    dayMaxEvents: true,

    // Chamar o arquivo PHP para recuperar os eventos
    events: {
      url: "../app/calendario/listar_evento.php",
      method: "POST",
      success: function (response) {
        return response.map(function (evento) {
          return {
            id: evento.idAgendamento,
            title: evento.titulo, // Mapeia titulo → title
            start: evento.dataInicio, // Mapeia dataInicio → start
            end: evento.dataFim, // Mapeia dataFim → end
            color: evento.cor, // Mapeia cor → color
            extendedProps: {
              status: evento.extendedProps.status,
            },
          };
        });
      },
    },

    // Identificar o clique do usuário sobre o evento
    eventClick: function (info) {
      // Apresentar os detalhes do evento
      document.getElementById("visualizarEvento").style.display = "block";
      document.getElementById("visualizarModalLabel").style.display = "block";

      // Ocultar o formulário editar do evento
      document.getElementById("editarEvento").style.display = "none";
      document.getElementById("editarModalLabel").style.display = "none";

      // Enviar para a janela modal os dados do evento
      document.getElementById("visualizar_id").innerText = info.event.id;
      document.getElementById("visualizar_title").innerText = info.event.title;
      document.getElementById("visualizar_start").innerText =
        info.event.start.toLocaleString();
      document.getElementById("visualizar_end").innerText =
        info.event.end !== null
          ? info.event.end.toLocaleString()
          : info.event.start.toLocaleString();

      // Enviar os dados do evento para o formulário editar
      document.getElementById("edit_id").value = info.event.id;
      document.getElementById("edit_title").value = info.event.title;
      document.getElementById("edit_start").value = converterData(
        info.event.start
      );
      document.getElementById("edit_end").value =
        info.event.end !== null
          ? converterData(info.event.end)
          : converterData(info.event.start);

      // Definir o status se existir
      const status = info.event.extendedProps.status || "confirmado";
      document.getElementById("edit_status").value = status;

      // Atualizar dropdown apenas se existir
      const editStatusDropdown = document
        .querySelector("#edit_status")
        .closest(".dropdown-container");
      if (editStatusDropdown) {
        updateDropdown("edit_status", status);
      }

      // Abrir a janela modal visualizar
      openModal(visualizarModal);
    },

    // Abrir a janela modal cadastrar quando clicar sobre o dia no calendário
    select: function (info) {
      // Chamar a função para converter a data selecionada para ISO8601 e enviar para o formulário
      document.getElementById("dataSelecionada").value = converterData(
        info.start
      );
      document.getElementById("inicioEvento").value = converterData(info.start);
      document.getElementById("fimEvento").value = converterData(info.start);

      // Resetar o status para confirmado apenas se existir
      const cadStatusInput = document.querySelector(
        "#formularioModal .realStatusInput"
      );
      if (cadStatusInput) {
        cadStatusInput.value = "confirmado";

        // Atualizar dropdown apenas se existir
        const cadStatusDropdown = cadStatusInput.closest(".dropdown-container");
        if (cadStatusDropdown) {
          updateDropdown(cadStatusInput.id, "confirmado");
        }
      }

      // Abrir a janela modal cadastrar
      openModal(formularioModal);
    },
  });

  // Renderizar o calendário
  calendar.render();

  const notyf = new Notyf({
    duration: 2000,
    position: {
      x: "right",
      y: "top",
    },
  });

  // Converter a data
  function converterData(data) {
    // Converter a string em um objeto Date
    const dataObj = new Date(data);

    // Extrair o ano da data
    const ano = dataObj.getFullYear();

    // Obter o mês, mês começa de 0, padStart adiciona zeros à esquerda para garantir que o mês tenha dígitos
    const mes = String(dataObj.getMonth() + 1).padStart(2, "0");

    // Obter o dia do mês, padStart adiciona zeros à esquerda para garantir que o dia tenha dois dígitos
    const dia = String(dataObj.getDate()).padStart(2, "0");

    // Obter a hora, padStart adiciona zeros à esquerda para garantir que a hora tenha dois dígitos
    const hora = String(dataObj.getHours()).padStart(2, "0");

    // Obter minuto, padStart adiciona zeros à esquerda para garantir que o minuto tenha dois dígitos
    const minuto = String(dataObj.getMinutes()).padStart(2, "0");

    // Retornar a data no formato para input datetime-local
    return `${ano}-${mes}-${dia}T${hora}:${minuto}`;
  }

  // Funções para manipulação dos modais
  function openModal(modal) {
    modal.style.display = "block";
    modalOverlay.style.display = "block";
    document.body.style.overflow = "hidden";
  }

  function closeModal(modal) {
    modal.style.display = "none";
    modalOverlay.style.display = "none";
    document.body.style.overflow = "auto";
  }

  // Fechar modais ao clicar no X ou no overlay
  document.querySelectorAll(".close, .custom-close").forEach((closeBtn) => {
    closeBtn.addEventListener("click", function () {
      const modal = this.closest(".modal, .custom-modal");
      closeModal(modal);
    });
  });

  modalOverlay.addEventListener("click", function () {
    closeModal(visualizarModal);
    closeModal(formularioModal);
  });

  // Receber o SELETOR do formulário cadastrar evento
  const formCadEvento = document.getElementById("eventoForm");

  // Receber o SELETOR da mensagem cadastrar evento
  const msgCadEvento = document.getElementById("msgCadEvento");

  // Receber o SELETOR do botão da janela modal cadastrar evento
  const btnCadEvento = document.getElementById("btnCadEvento");

  if (formCadEvento) {
    formCadEvento.addEventListener("submit", async (e) => {
      e.preventDefault();
      btnCadEvento.textContent = "Salvando...";

      try {
        const dadosForm = new FormData(formCadEvento);
        const response = await fetch("../app/calendario/cadastrar_evento.php", {
          method: "POST",
          body: dadosForm,
        });

        const resposta = await response.json();

        if (!resposta.status) {
          notyf.error(resposta.msg);
        } else {
          notyf.success(resposta.msg);
          msgCadEvento.innerHTML = "";
          formCadEvento.reset();

          calendar.addEvent({
            id: resposta.idAgendamento,
            title: resposta.titulo,
            color: resposta.cor,
            start: resposta.dataInicio,
            end: resposta.dataFim,
            extendedProps: {
              status: resposta.extendedProps?.status || "confirmado",
            },
          });

          closeModal(formularioModal);
        }
      } catch (error) {
        notyf.error("Ocorreu um erro ao cadastrar o evento");
      } finally {
        btnCadEvento.textContent = "Salvar";
      }
    });
  }

  // Função para remover a mensagem após 3 segundo
  function removerMsg() {
    setTimeout(() => {
      document.getElementById("msg").innerHTML = "";
    }, 3000);
  }

  // Receber o SELETOR ocultar detalhes do evento e apresentar o formulário editar evento
  const btnEditButton = document.getElementById("btnEditButton");

  // Somente acessa o IF quando existir o SELETOR "btnEditButton"
  if (btnEditButton) {
    // Aguardar o usuario clicar no botao editar
    btnEditButton.addEventListener("click", () => {
      // Ocultar os detalhes do evento
      document.getElementById("visualizarEvento").style.display = "none";
      document.getElementById("visualizarModalLabel").style.display = "none";

      // Apresentar o formulário editar do evento
      document.getElementById("editarEvento").style.display = "block";
      document.getElementById("editarModalLabel").style.display = "block";
    });
  }

  // Receber o SELETOR ocultar formulário editar evento e apresentar o detalhes do evento
  const btnViewEvento = document.getElementById("btnViewEvento");

  // Somente acessa o IF quando existir o SELETOR "btnViewEvento"
  if (btnViewEvento) {
    // Aguardar o usuario clicar no botao editar
    btnViewEvento.addEventListener("click", () => {
      // Apresentar os detalhes do evento
      document.getElementById("visualizarEvento").style.display = "block";
      document.getElementById("visualizarModalLabel").style.display = "block";

      // Ocultar o formulário editar do evento
      document.getElementById("editarEvento").style.display = "none";
      document.getElementById("editarModalLabel").style.display = "none";
    });
  }

  // Receber o SELETOR do formulário editar evento
  const formEditEvento = document.getElementById("formEditEvento");

  // Receber o SELETOR da mensagem editar evento
  const msgEditEvento = document.getElementById("msgEditEvento");

  // Receber o SELETOR do botão editar evento
  const btnEditEvento = document.getElementById("btnEditEvento");

  if (formEditEvento) {
    formEditEvento.addEventListener("submit", async (e) => {
      e.preventDefault();
      btnEditEvento.textContent = "Salvando...";

      try {
        const dadosForm = new FormData(formEditEvento);
        const response = await fetch("../app/calendario/editar_evento.php", {
          method: "POST",
          body: dadosForm,
        });

        const resposta = await response.json();

        if (!resposta.status) {
          notyf.error(resposta.msg);
        } else {
          notyf.success(resposta.msg);
          msgEditEvento.innerHTML = "";
          formEditEvento.reset();

          // Recuperar e atualizar o evento
          const eventoExiste = calendar.getEventById(resposta.idAgendamento); // Alterado para idAgendamento

          if (eventoExiste) {
            eventoExiste.setProp("title", resposta.titulo); // Alterado para titulo
            eventoExiste.setProp("color", resposta.cor); // Alterado para cor
            eventoExiste.setStart(resposta.dataInicio); // Alterado para dataInicio
            eventoExiste.setEnd(resposta.dataFim); // Alterado para dataFim

            if (resposta.extendedProps?.status) {
              eventoExiste.setExtendedProp(
                "status",
                resposta.extendedProps.status
              );
            }
          }

          closeModal(visualizarModal);
        }
      } catch (error) {
        notyf.error("Ocorreu um erro ao editar o evento");
      } finally {
        btnEditEvento.textContent = "Salvar";
      }
    });
  }

  // Receber o SELETOR apagar evento
  const btnDeleteButton = document.getElementById("btnDeleteButton");

  // Somente acessa o IF quando existir o SELETOR "btnDeleteButton"
  if (btnDeleteButton) {
    // Aguardar o usuario clicar no botao apagar
    btnDeleteButton.addEventListener("click", async () => {
      // Exibir uma caixa de diálogo de confirmação
      const confirmacao = window.confirm(
        "Tem certeza de que deseja apagar este evento?"
      );

      // Verificar se o usuário confirmou
      if (confirmacao) {
        // Receber o id do evento
        var idEvento = document.getElementById("visualizar_id").textContent;

        // Chamar o arquivo PHP responsável apagar o evento
        const dados = await fetch(
          "../app/calendario/apagar_evento.php?idAgendamento=" + idEvento
        );

        // Realizar a leitura dos dados retornados pelo PHP
        const resposta = await dados.json();

        // Acessa o IF quando não cadastrar com sucesso
        if (!resposta["status"]) {
          // Enviar a mensagem para o HTML
          notyf.error(resposta["msg"]);
        } else {
          // Enviar a mensagem para o HTML
          notyf.success(resposta["msg"]);

          // Enviar a mensagem para o HTML
          msgViewEvento.innerHTML = "";

          // Recuperar o evento no FullCalendar
          const eventoExisteRemover = calendar.getEventById(idEvento);

          // Verificar se encontrou o evento no FullCalendar
          if (eventoExisteRemover) {
            // Remover o evento do calendário
            eventoExisteRemover.remove();
          }

          // Chamar a função para remover a mensagem após 3 segundo
          removerMsg();

          // Fechar a janela modal
          closeModal(visualizarModal);
        }
      }
    });
  }

  // Função para atualizar o dropdown de status - COM PROTEÇÃO CONTRA NULL
  function updateDropdown(inputId, value) {
    const inputElement = document.getElementById(inputId);
    if (!inputElement) return;

    const dropdownContainer = inputElement.closest(".dropdown-container");
    if (!dropdownContainer) return;

    const selected = dropdownContainer.querySelector(".dropdown-selected");
    const options = dropdownContainer.querySelectorAll(".option");

    // Atualizar o valor do input
    inputElement.value = value;

    // Atualizar o texto exibido
    const selectedOption = Array.from(options).find(
      (opt) => opt.dataset.value === value
    );
    if (selectedOption) {
      const colorBox = selectedOption.querySelector(".color-box");
      const text = selectedOption.textContent.trim();

      const selectedColorBox = selected.querySelector(".color-box");
      const selectedText = selected.querySelector(".dropdown-text");

      if (selectedColorBox) selectedColorBox.className = `color-box ${value}`;
      if (selectedText) selectedText.textContent = text.split("\n")[0].trim();
    }
  }

  // Configurar os dropdowns de status
  document.querySelectorAll(".dropdown").forEach((dropdown) => {
    const selected = dropdown.querySelector(".dropdown-selected");
    const options = dropdown.querySelector(".dropdown-options");
    const realInput = dropdown
      .closest(".dropdown-container")
      .querySelector(".realStatusInput");

    selected.addEventListener("click", () => {
      options.style.display =
        options.style.display === "block" ? "none" : "block";
    });

    dropdown.querySelectorAll(".option").forEach((option) => {
      option.addEventListener("click", () => {
        const value = option.dataset.value;
        const colorBox = option.querySelector(".color-box").className;
        const text = option.textContent.trim();

        selected.querySelector(".color-box").className = `color-box ${value}`;
        selected.querySelector(".dropdown-text").textContent = text
          .split("\n")[0]
          .trim();
        realInput.value = value;

        options.style.display = "none";
      });
    });
  });

  // Fechar dropdowns ao clicar fora
  document.addEventListener("click", (e) => {
    if (!e.target.closest(".dropdown")) {
      document.querySelectorAll(".dropdown-options").forEach((options) => {
        options.style.display = "none";
      });
    }
  });
});
