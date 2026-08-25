<?php // Projetos dinâmicos vindos do banco através do Controller ?>

<style>
.portfolio-filters {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    justify-content: center;
    margin: 30px 0 10px;
}
.filter-btn {
    padding: 8px 20px;
    border: 1.5px solid rgba(26,23,20,0.12);
    background: #FFFFFF;
    color: #4A4540;
    border-radius: 100px;
    font-size: 13px;
    font-family: 'Inter', sans-serif;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.25s ease;
    text-decoration: none;
}
.filter-btn:hover {
    border-color: #1A1714;
    color: #1A1714;
}
.filter-btn.active {
    background: #1A1714;
    color: #F6F1EB;
    border-color: #1A1714;
}
.portfolio-card { cursor: pointer; }
.portfolio-category {
    display: inline-block;
    padding: 4px 12px;
    background: rgba(26,23,20,0.04);
    border: 1px solid rgba(26,23,20,0.06);
    border-radius: 100px;
    font-size: 11px;
    font-weight: 500;
    color: #8A8580;
    letter-spacing: 0.03em;
    text-transform: uppercase;
    margin-bottom: 10px;
}
.portfolio-card:hover .portfolio-category {
    background: rgba(26,23,20,0.08);
    color: #1A1714;
}
.portfolio-count {
    text-align: center;
    color: #B5B0AA;
    font-size: 13px;
    margin-bottom: 30px;
}

/* Modal */
.modal {
    display: none;
    position: fixed;
    z-index: 9999;
    padding-top: 50px;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgba(26,23,20,0.96);
    backdrop-filter: blur(10px);
}
.modal-content {
    margin: auto;
    display: block;
    width: 80%;
    max-width: 1000px;
    animation: modalZoom 0.4s cubic-bezier(0.22, 1, 0.36, 1);
    box-shadow: 0 0 60px rgba(0,0,0,0.4);
    border-radius: 12px;
}
@keyframes modalZoom {
    from { transform: scale(0.92); opacity: 0; }
    to { transform: scale(1); opacity: 1; }
}
.modal .close {
    position: absolute;
    top: 20px;
    right: 30px;
    color: rgba(255,255,255,0.6);
    font-size: 36px;
    font-weight: 300;
    cursor: pointer;
    transition: color 0.3s;
    z-index: 10000;
    line-height: 1;
}
.modal .close:hover { color: #fff; }
.nav-btn {
    transition: all 0.3s ease;
    border: none;
    cursor: pointer;
}
.nav-btn:hover {
    background: rgba(255,255,255,0.2) !important;
    transform: scale(1.08);
}
#gallery-counter {
    font-weight: 500;
    letter-spacing: 2px;
}
</style>

<section class="page-hero">
    <div class="container">
        <span class="eyebrow">Portfólio</span>
        <h1 class="page-hero-title">Móveis <span>planejados</span></h1>
        <p class="page-hero-subtitle">Caderno de inspirações com projetos que unem arte, tecnologia e funcionalidade.</p>
        <div class="hero-btns" style="justify-content: center; margin-top: var(--space-lg);">
            <a href="#projetos" class="btn-primary"><i class="fas fa-search"></i> Explorar Projetos</a>
            <a href="<?php echo BASE_URL; ?>/contato" class="btn-secondary"><i class="fas fa-file-alt"></i> Solicitar Orçamento</a>
        </div>
    </div>
</section>

<!-- Busca + Filtros -->
<div class="container">
    <div class="search-container">
        <form action="<?php echo BASE_URL; ?>/portfolio" method="GET" class="search-form">
            <input type="text" name="q" class="search-input" placeholder="Busque por ambiente (ex: cozinha, quarto, sala...)" value="<?php echo htmlspecialchars($search_term ?? ''); ?>">
            <button type="submit" class="search-button"><i class="fas fa-search"></i> Buscar</button>
        </form>
    </div>

    <?php if (!empty($categorias)): ?>
    <div class="portfolio-filters">
        <a href="<?php echo BASE_URL; ?>/portfolio" class="filter-btn <?php echo empty($categoria_atual) ? 'active' : ''; ?>">Todos</a>
        <?php foreach ($categorias as $cat): ?>
            <a href="<?php echo BASE_URL; ?>/portfolio?categoria=<?php echo urlencode($cat); ?>" class="filter-btn <?php echo ($categoria_atual === $cat) ? 'active' : ''; ?>"><?php echo htmlspecialchars($cat); ?></a>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>

<section id="projetos" class="portfolio-section">
    <div class="container">
        <?php if (!empty($search_term) || !empty($categoria_atual)): ?>
            <p class="portfolio-count">
                <?php echo count($projetos); ?> <?php echo count($projetos) === 1 ? 'projeto encontrado' : 'projetos encontrados'; ?>
                <?php if (!empty($search_term)): ?> para "<?php echo htmlspecialchars($search_term); ?>"<?php endif; ?>
                <?php if (!empty($categoria_atual)): ?> em <strong><?php echo htmlspecialchars($categoria_atual); ?></strong><?php endif; ?>
            </p>
        <?php endif; ?>

        <?php if (empty($projetos)): ?>
            <div class="text-center" style="padding: 80px 0;">
                <p class="text-muted">Nenhum projeto encontrado.</p>
                <a href="<?php echo BASE_URL; ?>/portfolio" class="btn-secondary" style="margin-top: 15px;">Ver todos os projetos</a>
            </div>
        <?php else: ?>
            <div class="portfolio-grid">
                <?php foreach ($projetos as $item): ?>
                    <?php 
                        $imgField = $item['imagem_capa'] ?? $item['imagem'] ?? '';
                        $imgUrl = (strpos($imgField, 'http') === 0) ? $imgField : BASE_URL . $imgField;
                        $videoField = $item['video_capa'] ?? '';
                        $videoUrl = !empty($videoField) ? ((strpos($videoField, 'http') === 0) ? $videoField : BASE_URL . $videoField) : '';
                        
                        $galeriaJS = [];
                        if (!empty($videoUrl)) {
                            $galeriaJS[] = ['src' => $videoUrl, 'title' => $item['titulo'], 'type' => 'video'];
                        }
                        $galeriaJS[] = ['src' => $imgUrl, 'title' => $item['titulo']];
                        if (!empty($item['galeria'])) {
                            foreach ($item['galeria'] as $g) {
                                $galeriaJS[] = [
                                    'src' => BASE_URL . $g['caminho'],
                                    'title' => $item['titulo']
                                ];
                            }
                        }
                        $galeriaJson = htmlspecialchars(json_encode($galeriaJS), ENT_QUOTES, 'UTF-8');

                        $cat = $item['categoria'] ?? '';
                    ?>
                    <article class="portfolio-card<?php echo !empty($videoUrl) ? ' has-video' : ''; ?>"
                             onclick="openPortfolioGallery(<?php echo $galeriaJson; ?>)">
                        <div class="portfolio-media">
                            <img src="<?php echo htmlspecialchars($imgUrl); ?>" alt="<?php echo htmlspecialchars($item['titulo']); ?>" class="portfolio-image" loading="lazy">
                            <?php if (!empty($videoUrl)): ?>
                                <span class="portfolio-video-badge">Video</span>
                                <video class="portfolio-video" src="<?php echo htmlspecialchars($videoUrl); ?>" muted loop playsinline preload="metadata"></video>
                            <?php endif; ?>
                        </div>
                        <div class="portfolio-content">
                            <?php if (!empty($cat)): ?>
                                <span class="portfolio-category"><?php echo htmlspecialchars($cat); ?></span>
                            <?php endif; ?>
                            <h3 class="portfolio-name"><?php echo htmlspecialchars($item['titulo']); ?></h3>
                            <p class="portfolio-description">
                                <?php
                                    $desc = $item['descricao'] ?? '';
                                    $desc = strip_tags($desc);
                                    $desc = preg_replace('/\s+/', ' ', trim($desc));
                                    if (mb_strlen($desc) > 120) {
                                        $desc = mb_substr($desc, 0, 120) . '...';
                                    }
                                    echo htmlspecialchars($desc, ENT_QUOTES, 'UTF-8', false);
                                ?>
                            </p>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Modal Galeria -->
<div id="imageModal" class="modal">
    <span class="close" onclick="closeGalleryModal()">&times;</span>
    <div style="display:flex;align-items:center;justify-content:center;height:80vh;position:relative;padding:0 60px;">
        <button onclick="prevImg()" class="nav-btn" style="position:absolute;left:20px;background:rgba(0,0,0,0.5);color:white;font-size:1.6rem;padding:12px;border-radius:50%;"><i class="fas fa-chevron-left"></i></button>
        <img class="modal-content" id="modalImage" style="max-height:100%;max-width:100%;object-fit:contain;">
        <video class="modal-content" id="modalVideo" style="max-height:100%;max-width:100%;object-fit:contain;display:none;" controls></video>
        <button onclick="nextImg()" class="nav-btn" style="position:absolute;right:20px;background:rgba(0,0,0,0.5);color:white;font-size:1.6rem;padding:12px;border-radius:50%;"><i class="fas fa-chevron-right"></i></button>
    </div>
    <div id="caption" style="text-align:center;color:rgba(255,255,255,0.8);margin-top:20px;font-size:1.1rem;"></div>
    <div id="gallery-counter" style="text-align:center;color:rgba(255,255,255,0.4);margin-top:8px;font-size:13px;"></div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.portfolio-card.has-video').forEach(card => {
        const video = card.querySelector('.portfolio-video');
        if (!video) return;
        card.addEventListener('mouseenter', () => { video.currentTime = 0; video.play().catch(() => {}); });
        card.addEventListener('mouseleave', () => { video.pause(); video.currentTime = 0; });
        card.addEventListener('touchstart', () => { video.currentTime = 0; video.play().catch(() => {}); }, { passive: true });
        card.addEventListener('touchend', () => { video.pause(); video.currentTime = 0; });
    });
});

let currentGallery = [];
let currentIndex = 0;

function openPortfolioGallery(images) {
    currentGallery = images;
    currentIndex = 0;
    updateModal();
    document.getElementById('imageModal').style.display = 'block';
    document.body.style.overflow = 'hidden';
}

function updateModal() {
    const imgEl = document.getElementById('modalImage');
    const vidEl = document.getElementById('modalVideo');
    const captionEl = document.getElementById('caption');
    const counterEl = document.getElementById('gallery-counter');
    if (currentGallery.length === 0) return;
    const item = currentGallery[currentIndex];
    if (item.type === 'video') {
        imgEl.style.display = 'none';
        vidEl.style.display = 'block';
        vidEl.src = item.src;
        vidEl.play();
    } else {
        vidEl.style.display = 'none';
        vidEl.pause();
        vidEl.src = '';
        imgEl.style.display = 'block';
        imgEl.src = item.src;
    }
    captionEl.textContent = item.title;
    counterEl.textContent = (currentIndex + 1) + ' / ' + currentGallery.length;
    document.querySelectorAll('.nav-btn').forEach(b => b.style.display = currentGallery.length > 1 ? 'block' : 'none');
}

function nextImg() {
    document.getElementById('modalVideo').pause();
    document.getElementById('modalVideo').src = '';
    currentIndex = (currentIndex + 1) % currentGallery.length;
    updateModal();
}

function prevImg() {
    document.getElementById('modalVideo').pause();
    document.getElementById('modalVideo').src = '';
    currentIndex = (currentIndex - 1 + currentGallery.length) % currentGallery.length;
    updateModal();
}

function closeGalleryModal() {
    const vid = document.getElementById('modalVideo');
    vid.pause(); vid.src = '';
    document.getElementById('modalImage').style.display = 'block';
    vid.style.display = 'none';
    document.getElementById('imageModal').style.display = 'none';
    document.body.style.overflow = '';
}

document.addEventListener('keydown', (e) => {
    if (document.getElementById('imageModal').style.display === 'block') {
        if (e.key === 'ArrowRight') nextImg();
        if (e.key === 'ArrowLeft') prevImg();
        if (e.key === 'Escape') closeGalleryModal();
    }
});
</script>
