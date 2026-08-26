<?php // Posts dinâmicos vindos do banco através do Controller ?>

<!-- Hero -->
<section class="page-hero">
    <div class="container">
        <span class="eyebrow">Blog</span>
        <h1 class="page-hero-title">Nosso <span>blog</span></h1>
        <div class="accent-line center"></div>
        <p class="page-hero-subtitle">Dicas, tendências e inspirações para transformar seus ambientes com sofisticação.</p>
    </div>
</section>

<!-- Busca -->
<div class="container">
    <div class="search-container">
        <form action="<?php echo BASE_URL; ?>/blog" method="GET" class="search-form">
            <input type="text" name="q" class="search-input" placeholder="O que você procura? (ex: cozinha, mdf, dicas...)" value="<?php echo htmlspecialchars($search_term ?? ''); ?>">
            <button type="submit" class="search-button"><i class="fas fa-search"></i> Buscar</button>
        </form>
    </div>
</div>

<section class="container" style="padding: 20px 0 80px;">
    <div class="blog-layout">
        <main class="posts-grid" style="display: grid; gap: var(--space-lg);">
            <?php if (empty($posts)): ?>
                <div class="text-center" style="padding: 80px 0;"><p class="text-muted">Nenhum post encontrado no momento.</p></div>
            <?php else: ?>
                <?php foreach ($posts as $post): ?>
                <article class="post-card">
                    <?php 
                        $imgUrl = (strpos($post['imagem'], 'http') === 0) ? $post['imagem'] : BASE_URL . $post['imagem'];
                    ?>
                    <img src="<?php echo $imgUrl; ?>" alt="<?php echo $post['titulo']; ?>" class="post-image" loading="lazy">
                    <div class="post-content">
                        <div class="post-meta">
                            <span class="post-date"><?php echo date('d/m/Y', strtotime($post['created_at'])); ?></span>
                            <span class="post-category"><?php echo htmlspecialchars($post['categoria'] ?? 'Móveis Planejados'); ?></span>
                        </div>
                        <h2 class="post-title"><?php echo htmlspecialchars($post['titulo']); ?></h2>
                        <p class="post-excerpt">
                            <?php echo htmlspecialchars($post['resumo'] ?? substr(strip_tags($post['conteudo'] ?? ''), 0, 150) . '...'); ?>
                        </p>
                        <a href="<?php echo BASE_URL; ?>/blog/<?php echo $post['slug']; ?>" class="read-more">Leia mais <i class="fas fa-arrow-right"></i></a>
                    </div>
                </article>
                <?php endforeach; ?>
            <?php endif; ?>
        </main>

        <aside class="sidebar">
            <div class="sidebar-widget">
                <h3 class="widget-title">Categorias</h3>
                <ul class="categories-list">
                    <?php if (empty($categorias)): ?>
                        <li><a href="#">Geral <span>(<?php echo count($posts); ?>)</span></a></li>
                    <?php else: ?>
                        <?php foreach ($categorias as $cat): ?>
                            <li>
                                <a href="<?php echo BASE_URL; ?>/blog?categoria=<?php echo urlencode($cat['categoria']); ?>">
                                    <?php echo htmlspecialchars($cat['categoria']); ?> 
                                    <span>(<?php echo $cat['total']; ?>)</span>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
            </div>
        </aside>
    </div>
</section>
