<?php
/**
 * Configuracao WhatsApp Cloud API (Meta)
 * 
 * Como configurar:
 * 1. Crie uma conta em https://business.facebook.com/
 * 2. Crie um App em https://developers.facebook.com/
 * 3. Adicione o produto WhatsApp
 * 4. Conecte seu numero de telefone
 * 5. Gere um token de acesso permanente
 * 6. Preencha as constantes abaixo
 * 
 * Modo alternativo (wa.me): funciona sem configuracao,
 * apenas abre o link wa.me/55XXXXXXXXX
 */

// ─── Modo de operacao ───
// 'api'    = usa WhatsApp Cloud API (requer config abaixo)
// 'walink' = apenas gera links wa.me (zero config)
define('WHATSAPP_MODE', 'walink');

// ─── WhatsApp Cloud API ───
define('WHATSAPP_TOKEN', '');
define('WHATSAPP_PHONE_ID', '');
define('WHATSAPP_BUSINESS_ID', '');
define('WHATSAPP_API_VERSION', 'v22.0');

// ─── Numero padrao da empresa (LC Solucoes) ───
define('WHATSAPP_COMPANY_NUMBER', '5567999999999');

// ─── Mensagens pre-definidas ───
$whatsappTemplates = [
    'boas_vindas' => "Olá {nome}! Aqui é da LC Soluções em Móveis. Recebemos seu contato e estamos prontos para transformar seu ambiente com móveis planejados. Como podemos ajudar?",
    'orcamento' => "Olá {nome}, tudo bem? Vi que você solicitou um orçamento conosco. Podemos agendar uma visita técnica para conhecer melhor seu espaço?",
    'acompanhamento' => "Olá {nome}, passando pra saber como está o andamento do seu projeto conosco. Precisa de alguma ajuda?",
    'finalizacao' => "Olá {nome}! Seu projeto ficou incrível! Ficamos muito felizes em transformar seu espaço. Se precisar de algo mais, estamos à disposição.",
];
