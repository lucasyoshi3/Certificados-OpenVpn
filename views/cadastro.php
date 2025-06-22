<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/funcoes.php';
require_once __DIR__ . '/../includes/conexao.php';
require_once __DIR__ . '/../includes/auth.php';

$erro = '';
$sucesso = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $nome = isset($_POST['nome']) ? trim($_POST['nome']) : '';
    $telefone = isset($_POST['telefone']) ? trim($_POST['telefone']) : '';
    $senha = isset($_POST['senha']) ? $_POST['senha'] : '';
    $confirmarSenha = isset($_POST['confirmarSenha']) ? $_POST['confirmarSenha'] : '';

    // Validações de campos
    if (strlen($email) < 3 || strlen($email) > 30 || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erro = 'Informe um email válido com até 30 caracteres.';
    } elseif (empty($nome)) {
        $erro = 'Informe o nome completo.';
    } elseif (empty($senha) || empty($confirmarSenha)) {
        $erro = 'Informe a senha e a confirmação de senha.';
    } elseif ($senha !== $confirmarSenha) {
        $erro = 'A senha e a confirmação de senha não coincidem.';
    }
    // Regras de senha
    elseif (
        strlen($senha) < 8 ||
        !preg_match('/[A-Za-z]/', $senha) ||
        !preg_match('/\d/', $senha) ||
        !preg_match('/[!@#$%&*\-_\+=]/', $senha)
    ) {
        $erro = 'A senha deve ter no mínimo 8 caracteres, incluir pelo menos uma letra, um número e um caractere especial (!@#$%&*-_+=).';
    }
    else {
        // Verifica se o e-mail já existe no banco
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);
        $existe = $stmt->fetchColumn();

        if ($existe) {
            $erro = 'Já existe um usuário com esse e-mail.';
        } else {
            $hash = password_hash($senha, PASSWORD_DEFAULT);

            $stmt = $pdo->prepare("INSERT INTO usuarios (email, nome, senha, ativo, telefone) VALUES (?, ?, ?, 1, ?)");
            if ($stmt->execute([$email, $nome, $hash, $telefone])) {
                $sucesso = 'Usuário cadastrado com sucesso.';
            } else {
                $erro = 'Erro ao cadastrar usuário.';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<?php
require_once __DIR__ . '/../includes/head.php';
renderHead('Cadastro de Usuário');
?>

<body class="bg-light d-flex flex-column min-vh-100">

<?php include __DIR__ . '/../includes/navbar.php'; ?>

<div class="container d-flex justify-content-center align-items-center flex-grow-1">
    <div class="card shadow p-4 rounded-4 w-100" style="max-width: 500px;">
        <div class="card-body">
            <h2 class="card-title text-center mb-4 text-primary">Cadastro de Usuário</h2>

            <?php if ($erro): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($erro); ?></div>
            <?php elseif ($sucesso): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($sucesso); ?></div>
            <?php endif; ?>

            <form method="post" action="" novalidate>
                <div class="mb-3">
                    <label for="email" class="form-label">Email (Username)</label>
                    <input type="email" name="email" id="email" class="form-control" maxlength="30" required>
                </div>

                <div class="mb-3">
                    <label for="nome" class="form-label">Nome completo</label>
                    <input type="text" name="nome" id="nome" minlength="3" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="telefone" class="form-label">Número de telefone</label>
                    <input type="text" name="telefone" id="telefone" minlength="3" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="senha" class="form-label">Senha</label>
                    <input type="password" name="senha" id="senha" minlength="8" class="form-control" required>
                    <div class="form-text">
                        A senha deve ter no mínimo 8 caracteres, com letra, número e caractere especial (!@#$%&*-_+=).
                    </div>
                </div>

                <div class="mb-4">
                    <label for="confirmarSenha" class="form-label">Confirme a senha</label>
                    <input type="password" name="confirmarSenha" id="confirmarSenha" minlength="8" class="form-control" required>
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-primary btn-lg">Cadastrar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>

