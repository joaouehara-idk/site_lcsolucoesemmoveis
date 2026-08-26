<?php
require_once ROOT_PATH . '/includes/config.php';
$page_title = 'Configurador 3D de Móveis Planejados | LC Soluções em Móveis';
$page_description = 'Configure seu móvel planejado em 3D. Escolha tipo, medidas, materiais, acabamentos e ferragens com visualização em tempo real.';
$breadcrumb_atual = 'Configurador 3D';
require_once ROOT_PATH . '/includes/head.php';
require_once ROOT_PATH . '/includes/header.php';
?>
<link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/configurator.css">
<section class="configurator-page">
    <div class="container">
        <div class="config-header">
            <span class="eyebrow">Ferramenta gratuita</span>
            <h1>Configure seu móvel e veja em <span>3D</span></h1>
            <p class="config-subtitle">Escolha tipo, medidas, materiais e ferragens. Visualize em tempo real e solicite orçamento sem compromisso.</p>
        </div>
        <div class="config-layout">
            <div class="config-sidebar">
                <div class="sidebar-content">
                    <div class="step-title" id="cfgTypeTitle">Guarda-roupa planejado</div>
                    <p class="muted">Arraste para girar &middot; Scroll para zoom</p>

                    <div class="config-step">
                        <div class="step-label">Tipo de móvel</div>
                        <select id="cfgType" class="form-input">
                            <option value="guarda_roupa">Guarda-roupa</option>
                            <option value="closet">Closet</option>
                            <option value="comoda">Cômoda</option>
                            <option value="cozinha_base">Armário Cozinha Base</option>
                            <option value="cozinha_aereo">Armário Cozinha Aéreo</option>
                            <option value="estante">Estante</option>
                            <option value="painel_tv">Painel TV</option>
                            <option value="nicho">Nicho</option>
                        </select>
                    </div>

                    <div class="config-step">
                        <div class="step-label">Medidas (cm)</div>
                        <div class="dim-grid">
                            <div class="dim-field"><label for="cfgWidth">Largura</label><div class="dim-input-wrap"><input type="number" id="cfgWidth" min="40" max="400" step="1" value="180"><span class="dim-unit">cm</span></div></div>
                            <div class="dim-field"><label for="cfgHeight">Altura</label><div class="dim-input-wrap"><input type="number" id="cfgHeight" min="30" max="300" step="1" value="220"><span class="dim-unit">cm</span></div></div>
                            <div class="dim-field"><label for="cfgDepth">Profundidade</label><div class="dim-input-wrap"><input type="number" id="cfgDepth" min="20" max="80" step="1" value="55"><span class="dim-unit">cm</span></div></div>
                        </div>
                        <div class="cfg-hint" id="cfgHint">Ideal para quartos: 120–240 cm de largura.</div>
                    </div>

                    <div class="config-step">
                        <div class="step-label">Cor / Material</div>
                        <div class="swatch-section">
                            <div class="swatch-row" id="colorGrid"></div>
                        </div>
                        <div class="custom-color-row">
                            <label for="cfgCustom">Cor personalizada</label>
                            <input type="color" id="cfgCustom" value="#c8a87c">
                        </div>
                    </div>

                    <div class="config-step">
                        <div class="step-label">Acabamento</div>
                        <select id="cfgFinish" class="form-input">
                            <option value="melamina">Melamina</option>
                            <option value="liso">Liso (fosco)</option>
                            <option value="texturizado">Texturizado (madeira)</option>
                            <option value="laca">Laca (brilhante)</option>
                        </select>
                    </div>

                    <div class="config-step" id="stepDoors">
                        <div class="step-label">Portas</div>
                        <div class="struct-row"><label>Quantidade</label><div class="struct-counter"><button class="sc-btn" id="btnDoorsLess">&minus;</button><span class="sc-val" id="valDoors">2</span><button class="sc-btn" id="btnDoorsMore">+</button></div></div>
                    </div>

                    <div class="config-step" id="stepDrawers">
                        <div class="step-label">Gavetas</div>
                        <div class="struct-row"><label>Quantidade</label><div class="struct-counter"><button class="sc-btn" id="btnDrawersLess">&minus;</button><span class="sc-val" id="valDrawers">0</span><button class="sc-btn" id="btnDrawersMore">+</button></div></div>
                    </div>

                    <div class="config-step" id="stepShelves">
                        <div class="step-label">Prateleiras</div>
                        <div class="struct-row"><label>Quantidade</label><div class="struct-counter"><button class="sc-btn" id="btnShelvesLess">&minus;</button><span class="sc-val" id="valShelves">3</span><button class="sc-btn" id="btnShelvesMore">+</button></div></div>
                    </div>

                    <div class="config-step" id="stepDividers">
                        <div class="step-label">Divisórias</div>
                        <div class="struct-row"><label>Quantidade</label><div class="struct-counter"><button class="sc-btn" id="btnDividersLess">&minus;</button><span class="sc-val" id="valDividers">0</span><button class="sc-btn" id="btnDividersMore">+</button></div></div>
                    </div>

                    <div class="config-step" id="stepInterior">
                        <label class="cfg-checkbox"><input type="checkbox" id="cfgOpen"> Ver interior (portas abertas)</label>
                    </div>

                    <div class="config-step" id="stepHardware">
                        <div class="step-label">Puxador</div>
                        <div class="hw-section">
                            <div class="hw-options" id="hardwareGrid"></div>
                        </div>
                    </div>

                    <div class="config-step">
                        <label class="cfg-checkbox"><input type="checkbox" id="cfgLED"> Adicionar LED interno</label>
                    </div>

                    <div class="config-summary">
                        <div class="summary-text" id="cfgSummary"></div>
                    </div>

                    <!-- Engineering Panel -->
                    <div class="engineering-panel" id="engineeringPanel">
                        <div class="eng-section">
                            <div class="eng-header">
                                <span class="eng-title">Lista de Peças</span>
                                <span class="eng-count" id="partsCount">0 itens</span>
                            </div>
                            <div class="eng-list" id="partsList"></div>
                        </div>
                        <div class="eng-section">
                            <div class="eng-header">
                                <span class="eng-title">Ferragens</span>
                                <span class="eng-count" id="hardwareCount">0 itens</span>
                            </div>
                            <div class="eng-list" id="hardwareList"></div>
                        </div>
                        <div class="eng-section eng-total">
                            <span class="eng-label">Área total de MDF:</span>
                            <span class="eng-value" id="totalArea">0 m²</span>
                        </div>
                    </div>

                    <p style="font-size:0.72rem;color:var(--ink-muted);margin-top:8px;font-style:italic;">As cores exibidas são representações digitais. A especificação final deve considerar a amostra física.</p>

                    <div class="config-actions">
                        <button class="cfg-btn cfg-btn-primary" id="btnOrcamento"><i class="fab fa-whatsapp"></i> Pedir orçamento</button>
                        <div class="cfg-btn-row">
                            <button class="cfg-btn" id="btnSalvar">Salvar</button>
                            <button class="cfg-btn" id="btnLink">Copiar link</button>
                            <button class="cfg-btn" id="btnImg">Baixar imagem</button>
                            <button class="cfg-btn" id="btnLimpar">Limpar</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="config-viewport" id="configViewport">
                <div class="viewport-toolbar">
                    <span class="viewport-label">Visualização 3D</span>
                    <div class="viewport-controls">
                        <button class="vp-btn" id="btnToggleDoors" title="Abrir/Fechar portas"><i class="fas fa-door-open"></i></button>
                        <button class="vp-btn" id="btnToggleDrawers" title="Abrir/Fechar gavetas"><i class="fas fa-inbox"></i></button>
                        <span class="vp-divider"></span>
                        <button class="vp-btn" id="btnResetCamera" title="Resetar câmera"><i class="fas fa-sync-alt"></i></button>
                        <button class="vp-btn" id="btnFront" title="Vista frontal"><i class="fas fa-square"></i></button>
                        <button class="vp-btn" id="btnSide" title="Vista lateral"><i class="fas fa-grip-lines-vertical"></i></button>
                        <button class="vp-btn" id="btnTop" title="Vista superior"><i class="fas fa-caret-up"></i></button>
                    </div>
                </div>
                <div class="viewport-canvas-wrap" id="canvasWrap">
                    <canvas id="cfgCanvas"></canvas>
                    <div class="viewport-hint"><i class="fas fa-hand-pointer"></i> Arraste para girar &middot; Scroll para zoom &middot; <span style="color:var(--ink);">Clique nas portas/gavetas para abrir</span></div>
                </div>
            </div>
        </div>
    </div>
    <div class="cfg-toast" id="cfgToast"></div>
</section>
<!-- Three.js r128 (compatible with OrbitControls global) -->
<script src="https://cdn.jsdelivr.net/npm/three@0.128.0/build/three.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/controls/OrbitControls.js"></script>
<!-- Furniture Engine (Parametric CAD / Manufacturing Core) -->
<script src="<?php echo BASE_URL; ?>/assets/js/configurador/furniture-engine.js"></script>
<script src="<?php echo BASE_URL; ?>/assets/js/configurador/furniture-renderer-adapter.js"></script>
<!-- Configurator Engine -->
<script src="<?php echo BASE_URL; ?>/assets/js/configurador/engine.js"></script>
<?php require_once ROOT_PATH . '/includes/footer.php'; ?>
