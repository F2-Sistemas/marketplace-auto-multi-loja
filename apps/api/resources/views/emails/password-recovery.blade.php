<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperação de Senha - AutoHub Central</title>
    <style>
        body {
            background-color: #0b0f19;
            color: #f1f5f9;
            font-family: 'Outfit', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            margin: 0;
            padding: 0;
            -webkit-font-smoothing: antialiased;
        }
        .container {
            max-width: 600px;
            margin: 40px auto;
            background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 100%);
            border: 1px solid rgba(99, 102, 241, 0.15);
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
        }
        .header {
            background: linear-gradient(90deg, #6366f1 0%, #4f46e5 100%);
            padding: 30px;
            text-align: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        .header h1 {
            margin: 0;
            color: #ffffff;
            font-size: 24px;
            font-weight: 800;
            letter-spacing: 0.5px;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }
        .body {
            padding: 40px 30px;
        }
        .body h2 {
            margin-top: 0;
            color: #ffffff;
            font-size: 20px;
            font-weight: 700;
        }
        .body p {
            color: #94a3b8;
            font-size: 15px;
            line-height: 1.6;
            margin-bottom: 24px;
        }
        .btn-container {
            text-align: center;
            margin: 35px 0;
        }
        .btn {
            display: inline-block;
            background-color: transparent;
            color: #818cf8 !important;
            font-size: 14px;
            font-weight: 700;
            text-decoration: none;
            padding: 12px 28px;
            border: 1.5px solid #4f46e5;
            border-radius: 8px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.15);
        }
        .footer {
            background-color: rgba(15, 23, 42, 0.6);
            padding: 24px;
            text-align: center;
            font-size: 12px;
            color: #64748b;
            border-top: 1px solid rgba(255, 255, 255, 0.05);
        }
        .footer p {
            margin: 5px 0;
        }
        .token-box {
            background-color: rgba(99, 102, 241, 0.1);
            border: 1px dashed rgba(99, 102, 241, 0.3);
            border-radius: 8px;
            padding: 15px;
            text-align: center;
            margin-bottom: 25px;
        }
        .token-code {
            font-family: 'Courier New', Courier, monospace;
            font-size: 24px;
            font-weight: 800;
            color: #818cf8;
            letter-spacing: 4px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🛡️ AutoHub Central</h1>
        </div>
        <div class="body">
            <h2>Olá, {{ $name }}!</h2>
            <p>Recebemos uma solicitação para redefinir a senha da sua conta no ecossistema do <strong>AutoHub Central</strong>. Se foi você quem solicitou, utilize o código de validação abaixo para prosseguir com a redefinição:</p>
            
            <div class="token-box">
                <div class="token-code">{{ $token }}</div>
            </div>

            <p>Este código é válido por 60 minutos. Se você não solicitou a redefinição de senha, nenhuma ação adicional é necessária e você pode descartar este e-mail com segurança.</p>
            
            <div class="btn-container">
                <a href="{{ $resetUrl }}" class="btn" target="_blank">Redefinir Senha no Portal</a>
            </div>
        </div>
        <div class="footer">
            <p>&copy; 2026 AutoHub Central. Todos os direitos reservados.</p>
            <p>Este é um e-mail transacional gerado automaticamente. Por favor, não responda a esta mensagem.</p>
        </div>
    </div>
</body>
</html>
