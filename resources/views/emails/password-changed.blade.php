<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Senha Alterada - RammesPharm</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, #14b8a6, #f97316); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
        .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 10px 10px; }
        .success { background: #d1fae5; border: 1px solid #10b981; padding: 15px; border-radius: 5px; margin: 20px 0; }
        .footer { text-align: center; margin-top: 20px; color: #666; font-size: 12px; }
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
            
            <div class="success">
                <strong>✅ Senha alterada com sucesso!</strong>
            </div>
            
            <p>Sua senha foi redefinida com sucesso em {{ now()->format('d/m/Y às H:i') }}.</p>
            
            <p>Se você não fez esta alteração, entre em contato conosco imediatamente:</p>
            
            <ul>
                <li>📧 Email: suporte@rammespharm.ao</li>
                <li>📱 WhatsApp: +244 900 000 000</li>
                <li>🌐 Site: www.rammespharm.ao</li>
            </ul>
            
            <p>Por segurança, recomendamos:</p>
            <ul>
                <li>Use uma senha forte e única</li>
                <li>Não compartilhe sua senha com ninguém</li>
                <li>Faça logout de dispositivos não utilizados</li>
            </ul>
        </div>
        
        <div class="footer">
            <p>© 2025 RammesPharm. Todos os direitos reservados.</p>
            <p>Este é um email automático, não responda a esta mensagem.</p>
        </div>
    </div>
</body>
</html>
