<!-- LINK DO FONT AWESOME (coloque no <head> se ainda não tiver) -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

<!-- ESTILO PERSONALIZADO PARA O BOTÃO ROXO -->
<style>
        .bg-purple {
            background-color: #6f42c1 !important;
        }

        .dropdown-menu-purple {
            background-color: #5a32a3;
        }

        .dropdown-menu-purple .dropdown-item {
            color: #ffffff;
        }

        .dropdown-menu-purple .dropdown-item:hover {
            background-color: #7046c9;
        }

        /* Estilo dos botões da navbar */
        .navbar-nav .nav-link {
            background-color: rgba(255, 255, 255, 0.1);
            margin: 0 6px;
            padding: 8px 16px;
            border-radius: 6px;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .navbar-nav .nav-link:hover,
        .navbar-nav .nav-link:focus {
            background-color: rgba(255, 255, 255, 0.25);
            color: #ffffff;
        }

        .navbar-nav .dropdown-menu .dropdown-item {
            border-radius: 4px;
            margin: 4px;
            padding: 8px 12px;
        }

        .navbar-nav .dropdown-toggle::after {
            margin-left: 0.3rem;
        }
    </style>

<!-- GRUPO DE BOTÕES MODERNOS -->
<nav class="navbar navbar-expand-lg navbar-dark bg-purple shadow-sm">
        <!-- Toggler -->
        <button class="navbar-toggler mx-2" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Content -->
        <div class="collapse navbar-collapse" id="navbarContent">
            <!-- Botões centralizados -->
            <ul class="navbar-nav mx-auto">
                <li class="nav-item">
                    <a class="nav-link" href="/index.php">
                        <i class="fa-solid fa-house"></i> Página inicial
                    </a>
                </li>

                <!-- Certificado -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="dropdown1" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fa-solid fa-file-lines"></i> Certificado
                    </a>
                    <ul class="dropdown-menu dropdown-menu-purple" aria-labelledby="dropdown1">
                        <li><a class="dropdown-item" href="/views/certificados.php">Início</a></li>
                    </ul>
                </li>

                <!-- ADM -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="dropdown2" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fa-solid fa-user-gear"></i> ADM
                    </a>
                    <ul class="dropdown-menu dropdown-menu-purple" aria-labelledby="dropdown2">
                        <li><a class="dropdown-item" href="/views/adms.php">Início</a></li>
                        <li><a class="dropdown-item" href="/views/cadastro.php">Cadastrar</a></li>
                    </ul>
                </li>

                <!-- Sair -->
                <li class="nav-item">
                    <a href="/views/logout.php" class="nav-link">
                        <i class="fa-solid fa-door-open"></i> Sair
                    </a>
                </li>
            </ul>
        </div>
    </nav>