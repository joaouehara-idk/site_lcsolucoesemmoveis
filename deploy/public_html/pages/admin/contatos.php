<h1>Contatos</h1>

<div class="card">
    <table>
        <thead>
            <tr>
                <th>Nome</th>
                <th>Telefone</th>
                <th>Categoria</th>
                <th style="width: 30%;">Mensagem</th>
                <th>Status</th>
                <th>Data</th>
                <th>IP</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($contatos)): ?>
                <tr>
                    <td colspan="8" style="text-align:center; color: var(--ink-muted); padding: 40px 0;">Nenhum contato recebido ainda.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($contatos as $contato): ?>
                    <tr>
                        <td style="font-weight: 600; color: var(--ink);"><?php echo htmlspecialchars($contato['nome']); ?></td>
                        <td><?php echo htmlspecialchars($contato['telefone'] ?? '---'); ?></td>
                        <td><?php echo htmlspecialchars($contato['assunto']); ?></td>
                        <td style="color: var(--ink-muted); word-break: break-word;"><?php echo nl2br(htmlspecialchars($contato['mensagem'])); ?></td>
                        <td>
                            <?php
                                $status = $contato['status'] ?? 'novo';
                                $badgeColor = $status === 'lido' ? 'rgba(74,124,89,0.12)' : 'rgba(184,147,90,0.15)';
                                $textColor = $status === 'lido' ? '#4A7C59' : '#1A1714';
                            ?>
                            <span style="background: <?php echo $badgeColor; ?>; color: <?php echo $textColor; ?>; padding: 4px 12px; border-radius: 100px; font-size: 12px; font-weight: 600; text-transform: capitalize;">
                                <?php echo htmlspecialchars($status); ?>
                            </span>
                        </td>
                        <td><?php echo date('d/m/Y H:i', strtotime($contato['data_envio'] ?? $contato['created_at'])); ?></td>
                        <td style="color: var(--ink-faint); font-size: 12px;"><?php echo htmlspecialchars($contato['ip'] ?? '---'); ?></td>
                        <td>
                            <form action="<?php echo BASE_URL; ?>/admin/contatos/deletar" method="POST" onsubmit="return confirm('Excluir este contato?');">
                                <?php echo csrf_field(); ?>
                                <input type="hidden" name="id" value="<?php echo (int)$contato['id']; ?>">
                                <button type="submit" class="btn-delete"><i class="fas fa-trash-alt"></i> Excluir</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
