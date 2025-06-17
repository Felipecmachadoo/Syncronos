function toggleHours() {
  // Implementar toggle dos horários
  alert("Horários de funcionamento seriam exibidos aqui");
}

// Search functionality
document.querySelector(".search-input").addEventListener("input", function (e) {
  const searchTerm = e.target.value.toLowerCase();
  const serviceCards = document.querySelectorAll(".service-card");

  serviceCards.forEach((card) => {
    const serviceName = card.querySelector("h3").textContent.toLowerCase();
    const serviceDesc = card
      .querySelector(".service-description")
      .textContent.toLowerCase();

    if (serviceName.includes(searchTerm) || serviceDesc.includes(searchTerm)) {
      card.style.display = "flex";
    } else {
      card.style.display = "none";
    }
  });
});

// Função para alternar o dropdown do usuário
function toggleUserDropdown() {
  const dropdown = document.querySelector(".user-dropdown");
  dropdown.classList.toggle("active");
}

// Fechar dropdown quando clicar fora dele
document.addEventListener("click", (event) => {
  const dropdown = document.querySelector(".user-dropdown");
  const isClickInsideDropdown = dropdown && dropdown.contains(event.target);

  if (
    !isClickInsideDropdown &&
    dropdown &&
    dropdown.classList.contains("active")
  ) {
    dropdown.classList.remove("active");
  }
});

// Função existente para horários (se houver)
function toggleHours() {
  // Implementar lógica dos horários aqui
  console.log("Toggle hours functionality");
}
