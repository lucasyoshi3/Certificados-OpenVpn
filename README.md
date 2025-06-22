# Painel de Gerenciamento de VPN com Certificados Individuais

Este projeto tem como finalidade centralizar e facilitar o gerenciamento de certificados VPN para funcionários de uma empresa. Através deste painel web, é possível emitir, visualizar, revogar e baixar certificados VPN de forma segura, evitando o uso de certificados compartilhados e promovendo maior controle sobre o acesso à rede corporativa.

---

## 🧩 Visão Geral da Arquitetura

### Estrutura de Rede

A solução é distribuída em **três máquinas virtuais (VMs)**, cada uma com uma função específica:

| VM         | IP        | Responsabilidade                    | Tecnologias Utilizadas |
|------------|-----------|-------------------------------------|-------------------------|
| Firewall   | 10.0.0.1  | Controle de tráfego interno/externo | nftables                |
| OpenVPN    | 10.0.0.10 | Servidor VPN + Aplicação Web        | Apache2, PHP            |
| Banco de Dados | 10.0.0.20 | Armazenamento de dados             | MySQL                   |

**Fluxo de comunicação:**
- O **Firewall** possui acesso às duas outras máquinas.
- O **OpenVPN** se comunica apenas com o **Banco de Dados**.
- Nenhuma máquina (exceto o Firewall) possui acesso direto à internet.

---

## 🔒 Regras de Firewall

| Porta | Protocolo | Destino  | Serviço                         |
|-------|-----------|----------|----------------------------------|
| 80    | TCP       | OpenVPN  | Redirecionamento para HTTPS      |
| 443   | TCP       | OpenVPN  | Painel Web                       |
| 1194  | UDP       | OpenVPN  | Servidor OpenVPN                 |
| 22    | TCP       | OpenVPN  | SSH (restrito à rede interna)    |

> 🚫 Todo o tráfego não listado é bloqueado por padrão.

---

## ⚙️ Funcionalidades Disponíveis

### Certificados VPN

- **Geração:** Cria um identificador aleatório (7 caracteres) e arquivos `.zip` contendo `.ovpn`, `.crt` e `.key`. A validade é de 7 dias.
- **Listagem:** Exibe todos os certificados, com filtros por data e ID.
- **Download:** Apenas disponível via painel, com leitura direta (sem URLs públicas).
- **Revogação/Exclusão:** O certificado é revogado no servidor e removido do sistema.

### Usuários

- **Cadastro e visualização:** Gerenciamento de administradores do sistema.
- **Autenticação:** Login protegido com hash de senha usando `password_hash()`.
- **Controle de acesso:** Todas as páginas protegidas requerem autenticação válida.

---

## 🔐 Políticas de Segurança

- **Política padrão (default):** Bloqueia todo tráfego de entrada e encaminhamento.
- **Permissões explícitas:** Apenas portas essenciais (HTTP/HTTPS, VPN e SSH interno) são liberadas.
- **Isolamento:** Máquinas OpenVPN e Banco não têm acesso à internet diretamente.

---

## 🖥️ Requisitos por Máquina

### Acesso comum às VMs

- Rede definida em `/etc/network/interfaces`.
- Credenciais padrão:
  - **Usuário:** `usuario`
  - **Senha:** `123456`

### Configuração do Firewall

Arquivo `/etc/network/interfaces`:

```bash
source /etc/network/interfaces.d/*

auto lo
iface lo inet loopback

auto enp0s8
iface enp0s8 inet dhcp

auto enp0s3
iface enp0s3 inet static
    address 10.0.0.1
    netmask 255.255.255.0
    network 10.0.0.0
    broadcast 10.0.0.255
```

> Regras configuradas via `/etc/nftables.conf`.

### Configuração da VM OpenVPN

Rede (`/etc/network/interfaces`):

```bash
source /etc/network/interfaces.d/*

auto lo
iface lo inet loopback

auto enp0s3
iface enp0s3 inet static
    address 10.0.0.10
    netmask 255.255.255.0
    network 10.0.0.0
    broadcast 10.0.0.255
    gateway 10.0.0.1
```

> Instalar: **Apache2**, **PHP**, **OpenVPN**  
> Copiar os arquivos do projeto para `/var/www/html/`

### Configuração da VM Database

- IP: `10.0.0.20` (rede igual à OpenVPN)
- Instalar **MySQL Server**
- Criar banco e tabelas para `usuarios` e `certificados`.

---

## 📥 VMs Prontas para Download

Você pode baixar as três VMs já configuradas no seguinte link:

👉 [Download via Google Drive](https://drive.google.com/drive/folders/1MhDxd-Ku4oU6KndtwsuQVd44Br34tUGs)

---

## 🌐 Acesso ao Painel Web

1. No Firewall, execute:

```bash
ip a
```

Copie o IP externo da interface `enp0s8`.

2. No navegador, acesse:

```bash
http://<IP_FIREWALL>/index.php
```

3. Login padrão:

- **Usuário:** `admin@gmail.com`
- **Senha:** `Admin123!`

---

## 📁 Organização do Projeto

Estrutura dos arquivos na pasta `/var/www/html/` da VM OpenVPN:

```
/var/www/html/
├── index.php                 
├── views/
│   ├── cadastro.php          
│   ├── login.php             
│   ├── logout.php            
│   ├── adms.php              
│   ├── baixar.php            
│   └── certificados.php      
├── includes/
│   ├── head.php              
│   ├── footer.php            
│   ├── navbar.php            
│   ├── auth.php              
│   └── funcoes.php           
└── storage/
    ├── registros.json        
    ├── usuarios.json         
    └── <id>_cert.zip         
```

---

## 🖼️ Captura de Tela

### Página inicial

![Tela inicial](Screenshots/1-index.png)
