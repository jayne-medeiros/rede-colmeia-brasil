<?php
// Arquivo: admin/eventos_crud.php - CRUD de Eventos
session_start();
include('config.php');

// --- CÓDIGO DE SEGURANÇA ---
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}
// ----------------------------

// 1. Conectar ao Banco de Dados (PDO)
try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (\PDOException $e) {
    die("Erro de Conexão: " . $e->getMessage());
}

// 2. Processar Ações (Inserir/Deletar)
$mensagem = '';
$uploadDir = '../uploads/'; // Pasta onde as imagens serão salvas (um nível acima de admin/)

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Ação de INSERIR NOVO EVENTO
    if (isset($_POST['inserir'])) {
        $titulo = filter_input(INPUT_POST, 'titulo', FILTER_SANITIZE_STRING);
        $descricao = filter_input(INPUT_POST, 'descricao', FILTER_SANITIZE_STRING);
        $link_url = filter_input(INPUT_POST, 'link_url', FILTER_SANITIZE_URL);
        $tipo = filter_input(INPUT_POST, 'tipo', FILTER_SANITIZE_STRING);
        $imagem_path = NULL; // Inicializa como NULL

        // --- LÓGICA DE UPLOAD DE IMAGEM ---
        if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['imagem']['tmp_name'];
            $fileName = $_FILES['imagem']['name'];
            $fileSize = $_FILES['imagem']['size'];
            $fileType = $_FILES['imagem']['type'];
            $fileNameCmps = explode(".", $fileName);
            $fileExtension = strtolower(end($fileNameCmps));
            $newFileName = md5(time() . $fileName) . '.' . $fileExtension;

            // Verifica se a extensão é permitida
            $allowedfileExtensions = array('jpg', 'gif', 'png', 'jpeg');
            if (in_array($fileExtension, $allowedfileExtensions)) {
                
                // Cria a pasta de uploads se ela não existir
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                
                $destPath = $uploadDir . $newFileName;
                
                if(move_uploaded_file($fileTmpPath, $destPath)) {
                    // Salva o caminho relativo no banco de dados
                    $imagem_path = 'uploads/' . $newFileName;
                } else {
                    $mensagem = "Erro ao mover o arquivo de upload.";
                }
            } else {
                $mensagem = "Erro: Extensão de arquivo não permitida.";
            }
        }
        // --- FIM DA LÓGICA DE UPLOAD ---

        if ($titulo && $link_url && $tipo) {
            $sql = "INSERT INTO eventos (titulo, descricao, link_url, tipo, imagem_path) VALUES (?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$titulo, $descricao, $link_url, $tipo, $imagem_path]);
            $mensagem = "Evento '{$titulo}' inserido com sucesso!";
        } else {
            $mensagem = "Erro: Preencha todos os campos obrigatórios.";
        }
    }

    // Ação de DELETAR EVENTO
    if (isset($_POST['deletar'])) {
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        if ($id) {
            // 1. Busca o caminho da imagem para deletar o arquivo do servidor
            $stmt = $pdo->prepare("SELECT imagem_path FROM eventos WHERE id = ?");
            $stmt->execute([$id]);
            $evento = $stmt->fetch();

            // 2. Se houver imagem, tenta deletar do servidor
            if ($evento && $evento['imagem_path'] && file_exists('../' . $evento['imagem_path'])) {
                unlink('../' . $evento['imagem_path']);
            }

            // 3. Deleta o registro do banco de dados
            $sql = "DELETE FROM eventos WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$id]);
            $mensagem = "Evento ID {$id} deletado com sucesso!";
        }
    }
}

// 3. Buscar Todos os Eventos para Exibição
$stmt = $pdo->query('SELECT * FROM eventos ORDER BY data_criacao DESC');
$eventos = $stmt->fetchAll();

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Admin - Gerenciar Eventos</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 20px; background-color: #f4f4f4; }
        .container { max-width: 900px; margin: 0 auto; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        h1 { color: #FFC107; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input[type="text"], input[type="file"], textarea, select { width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        button { background-color: #FFC107; color: #333; padding: 10px 15px; border: none; border-radius: 4px; cursor: pointer; margin-top: 10px; }
        .btn-logout { background-color: #f44336; color: white; margin-left: 10px; }
        .success { color: green; font-weight: bold; margin-top: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 10px; border: 1px solid #ddd; text-align: left; font-size: 14px; }
        th { background-color: #f0f0f0; }
        .img-thumb { max-width: 50px; height: auto; border-radius: 4px; }
    </style>
</head>
<body>
    <div class="container">
        <div style="background: #333; padding: 15px; border-radius: 8px; margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center;">
            <div style="display: flex; gap: 10px;">
                <a href="eventos_crud.php" style="background: #FFC107; color: #333; padding: 10px 20px; text-decoration: none; border-radius: 4px; font-weight: bold;">Gerenciar Eventos</a>
                
                <a href="produtos_crud.php" style="background: #555; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px; font-weight: bold;">Gerenciar Produtos</a>
            </div>
            <a href="logout.php" style="color: #ff6b6b; text-decoration: none; font-weight: bold;">Sair</a>
        </div>

        <h1>Painel Admin - Eventos</h1>
        
        <?php if ($mensagem): ?>
            <p class="success"><?php echo $mensagem; ?></p>
        <?php endif; ?>

        <h2>1. Inserir Novo Evento</h2>
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="inserir" value="1">
            <div class="form-group">
                <label>Título:</label>
                <input type="text" name="titulo" required>
            </div>
            <div class="form-group">
                <label>Descrição:</label>
                <textarea name="descricao" rows="3" required></textarea>
            </div>
            <div class="form-group">
                <label>Link Principal (YouTube/Instagram URL):</label>
                <input type="text" name="link_url" required>
            </div>
            <div class="form-group">
                <label>Foto/Mídia (Opcional):</label>
                <input type="file" name="imagem" accept="image/*">
            </div>
            <div class="form-group">
                <label>Tipo:</label>
                <select name="tipo" required>
                    <option value="youtube">YouTube (Vídeo)</option>
                    <option value="instagram">Instagram (Post/Reel)</option>
                    <option value="outros">Outros</option>
                </select>
            </div>
            <button type="submit">Adicionar Evento</button>
        </form>

        <h2>2. Eventos Cadastrados (<?php echo count($eventos); ?>)</h2>
        <?php if (!empty($eventos)): ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Capa</th>
                    <th>Título</th>
                    <th>Tipo</th>
                    <th>Link</th>
                    <th>Ação</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($eventos as $evento): ?>
                <tr>
                    <td><?php echo $evento['id']; ?></td>
                    <td>
                        <?php if ($evento['imagem_path']): ?>
                            <img src="../<?php echo htmlspecialchars($evento['imagem_path']); ?>" alt="Capa" class="img-thumb">
                        <?php else: ?>
                            N/A
                        <?php endif; ?>
                    </td>
                    <td><?php echo htmlspecialchars($evento['titulo']); ?></td>
                    <td><?php echo $evento['tipo']; ?></td>
                    <td><a href="<?php echo htmlspecialchars($evento['link_url']); ?>" target="_blank">Abrir</a></td>
                    <td>
                        <form method="POST" style="display: inline-block;">
                            <input type="hidden" name="deletar" value="1">
                            <input type="hidden" name="id" value="<?php echo $evento['id']; ?>">
                            <button type="submit" onclick="return confirm('Tem certeza que deseja deletar este evento e a imagem associada?');" style="background-color: #f44336; color: white;">Deletar</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
            <p>Nenhum evento cadastrado no momento.</p>
        <?php endif; ?>
    </div>
</body>
</html>