<?php

namespace Database\Seeders;

use App\Models\LegalDocument;
use Illuminate\Database\Seeder;

class LegalDocumentSeeder extends Seeder
{
    public function run()
    {
        // Termos de Uso
        LegalDocument::create([
            'type' => 'terms_of_use',
            'title' => 'Termos de Uso - RammesPharm',
            'content' => $this->getTermsContent(),
            'version' => '1.0',
            'effective_date' => now(),
            'is_active' => true,
            'created_by' => 1
        ]);

        // Política de Privacidade
        LegalDocument::create([
            'type' => 'privacy_policy',
            'title' => 'Política de Privacidade - RammesPharm',
            'content' => $this->getPrivacyContent(),
            'version' => '1.0',
            'effective_date' => now(),
            'is_active' => true,
             'created_by' => 1
        ]);
    }

    private function getTermsContent()
    {
        return '<h1>TERMOS DE USO DA PLATAFORMA RAMMESPHARM</h1>

<h2>1. ACEITAÇÃO DOS TERMOS</h2>
<p>Ao acessar e usar a plataforma RammesPharm, você concorda com estes Termos de Uso. Se não concordar, não utilize nossos serviços.</p>

<h2>2. DESCRIÇÃO DO SERVIÇO</h2>
<p>A plataforma RammesPharm conecta usuários a farmácias licenciadas em Angola para:</p>
<ul>
<li>Solicitação de medicamentos com prescrição médica</li>
<li>Consulta de disponibilidade e preços de medicamentos</li>
<li>Processamento de pedidos e pagamentos seguros</li>
<li>Acompanhamento de entregas em tempo real</li>
<li>Comunicação direta com farmácias parceiras</li>
</ul>

<h2>3. RESPONSABILIDADES DO USUÁRIO</h2>

<h3>3.1 Informações Verdadeiras</h3>
<ul>
<li>Fornecer dados pessoais corretos e atualizados</li>
<li>Manter confidencialidade de login e senha</li>
<li>Notificar imediatamente sobre uso não autorizado da conta</li>
<li>Atualizar informações de contato e endereço quando necessário</li>
</ul>

<h3>3.2 Uso Adequado</h3>
<ul>
<li>Usar a plataforma apenas para fins legítimos e médicos</li>
<li>Não tentar burlar sistemas de segurança ou autenticação</li>
<li>Respeitar direitos de propriedade intelectual da plataforma</li>
<li>Não usar para atividades ilegais, fraudulentas ou não autorizadas</li>
<li>Não interferir no funcionamento normal da plataforma</li>
</ul>

<h3>3.3 Medicamentos e Prescrições</h3>
<ul>
<li>Apresentar prescrições médicas válidas quando exigido por lei</li>
<li>Usar medicamentos conforme orientação médica profissional</li>
<li>Não revender ou transferir medicamentos adquiridos através da plataforma</li>
<li>Verificar validade e integridade dos medicamentos recebidos</li>
<li>Informar reações adversas às autoridades competentes</li>
</ul>

<h2>4. RESPONSABILIDADES DA PLATAFORMA</h2>

<h3>4.1 Disponibilidade do Serviço</h3>
<ul>
<li>Manter plataforma funcionando adequadamente dentro do possível</li>
<li>Realizar manutenções programadas com aviso prévio quando possível</li>
<li>Proteger dados pessoais conforme Política de Privacidade</li>
<li>Facilitar comunicação entre usuários e farmácias</li>
</ul>

<h3>4.2 Limitações de Responsabilidade</h3>
<ul>
<li>Não somos farmácia nem prestamos serviços médicos diretos</li>
<li>Não garantimos disponibilidade constante de medicamentos específicos</li>
<li>Preços e condições de venda são definidos pelas farmácias parceiras</li>
<li>Não nos responsabilizamos por conselhos médicos de terceiros</li>
</ul>

<h2>5. FARMÁCIAS PARCEIRAS</h2>

<h3>5.1 Requisitos</h3>
<ul>
<li>Possuir licenças válidas emitidas pelas autoridades angolanas</li>
<li>Cumprir regulamentações farmacêuticas nacionais</li>
<li>Manter padrões de qualidade e segurança</li>
<li>Fornecer informações precisas sobre medicamentos</li>
</ul>

<h3>5.2 Responsabilidades</h3>
<ul>
<li>Verificar validade de prescrições médicas</li>
<li>Garantir qualidade e autenticidade dos medicamentos</li>
<li>Cumprir prazos de entrega acordados</li>
<li>Prestar atendimento adequado aos clientes</li>
</ul>

<h2>6. PAGAMENTOS E CANCELAMENTOS</h2>

<h3>6.1 Pagamentos</h3>
<ul>
<li>Processados através de parceiros de pagamento seguros e licenciados</li>
<li>Sujeitos a aprovação da operadora financeira</li>
<li>Taxas de entrega aplicáveis conforme localização em Angola</li>
<li>Preços em Kwanza Angolano (AOA) ou moeda aceita localmente</li>
</ul>

<h3>6.2 Cancelamentos e Reembolsos</h3>
<ul>
<li>Cancelamento possível até confirmação e preparação pela farmácia</li>
<li>Medicamentos controlados podem ter restrições de cancelamento</li>
<li>Reembolsos processados conforme política de cada farmácia</li>
<li>Prazo de reembolso de até 14 dias úteis após aprovação</li>
</ul>

<h2>7. PROPRIEDADE INTELECTUAL</h2>
<p>Todos os direitos sobre a plataforma RammesPharm, incluindo design, código-fonte, logotipos, marcas registradas e conteúdo, são de propriedade da empresa ou licenciados por terceiros autorizados.</p>

<h2>8. LIMITAÇÃO DE RESPONSABILIDADE</h2>
<p>A RammesPharm não se responsabiliza por:</p>
<ul>
<li>Ações, omissões ou negligência das farmácias parceiras</li>
<li>Efeitos adversos, reações ou complicações de medicamentos</li>
<li>Problemas de entrega causados por fatores externos (clima, trânsito, etc.)</li>
<li>Perdas indiretas, consequenciais ou danos morais</li>
<li>Interrupções de serviço por manutenção ou fatores técnicos</li>
<li>Decisões médicas baseadas em informações da plataforma</li>
</ul>

<h2>9. SUSPENSÃO E ENCERRAMENTO</h2>
<p>Podemos suspender ou encerrar contas de usuário em caso de:</p>
<ul>
<li>Violação destes termos de uso</li>
<li>Atividade suspeita, fraudulenta ou ilegal</li>
<li>Uso inadequado da plataforma</li>
<li>Solicitação do próprio usuário</li>
<li>Descontinuidade do serviço</li>
</ul>

<h2>10. PROTEÇÃO DE DADOS</h2>
<p>Coletamos e processamos dados pessoais conforme nossa Política de Privacidade, respeitando as leis de proteção de dados aplicáveis em Angola e internacionalmente.</p>

<h2>11. ALTERAÇÕES DOS TERMOS</h2>
<p>Reservamos o direito de alterar estes termos a qualquer momento, notificando usuários com antecedência razoável através da plataforma, email ou SMS.</p>

<h2>12. LEI APLICÁVEL E JURISDIÇÃO</h2>
<p>Estes termos são regidos pela legislação da República de Angola, com foro na comarca de Luanda para resolução de disputas.</p>

<h2>13. CONTATO</h2>
<p>Para dúvidas sobre estes termos:</p>
<ul>
<li><strong>Email:</strong> legal@rammespharm.ao</li>
<li><strong>Telefone:</strong> +244 XXX XXX XXX</li>
<li><strong>Endereço:</strong> Luanda, Angola</li>
</ul>

<hr>
<p><strong>Ao clicar em "Aceito", você confirma ter lido e concordado com todos os termos acima.</strong></p>
<p><em>Última atualização: ' . now()->format('d/m/Y') . '</em></p>';
    }

    private function getPrivacyContent()
    {
        return '<h1>POLÍTICA DE PRIVACIDADE E PROTEÇÃO DE DADOS - RAMMESPHARM</h1>

<p><strong>Última atualização:</strong> ' . now()->format('d/m/Y') . '</p>

<h2>1. INFORMAÇÕES GERAIS</h2>
<p>Esta Política de Privacidade descreve como a RammesPharm coleta, usa, armazena e protege suas informações pessoais ao utilizar nossa plataforma de medicamentos e farmácias em Angola.</p>

<p><strong>Responsável pelo tratamento:</strong> RammesPharm Angola, Lda.<br>
<strong>Contato:</strong> privacidade@rammespharm.ao<br>
<strong>Telefone:</strong> +244 XXX XXX XXX<br>
<strong>Endereço:</strong> Luanda, República de Angola</p>

<h2>2. DADOS QUE COLETAMOS</h2>

<h3>2.1 Dados Pessoais Básicos</h3>
<ul>
<li>Nome completo</li>
<li>Número de identificação (Bilhete de Identidade)</li>
<li>Data de nascimento</li>
<li>Endereço completo de residência e entrega</li>
<li>Número de telefone e email</li>
<li>Dados de pagamento (processados por terceiros seguros)</li>
</ul>

<h3>2.2 Dados de Saúde e Medicamentos</h3>
<ul>
<li>Prescrições médicas digitalizadas</li>
<li>Histórico de pedidos de medicamentos</li>
<li>Informações sobre medicamentos controlados</li>
<li>Dados fornecidos por farmácias parceiras</li>
<li>Preferências de medicamentos e tratamentos</li>
</ul>

<h3>2.3 Dados Técnicos e de Uso</h3>
<ul>
<li>Endereço IP e localização aproximada</li>
<li>Dados de navegação e uso da plataforma</li>
<li>Cookies e tecnologias similares</li>
<li>Logs de sistema, segurança e acesso</li>
<li>Informações do dispositivo e navegador</li>
</ul>

<h2>3. COMO USAMOS SEUS DADOS</h2>

<h3>3.1 Finalidades Principais</h3>
<ul>
<li><strong>Processamento de pedidos:</strong> Validar, processar e entregar medicamentos</li>
<li><strong>Comunicação:</strong> Enviar notificações sobre status dos pedidos via SMS e email</li>
<li><strong>Segurança:</strong> Verificar identidade e prevenir fraudes</li>
<li><strong>Compliance:</strong> Cumprir obrigações legais e regulatórias angolanas</li>
<li><strong>Melhoria do serviço:</strong> Analisar uso e otimizar experiência</li>
</ul>

<h3>3.2 Comunicações e Notificações</h3>
<ul>
<li>SMS e emails sobre status e atualizações de pedidos</li>
<li>Notificações sobre produtos em falta ou com validade próxima</li>
<li>Comunicações de segurança e atualizações importantes</li>
<li>Marketing e promoções (apenas com seu consentimento explícito)</li>
<li>Lembretes de medicamentos (se solicitado)</li>
</ul>

<h2>4. COMPARTILHAMENTO DE DADOS</h2>

<h3>4.1 Farmácias Parceiras Licenciadas</h3>
<p>Compartilhamos dados necessários com farmácias para:</p>
<ul>
<li>Processamento e dispensação segura de medicamentos</li>
<li>Verificação de prescrições médicas</li>
<li>Controle de estoque e validade</li>
<li>Cumprimento de regulamentações farmacêuticas</li>
</ul>

<h3>4.2 Terceiros Autorizados</h3>
<ul>
<li><strong>Operadoras de SMS:</strong> Para envio de notificações importantes</li>
<li><strong>Processadores de pagamento:</strong> Para transações financeiras seguras</li>
<li><strong>Serviços de entrega:</strong> Para logística e rastreamento</li>
<li><strong>Órgãos reguladores:</strong> Quando exigido por lei (Ministério da Saúde, etc.)</li>
</ul>

<h3>4.3 Compromisso de Não Comercialização</h3>
<p><strong>Nunca vendemos, alugamos ou comercializamos seus dados pessoais com terceiros para fins comerciais.</strong></p>

<h2>5. SEGURANÇA E PROTEÇÃO</h2>

<h3>5.1 Medidas Técnicas</h3>
<ul>
<li>Criptografia avançada de dados sensíveis (AES-256)</li>
<li>Controle de acesso baseado em funções e permissões</li>
<li>Monitoramento de segurança 24/7</li>
<li>Backups seguros e regulares</li>
<li>Firewall e proteção contra ataques</li>
</ul>

<h3>5.2 Medidas Organizacionais</h3>
<ul>
<li>Treinamento regular de equipe em proteção de dados</li>
<li>Políticas internas rigorosas de segurança</li>
<li>Auditoria regular de processos e sistemas</li>
<li>Contratos de confidencialidade com funcionários</li>
<li>Certificações de segurança internacionais</li>
</ul>

<h2>6. SEUS DIREITOS DE PROTEÇÃO DE DADOS</h2>

<p>Conforme legislação aplicável, você tem direito a:</p>
<ul>
<li><strong>Acesso:</strong> Saber quais dados pessoais temos sobre você</li>
<li><strong>Correção:</strong> Corrigir dados incorretos ou incompletos</li>
<li><strong>Exclusão:</strong> Solicitar remoção de dados (quando legalmente aplicável)</li>
<li><strong>Portabilidade:</strong> Receber seus dados em formato estruturado</li>
<li><strong>Oposição:</strong> Opor-se ao tratamento em certas situações</li>
<li><strong>Revogação:</strong> Retirar consentimento a qualquer momento</li>
<li><strong>Limitação:</strong> Restringir processamento em casos específicos</li>
</ul>

<p><strong>Para exercer seus direitos:</strong> Entre em contato através de privacidade@rammespharm.ao</p>

<h2>7. RETENÇÃO DE DADOS</h2>

<h3>7.1 Dados Pessoais Gerais</h3>
<ul>
<li>Mantidos enquanto sua conta estiver ativa</li>
<li>Após exclusão: 30 dias para backup e segurança</li>
<li>Dados essenciais: conforme exigências legais</li>
</ul>

<h3>7.2 Dados de Saúde e Medicamentos</h3>
<ul>
<li>Prescrições médicas: 5 anos (conforme legislação farmacêutica angolana)</li>
<li>Medicamentos controlados: 2 anos (regulamentações nacionais)</li>
<li>Histórico médico: Conforme determinação médica e legal</li>
</ul>

<h3>7.3 Dados Financeiros</h3>
<ul>
<li>Transações: 5 anos (legislação fiscal angolana)</li>
<li>Dados de pagamento: Não armazenamos dados completos de cartão</li>
<li>Comprovantes: Disponíveis conforme lei</li>
</ul>

<h2>8. COOKIES E TECNOLOGIAS DE RASTREAMENTO</h2>

<h3>8.1 Tipos de Cookies Utilizados</h3>
<ul>
<li><strong>Essenciais:</strong> Funcionamento básico da plataforma</li>
<li><strong>Funcionais:</strong> Melhorar experiência do usuário</li>
<li><strong>Analíticos:</strong> Entender uso da plataforma</li>
<li><strong>Marketing:</strong> Personalizar conteúdo (com consentimento)</li>
</ul>

<h3>8.2 Gerenciamento de Cookies</h3>
<p>Você pode gerenciar cookies através das configurações do seu navegador ou através das preferências na plataforma.</p>

<h2>9. MENORES DE IDADE</h2>
<ul>
<li>Não coletamos dados de menores de 18 anos sem consentimento dos responsáveis legais</li>
<li>Medicamentos para menores requerem prescrição e autorização parental</li>
<li>Responsáveis legais podem exercer direitos em nome do menor</li>
<li>Verificação de idade implementada para medicamentos restritos</li>
</ul>

<h2>10. TRANSFERÊNCIA INTERNACIONAL DE DADOS</h2>
<p>Atualmente processamos dados principalmente em Angola. Caso seja necessária transferência internacional, você será notificado e solicitaremos consentimento quando exigido por lei, garantindo proteções adequadas.</p>

<h2>11. ALTERAÇÕES NESTA POLÍTICA</h2>

<h3>11.1 Processo de Notificação</h3>
<ul>
<li>Alterações significativas serão comunicadas por email e SMS</li>
<li>Versão atualizada sempre disponível na plataforma</li>
<li>Histórico de versões mantido para transparência</li>
</ul>

<h3>11.2 Consentimento para Alterações</h3>
<ul>
<li>Alterações substanciais requerem novo consentimento explícito</li>
<li>Uso continuado após notificação implica aceitação de mudanças menores</li>
</ul>

<h2>12. BASE LEGAL PARA PROCESSAMENTO</h2>

<p>Tratamos seus dados com base em:</p>
<ul>
<li><strong>Consentimento:</strong> Para marketing e comunicações opcionais</li>
<li><strong>Execução de contrato:</strong> Para processar pedidos e pagamentos</li>
<li><strong>Obrigação legal:</strong> Para compliance regulatório em Angola</li>
<li><strong>Interesse legítimo:</strong> Para segurança e prevenção de fraudes</li>
<li><strong>Proteção da vida:</strong> Para situações de emergência médica</li>
</ul>

<h2>13. CONTATO E RECLAMAÇÕES</h2>

<h3>13.1 Encarregado de Proteção de Dados (DPO)</h3>
<p><strong>Nome:</strong> [Nome do DPO]<br>
<strong>Email:</strong> dpo@rammespharm.ao<br>
<strong>Telefone:</strong> +244 XXX XXX XXX</p>

<h3>13.2 Autoridades Competentes</h3>
<p>Em caso de não resolução de questões de privacidade, você pode contatar as autoridades de proteção de dados competentes em Angola.</p>

<h2>14. DISPOSIÇÕES FINAIS</h2>
<ul>
<li>Esta política é regida pela legislação da República de Angola</li>
<li>Foro da comarca de Luanda para resolução de conflitos</li>
<li>Versões em outros idiomas são apenas para conveniência</li>
<li>Em caso de conflito, prevalece a versão em português</li>
</ul>

<hr>

<h2>CONSENTIMENTO</h2>
<p>Ao utilizar nossa plataforma, você declara ter lido, compreendido e concordado com esta Política de Privacidade e Proteção de Dados.</p>

<p><strong>Data de aceitação:</strong> [Será preenchida automaticamente pelo sistema]<br>
<strong>Versão aceita:</strong> v1.0<br>
<strong>IP de aceitação:</strong> [Registrado automaticamente]</p>';
    }
}
