<?php 
    $postImg = $post['imagem'] ?? '';
    $imgUrl = (strpos($postImg, 'http') === 0) ? $postImg : BASE_URL . $postImg;
    $fullImgUrl = (strpos($postImg, 'http') === 0) ? $postImg : SITE_URL . $postImg;

    $postTitle = $post['titulo'] ?? '';
    $postDesc = $post['meta_description'] ?? ($post['resumo'] ?? substr(strip_tags($post['conteudo'] ?? ''), 0, 160));
    $postDate = $post['created_at'] ?? date('Y-m-d H:i:s');
    $postSlug = $post['slug'] ?? '';
    $postCategoria = $post['categoria'] ?? 'Móveis Planejados';
?>

<!-- Article Schema -->
<style>
.blog-cta-box { background: #f8f6f3; padding: 32px; border-radius: 12px; margin: 40px 0; border: 1px solid #e8e4df; }
.blog-cta-box h3 { margin-top: 0; color: #1A1714; }
.blog-cta-box p { color: #4a4540; line-height: 1.6; }
.blog-cta-box a { display: inline-block; background: #1A1714; color: #fff !important; padding: 12px 28px; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 14px; margin-top: 8px; transition: opacity 0.2s; }
.blog-cta-box a:hover { opacity: 0.85; }
.post-body-content h2 { color: #1A1714; font-family: 'DM Serif Display', serif; font-size: 1.6rem; margin-top: 48px; margin-bottom: 16px; }
.post-body-content h3 { color: #1A1714; font-size: 1.15rem; margin-top: 32px; margin-bottom: 12px; }
.post-body-content p { color: #3a3530; line-height: 1.8; margin-bottom: 16px; }
.post-body-content ul { margin: 16px 0; padding-left: 24px; }
.post-body-content li { margin-bottom: 8px; color: #3a3530; line-height: 1.7; }
.post-body-content strong { color: #1A1714; }
.post-body-content blockquote { border-left: 3px solid #1A1714; padding: 16px 24px; margin: 32px 0; background: #f8f6f3; border-radius: 0 8px 8px 0; font-style: italic; color: #5a5550; }
</style>
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "BlogPosting",
    "headline": <?php echo json_encode($postTitle); ?>,
    "description": <?php echo json_encode($postDesc); ?>,
    "image": "<?php echo $fullImgUrl; ?>",
    "author": {
        "@type": "Organization",
        "name": "LC Soluções em Móveis"
    },
    "publisher": {
        "@type": "Organization",
        "name": "LC Soluções em Móveis",
        "logo": {
            "@type": "ImageObject",
            "url": "<?php echo SITE_URL; ?>/assets/img/logo.jpg"
        }
    },
    "datePublished": "<?php echo date('Y-m-d', strtotime($postDate)); ?>",
    "dateModified": "<?php echo date('Y-m-d', strtotime($post['updated_at'] ?? $postDate)); ?>",
    "mainEntityOfPage": {
        "@type": "WebPage",
        "@id": "<?php echo SITE_URL . '/blog/' . $postSlug; ?>"
    }
}
</script>

<!-- Blog Post Hero -->
<section class="blog-hero" style="background-image: url('<?php echo $imgUrl; ?>'); background-size: cover; background-position: center;">
    <div class="container text-center">
        <span class="eyebrow"><?php echo htmlspecialchars($postCategoria); ?></span>
        <h1 class="page-hero-title" style="max-width: 800px; margin: 0 auto var(--space-sm);"><?php echo $post['titulo']; ?></h1>
        <div class="accent-line center"></div>
        <div style="color: var(--ink-muted); font-size: var(--text-sm); font-weight: 500; margin-top: var(--space-sm);">
            <span><i class="fas fa-calendar-alt"></i> <?php echo date('d/m/Y', strtotime($postDate)); ?></span>
            <?php if (!empty($post['visualizacoes'])): ?>
                <span style="margin-left: 16px;"><i class="fas fa-eye"></i> <?php echo number_format($post['visualizacoes'], 0, ',', '.'); ?> visualizações</span>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Blog Post Content -->
<article class="post-detail-section">
    <div class="container">
        <div class="post-container">
            <?php if (!empty($postImg)): ?>
            <div class="post-header-image" style="margin-bottom: var(--space-xl);">
                <img src="<?php echo $imgUrl; ?>" alt="<?php echo htmlspecialchars($postTitle); ?>" style="width: 100%; border-radius: var(--radius-md); border: var(--border);" loading="lazy">
            </div>
            <?php endif; ?>
            
            <div class="post-body-content">
                <?php 
                    $conteudo = $post['conteudo'];
                    
                    if (strpos($conteudo, '<h') === false && strpos($conteudo, '<p>') === false) {
                        $conteudo = preg_replace('/^### (.*)$/m', '<h3>$1</h3>', $conteudo);
                        $conteudo = preg_replace('/^## (.*)$/m', '<h2>$1</h2>', $conteudo);
                        $conteudo = preg_replace('/^# (.*)$/m', '<h2>$1</h2>', $conteudo);
                        $conteudo = preg_replace('/\*\*(.*?)\*\*/', '<strong>$1</strong>', $conteudo);
                        
                        $conteudo = preg_replace('/^\* (.*)$/m', '<li>$1</li>', $conteudo);
                        $conteudo = '<ul>' . $conteudo . '</ul>';
                        $conteudo = str_replace('</li><h', '</li></ul><h', $conteudo);
                        $conteudo = str_replace('</li><p', '</li></ul><p', $conteudo);
                        $conteudo = str_replace('<ul><h', '<h', $conteudo);
                        $conteudo = str_replace('<ul><p', '<p', $conteudo);
                        $conteudo = str_replace('<li></li>', '', $conteudo);
                        
                        $conteudo = nl2br($conteudo);
                        
                        $conteudo = str_replace('<ul><br />', '<ul>', $conteudo);
                        $conteudo = str_replace('</ul><br />', '</ul>', $conteudo);
                    }
                    
                    echo $conteudo; 
                ?>
            </div>
            
            <div class="post-footer">
                <div class="post-share">
                    <span style="color: var(--ink-muted); margin-right: 15px; font-size: var(--text-sm);">Compartilhe:</span>
                    <a href="https://api.whatsapp.com/send?text=<?php echo urlencode($postTitle . ' - ' . SITE_URL . '/blog/' . $postSlug); ?>" target="_blank" style="color: #25d366; font-size: 1.3rem;"><i class="fab fa-whatsapp"></i></a>
                </div>
                <a href="<?php echo BASE_URL; ?>/blog" class="btn-secondary" style="padding: 10px 24px; font-size: var(--text-xs);">
                    <i class="fas fa-arrow-left"></i> Voltar ao Blog
                </a>
            </div>

            <?php if (!empty($related_posts)): ?>
            <div class="related-posts" style="margin-top: var(--space-2xl); padding-top: var(--space-xl); border-top: var(--border);">
                <h3 style="margin-bottom: var(--space-md);">Artigos <span class="text-accent">relacionados</span></h3>
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: var(--space-md);">
                    <?php foreach ($related_posts as $rp): ?>
                        <?php 
                            $rpImg = $rp['imagem'] ?? '';
                            $rpImgUrl = !empty($rpImg) ? ((strpos($rpImg, 'http') === 0) ? $rpImg : BASE_URL . $rpImg) : BASE_URL . '/assets/img/logo.jpg';
                        ?>
                        <a href="<?php echo BASE_URL; ?>/blog/<?php echo $rp['slug']; ?>" style="text-decoration: none; color: inherit; border: var(--border); border-radius: var(--radius-md); overflow: hidden; transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='none'">
                            <img src="<?php echo $rpImgUrl; ?>" alt="<?php echo htmlspecialchars($rp['titulo']); ?>" style="width: 100%; height: 160px; object-fit: cover;" loading="lazy">
                            <div style="padding: 16px;">
                                <span style="font-size: var(--text-xs); color: var(--ink-muted); text-transform: uppercase; letter-spacing: 0.5px;"><?php echo htmlspecialchars($rp['categoria'] ?? 'Móveis Planejados'); ?></span>
                                <h4 style="margin: 8px 0 0; font-size: var(--text-sm); line-height: 1.4;"><?php echo htmlspecialchars($rp['titulo']); ?></h4>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <div class="post-services" style="margin-top: var(--space-2xl); padding-top: var(--space-xl); border-top: var(--border);">
                <h3 style="margin-bottom: var(--space-md);">Conheça nossos <span class="text-accent">serviços</span></h3>
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: var(--space-sm);">
                    <a href="<?php echo BASE_URL; ?>/servicos/cozinha-planejada" class="btn-secondary" style="text-align: center;">Cozinha Planejada</a>
                    <a href="<?php echo BASE_URL; ?>/servicos/closet-e-quarto-planejado" class="btn-secondary" style="text-align: center;">Closet e Quarto</a>
                    <a href="<?php echo BASE_URL; ?>/servicos/sala-e-painel-planejado" class="btn-secondary" style="text-align: center;">Sala e Painel</a>
                    <a href="<?php echo BASE_URL; ?>/servicos/home-office-planejado" class="btn-secondary" style="text-align: center;">Home Office</a>
                    <a href="<?php echo BASE_URL; ?>/servicos/moveis-comerciais" class="btn-secondary" style="text-align: center;">Móveis Comerciais</a>
                </div>
            </div>
        </div>
    </div>
</article>