<?php
// --- CONFIGURAÇÕES DE CONEXÃO ---
$host = 'localhost';
$db   = 'u599217201_rede_colmeia';      // <--- Substitua aqui
$user = 'u599217201_rede_colmeia';   // <--- Substitua aqui
$pass = 'SENHA_DO_BANCO';     // <--- Substitua aqui

$produtos = [];

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $stmt = $pdo->query("SELECT * FROM produtos WHERE status='aprovado' ORDER BY id DESC");
    $produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $produtos = [];
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produtos Gourmet - Rede Colmeia Brasil</title>
    
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
                <li><a href="produtos.php">Produtos Gourmet</a></li>
                <li><a href="produtores.php">Seja um Produtor</a></li>
                <li><a href="eventos.php">Eventos</a></li> 
                <li><a href="formacao-tecnica.html">Formação Técnica</a></li>
                <li><a href="contato.php">Contato</a></li>
            </ul>
            <button class="menu-toggle">
                <span class="bar"></span>
                <span class="bar"></span>
                <span class="bar"></span>
            </button>
        </nav>
    </header>

    <main>
        <section class="page-title">
            <h1>Nossos Produtos Gourmet</h1>
        </section>

        <section class="single-product-container" style="display: flex; flex-wrap: wrap; justify-content: center; gap: 30px; padding: 40px 20px;">
            
            <?php if (empty($produtos)): ?>
                <div style="text-align: center; padding: 50px; width: 100%;">
                    <p style="font-size: 1.2em; color: #666;">Nossa vitrine está sendo atualizada com novos produtos deliciosos.</p>
                    <p>É produtor? <a href="produtores.php" style="color: #FFC107; font-weight: bold;">Cadastre seu produto aqui.</a></p>
                </div>
            <?php else: ?>
                
                <?php foreach ($produtos as $prod): 
                    $zap_number = preg_replace('/[^0-9]/', '', $prod['whatsapp_link']);
                    $zap_msg = urlencode("Olá! Vi o produto *" . $prod['nome_produto'] . "* no site da Rede Colmeia e gostaria de saber mais.");
                    $zap_final_url = "https://wa.me/{$zap_number}?text={$zap_msg}";
                ?>
                
                <div class="product-card" style="width: 100%; max-width: 350px; background: #fff; border: 1px solid #eee; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 10px rgba(0,0,0,0.05); display: flex; flex-direction: column;">
                    
                    <div style="height: 220px; overflow: hidden; background-color: #f9f9f9; position: relative;">
                        <?php if (!empty($prod['imagem_path'])): ?>
                            <img src="<?php echo htmlspecialchars($prod['imagem_path']); ?>" 
                                 alt="<?php echo htmlspecialchars($prod['nome_produto']); ?>" 
                                 class="zoomable-image"
                                 onclick="openModal(this.src)"
                                 style="width: 100%; height: 100%; object-fit: cover; cursor: zoom-in; transition: transform 0.3s;">
                        <?php else: ?>
                            <div style="display: flex; align-items: center; justify-content: center; height: 100%; color: #ccc;">
                                <i class="fas fa-image fa-3x"></i>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="product-details" style="padding: 20px; flex: 1; display: flex; flex-direction: column;">
                        <h2 style="font-size: 20px; margin: 0 0 10px 0; color: #333; line-height: 1.3;"><?php echo htmlspecialchars($prod['nome_produto']); ?></h2>
                        
                        <p style="font-size: 13px; color: #888; margin-bottom: 10px;">
                            <i class="fas fa-user"></i> <?php echo htmlspecialchars($prod['nome_produtor']); ?><br>
                            <i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($prod['regiao_produtor']); ?>
                        </p>
                        
                        <p style="color: #555; font-size: 14px; line-height: 1.5; margin-bottom: 20px; flex: 1;">
                            <?php echo nl2br(htmlspecialchars($prod['descricao_produto'])); ?>
                        </p>
                        
                        <?php if ($zap_number): ?>
                            <a href="<?php echo $zap_final_url; ?>" target="_blank" class="btn btn-action-course btn-certificado" 
                               style="display: block; width: 100%; text-align: center; box-sizing: border-box; margin-top: auto; font-size: 16px; padding: 12px 10px;">
                                <i class="fab fa-whatsapp"></i> Comprar no WhatsApp
                            </a>
                        <?php else: ?>
                            <button disabled style="background: #eee; color: #999; width: 100%; padding: 10px; border: none; border-radius: 5px; cursor: not-allowed;">Indisponível</button>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>

            <?php endif; ?>

        </section>
    </main>

    <div id="imageModal" class="modal" onclick="closeModal()">
        <span class="close-modal">&times;</span>
        <img class="modal-content" id="img01">
    </div>

    <footer>
        <div class="footer-container">
            <div class="footer-col footer-logo">
                <a href="index.html">
                    <img src="img/logo-rede-colmeia.png" alt="Logo Rede Colmeia Brasil">
                </a>
                <p>Transformando a apicultura e a meliponicultura no Brasil.</p>
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