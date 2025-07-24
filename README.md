# 📅 Meet2IC — Sistema de Consulta de Disponibilidade

**Meet2IC** é uma aplicação web desenvolvida em PHP com MySQL, que permite criar enquetes de disponibilidade semelhantes ao Doodle, com autenticação via Google e interface moderna usando FullCalendar e Bootstrap.

A solução para criar e responder enquetes de disponibilidade com facilidade usando sua conta Google, ou a diretamente incluindo seu nome.

---

## 📁 Estrutura do Projeto

```
/
├── config.php              # Configuração de sessão, Google OAuth e conexão PDO
├── dashboard.php           # Página de informação de Minhas enquetes
├── index.php               # Página inicial com botão de login via Google
├── login.php               # Realiza autenticação OAuth2 com o Google
├── logout.php              # Finaliza a sessão
├── create_poll.php         # Tela de criação de enquetes com FullCalendar (requer login)
├── poll.php                # Tela de resposta à enquete por convidados ou usuários logados
├── callback.php            # Endpoint de retorno do Google OAuth
├── submit_availability.php # Controle para persistência de resposta na enquete e envio de notificação
├── doc/                    # Documentação sobre a utilização do ambiente
├── vendor/                 # Pacotes Composer (após instalação)
├── images/
│   └── meet2ic.png         # Logo da aplicação
├── sql/
│   └── init.sql            # Script para criação do banco de dados MySQL
└── README.md               # Esta documentação
```

---

## 🧰 Tecnologias Utilizadas

* **PHP 7+**
* **MySQL 5.7+**
* **Bootstrap 5.3**
* **jQuery 3.6**
* **FullCalendar 6.1.10 (modo global via CDN)**
* **Google OAuth 2.0**
* **CKEditor (utilizado para criação da descrição da enquete)**

---

## 🔐 Autenticação com Google

1. Crie um projeto no [Google Cloud Console](https://console.cloud.google.com/)
2. Ative o OAuth2 e configure:

   * URI de redirecionamento: `https://SEU_DOMINIO/callback.php`
3. Coloque as credenciais no `config.php`:

```php
$client_id = 'SEU_CLIENT_ID';
$client_secret = 'SEU_CLIENT_SECRET';
$redirect_uri = 'https://SEU_DOMINIO/callback.php';
```

---

## 🗃️ Banco de Dados (MySQL)

Crie o banco com o seguinte esquema:

```sql
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE polls (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    token VARCHAR(64) NOT NULL UNIQUE,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE poll_slots (
    id INT AUTO_INCREMENT PRIMARY KEY,
    poll_id INT NOT NULL,
    start_time DATETIME NOT NULL,
    end_time DATETIME NOT NULL,
    FOREIGN KEY (poll_id) REFERENCES polls(id)
);

CREATE TABLE responses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    poll_id INT NOT NULL,
    user_name VARCHAR(100) NOT NULL,
    user_email VARCHAR(100),
    slot_id INT NOT NULL,
    available BOOLEAN NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (poll_id) REFERENCES polls(id),
    FOREIGN KEY (slot_id) REFERENCES poll_slots(id)
);
```

---

## 🚀 Funcionalidades

* Login com Google (OAuth2)
* Criação de enquetes com seleção visual de horários (via FullCalendar) e descrição com (via CKEditor)
* Compartilhamento por URL única
* Participantes podem responder com ou sem login
* Visualização de quem está disponível em cada horário
* Possibilidade de remover horários durante a criação
* Notificações por e-mail (utilizando MTA local)
* Define horário ideal com base na sobreposição
* Dashboard com histórico de enquetes

---

## 🧪 Requisitos

* PHP 7.4+
* MySQL/MariaDB
* Servidor Apache/Nginx com rewrite ativo

Para containers, o PHP\:Apache já pode ser utilizado com volume montado e permissões ajustadas.

---

## 🧭 Roadmap Futuro

* Exportar para Google Agenda ou .ics

---

## 📝 Licença

Este projeto está sendo utilizado internamente na UNICAMP (Instituto de Computação) e segue diretrizes de software livre acadêmico. Ele pode ser adaptado por outras unidades ou por qualquer pessoa interessada. Caso sejam implementadas melhorias, agradecemos se forem compartilhadas conosco.
