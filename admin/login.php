<?php
// Arquivo: admin/login.php - Tela de Login do Painel Admin
session_start();
include('config.php');

// Se o usuário já estiver logado (sessão ativa), redireciona para o painel
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: eventos_crud.php');
    exit;
}

$erro = '';

// Processar formulário de login (quando o usuário clica em Entrar)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // 
    // ATENÇÃO: VERIFICA SE O QUE FOI DIGITADO É IGUAL AO QUE ESTÁ NO config.php
    // 
    if ($username === ADMIN_USERNAME && $password === ADMIN_PASSWORD) {
        $_SESSION['admin_logged_in'] = true;
        // Redireciona para o painel de administração
        header('Location: eventos_crud.php');
        exit;
    } else {
        $erro = 'Usuário ou Senha incorretos.';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Login - Painel Admin Rede Colmeia</title>
    <style>
        /* Estilos básicos para a tela de login */
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .login-box { background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); width: 300px; }
        h1 { text-align: center; color: #FFC107; margin-bottom: 20px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input[type="text"], input[type="password"] { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        button { background-color: #FFC107; color: #333; padding: 10px 15px; border: none; border-radius: 4px; cursor: pointer; width: 100%; margin-top: 10px; font-weight: bold; }
        .error { color: red; text-align: center; margin-bottom: 15px; }
    </style>
</head>
<body>
    <div class="login-box">
        <h1>Admin - Rede Colmeia</h1>
        <?php if ($erro): ?>
            <p class="error"><?php echo $erro; ?></p>
        <?php endif; ?>
        <form method="POST">
            <div class="form-group">
                <label for="username">Usuário:</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Senha:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit">Entrar</button>
        </form>
        <p style="text-align: center; margin-top: 20px;">
            <a href="../eventos.php" style="color: #FFC107;">&larr; Voltar para a página de Eventos</a>
        </p>
    </div>
</body>
</html>