<?php
require_once '../includes/funcoes.php';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Gerar Certificado VPN</title>
    <meta charset="UTF-8">
</head>
<body>
    <h2>Gerar novo certificado VPN</h2>

    <form method="POST">
        <button type="submit" name="executar" value="1">Criar certificado</button>
    </form>

<?php
$jsonPath = "../storage/registros.json";

// Geração de novo certificado
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['executar'])) {
    $id = gerarIdUnico();
    $comando = shell_exec("sudo /usr/bin/python3 /opt/vpn-cert-generator/gerar_certificado.py $id");
    $output = shell_exec($comando);
    echo "<pre>$output</pre>";

    // Caminhos de segurança
    $origem = "/var/www/html/storage/{$id}_cert.zip";
    $destino = "../storage/{$id}_cert.zip";
    if (file_exists($origem) && !file_exists($destino)) {
        rename($origem, $destino);
    }

    // Registro no JSON
    $registro = [
        "id" => $id,
        "data" => date("Y-m-d H:i:s"),
        "validade" => date("Y-m-d", strtotime("+7 days"))
    ];
    $dadosAtuais = file_exists($jsonPath) ? json_decode(file_get_contents($jsonPath), true) : [];
    $dadosAtuais[] = $registro;
    file_put_contents($jsonPath, json_encode($dadosAtuais, JSON_PRETTY_PRINT));

    echo "<p style='color: green;'>✅ Certificado gerado com ID: <strong>$id</strong></p>";
}

// Exclusão de certificados
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['apagar']) && isset($_POST['remover'])) {
    foreach ($_POST['remover'] as $id) {
        $comando = "sudo /usr/bin/python3 /opt/vpn-cert-generator/deletar_certificado.py $id";
        shell_exec($comando);

        // Remover do JSON
        $dadosAtuais = file_exists($jsonPath) ? json_decode(file_get_contents($jsonPath), true) : [];
        $dadosAtuais = array_filter($dadosAtuais, fn($item) => $item['id'] !== $id);
        file_put_contents($jsonPath, json_encode(array_values($dadosAtuais), JSON_PRETTY_PRINT));
    }
    echo "<p style='color:red'>❌ Certificados selecionados foram removidos.</p>";
}
?>

<hr>

<h3>Certificados existentes:</h3>

<?php
if (file_exists($jsonPath)) {
    $lista = json_decode(file_get_contents($jsonPath), true);
    if (count($lista) > 0): ?>
        <form method="POST">
            <table border='1' cellpadding='6'>
                <tr>
                    <th>Remover</th>
                    <th>Download</th>
                    <th>Nome</th>
                    <th>Data de criação</th>
                    <th>Validade até</th>
                </tr>
                <?php foreach ($lista as $cert): ?>
                    <tr>
                        <td><input type="checkbox" name="remover[]" value="<?= $cert['id'] ?>"></td>
                        <td><a href="../baixar.php?id=<?= $cert['id'] ?>">Baixar</a></td>
                        <td><?= $cert['id'] ?></td>
                        <td><?= $cert['data'] ?></td>
                        <td><?= $cert['validade'] ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
            <br>
            <button type="submit" name="apagar" value="1"
                onclick="return confirm('Você realmente deseja excluir os certificados selecionados?')"
                style="background-color: red; color: white;">Remover selecionados</button>
        </form>
    <?php else:
        echo "<p>Nenhum certificado gerado ainda.</p>";
    endif;
} else {
    echo "<p>Nenhum certificado gerado ainda.</p>";
}
?>

</body>
</html>
