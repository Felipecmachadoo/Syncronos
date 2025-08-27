
# 🗓️ Syncronos – Sistema de Agendamento Online

O **Syncronos** é um sistema web de agendamentos desenvolvido para barbearias, salões de beleza, clínicas e outros serviços que trabalham com horários pré-definidos. O objetivo é permitir que **clientes agendem atendimentos online de forma autônoma**, enquanto os profissionais têm controle total sobre seus serviços, horários e agenda.

---

## 🚀 Funcionalidades
- Cadastro e autenticação de **administradores, profissionais e clientes**
- **Página exclusiva por estabelecimento**, com serviços e agenda disponíveis para os clientes
- **Agenda interativa** (com FullCalendar) para visualização e edição de agendamentos
- **Agendamento online** por clientes, com seleção de serviço, profissional e horário disponível
- **Agendamento manual** feito pelo administrador diretamente no calendário
- **Gerenciamento de serviços** (nome, descrição, preço, duração)
- **Cadastro de profissionais** com seus respectivos horários de expediente e intervalos
- **Configuração de horários de funcionamento** do estabelecimento

---

## 🖥️ Tecnologias Utilizadas
- **Frontend:** HTML, CSS, JavaScript, Bootstrap, FullCalendar
- **Backend:** PHP (MVC), MySQL
- **Outros:** Mithril.js (SPA), integração com modais e rotas dinâmicas

---

## 📸 Demonstração
(👉 Aqui você pode colocar prints do sistema – login, agenda, cadastro de serviços, etc. – ou até GIFs mostrando a navegação.)

---

## 📖 Como Usar

### 🔑 Login como Administrador
- Acesse a página de login e entre com o usuário administrador.
- Após o login, você terá acesso ao painel com as abas: **Dashboard, Agenda, Horários, Serviços, Profissionais, Perfil, Configurações e Sair**.

### 📅 Agenda
- Visualize os agendamentos realizados pelos clientes.
- Crie agendamentos manuais preenchendo título, horário e status (Confirmado, Pendente, Cancelado).

### 🕒 Horários do Estabelecimento
- Configure dias de atendimento e horários de abertura/fechamento.
- Essas informações ficam disponíveis para os clientes na página do estabelecimento.

### ✂️ Serviços
- Cadastre novos serviços informando nome, preço, duração e descrição.
- Os serviços ficam automaticamente disponíveis para agendamento.

### 👨‍💼 Profissionais
- Cadastre profissionais com seus horários de expediente e intervalos.
- Os clientes podem selecionar o profissional desejado na hora do agendamento.

### 👥 Login como Cliente
- O cliente acessa a página exclusiva do estabelecimento.
- Escolhe um serviço, seleciona o profissional, define o horário e confirma o agendamento.

---

## ⚙️ Instalação e Configuração
1. Clone o repositório:
   ```bash
   git clone https://github.com/Felipecmachadoo/Syncronos.git
   ```
2. Configure o banco de dados MySQL (script disponível em `/database`).
3. Ajuste as credenciais de acesso ao banco em `config.php`.
4. Inicie o servidor local (ex.: XAMPP, WAMP).
5. Acesse `http://localhost/syncronos` no navegador.

---

## 👨‍💻 Autor
**Felipe Machado**  
🔗 [LinkedIn](https://www.linkedin.com/in/felipe-campos-machado/)  
🔗 [GitHub](https://github.com/Felipecmachadoo)
