/**
 * UI Manager — Coordinates all UI modules and handles the step workflow,
 * camera controls, save/share/export, and WhatsApp integration.
 */
import { SceneManager } from '../3d/scene-manager.js';
import { MaterialFactory } from '../3d/materials.js';
import { FurnitureBuilder } from '../3d/furniture-builder.js';
import { ModelLoader } from '../3d/loader.js';
import { DebugLogger } from '../utils/debug.js';
import { GeometryUtils } from '../utils/geometry.js';
import { MaterialSelector } from './material-selector.js';
import { HardwareSelector } from './hardware-selector.js';
import { DimensionPanel } from './dimension-panel.js';
import { ApiService } from '../services/api-service.js';
import { ProjectService } from '../services/project-service.js';
import {
    FURNITURE_CATALOG,
    getFurnitureType,
} from '../catalog/furniture.js';
import {
    FINISH_TYPES,
    COLOR_PRESETS,
    COLOR_APPROX_WARNING,
} from '../catalog/materials.js';
import {
    HANDLE_TYPES,
    LED_OPTIONS,
    getHandleById,
} from '../catalog/hardware.js';

export class UIManager {
    constructor() {
        this.THREE = null;
        this.OrbitControls = null;

        this.debug = new DebugLogger();
        this.api = new ApiService(this.debug);
        this.projectService = new ProjectService(this.api, this.debug);

        this.sceneManager = null;
        this.materialFactory = null;
        this.furnitureBuilder = null;
        this.modelLoader = null;

        this.dimensionPanel = null;
        this.materialSelector = null;
        this.hardwareSelector = null;

        this.project = null;
        this.isLoading = false;
    }

    async init(THREE, OrbitControls) {
        this.THREE = THREE;
        this.OrbitControls = OrbitControls;
        this.debug.step('[3D] Configurator startup...');

        this.project = this.projectService.loadFromStorage();

        try {
            this.dimensionPanel = new DimensionPanel(
                this.debug, this.projectService, FURNITURE_CATALOG
            );
            this.dimensionPanel.loadFromProject(this.project);

            this.materialSelector = new MaterialSelector(this.debug, this.projectService);
            this.materialSelector.populate({});
            this.materialSelector.updateFromProject(this.project);

            this.hardwareSelector = new HardwareSelector(this.debug, this.projectService);
            this.hardwareSelector.populate({});
            this.hardwareSelector.updateFromProject(this.project);

            this.debug.step('[UI] UI modules initialized');
        } catch (err) {
            this.debug.error('[UI] UI module init failed', err);
        }

        const canvas = document.getElementById('cfgCanvas');
        if (!canvas) {
            this.debug.error('[3D] Canvas element not found');
            return this._renderFatalError('Canvas 3D não encontrado');
        }

        this.sceneManager = new SceneManager(canvas, this.debug);
        const ok = this.sceneManager.init(OrbitControls, THREE);
        if (!ok) {
            this.debug.error('[3D] Scene initialization failed');
            return this._renderFatalError('Não foi possível inicializar o visualizador 3D');
        }

        this.materialFactory = new MaterialFactory(THREE, this.debug);
        this.furnitureBuilder = new FurnitureBuilder(THREE, this.sceneManager, this.materialFactory, this.debug);
        this.modelLoader = new ModelLoader(THREE, this.debug);

        this._bindGlobalEvents();
        this._bindCameraButtons();
        this._bindActionButtons();

        this._buildFurnitureFromProject();

        this.debug.step('[3D] Rendering started');
        this.sceneManager.animate(() => {
            if (this.furnitureBuilder) {
                this.furnitureBuilder._animateLED && this.furnitureBuilder._animateLED();
            }
        });

        this.debug.step('[3D] Configurator ready');
    }

    _buildFurnitureFromProject() {
        const project = this.projectService.getCurrent();
        const config = {
            typeKey: project.furnitureType,
            W: project.dimensions.W,
            H: project.dimensions.H,
            D: project.dimensions.D,
            doors: project.structure.doors,
            shelves: project.structure.shelves,
            drawers: project.structure.drawers,
            mounted: project.structure.mounted,
            doorStyle: project.structure.doorStyle,
            doorColor: project.finish.color,
            customColor: project.finish.customColor,
            finish: project.finish.materialFinish,
            hasGrain: project.finish.materialFinish === 'texturizado' || project.finish.materialFinish === 'madeira',
            sideGrainDirection: 'vertical',
            topGrainDirection: 'horizontal',
            backColor: project.finish.backColor,
            handleType: project.hardware.handleType,
            handleColor: project.hardware.handleColor,
            hardwareColor: project.hardware.hardwareColor,
            ledEnabled: project.extras.led,
            ledType: project.extras.ledType,
            doorsOpen: project.view.doorsOpen,
            dividerCount: project.structure.dividers,
            maxDoors: this._getMaxDoors(),
            maxShelves: this._getMaxShelves(),
            maxDrawers: this._getMaxDrawers(),
            maxDividers: this._getMaxDividers(),
        };

        const furnitureType = getFurnitureType(config.typeKey);
        if (furnitureType) {
            config.maxDoors = furnitureType.limits.maxDoors;
            config.maxShelves = furnitureType.limits.maxShelves;
            config.maxDrawers = furnitureType.limits.maxDrawers;
            config.maxDividers = furnitureType.limits.maxDividers;
        }

        if (!config.doors || config.doors < 0) config.doors = 0;
        if (!config.shelves || config.shelves < 0) config.shelves = 0;
        if (!config.drawers || config.drawers < 0) config.drawers = 0;
        if (!config.dividerCount || config.dividerCount < 0) config.dividerCount = 0;

        this.furnitureBuilder.build(config);

        if (config.doorsOpen) {
            this.furnitureBuilder.setOpenDoors(true);
        }

        this._updateSummary();
    }

    _getMaxDoors() {
        const project = this.projectService.getCurrent();
        const ft = getFurnitureType(project.furnitureType);
        return ft ? ft.limits.maxDoors : 8;
    }

    _getMaxShelves() {
        const project = this.projectService.getCurrent();
        const ft = getFurnitureType(project.furnitureType);
        return ft ? ft.limits.maxShelves : 10;
    }

    _getMaxDrawers() {
        const project = this.projectService.getCurrent();
        const ft = getFurnitureType(project.furnitureType);
        return ft ? ft.limits.maxDrawers : 6;
    }

    _getMaxDividers() {
        const project = this.projectService.getCurrent();
        const ft = getFurnitureType(project.furnitureType);
        return ft ? ft.limits.maxDividers : 4;
    }

    _bindGlobalEvents() {
        window.addEventListener('configChanged', (e) => {
            const section = e.detail?.section;
            if (section === 'type' || section === 'dimensions' || section === 'structure') {
                this._buildFurnitureFromProject();
            } else if (section === 'material') {
                this._buildFurnitureFromProject();
            } else if (section === 'hardware') {
                this._buildFurnitureFromProject();
            } else if (section === 'extras') {
                this._buildFurnitureFromProject();
            } else if (section === 'view') {
                const project = this.projectService.getCurrent();
                if (this.furnitureBuilder) {
                    this.furnitureBuilder.setOpenDoors(project.view.doorsOpen);
                }
                this._updateSummary();
            }
        });

        window.addEventListener('resize', () => {
            if (this.sceneManager) this.sceneManager.onResize();
        });

        const hash = location.hash.match(/p=([^&]+)/);
        if (hash) {
            const project = this.projectService.deserializeFromUrl(decodeURIComponent(hash[1]));
            if (project) {
                this.projectService.saveToStorage(project);
                this._rebuildAll();
            }
        }
    }

    _rebuildAll() {
        this.projectService.current = this.projectService.getCurrent();
        if (this.dimensionPanel) this.dimensionPanel.loadFromProject(this.projectService.getCurrent());
        if (this.materialSelector) this.materialSelector.updateFromProject(this.projectService.getCurrent());
        if (this.hardwareSelector) this.hardwareSelector.updateFromProject(this.projectService.getCurrent());
        this._buildFurnitureFromProject();
    }

    _bindCameraButtons() {
        const buttons = {
            btnResetCamera: 'reset',
            btnFront: 'front',
            btnSide: 'side',
            btnTop: 'top',
        };

        for (const [id, preset] of Object.entries(buttons)) {
            const btn = document.getElementById(id);
            if (btn) {
                btn.addEventListener('click', () => {
                    this.sceneManager.setCameraPreset(preset);
                    const project = this.projectService.getCurrent();
                    project.view.cameraPreset = preset;
                    this.projectService.saveToStorage(project);
                });
            }
        }
    }

    _bindActionButtons() {
        const orcBtn = document.getElementById('btnOrcamento');
        if (orcBtn) {
            orcBtn.addEventListener('click', () => {
                const project = this.projectService.getCurrent();
                const msg = this.projectService.generateWhatsAppMessage(project);
                window.open('https://wa.me/556732537898?text=' + msg, '_blank');
            });
        }

        const saveBtn = document.getElementById('btnSalvar');
        if (saveBtn) {
            saveBtn.addEventListener('click', () => {
                const project = this.projectService.getCurrent();
                this.projectService.saveToStorage(project);
                this._showToast('Projeto salvo neste navegador');
            });
        }

        const linkBtn = document.getElementById('btnLink');
        if (linkBtn) {
            linkBtn.addEventListener('click', () => {
                const project = this.projectService.getCurrent();
                const encoded = this.projectService.serializeToUrl(project);
                const url = location.origin + location.pathname + '#p=' + encodeURIComponent(encoded);
                try {
                    history.replaceState(null, '', url);
                    if (navigator.clipboard && navigator.clipboard.writeText) {
                        navigator.clipboard.writeText(url).then(() => {
                            this._showToast('Link copiado!');
                        }, () => {
                            this._showToast('Link na barra de endereço');
                        });
                    } else {
                        this._showToast('Link na barra de endereço');
                    }
                } catch (e) {
                    this._showToast('Link na barra de endereço');
                }
            });
        }

        const imgBtn = document.getElementById('btnImg');
        if (imgBtn) {
            imgBtn.addEventListener('click', () => {
                try {
                    const url = this.sceneManager.renderer.domElement.toDataURL('image/png');
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = 'projeto-lc-moveis.png';
                    document.body.appendChild(a);
                    a.click();
                    document.body.removeChild(a);
                    this._showToast('Imagem baixada');
                } catch (e) {
                    this._showToast('Erro ao gerar imagem');
                }
            });
        }

        const clearBtn = document.getElementById('btnLimpar');
        if (clearBtn) {
            clearBtn.addEventListener('click', () => {
                this.projectService.clearStorage();
                this.projectService.current = this.projectService.createDefault();
                this._rebuildAll();
                this._showToast('Configuração reiniciada');
            });
        }
    }

    _updateSummary() {
        const project = this.projectService.getCurrent();
        const priceEl = document.getElementById('cfgPrice');
        const summaryEl = document.getElementById('cfgSummary');

        if (priceEl) {
            const areaM2 = (project.dimensions.W * project.dimensions.H) / 10000;
            const base = 420;
            const fMult = { melamina: 1.0, liso: 1.05, texturizado: 1.1, laca: 1.4, madeira: 1.1, acetinado: 1.03 }[project.finish.materialFinish] || 1.0;
            const cMult = (project.finish.color === 'preto' || project.finish.color === 'grafite' || project.finish.color === 'nogueira' || project.finish.color === 'wenge') ? 1.12 : 1.0;
            let hw = 0;
            if (project.hardware.handleType === 'alca') hw = project.structure.doors * 25;
            else if (project.hardware.handleType === 'botao') hw = project.structure.doors * 15;
            else if (project.hardware.handleType === 'cava') hw = project.structure.doors * 35;
            const mods = project.structure.doors * 45 + project.structure.shelves * 18 + project.structure.drawers * 85;
            const price = Math.round(areaM2 * base * fMult * cMult + hw + mods);
            priceEl.textContent = 'A partir de R$ ' + price.toLocaleString('pt-BR');
        }

        if (summaryEl) {
            const colorLabel = COLOR_PRESETS.find(c => c.id === project.finish.color);
            const colorName = (project.finish.customColor || colorLabel?.name || project.finish.color || 'Padrão');
            const finishLabel = FINISH_TYPES.find(f => f.id === project.finish.materialFinish);
            const handle = HANDLE_TYPES.find(h => h.id === project.hardware.handleType);
            const d = project.dimensions;
            summaryEl.textContent =
                (FURNITURE_CATALOG[project.furnitureType]?.label || 'Móvel') +
                ' · ' + d.W + 'L x ' + d.H + 'A x ' + d.D + 'P · ' +
                colorName + ' · ' +
                (finishLabel?.name || project.finish.materialFinish);
            if (handle) summaryEl.textContent += ' · ' + handle.name;
        }
    }

    _showToast(msg) {
        const toast = document.getElementById('cfgToast');
        if (toast) {
            toast.textContent = msg;
            toast.classList.add('show');
            setTimeout(() => toast.classList.remove('show'), 1600);
        }
    }

    _renderFatalError(message) {
        const wrap = document.getElementById('canvasWrap');
        if (wrap) {
            wrap.innerHTML = `
                <div class="cfg-error-overlay" style="
                    display:flex;align-items:center;justify-content:center;
                    flex-direction:column;height:100%;background:var(--surface);
                    border:var(--border);border-radius:var(--radius-md);padding:40px;text-align:center;
                ">
                    <i class="fas fa-cube" style="font-size:3rem;color:var(--ink-muted);margin-bottom:16px;"></i>
                    <h3 style="color:var(--ink);margin-bottom:10px;">${message}</h3>
                    <p style="color:var(--ink-muted);margin-bottom:20px;font-size:0.9rem;max-width:400px;line-height:1.6;">
                        O visualizador 3D não pôde ser carregado. Isso pode acontecer se o navegador não
                        suportar WebGL ou se houver uma falha no carregamento dos recursos.
                    </p>
                    <button onclick="location.reload()" class="retry-btn" style="
                        padding:12px 28px;background:var(--ink);color:var(--surface);
                        border:none;border-radius:var(--radius-sm);font-weight:600;
                        cursor:pointer;font-family:var(--font-body);
                    ">Tentar novamente</button>
                </div>
            `;
        }
    }

    dispose() {
        if (this.sceneManager) this.sceneManager.stopAnimate();
        if (this.materialFactory) this.materialFactory.disposeAll();
        if (this.furnitureBuilder && this.sceneManager) {
            this.furnitureBuilder.clear();
        }
    }
}
