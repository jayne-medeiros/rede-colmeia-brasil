<?php
session_start(); // Inicia a sessão para lembrar os dados

// --- CONFIGURAÇÕES DO BANCO DE DADOS E E-MAIL ---
// Recomenda-se usar o admin/config.php
require_once 'admin/config.php';

$host = DB_HOST;
$db   = DB_NAME;
$user = DB_USER;
$pass = DB_PASS;
$email_dono = 'EMAIL_DESTINO_HERE'; // E-mail que receberá o aviso

$mensagem_status = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Upload da Imagem
        $imagem_path = null;
        if (isset($_FILES['foto_produto']) && $_FILES['foto_produto']['error'] === UPLOAD_ERR_OK) {
            $ext = strtolower(pathinfo($_FILES['foto_produto']['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            if (in_array($ext, $allowed)) {
                $novo_nome = md5(time() . uniqid()) . '.' . $ext;
                if (!is_dir('uploads')) { mkdir('uploads', 0755, true); }
                $destino = 'uploads/' . $novo_nome;
                if (move_uploaded_file($_FILES['foto_produto']['tmp_name'], $destino)) {
                    $imagem_path = $destino;
                }
            }
        }

        // Inserir no Banco
        $sql = "INSERT INTO produtos (nome_produtor, email_produtor, telefone_produtor, regiao_produtor, nome_produto, descricao_produto, imagem_path, status) VALUES (?, ?, ?, ?, ?, ?, ?, 'pendente')";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $_POST['nome'], $_POST['email'], $_POST['telefone'], $_POST['regiao'], 
            $_POST['nome_produto'], $_POST['descricao_produto'], $imagem_path
        ]);

        // Enviar E-mail
        $assunto = "Novo Produto: " . $_POST['nome_produto'];
        $corpo = "Novo cadastro!\nProdutor: {$_POST['nome']}\nProduto: {$_POST['nome_produto']}";
        $headers = "From: no-reply@redecolmeia.com.br";
        @mail($email_dono, $assunto, $corpo, $headers);

        // --- A MÁGICA DA OTIMIZAÇÃO ACONTECE AQUI ---
        // Salva os dados pessoais na memória (Sessão)
        $_SESSION['produtor_nome'] = $_POST['nome'];
        $_SESSION['produtor_email'] = $_POST['email'];
        $_SESSION['produtor_telefone'] = $_POST['telefone'];
        $_SESSION['produtor_regiao'] = $_POST['regiao'];

        $mensagem_status = "<div style='background-color: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin-bottom: 20px; text-align: center; border: 1px solid #c3e6cb;'>
            ✅ <strong>Produto cadastrado!</strong><br>
            Seus dados foram mantidos abaixo para você cadastrar o próximo produto mais rápido.
        </div>";

    } catch (Exception $e) {
        $mensagem_status = "<div style='background-color: #f8d7da; color: #721c24; padding: 15px;'>Erro: " . $e->getMessage() . "</div>";
    }
}

// Recupera dados da sessão se existirem (Preenchimento automático)
$nome_val = $_SESSION['produtor_nome'] ?? '';
$email_val = $_SESSION['produtor_email'] ?? '';
$tel_val = $_SESSION['produtor_telefone'] ?? '';
$regiao_val = $_SESSION['produtor_regiao'] ?? '';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seja um Produtor - Rede Colmeia Brasil</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>

    <header>
        <nav>
            <a href="index.html" class="logo"><img src="img/logo-rede-colmeia.png" alt="Logo"></a>
            <ul>
                <li><a href="quem-somos.html">Quem Somos</a></li>
                <li><a href="produtos.php">Produtos Gourmet</a></li>
                <li><a href="produtores.php">Seja um Produtor</a></li>
                <li><a href="eventos.php">Eventos</a></li>
                <li><a href="formacao-tecnica.html">Formação Técnica</a></li>
                <li><a href="contato.php">Contato</a></li>
            </ul>
            <button class="menu-toggle"><span class="bar"></span><span class="bar"></span><span class="bar"></span></button>
        </nav>
    </header>

    <main>
        <section class="page-title"><h1>Cadastrar Produtos</h1></section>

        <section class="produtor-container">
            <div class="produtor-beneficios">
                <h2>Junte-se à Rede</h2>
                <p>Cadastre seus produtos para nossa vitrine gourmet.</p>
                
                <?php if($nome_val): ?>
                    <div style="background: #fff3cd; padding: 15px; border-radius: 8px; border: 1px solid #ffeeba;">
                        <p style="margin: 0 0 10px 0; font-size: 14px;">Olá, <strong><?php echo $nome_val; ?></strong>! Seus dados estão preenchidos para facilitar.</p>
                        <a href="limpar_sessao.php" style="font-size: 13px; color: #856404; text-decoration: underline;">Não é você? Limpar dados</a>
                    </div>
                <?php endif; ?>
            </div>

            <div class="produtor-formulario">
                <h2>Cadastro de Produto</h2>
                <?php echo $mensagem_status; ?>
                
                <form method="POST" enctype="multipart/form-data">
                    <h3 style="margin-top: 0; color: #333; border-bottom: 1px solid #eee; padding-bottom: 10px;">1. Seus Dados</h3>
                    
                    <div class="form-group">
                        <label>Nome Completo:</label>
                        <input type="text" name="nome" value="<?php echo htmlspecialchars($nome_val); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Seu E-mail:</label>
                        <input type="email" name="email" value="<?php echo htmlspecialchars($email_val); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>WhatsApp:</label>
                        <input type="text" name="telefone" value="<?php echo htmlspecialchars($tel_val); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Região / Cidade:</label>
                        <input type="text" name="regiao" value="<?php echo htmlspecialchars($regiao_val); ?>" required>
                    </div>
                    
                    <h3 style="color: #333; margin-top: 30px; border-bottom: 1px solid #eee; padding-bottom: 10px;">2. Dados do Produto</h3>
                    
                    <div class="form-group">
                        <label>Nome do Produto:</label>
                        <input type="text" name="nome_produto" placeholder="Ex: Mel Silvestre" required>
                    </div>
                    <div class="form-group">
                        <label>Descrição:</label>
                        <textarea name="descricao_produto" rows="4" required></textarea>
                    </div>
                    <div class="form-group">
                        <label>Foto do Produto:</label>
                        <input type="file" name="foto_produto" accept="image/*" required>
                    </div>

                    <button type="submit" class="btn-enviar">
                        <?php echo ($nome_val) ? 'Cadastrar Mais Um Produto' : 'Enviar Cadastro'; ?>
                    </button>
                </form>
            </div>
        </section>
    </main>
    
    <footer>
        <div class="footer-copyright"><p>Rede Colmeia Brasil &copy; 2025</p></div>
    </footer>
    <script src="js/main.js"></script>
</body>
</html>