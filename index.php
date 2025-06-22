<!DOCTYPE html>
<html lang="pt-br">

<?php
require_once __DIR__ . '/includes/head.php';
renderHead('Página Inicial VPN');
?>

<body class="bg-light d-flex justify-content-center align-items-center vh-100">

    <div class="card shadow-lg p-4 rounded-4" style="max-width: 400px; width: 100%;">
        <div class="card-body text-center">
            <h1 class="card-title mb-3 text-primary fw-bold">Painel VPN</h1>
            <p class="card-text mb-4 text-muted">Escolha uma das opções abaixo para começar:</p>

            <div class="d-grid gap-3">
                <a href="views/certificados.php" class="btn btn-outline-primary btn-lg d-flex align-items-center justify-content-center gap-2">
                    <i class="fas fa-certificate fa-lg"></i> <span>Gerenciar Certificados</span>
                </a>

                <a href="views/adms.php" class="btn btn-outline-success btn-lg d-flex align-items-center justify-content-center gap-2">
                    <i class="fas fa-users fa-lg"></i> <span>Gerenciar Usuários</span>
                </a>

                <a href="views/login.php" class="btn btn-outline-warning btn-lg d-flex align-items-center justify-content-center gap-2 text-dark">
                    <i class="fas fa-sign-in-alt fa-lg"></i> <span>Fazer Login</span>
                </a>
            </div>
        </div>
    </div>

</body>
</html>
