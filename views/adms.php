<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/conexao.php'; // ✅ Conexão com o banco

$tituloPagina = "Lista de Administradores";
include_once __DIR__ . '/../includes/head.php';
include_once __DIR__ . '/../includes/navbar.php';

$mensagem = '';

// Processamento do POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['acao'], $_POST['selecionados'])) {
        $idsSelecionados = $_POST['selecionados'];

        if ($_POST['acao'] === 'desativar') {
            $stmt = $pdo->prepare("UPDATE usuarios SET ativo = 0 WHERE email = ?");
            foreach ($idsSelecionados as $email) {
                $stmt->execute([$email]);
            }
            $mensagem = '<div class="alert alert-warning">Usuários desativados.</div>';
        }

        if ($_POST['acao'] === 'ativar') {
            $stmt = $pdo->prepare("UPDATE usuarios SET ativo = 1 WHERE email = ?");
            foreach ($idsSelecionados as $email) {
                $stmt->execute([$email]);
            }
            $mensagem = '<div class="alert alert-success">Usuários ativados.</div>';
        }

        if ($_POST['acao'] === 'remover') {
            $stmt = $pdo->prepare("DELETE FROM usuarios WHERE email = ?");
            foreach ($idsSelecionados as $email) {
                $stmt->execute([$email]);
            }
            $mensagem = '<div class="alert alert-danger">Usuários removidos.</div>';
        }
    }
}

// Buscar todos os usuários
$stmt = $pdo->query("SELECT email, nome, ativo FROM usuarios ORDER BY nome");
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>


<!DOCTYPE html>
<html lang="pt-BR">
<?php renderHead($tituloPagina); ?>

<body class="bg-light d-flex flex-column min-vh-100">
<?php include_once __DIR__ . '/../includes/navbar.php'; ?>

<div class="container py-5 flex-grow-1">
    <div class="card shadow rounded-4 p-4">
        <h2 class="text-primary mb-4"><?= $tituloPagina ?></h2>

        <?php
        $mensagem = '';

        // Processamento do POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['acao'], $_POST['selecionados'])) {
                $idsSelecionados = $_POST['selecionados'];

                if ($_POST['acao'] === 'desativar') {
                    $stmt = $pdo->prepare("UPDATE usuarios SET ativo = 0 WHERE email = ?");
                    foreach ($idsSelecionados as $email) {
                        $stmt->execute([$email]);
                    }
                    $mensagem = '<div class="alert alert-warning">❌ Usuários desativados.</div>';
                }

                if ($_POST['acao'] === 'ativar') {
                    $stmt = $pdo->prepare("UPDATE usuarios SET ativo = 1 WHERE email = ?");
                    foreach ($idsSelecionados as $email) {
                        $stmt->execute([$email]);
                    }
                    $mensagem = '<div class="alert alert-success">✅ Usuários ativados.</div>';
                }

                if ($_POST['acao'] === 'remover') {
                    $stmt = $pdo->prepare("DELETE FROM usuarios WHERE email = ?");
                    foreach ($idsSelecionados as $email) {
                        $stmt->execute([$email]);
                    }
                    $mensagem = '<div class="alert alert-danger">🗑️ Usuários removidos.</div>';
                }
            }
        }

        // Buscar usuários
        $stmt = $pdo->query("SELECT email, nome, ativo, telefone FROM usuarios ORDER BY nome");
        $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
        ?>

        <?php if (!empty($mensagem)) echo $mensagem; ?>

        <form method="POST">
            <div class="d-flex flex-wrap gap-2 mb-3">
                <a href="/views/cadastro.php" class="btn btn-primary">
                    ➕ Cadastrar novo
                </a>

                <div class="btn-group">
                    <button type="button" class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                        Ações em lote
                    </button>
                    <ul class="dropdown-menu">
                        <li><button type="submit" name="acao" value="desativar" class="dropdown-item text-warning">Desativar acesso</button></li>
                        <li><button type="submit" name="acao" value="ativar" class="dropdown-item text-success">Ativar acesso</button></li>
                        <li><button type="submit" name="acao" value="remover" class="dropdown-item text-danger" onclick="return confirm('Tem certeza que deseja remover os usuários selecionados?')">Remover usuário</button></li>
                    </ul>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">
                    <thead class="table-dark text-center">
                        <tr>
                            <th>Selecionar</th>
                            <th>Email</th>
                            <th>Nome</th>
                            <th>Status</th>
                            <th>Telefone</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($usuarios)): ?>
                            <tr>
                                <td colspan="5" class="text-center">Nenhum administrador cadastrado.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($usuarios as $usuario): ?>
                                <tr>
                                    <td class="text-center">
                                        <input type="checkbox" name="selecionados[]" value="<?= htmlspecialchars($usuario['email']) ?>">
                                    </td>
                                    <td><?= htmlspecialchars($usuario['email']) ?></td>
                                    <td><?= htmlspecialchars($usuario['nome']) ?></td>
                                    <td class="<?= $usuario['ativo'] ? 'text-success' : 'text-muted' ?>">
                                        <?= $usuario['ativo'] ? 'Ativo' : 'Inativo' ?>
                                    </td>
                                    <td><?= htmlspecialchars($usuario['telefone'] ?? '-') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </form>
    </div>
</div>

</body>
</html>
