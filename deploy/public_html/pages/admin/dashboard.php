<h1>Dashboard</h1>

<div class="stats-overview" style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 30px;">
    <div class="card">
        <h3 style="color: var(--accent); margin-bottom: 15px; font-family: 'DM Serif Display', serif; font-weight: 400;"><i class="fas fa-blog"></i> Posts Mais Vistos</h3>
        <ul style="list-style: none; padding: 0;">
            <?php foreach ($topPosts as $tp): ?>
                <li style="display: flex; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid rgba(26,23,20,0.06); font-size: 13px;">
                    <span style="color: var(--ink-soft);"><?php echo htmlspecialchars($tp['titulo']); ?></span>
                    <strong style="color: var(--accent); font-weight: 600;"><?php echo $tp['visualizacoes']; ?></strong>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
    <div class="card">
        <h3 style="color: var(--accent); margin-bottom: 15px; font-family: 'DM Serif Display', serif; font-weight: 400;"><i class="fas fa-images"></i> Portfólio Popular</h3>
        <ul style="list-style: none; padding: 0;">
            <?php foreach ($topProjetos as $tj): ?>
                <li style="display: flex; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid rgba(26,23,20,0.06); font-size: 13px;">
                    <span style="color: var(--ink-soft);"><?php echo htmlspecialchars($tj['titulo']); ?></span>
                    <strong style="color: var(--accent); font-weight: 600;"><?php echo $tj['visualizacoes']; ?></strong>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>

<div class="card">
    <h2 style="font-family: 'DM Serif Display', serif; font-weight: 400; color: var(--ink); margin-bottom: 20px;">Mensagens Recentes</h2>
    <table>
        <thead>
            <tr>
                <th>Nome</th>
                <th>E-mail</th>
                <th>WhatsApp</th>
                <th>Assunto</th>
                <th style="width: 30%;">Mensagem</th>
                <th>Data</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($contatos as $contato): ?>
                <tr>
                    <td style="font-weight: 600; color: var(--ink);"><?php echo htmlspecialchars($contato['nome']); ?></td>
                    <td><?php echo htmlspecialchars($contato['email'] ?? '---'); ?></td>
                    <td><?php echo htmlspecialchars($contato['telefone'] ?? '---'); ?></td>
                    <td><?php echo htmlspecialchars($contato['assunto']); ?></td>
                    <td style="color: var(--ink-muted);"><?php echo nl2br(htmlspecialchars($contato['mensagem'])); ?></td>
                    <td><?php echo date('d/m/Y H:i', strtotime($contato['data_envio'])); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
