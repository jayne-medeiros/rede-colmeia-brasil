<?php
// Arquivo: admin/produtos_crud.php
session_start();
include('config.php');

// --- SEGURANÇA ---
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

// --- CONEXÃO ---
try {
    $pdo = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=".DB_CHARSET, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) { die("Erro de conexão: " . $e->getMessage()); }

$mensagem = '';

// ===========================================================
// AÇÃO 1: INSERIR NOVO PRODUTO
// ===========================================================
if (isset($_POST['inserir'])) {
    $imagem_path = null;
    
    // Upload da Imagem
    if (isset($_FILES['foto_produto']) && $_FILES['foto_produto']['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES['foto_produto']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        if (in_array($ext, $allowed)) {
            $novo_nome = md5(time()) . '.' . $ext;
            $diretorio_fisico = '../uploads/';
            if (!is_dir($diretorio_fisico)) { mkdir($diretorio_fisico, 0755, true); }
            if (move_uploaded_file($_FILES['foto_produto']['tmp_name'], $diretorio_fisico . $novo_nome)) {
                $imagem_path = 'uploads/' . $novo_nome;
            }
        }
    }

    // Limpa o telefone para salvar apenas números
    $whatsapp_limpo = preg_replace('/[^0-9]/', '', $_POST['whatsapp_link']);

    $sql = "INSERT INTO produtos (nome_produtor, email_produtor, telefone_produtor, regiao_produtor, nome_produto, descricao_produto, whatsapp_link, status, imagem_path) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    
    try {
        $stmt->execute([
            $_POST['nome_produtor'], $_POST['email_produtor'], $_POST['telefone_produtor'], 
            $_POST['regiao_produtor'], $_POST['nome_produto'], $_POST['descricao_produto'], 
            $whatsapp_limpo, // Salva só o número limpo
            $_POST['status'], $imagem_path
        ]);
        $mensagem = "✅ Produto inserido com sucesso!";
    } catch (Exception $e) {
        $mensagem = "❌ Erro: " . $e->getMessage();
    }
}

// ===========================================================
// AÇÃO 2: ATUALIZAR PRODUTO
// ===========================================================
if (isset($_POST['atualizar'])) {
    // Limpa o telefone
    $whatsapp_limpo = preg_replace('/[^0-9]/', '', $_POST['whatsapp_link']);

    $sql = "UPDATE produtos SET nome_produto=?, descricao_produto=?, whatsapp_link=?, status=? WHERE id=?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$_POST['nome_produto'], $_POST['descricao_produto'], $whatsapp_limpo, $_POST['status'], $_POST['id']]);
    $mensagem = "✅ Produto atualizado!";
}

// ===========================================================
// AÇÃO 3: DELETAR
// ===========================================================
if (isset($_POST['deletar'])) {
    $stmt = $pdo->prepare("SELECT imagem_path FROM produtos WHERE id=?");
    $stmt->execute([$_POST['id']]);
    $p = $stmt->fetch();
    if ($p && $p['imagem_path'] && file_exists('../' . $p['imagem_path'])) { unlink('../' . $p['imagem_path']); }
    $stmt = $pdo->prepare("DELETE FROM produtos WHERE id=?");
    $stmt->execute([$_POST['id']]);
    $mensagem = "🗑️ Produto deletado!";
}

// BUSCAR DADOS
$pendentes = $pdo->query("SELECT * FROM produtos WHERE status='pendente' ORDER BY data_cadastro DESC")->fetchAll();
$aprovados = $pdo->query("SELECT * FROM produtos WHERE status='aprovado' ORDER BY nome_produto ASC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Admin - Produtos | Rede Colmeia</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f4f4f4; }
        .container { max-width: 1100px; margin: 0 auto; background: #fff; padding: 20px; border-radius: 8px; }
        h1, h2 { color: #333; border-bottom: 2px solid #FFC107; padding-bottom: 10px; }
        .btn { padding: 8px 15px; border-radius: 4px; cursor: pointer; border: none; color: white; font-weight: bold; }
        .btn-save { background: #007bff; } .btn-del { background: #dc3545; } .btn-add { background: #28a745; width: 100%; }
        .btn-back { background: #6c757d; text-decoration: none; display: inline-block; margin-right: 10px; }
        input, textarea, select { width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; margin-bottom: 10px; box-sizing: border-box; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 10px; border-bottom: 1px solid #eee; text-align: left; vertical-align: top; }
        th { background: #f8f9fa; }
        .img-thumb { width: 60px; height: 60px; object-fit: cover; border-radius: 4px; }
        .insert-box { background: #fffbea; border: 2px solid #FFC107; padding: 20px; border-radius: 8px; margin-bottom: 30px; }
        .row { display: flex; gap: 20px; } .col { flex: 1; }
    </style>
</head>
<body>
    <div class="container">
        <div style="background: #333; padding: 15px; border-radius: 8px; margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center;">
            <div style="display: flex; gap: 10px;">
                <a href="eventos_crud.php" style="background: #555; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px; font-weight: bold;">Gerenciar Eventos</a>
                <a href="produtos_crud.php" style="background: #FFC107; color: #333; padding: 10px 20px; text-decoration: none; border-radius: 4px; font-weight: bold;">Gerenciar Produtos</a>
            </div>
            <a href="logout.php" style="color: #ff6b6b; text-decoration: none; font-weight: bold;">Sair</a>
        </div>

        <?php if($mensagem) echo "<p style='background:#d4edda; color:#155724; padding:10px;'>$mensagem</p>"; ?>

        <div class="insert-box">
            <h2>+ Cadastrar Novo Produto</h2>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="inserir" value="1">
                <div class="row">
                    <div class="col">
                        <label>Nome Produtor:</label><input type="text" name="nome_produtor" required>
                        <label>E-mail:</label><input type="email" name="email_produtor">
                        <label>Telefone Contato:</label><input type="text" name="telefone_produtor">
                        <label>Região:</label><input type="text" name="regiao_produtor" required>
                    </div>
                    <div class="col">
                        <label>Nome Produto:</label><input type="text" name="nome_produto" required>
                        
                        <label>WhatsApp de Venda (Somente números com DDD):</label>
                        <input type="text" name="whatsapp_link" placeholder="Ex: 5584999998888" required>
                        
                        <label>Descrição:</label><textarea name="descricao_produto" rows="3"></textarea>
                        <label>Foto:</label><input type="file" name="foto_produto" accept="image/*" required>
                        <label>Status:</label>
                        <select name="status"><option value="aprovado">Publicar Agora</option><option value="pendente">Rascunho</option></select>
                    </div>
                </div>
                <button type="submit" class="btn btn-add">Cadastrar</button>
            </form>
        </div>

        <?php if(count($pendentes) > 0): ?>
        <h2 style="color: #d9534f;">Aprovações Pendentes</h2>
        <table>
            <tr><th>Foto</th><th>Dados</th><th>WhatsApp (Número)</th><th>Ação</th></tr>
            <?php foreach ($pendentes as $p): ?>
            <tr>
                <form method="POST">
                    <input type="hidden" name="id" value="<?php echo $p['id']; ?>">
                    <td><?php if($p['imagem_path']) echo "<img src='../{$p['imagem_path']}' class='img-thumb'>"; ?></td>
                    <td>
                        <input type="text" name="nome_produto" value="<?php echo htmlspecialchars($p['nome_produto']); ?>"><br>
                        <small>Produtor: <?php echo htmlspecialchars($p['nome_produtor']); ?></small>
                    </td>
                    <td>
                        <input type="text" name="whatsapp_link" value="<?php echo htmlspecialchars($p['whatsapp_link']); ?>" placeholder="5584...">
                    </td>
                    <td>
                        <select name="status"><option value="pendente">Pendente</option><option value="aprovado">APROVAR</option></select>
                        <button type="submit" name="atualizar" class="btn btn-save">Salvar</button>
                        <button type="submit" name="deletar" class="btn btn-del" onclick="return confirm('Excluir?');">X</button>
                    </td>
                </form>
            </tr>
            <?php endforeach; ?>
        </table>
        <?php endif; ?>

        <h2>Produtos Ativos</h2>
        <table>
            <tr><th>Foto</th><th>Produto</th><th>WhatsApp (Número)</th><th>Ação</th></tr>
            <?php foreach ($aprovados as $p): ?>
            <tr>
                <form method="POST">
                    <input type="hidden" name="id" value="<?php echo $p['id']; ?>">
                    <td><?php if($p['imagem_path']) echo "<img src='../{$p['imagem_path']}' class='img-thumb'>"; ?></td>
                    <td><input type="text" name="nome_produto" value="<?php echo htmlspecialchars($p['nome_produto']); ?>"></td>
                    <td>
                        <input type="text" name="whatsapp_link" value="<?php echo htmlspecialchars($p['whatsapp_link']); ?>">
                    </td>
                    <td>
                        <select name="status"><option value="aprovado">Ativo</option><option value="pendente">Ocultar</option></select>
                        <button type="submit" name="atualizar" class="btn btn-save">Salvar</button>
                        <button type="submit" name="deletar" class="btn btn-del" onclick="return confirm('Excluir?');">X</button>
                    </td>
                </form>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
</body>
</html>