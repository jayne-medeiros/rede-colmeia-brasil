<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nossos Eventos e Destaques - Rede Colmeia Brasil</title>
    
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
            <h1>Nossos Eventos e Destaques</h1>
        </section>

        <section class="event-list-section">
            <?php
            // ** 1. CONFIGURAÇÕES DO BANCO DE DADOS (SUBSTITUA AQUI!) **
            $host = 'localhost'; 
            $db   = 'u599217201_rede_colmeia'; 
            $user = 'u599217201_rede_colmeia'; 
            $pass = 'SENHA_DO_BANCO'; 
            $charset = 'utf8mb4';

            $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];

            try {
                // 2. TENTAR CONECTAR
                $pdo = new PDO($dsn, $user, $pass, $options);
            } catch (\PDOException $e) {
                // Em caso de falha de conexão, exibe uma mensagem de erro
                echo "<p style='color: red; text-align: center; font-weight: bold;'>[ERRO DE CONEXÃO]: Verifique as credenciais do seu banco de dados na Hostinger.</p>";
                echo "<p style='text-align: center;'>Status: A página de Eventos está desativada.</p>";
                exit; // Interrompe a execução
            }

            // 3. Puxar Eventos do Banco de Dados
            $stmt = $pdo->query('SELECT * FROM eventos ORDER BY data_criacao DESC');
            $eventos = $stmt->fetchAll();

            if (empty($eventos)) {
                echo "<p style='text-align: center; font-size: 1.2em;'>Nenhum evento ou destaque encontrado. Acesse o Painel Admin para criar o primeiro!</p>";
            } else {
                // 4. Loop para exibir os eventos
                foreach ($eventos as $evento) {
                    // Determina o texto e classe do botão baseado no tipo
                    $link_texto = ($evento['tipo'] === 'youtube') ? 'Ver Vídeo no YouTube' : 'Ver Post no Instagram';
                    $btn_class  = ($evento['tipo'] === 'youtube') ? 'btn-certificado' : 'btn-hotmart';
                    $icone      = ($evento['tipo'] === 'youtube') ? '<i class="fab fa-youtube"></i>' : '<i class="fab fa-instagram"></i>';
                    ?>
                    <div class="event-list-item">
                        <h3><?php echo $icone . ' ' . htmlspecialchars($evento['titulo']); ?></h3>
                        <p><?php echo nl2br(htmlspecialchars($evento['descricao'])); ?></p>
                        <a href="<?php echo htmlspecialchars($evento['link_url']); ?>" target="_blank" class="btn btn-action-course <?php echo $btn_class; ?> btn-small">
                            <?php echo $link_texto; ?>
                        </a>
                    </div>
                    <?php
                }
            }
            ?>
        </section>
        </main>

    <footer>
        <div class="footer-container">
            <div class="footer-col footer-logo">
                <a href="index.html">
                    <img src="img/logo-rede-colmeia.png" alt="Logo Rede Colmeia Brasil">
                </a>
                <p>Transformando a apicultura e a meliponicultura no Brasil. Conectando produtores e apaixonados por abelhas.</p>
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