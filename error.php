<?php 

// Define a mensagem de erro padrão

$error_title_pt = "PÁGINA NÃO ENCONTRADA";

$error_message_pt = "O conteúdo litúrgico que você tentou acessar não existe em nosso banco de dados. Por favor, volte à página inicial para tentar novamente.";

// URL de destino para o botão "Home" (conforme solicitado)

$home_url = "https://hinario-iesa-online.wuaze.com/home";

// A variável $page pode vir do escopo que inclui este arquivo (por exemplo, geral.php)

$page_display = $page ?? 'indefinida'; 

?>

<!DOCTYPE html>

<html lang="pt">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Erro 404 - <?= htmlspecialchars($error_title_pt) ?></title>

    <style>

        body { 

            font-family: sans-serif; 

            margin: 0; 

            line-height: 1.6; 

            padding: 0; 

            background-color: #f4f4f4; /* Fundo cinza claro */

            text-align: center;

        }

        .header { 

            display: flex; 

            align-items: center; 

            background-color: #ff6600; /* Laranja da paleta */

            color: white; 

            padding: 10px 15px; 

            font-size: 1.2em; 

        }

        .header a { 

            color: white; 

            text-decoration: none; 

            margin-right: 15px; 

        }

        .error-container {

            padding: 40px 20px;

            background-color: white;

            margin: 20px;

            border-radius: 8px;

            box-shadow: 0 2px 4px rgba(0,0,0,0.1);

        }

        h1 {

            color: #ff6600; /* Título de erro em laranja */

            font-size: 2.5em;

            margin-bottom: 0.2em;

        }

        h2 {

            color: #333;

            font-size: 1.5em;

            margin-top: 0;

            margin-bottom: 20px;

        }

        .error-text {

            color: #666;

            margin-bottom: 30px;

            border-top: 1px solid #eee;

            padding-top: 20px;

            text-align: left;

        }

        .error-text p {

            margin-bottom: 10px;

        }

        .home-link {

            display: inline-block;

            background-color: #ff6600;

            color: white;

            text-decoration: none;

            padding: 12px 25px;

            border-radius: 5px;

            font-weight: bold;

            transition: background-color 0.3s;

            text-transform: uppercase;

        }

        .home-link:hover {

            background-color: #e55c00;

        }

    </style>

</head>

<body>

    <div class="header">

        <a href="<?= htmlspecialchars($home_url) ?>">&larr;</a>

        <span>Erro!</span>

    </div>

    <div class="error-container">

        <h1>404</h1>

        <h2><?= htmlspecialchars($error_title_pt) ?></h2>

        <div class="error-text">

            <p><?= htmlspecialchars($error_message_pt) ?></p>

            

            <?php if ($page_display !== 'indefinida'): ?>

                <p>O URL da página incorreta era: <strong>?page=<?= htmlspecialchars($page_display) ?></strong></p>

            <?php endif; ?>

        </div>

        <a href="<?= htmlspecialchars($home_url) ?>" class="home-link">Voltar para a Página Inicial</a>

    </div>

</body>

</html>

