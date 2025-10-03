<?php
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laudo de Qualidade - Cupcake Store</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        .container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
            max-width: 600px;
            width: 100%;
        }

        .header {
            background: linear-gradient(135deg, #2c3e50, #34495e);
            color: white;
            padding: 3rem 2rem;
            text-align: center;
        }

        .header h1 {
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
        }

        .header p {
            opacity: 0.9;
            font-size: 1.1rem;
        }

        .content {
            padding: 2rem;
        }

        .file-grid {
            display: grid;
            gap: 1rem;
        }

        .file-card {
            background: #f8f9fa;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 1.5rem;
            text-align: center;
            transition: all 0.3s ease;
            text-decoration: none;
            color: inherit;
        }

        .file-card:hover {
            border-color: #6b8e6b;
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        .file-icon {
            font-size: 3rem;
            color: #6b8e6b;
            margin-bottom: 1rem;
        }

        .file-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 0.5rem;
        }

        .file-description {
            color: #666;
            font-size: 0.9rem;
            line-height: 1.4;
        }

        .file-size {
            background: #e9ecef;
            color: #6c757d;
            padding: 0.3rem 0.8rem;
            border-radius: 15px;
            font-size: 0.8rem;
            margin-top: 1rem;
            display: inline-block;
        }

        .stats {
            background: linear-gradient(135deg, #27ae60, #2ecc71);
            color: white;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            text-align: center;
        }

        .stats h3 {
            margin-bottom: 1rem;
        }

        .back-button {
            background: #6b8e6b;
            color: white;
            padding: 1rem 2rem;
            border-radius: 10px;
            text-decoration: none;
            display: inline-block;
            margin-top: 1rem;
            transition: background 0.3s;
        }

        .back-button:hover {
            background: #5a7a5a;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-folder-open"></i> Laudo de Qualidade</h1>
            <p>Documentação Completa do Projeto Cupcake Store</p>
        </div>

        <div class="content">
            <div class="stats">
                <h3><i class="fas fa-check-circle"></i> Projeto Concluído com Sucesso</h3>
                <p>Todos os arquivos de documentação e laudo de qualidade estão disponíveis abaixo</p>
            </div>

            <div class="file-grid">
                <a href="visualizador_laudo.html" class="file-card" target="_blank">
                    <div class="file-icon">
                        <i class="fas fa-globe"></i>
                    </div>
                    <div class="file-title">Visualizador Web</div>
                    <div class="file-description">
                        Versão interativa e visual do laudo de qualidade com design moderno e navegação intuitiva
                    </div>
                    <div class="file-size">HTML • Interface Web</div>
                </a>

                <a href="LAUDO_DE_QUALIDADE_CUPCAKE_STORE.txt" class="file-card" target="_blank">
                    <div class="file-icon">
                        <i class="fas fa-file-text"></i>
                    </div>
                    <div class="file-title">Laudo Completo</div>
                    <div class="file-description">
                        Documento técnico detalhado com todas as operações, correções e métricas de qualidade do projeto
                    </div>
                    <div class="file-size">
                        <?php 
                        $file = 'LAUDO_DE_QUALIDADE_CUPCAKE_STORE.txt';
                        if (file_exists($file)) {
                            $size = filesize($file);
                            echo number_format($size / 1024, 1) . ' KB';
                        } else {
                            echo 'TXT';
                        }
                        ?> • Texto
                    </div>
                </a>
            </div>

            <div style="text-align: center; margin-top: 2rem;">
                <h3 style="color: #2c3e50; margin-bottom: 1rem;">
                    <i class="fas fa-info-circle"></i> Informações
                </h3>
                <p style="color: #666; margin-bottom: 1rem;">
                    Esta documentação certifica que o projeto Cupcake Store foi desenvolvido 
                    seguindo os mais altos padrões de qualidade em desenvolvimento web.
                </p>
                <div style="background: #e8f5e8; padding: 1rem; border-radius: 8px; margin: 1rem 0;">
                    <p style="color: #27ae60; font-weight: 600; margin: 0;">
                        <i class="fas fa-certificate"></i> Projeto Aprovado para Produção
                    </p>
                </div>
                <a href="../index.php" class="back-button">
                    <i class="fas fa-arrow-left"></i> Voltar ao Site
                </a>
            </div>
        </div>
    </div>

    <script>
        // Adicionar animação aos cards
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.file-card');
            cards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                setTimeout(() => {
                    card.style.transition = 'all 0.5s ease';
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, index * 200);
            });
        });
    </script>
</body>
</html>

