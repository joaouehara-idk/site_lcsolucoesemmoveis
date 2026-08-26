<?php // Projetos dinâmicos vindos do banco através do Controller ?>

<style>
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
    animation: modalZoom 0.5s var(--ease);
    box-shadow: 0 0 60px rgba(0,0,0,0.4);
    border-radius: var(--radius-md);
}

@keyframes modalZoom {
    from { transform: scale(0.92); opacity: 0; }
    to { transform: scale(1); opacity: 1; }
}

.close {
    position: absolute;
    top: 20px;
    right: 30px;
    color: rgba(255,255,255,0.6);
    font-size: 36px;
    font-weight: 300;
    cursor: pointer;
    transition: color 0.3s;
    z-index: 10000;
}

.close:hover { color: var(--accent-light); }

.nav-btn {
    transition: all 0.3s var(--ease);
}

.nav-btn:hover {
    background: var(--accent) !important;
    color: var(--ink) !important;
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

<!-- Busca -->
<div class="container">
    <div class="search-container">
        <form action="<?php echo BASE_URL; ?>/portfolio" method="GET" class="search-form">
            <input type="text" name="q" class="search-input" placeholder="Busque por ambiente (ex: cozinha, quarto, sala...)" value="<?php echo htmlspecialchars($search_term ?? ''); ?>">
            <button type="submit" class="search-button"><i class="fas fa-search"></i> Buscar</button>
        </form>
    </div>
</div>

<section id="projetos" class="portfolio-section">
    <div class="container">
        <?php if (empty($projetos)): ?>
            <div class="text-center" style="padding: 80px 0;"><p class="text-muted">Nenhum projeto cadastrado no momento.</p></div>
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
                        ?>
                        <article class="portfolio-card<?php echo !empty($videoUrl) ? ' has-video' : ''; ?>" data-video="<?php echo htmlspecialchars($videoUrl); ?>">
                            <div class="portfolio-media">
                                <img src="<?php echo htmlspecialchars($imgUrl); ?>" alt="<?php echo htmlspecialchars($item['titulo']); ?>" class="portfolio-image" loading="lazy">
                                <?php if (!empty($videoUrl)): ?>
                                    <span class="portfolio-video-badge">Video</span>
                                    <video class="portfolio-video" src="<?php echo htmlspecialchars($videoUrl); ?>" muted loop playsinline preload="metadata"></video>
                                <?php endif; ?>
                            </div>
                            <div class="portfolio-content">
                                <h3 class="portfolio-name"><?php echo htmlspecialchars($item['titulo']); ?></h3>
                                <p class="portfolio-description">
                                    <?php
                                        $desc = $item['descricao'] ?? '';
                                        $desc = strip_tags($desc);
                                        $desc = preg_replace('/\s+/', ' ', trim($desc));
                                        if (mb_strlen($desc) > 150) {
                                            $desc = mb_substr($desc, 0, 150) . '...';
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

<!-- Modal -->
<div id="imageModal" class="modal">
    <span class="close" onclick="closeGalleryModal()">&times;</span>
    <div class="modal-gallery-container" style="display: flex; align-items: center; justify-content: center; height: 80vh; position: relative; padding: 0 60px;">
        <button onclick="prevImg()" class="nav-btn" style="position: absolute; left: 20px; background: rgba(0,0,0,0.5); color: white; border: none; font-size: 1.8rem; cursor: pointer; padding: 12px; border-radius: 50%;"><i class="fas fa-chevron-left"></i></button>
        
        <img class="modal-content" id="modalImage" style="max-height: 100%; max-width: 100%; object-fit: contain;">
        <video class="modal-content" id="modalVideo" style="max-height: 100%; max-width: 100%; object-fit: contain; display: none;" controls></video>
        
        <button onclick="nextImg()" class="nav-btn" style="position: absolute; right: 20px; background: rgba(0,0,0,0.5); color: white; border: none; font-size: 1.8rem; cursor: pointer; padding: 12px; border-radius: 50%;"><i class="fas fa-chevron-right"></i></button>
    </div>
    <div id="caption" style="text-align: center; color: white; margin-top: 20px; font-family: var(--font-heading); font-size: 1.3rem;"></div>
    <div id="gallery-counter" style="text-align: center; color: var(--accent-light); margin-top: 10px;"></div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const cards = document.querySelectorAll('.portfolio-card.has-video');
    
    cards.forEach(card => {
        const video = card.querySelector('.portfolio-video');
        if (!video) return;
        
        const playVideo = () => {
            video.currentTime = 0;
            video.play().catch(() => {});
        };
        
        const pauseVideo = () => {
            video.pause();
            video.currentTime = 0;
        };
        
        card.addEventListener('mouseenter', () => playVideo());
        card.addEventListener('mouseleave', () => pauseVideo());
        
        card.addEventListener('touchstart', () => playVideo(), { passive: true });
        card.addEventListener('touchend', () => pauseVideo());
        card.addEventListener('touchmove', () => pauseVideo());
    });
});
</script>

<script>
let currentGallery = [];
let currentIndex = 0;

function openPortfolioGallery(images) {
    currentGallery = images;
    currentIndex = 0;
    updateModal();
    document.getElementById("imageModal").style.display = "block";
}

function updateModal() {
    const imgEl = document.getElementById("modalImage");
    const vidEl = document.getElementById("modalVideo");
    const captionEl = document.getElementById("caption");
    const counterEl = document.getElementById("gallery-counter");
    
    if (currentGallery.length > 0) {
        const item = currentGallery[currentIndex];
        const isVideo = item.type === 'video';
        
        if (isVideo) {
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
        
        captionEl.innerHTML = item.title;
        counterEl.innerHTML = (currentIndex + 1) + " / " + currentGallery.length;
        const btns = document.querySelectorAll(".nav-btn");
        btns.forEach(b => b.style.display = currentGallery.length > 1 ? "block" : "none");
    }
}

function nextImg() {
    const vidEl = document.getElementById("modalVideo");
    vidEl.pause();
    vidEl.src = '';
    
    currentIndex = (currentIndex + 1) % currentGallery.length;
    updateModal();
}

function prevImg() {
    const vidEl = document.getElementById("modalVideo");
    vidEl.pause();
    vidEl.src = '';
    
    currentIndex = (currentIndex - 1 + currentGallery.length) % currentGallery.length;
    updateModal();
}

function closeGalleryModal() {
    const vidEl = document.getElementById("modalVideo");
    vidEl.pause();
    vidEl.src = '';
    document.getElementById("modalImage").style.display = "block";
    document.getElementById("modalVideo").style.display = "none";
    document.getElementById("imageModal").style.display = "none";
}

document.addEventListener('keydown', (e) => {
    if (document.getElementById("imageModal").style.display === "block") {
        if (e.key === "ArrowRight") nextImg();
        if (e.key === "ArrowLeft") prevImg();
        if (e.key === "Escape") closeGalleryModal();
    }
});
</script>
