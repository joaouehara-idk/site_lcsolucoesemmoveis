<section id="tab-chat">

<div style="margin-bottom:16px;">
    <h1 style="font-family:var(--font-heading);font-size:20px;font-weight:800;color:var(--text);letter-spacing:-0.3px;">
        Chat com IA
        <span style="font-size:12px;font-weight:400;color:var(--text-dim);margin-left:10px;font-family:var(--font-body);">
            Pergunte sobre seus parceiros
        </span>
    </h1>
</div>

<div class="chat-container">
    <div class="chat-suggestions">
        <h4>Sugestoes</h4>
        <button class="chip" onclick="send('Quantos arquitetos existem em Campo Grande?')">Arquitetos</button>
        <button class="chip" onclick="send('Quantas construtoras existem em Campo Grande?')">Construtoras</button>
        <button class="chip" onclick="send('Quais imobiliarias tem telefone?')">Imobiliarias c/ telefone</button>
        <button class="chip" onclick="send('Mostre designers de interiores ativos')">Designers ativos</button>
        <button class="chip" onclick="send('Quantos engenheiros estao ativos?')">Engenheiros ativos</button>
        <button class="chip" onclick="send('Mostre marcenarias no bairro Centro')">Marcenarias no Centro</button>
        <button class="chip" onclick="send('Qual o total de parceiros em cada segmento?')">Total por segmento</button>
        <button class="chip" onclick="send('Empresas de paisagismo com telefone')">Paisagismo c/ telefone</button>
    </div>
    <div class="chat-main">
        <div class="chat-messages" id="chatMessages">
            <div class="msg msg-bot">
                <div class="msg-avatar">LC</div>
                <div class="msg-content">Ola! Sou o assistente do CRM de Parceiros LC. Posso consultar dados sobre arquitetos, designers, construtoras, imobiliarias, engenheiros, marcenarias e paisagismo em Mato Grosso do Sul. Pergunte!</div>
            </div>
        </div>
        <div class="chat-input">
            <input type="text" id="chatInput" placeholder="Pergunte algo sobre os parceiros..." onkeydown="if(event.key==='Enter') sendFromInput()">
            <button onclick="sendFromInput()">Enviar</button>
        </div>
    </div>
</div>

<script>
let chatHistory = [];

function addMessage(role, text) {
    const div = document.createElement('div');
    div.className = 'msg msg-' + role;
    div.innerHTML = '<div class="msg-avatar">' + (role === 'user' ? 'U' : 'LC') + '</div><div class="msg-content">' + text + '</div>';
    document.getElementById('chatMessages').appendChild(div);
    div.scrollIntoView({ behavior: 'smooth' });
}

function showLoading() {
    const div = document.createElement('div');
    div.className = 'msg msg-bot';
    div.id = 'loadingMsg';
    div.innerHTML = '<div class="msg-avatar">LC</div><div class="msg-content"><div class="loading"><span></span><span></span><span></span></div></div>';
    document.getElementById('chatMessages').appendChild(div);
    div.scrollIntoView({ behavior: 'smooth' });
}

function hideLoading() {
    const el = document.getElementById('loadingMsg');
    if (el) el.remove();
}

function send(msg) {
    addMessage('user', msg);
    showLoading();
    chatHistory.push({ role: 'user', text: msg });
    fetch('api/chat.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ message: msg, history: chatHistory.slice(-10) })
    })
    .then(r => r.json())
    .then(data => { hideLoading(); addMessage('bot', data.response); chatHistory.push({ role: 'bot', text: data.response }); })
    .catch(e => { hideLoading(); addMessage('bot', 'Desculpe, ocorreu um erro.'); });
}

function sendFromInput() {
    const input = document.getElementById('chatInput');
    const msg = input.value.trim();
    if (!msg) return;
    input.value = '';
    send(msg);
}
</script>

</section>
<!-- EXPORTAR -->
