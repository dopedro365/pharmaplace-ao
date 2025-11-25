<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redefinir Senha - RammesPharm</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, #14b8a6, #f97316); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
        .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 10px 10px; }
        .button { display: inline-block; background: #14b8a6; color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px; font-weight: bold; margin: 20px 0; }
        .footer { text-align: center; margin-top: 20px; color: #666; font-size: 12px; }
        .warning { background: #fef3cd; border: 1px solid #fecaca; padding: 15px; border-radius: 5px; margin: 20px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🏥 RammesPharm</h1>
            <p>Conectando saúde e tecnologia</p>
        </div>
        
        <div class="content">
            <h2>Olá, {{ $user->name }}!</h2>
            
            <p>Recebemos uma solicitação para redefinir a senha da sua conta no RammesPharm.</p>
            
            <p>Se você fez esta solicitação, clique no botão abaixo para criar uma nova senha:</p>
            
            <div style="text-align: center;">
                <a href="{{ $resetUrl }}" class="button">Redefinir Minha Senha</a>
            </div>
            
            <div class="warning">
                <strong>⚠️ Importante:</strong>
                <ul>
                    <li>Este link é válido por apenas <strong>1 hora</strong></li>
                    <li>Se você não solicitou esta redefinição, ignore este email</li>
                    <li>Sua senha atual permanecerá inalterada</li>
                </ul>
            </div>
            
            <p>Se o botão não funcionar, copie e cole este link no seu navegador:</p>
            <p style="word-break: break-all; background: #e5e7eb; padding: 10px; border-radius: 5px;">
                {{ $resetUrl }}
            </p>
            
            <hr style="margin: 30px 0; border: none; border-top: 1px solid #ddd;">
            
            <p><strong>Precisa de ajuda?</strong></p>
            <p>Entre em contato conosco:</p>
            <ul>
                <li>📧 Email: suporte@rammespharm.ao</li>
                <li>📱 WhatsApp: +244 900 000 000</li>
                <li>🌐 Site: www.rammespharm.ao</li>
            </ul>
        </div>
        
        <div class="footer">
            <p>© 2025 RammesPharm. Todos os direitos reservados.</p>
            <p>Este é um email automático, não responda a esta mensagem.</p>
        </div>
    </div>
</body>
</html>
