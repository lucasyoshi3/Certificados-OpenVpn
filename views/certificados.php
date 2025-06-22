<?php
require_once __DIR__ . '/../includes/auth.php';
require_once '../includes/funcoes.php';
require_once __DIR__ . '/../includes/conexao.php';

// Tratamento do POST para criar ou remover certificados
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['executar'])) {
        // Criar certificado
        $id = gerarIdUnico();
        $comando = "sudo /usr/bin/python3 /opt/vpn-cert-generator/gerar_certificado.py $id";
        $output = shell_exec($comando);

        // Verifica se arquivo foi criado no storage
        $origem = "/var/www/html/storage/{$id}_cert.zip";
        $destino = "../storage/{$id}_cert.zip";
        if (file_exists($origem) && !file_exists($destino)) {
            rename($origem, $destino);
        }

        // Registrar no MySQL
        $dataAtual = date("Y-m-d H:i:s");
        $validade = date("Y-m-d", strtotime("+7 days"));
        $sql = "INSERT INTO certificados (id, data, validade) VALUES (:id, :data, :validade)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':id' => $id,
            ':data' => $dataAtual,
            ':validade' => $validade
        ]);

        $msgSucesso = "✅ Certificado gerado com ID: <strong>$id</strong>";
    }

    if (isset($_POST['apagar']) && isset($_POST['remover'])) {
        foreach ($_POST['remover'] as $id) {
            $comando = "sudo /usr/bin/python3 /opt/vpn-cert-generator/deletar_certificado.py $id";
            shell_exec($comando);

            $sql = "DELETE FROM certificados WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':id' => $id]);
        }
        $msgRemovido = "❌ Certificados selecionados foram removidos.";
    }
}

// Preenche a tabela
$sql = "SELECT * FROM certificados ORDER BY data DESC";
$stmt = $pdo->query($sql);
$lista = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="pt-br">

<?php
require_once __DIR__ . '/../includes/head.php';
renderHead('Gerar Certificado VPN');
?>

<body class="bg-light d-flex flex-column min-vh-100">

<!-- NAVBAR -->
<?php include __DIR__ . '/../includes/navbar.php'; ?>

<div class="container py-5">
    <div class="card shadow-lg rounded-4 p-4 mb-5">
        <h2 class="text-center text-primary mb-4">Gerar novo certificado VPN</h2>

        <?php if (!empty($msgSucesso)): ?>
            <div class="alert alert-success"><?= $msgSucesso ?></div>
        <?php endif; ?>

        <?php if (!empty($msgRemovido)): ?>
            <div class="alert alert-danger"><?= $msgRemovido ?></div>
        <?php endif; ?>

        <form method="POST" class="text-center">
            <button type="submit" name="executar" value="1" class="btn btn-lg btn-primary px-5">
                <i class="fas fa-plus-circle me-2"></i>Criar certificado
            </button>
        </form>
    </div>

    <div class="card shadow-sm rounded-4 p-4">
        <h3 class="mb-4">📄 Certificados existentes</h3>

        <?php if (count($lista) > 0): ?>
            <form method="POST" onsubmit="return confirm('Você realmente deseja excluir os certificados selecionados?')">
                <div class="table-responsive">
                    <table class="table table-hover align-middle table-bordered text-center">
                        <thead class="table-dark">
                            <tr>
                                <th>Remover</th>
                                <th>Download</th>
                                <th>ID</th>
                                <th>Criado em</th>
                                <th>Válido até</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($lista as $cert): ?>
                                <tr>
                                    <td>
                                        <input type="checkbox" name="remover[]" value="<?= htmlspecialchars($cert['id']) ?>">
                                    </td>
                                    <td>
                                        <a href="baixar.php?id=<?= urlencode($cert['id']) ?>" class="btn btn-sm btn-success">
                                            <i class="fas fa-download"></i> Baixar
                                        </a>
                                    </td>
                                    <td><code><?= htmlspecialchars($cert['id']) ?></code></td>
                                    <td><?= (new DateTime($cert['data']))->format('d/m/Y - H:i') ?></td>
                                    <td><?= (new DateTime($cert['validade']))->format('d/m/Y') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-end mt-3">
                    <button type="submit" name="apagar" value="1" class="btn btn-danger">
                        <i class="fas fa-trash-alt me-2"></i>Remover selecionados
                    </button>
                </div>
            </form>
        <?php else: ?>
            <p class="text-muted">Nenhum certificado gerado ainda.</p>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
