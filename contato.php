<?php
// Processamento do Formulário de Contato
$mensagem_status = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome_contato'] ?? '';
    $email = $_POST['email_contato'] ?? '';
    $assunto = $_POST['assunto_contato'] ?? '';
    $mensagem = $_POST['mensagem_contato'] ?? '';
    
    // E-mail de destino (Dono da Rede Colmeia)
    $to = 'EMAIL_DESTINO_HERE';
    
    $subject = "Contato do Site: $assunto";
    
    $body = "Nova mensagem de contato do site Rede Colmeia:\n\n";
    $body .= "Nome: $nome\n";
    $body .= "E-mail: $email\n\n";
    $body .= "Mensagem:\n$mensagem\n";
    
    $headers = "From: no-reply@redecolmeia.com.br" . "\r\n" .
               "Reply-To: $email" . "\r\n" .
               "X-Mailer: PHP/" . phpversion();

    // Tenta enviar
    if(@mail($to, $subject, $body, $headers)) {
        $mensagem_status = "<p style='color: green; text-align: center; padding: 10px; background: #d4edda; border-radius: 5px;'>Mensagem enviada com sucesso! Obrigado pelo contato.</p>";
    } else {
        $mensagem_status = "<p style='color: red; text-align: center; padding: 10px; background: #f8d7da; border-radius: 5px;'>Erro ao enviar mensagem. Por favor, tente novamente ou use o WhatsApp.</p>";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contato - Rede Colmeia Brasil</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-XXXXXXXXX-X"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());
      gtag('config', 'UA-XXXXXXXXX-X');
    </script>
</head>
<body>

    <header>
        <nav>
            <a href="index.html" class="logo">
                <img src="img/logo-rede-colmeia.png" alt="Logo Rede Colmeia Brasil">
            </a>
            <ul>
                <li><a href="quem-somos.html">Quem Somos</a></li>
                <li><a href="produtos.php">Produtos Gourmet</a></li> <li><a href="produtores.php">Seja um Produtor</a></li> <li><a href="eventos.php">Eventos</a></li> <li><a href="formacao-tecnica.html">Formação Técnica</a></li>
                <li><a href="contato.php">Contato</a></li> </ul>
            <button class="menu-toggle">
                <span class="bar"></span>
                <span class="bar"></span>
                <span class="bar"></span>
            </button>
        </nav>
    </header>

    <main>
        <section class="page-title">
            <h1>Fale Conosco</h1>
        </section>

        <section class="contato-container">
            <div class="contato-formulario">
                <h2>Envie sua mensagem</h2>
                <p>Tem dúvidas, sugestões ou interesse em parcerias? Preencha o formulário abaixo.</p>
                
                <?php echo $mensagem_status; ?>

                <form method="POST"> 
                    <div class="form-group">
                        <label for="nome">Seu Nome:</label>
                        <input type="text" id="nome" name="nome_contato" required>
                    </div>

                    <div class="form-group">
                        <label for="email">Seu E-mail:</label>
                        <input type="email" id="email" name="email_contato" required>
                    </div>

                    <div class="form-group">
                        <label for="assunto">Assunto:</label>
                        <input type="text" id="assunto" name="assunto_contato" value="<?php echo isset($_GET['assunto']) ? htmlspecialchars($_GET['assunto']) : ''; ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="mensagem">Sua Mensagem:</label>
                        <textarea id="mensagem" name="mensagem_contato" rows="6"></textarea>
                    </div>

                    <button type="submit" class="btn-enviar">Enviar Mensagem</button>
                </form>
            </div>

            <div class="contato-info">
                <h2>Nossos Canais</h2>
                <p>Você também pode nos encontrar em nossas redes sociais ou pelo nosso telefone.</p>
                
                <div class="info-item">
                    <i class="fas fa-envelope"></i>
                    <span>EMAIL_SISTEMA_HERE</span>
                </div>
                
                <div class="info-item">
                    <i class="fas fa-phone"></i>
                    <span>(XX) XXXXX-XXXX</span> 
                </div>

                <h3>Siga-nos:</h3>
                <div class="social-links">
                    <a href="https://www.instagram.com/redecolmeiabr/" target="_blank" title="Instagram"><i class="fab fa-instagram"></i></a>
                </div>
            </div>
        </section>
    </main>

    <footer>
        <div class="footer-container">
            <div class="footer-col footer-logo">
                <a href="index.html">
                    <img src="img/logo-rede-colmeia.png" alt="Logo Rede Colmeia Brasil">
                </a>
                <p>Transformando a apicultura no Brasil. Conectando produtores e apaixonados por abelhas.</p>
            </div>
            <div class="footer-col footer-social">
                <h4>Siga-nos</h4>
                <div class="social-links-footer">
                <a href="https://www.instagram.com/redecolmeiabr/" target="_blank" title="Instagram"><i class="fab fa-instagram"></i></a>
                </div>
            </div>
        </div>
        <div class="footer-copyright">
            <p>Rede Colmeia Brasil &copy; 2025</p>
            <p class="lumio-credit">Desenvolvido com &hearts; por Lumiô Lab</p>
        </div>
    </footer>

    <script src="js/main.js"></script>
</body>
</html>