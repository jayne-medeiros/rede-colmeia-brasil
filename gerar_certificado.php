<?php
// ATIVA DEBUG
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Verifica bibliotecas
if (!file_exists('fpdf/fpdf.php')) {
    die("ERRO CRÍTICO: A pasta 'fpdf' não foi encontrada.");
}
require('fpdf/fpdf.php');

$imagem_fundo = 'img/certificado_bg.jpg';
if (!file_exists($imagem_fundo)) {
    die("ERRO: A imagem de fundo '$imagem_fundo' não foi encontrada.");
}

// 1. Formulário
if (!isset($_POST['nome_aluno'])) {
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerar Certificado - Rede Colmeia</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        body { display: flex; justify-content: center; align-items: center; height: 100vh; background-color: #f4f4f4; font-family: Arial, sans-serif; }
        .cert-box { background: white; padding: 40px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); text-align: center; max-width: 400px; width: 90%; }
        input { width: 100%; padding: 12px; margin: 15px 0; border: 1px solid #ccc; border-radius: 5px; box-sizing: border-box; font-size: 16px; }
        h2 { color: #FFC107; margin-top: 0; }
        .btn-cert { background-color: #FFC107; color: #333; padding: 15px; border: none; border-radius: 5px; font-weight: bold; cursor: pointer; width: 100%; font-size: 16px; }
        .btn-cert:hover { background-color: #e6a200; }
    </style>
</head>
<body>
    <div class="cert-box">
        <?php if(file_exists('img/logo-rede-colmeia.png')): ?>
            <img src="img/logo-rede-colmeia.png" alt="Logo" style="height: 60px; margin-bottom: 20px;">
        <?php endif; ?>
        <h2>Certificado de Conclusão</h2>
        <p>Parabéns! Digite seu nome completo para gerar o PDF.</p>
        <form method="POST">
            <input type="text" name="nome_aluno" placeholder="Seu Nome Completo" required>
            <button type="submit" class="btn-cert">Baixar Certificado em PDF</button>
        </form>
        <br>
        <a href="formacao-tecnica.html" style="color: #777; text-decoration: none; font-size: 14px;">&larr; Voltar para o curso</a>
    </div>
</body>
</html>
<?php
    exit;
}

// 2. GERAÇÃO DO PDF
$nome = strtoupper($_POST['nome_aluno']); 
// Tenta corrigir acentos
$nome = iconv('UTF-8', 'windows-1252', $nome); 

setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
date_default_timezone_set('America/Sao_Paulo');
$data_hoje = date('d/m/Y'); 

$pdf = new FPDF('L','mm','A4');
$pdf->AddPage();

// Fundo
$pdf->Image($imagem_fundo, 0, 0, 297, 210);

// --- CONFIGURAÇÃO DO NOME ---
$pdf->SetFont('Arial', 'B', 17);
$pdf->SetTextColor(50, 50, 50);

// AJUSTE 1: Posição do NOME (Vertical)
$pdf->SetY(126); 

// AJUSTE 2: Posição do NOME (Horizontal)
// Mude o 30 para ajustar a margem esquerda
$pdf->SetX(76); 

$pdf->Cell(0, 10, $nome, 0, 1, 'L'); 


// --- CONFIGURAÇÃO DA DATA ---
$pdf->SetFont('Arial', '', 14);

// AJUSTE 3: Posição da DATA (Vertical)
$pdf->SetY(134); 

// AJUSTE 4: Margem Direita da DATA
$pdf->SetRightMargin(100); 

$pdf->Cell(0, 10, $data_hoje, 0, 1, 'R'); // 'R' = Alinhado à Direita

// Saída
$pdf->Output('D', 'Certificado_Rede_Colmeia.pdf');
?>