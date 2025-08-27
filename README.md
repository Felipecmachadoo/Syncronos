
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

---

## 📸 Demonstração <br>

Página de Login
<img width="897" height="534" alt="image" src="https://github.com/user-attachments/assets/f14913e9-013e-4ad0-80f1-1f73db69792f" />

Cadastro de Agendamentos
<img width="1019" height="609" alt="image" src="https://github.com/user-attachments/assets/be501a9c-32c6-4236-99bf-c219022fa5c4" />

Horários do Estabelecimento
<img width="1014" height="570" alt="image" src="https://github.com/user-attachments/assets/25e46687-7983-4cbc-8d1b-b741f3115232" />

Cadastro de Serviços
<img width="1038" height="617" alt="image" src="https://github.com/user-attachments/assets/601c2304-0f72-4b31-a890-854e56db7c1d" />

Cadastro de Profissionais
<img width="1012" height="620" alt="image" src="https://github.com/user-attachments/assets/e31d3c53-e6d9-48dd-8e92-14583a796e20" />

Cadastro do Expediente dos Profissionais
<img width="1007" height="616" alt="image" src="https://github.com/user-attachments/assets/a9950989-81ad-4e13-adf5-bbbe05a13207" />

Página do Estabelecimento
<img width="1016" height="598" alt="image" src="https://github.com/user-attachments/assets/5e22cd68-deca-4a04-be8e-346b81d7cd3d" />

Agendar Serviço (Cliente)
<img width="989" height="556" alt="image" src="https://github.com/user-attachments/assets/e497cbe8-a0df-458b-b9a9-9ede7f983092" />

Página para Selecionar Horários do Agendamento (Cliente)
<img width="944" height="531" alt="image" src="https://github.com/user-attachments/assets/0ea36649-1452-40ad-8f8e-a8c0a077bc50" />

Confirmação do Agendamento
<img width="1000" height="563" alt="image" src="https://github.com/user-attachments/assets/8de6e310-9184-442f-b157-7455694a9333" />

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
