/**
 * Configurator App — Main entry point.
 * Wires together the 3D scene, furniture builder, materials, UI, and services.
 *
 * Loaded as an ES6 module. Expects Three.js r128+ and OrbitControls
 * to be available as imports.
 */
import * as THREE from 'https://cdn.jsdelivr.net/npm/three@0.128.0/build/three.module.js';
import { OrbitControls } from 'https://cdn.jsdelivr.net/npm/three@0.128.0/examples/jsm/controls/OrbitControls.js';
import { GLTFLoader } from 'https://cdn.jsdelivr.net/npm/three@0.128.0/examples/jsm/loaders/GLTFLoader.js';

import { DebugLogger } from './utils/debug.js';
import { GeometryUtils } from './utils/geometry.js';
import { SceneManager } from './3d/scene-manager.js';
import { MaterialFactory } from './3d/materials.js';
import { FurnitureBuilder } from './3d/furniture-builder.js';
import { ModelLoader } from './3d/loader.js';
import { UIManager } from './ui/ui-manager.js';
import { ApiService } from './services/api-service.js';
import { ProjectService } from './services/project-service.js';

import { FURNITURE_CATALOG, FURNITURE_CATEGORIES } from './catalog/furniture.js';
import { COLOR_PRESETS, FINISH_TYPES, MATERIAL_BRANDS } from './catalog/materials.js';
import { HANDLE_TYPES, LED_OPTIONS } from './catalog/hardware.js';

class ConfiguratorApp {
    constructor() {
        this.debug = new DebugLogger('cfgDebug');
        this.initialized = false;
        this.config = this._getDefaultConfig();
        this.price = 0;
    }

    _getDefaultConfig() {
        return {
            typeKey: 'guarda_roupa',
            W: 180,
            H: 220,
            D: 55,
            doors: 2,
            shelves: 3,
            drawers: 0,
            dividerCount: 0,
            doorColor: 'caramelo',
            customColor: null,
            finish: 'melamina',
            materialBrand: 'generic',
            materialLine: 'lc_padrao',
            handleType: 'alca',
            handleColor: '#6B5340',
            hingeType: 'padrao',
            slideType: 'telescopica',
            ledEnabled: false,
            interiorOpen: false,
            mounted: false,
            typeLabel: 'Guarda-roupa planejado',
            colorLabel: 'Caramelo',
            finishLabel: 'Melamina',
            handleLabel: 'Alça',
            hint: 'Ideal para quartos: 120–240 cm de largura.',
        };
    }

    async init() {
        this.debug.step('[App] Initializing Configurator 3D...');

        try {
            this.ui = new UIManager(this.debug);
            this.sceneManager = new SceneManager(
                document.getElementById('cfgCanvas'),
                this.debug
            );
            this.materialFactory = new MaterialFactory(THREE, this.debug);
            this.modelLoader = new ModelLoader(THREE, this.debug);
            this.builder = new FurnitureBuilder(
                THREE,
                this.sceneManager,
                this.materialFactory,
                this.debug
            );
            this.apiService = new ApiService(this.debug);
            this.projectService = new ProjectService(this.apiService, this.debug);

            const success = this.sceneManager.init(OrbitControls, THREE);
            if (!success) {
                this.showError('Não foi possível inicializar o visualizador 3D.');
                return;
            }

            this.ui.init();
            this.ui.onConfigChange = (event) => this.handleUIChange(event);

            this.sceneManager.animate(() => {
                const led = this.builder.components.get('led');
                if (led && led.userData._animateFn) {
                    led.userData._animateFn();
                }
            });

            window.addEventListener('resize', () => this.sceneManager.onResize());

            this.loadInitialConfig();
            this.buildFurniture();
            this.updatePrice();
            this.updateSummary();

            this.bindActionButtons();

            this.initialized = true;
            this.debug.step('[App] Configurator ready ✓');

        } catch (err) {
            this.debug.error('Fatal initialization error', err);
            this.showError('Ocorreu um erro ao carregar o configurador.');
        }
    }

    loadInitialConfig() {
        const fromHash = this.projectService.loadFromHash();
        if (fromHash) {
            this.config = { ...this.config, ...fromHash };
            this.syncTypeDefaults();
            this.ui.updateUIFromConfig(this.config);
            this.debug.step('[App] Loaded project from URL');
            return;
        }

        const fromStorage = this.projectService.loadLocal();
        if (fromStorage) {
            this.config = { ...this.config, ...fromStorage };
            this.syncTypeDefaults();
            this.ui.updateUIFromConfig(this.config);
            this.debug.step('[App] Loaded project from localStorage');
        }
    }

    syncTypeDefaults() {
        const typeDef = FURNITURE_CATALOG[this.config.typeKey];
        if (!typeDef) return;
        this.config.mounted = typeDef.mounted;
        this.config.typeLabel = typeDef.label;
        this.config.hint = typeDef.description;
        this.ui.updateStepVisibility(this.config.typeKey, typeDef);
    }

    handleUIChange(event) {
        if (!this.initialized) return;

        if (event.type === 'config') {
            this.readConfigFromUI();
            this.buildFurniture();
            this.updatePrice();
            this.updateSummary();
        } else if (event.type === 'counter') {
            const maxDef = FURNITURE_CATALOG[this.config.typeKey];
            const maxKey = event.counter === 'doors' ? 'maxDoors' :
                          event.counter === 'shelves' ? 'maxShelves' : 'maxDrawers';
            const max = maxDef?.limits?.[maxKey] || 0;
            const current = this.config[event.counter] || 0;
            const next = current + event.delta;
            if (next >= 0 && next <= max) {
                this.config[event.counter] = next;
                this.ui.updateUIFromConfig(this.config);
                this.buildFurniture();
                this.updatePrice();
                this.updateSummary();
            }
        } else if (event.type === 'camera') {
            this.sceneManager.setCameraPreset(event.preset);
        }
    }

    readConfigFromUI() {
        const uiCfg = this.ui.getConfigFromUI();
        this.config = { ...this.config, ...uiCfg };
        this.syncTypeDefaults();

        const colorDef = COLOR_PRESETS.find(c => c.id === this.config.doorColor);
        this.config.colorLabel = colorDef?.name || this.config.doorColor;

        const finishDef = FINISH_TYPES.find(f => f.id === this.config.finish);
        this.config.finishLabel = finishDef?.name || this.config.finish;

        const handleDef = HANDLE_TYPES.find(h => h.id === this.config.handleType);
        this.config.handleLabel = handleDef?.name || this.config.handleType;
    }

    buildFurniture() {
        this.builder.build({
            typeKey: this.config.typeKey,
            W: this.config.W,
            H: this.config.H,
            D: this.config.D,
            doors: this.config.doors,
            shelves: this.config.shelves,
            drawers: this.config.drawers,
            dividerCount: this.config.dividerCount || 0,
            mounted: this.config.mounted,
            doorColor: this.config.customColor || this._getColorHex(this.config.doorColor),
            finish: this.config.finish,
            hasGrain: this.config.finish === 'texturizado' || this.config.finish === 'madeira',
            handleType: this.config.handleType,
            handleColor: this.config.handleColor || '#6B5340',
            ledEnabled: this.config.ledEnabled,
            interiorOpen: this.config.interiorOpen,
        });

        this.builder.setOpenDoors(this.config.interiorOpen);
    }

    _getColorHex(colorId) {
        const c = COLOR_PRESETS.find(p => p.id === colorId);
        return c?.hex || '#C8A87C';
    }

    updatePrice() {
        const areaM2 = (this.config.W * this.config.H) / 10000;
        const base = 420;
        const finishMult = { melamina: 1.0, liso: 1.05, texturizado: 1.1, laca: 1.4, madeira: 1.15, acetinado: 1.25 }[this.config.finish] || 1;
        const colorMult = (['preto', 'wenge', 'grafite'].includes(this.config.doorColor)) ? 1.12 : 1.0;
        const handlePrice = { alca: 25, botao: 15, cava: 35, perfil: 45, concha: 30, embutido: 20, nenhum: 0 }[this.config.handleType] || 0;
        const mods = this.config.doors * 45 + this.config.shelves * 18 + this.config.drawers * 85 + (this.config.ledEnabled ? 120 : 0);

        this.price = Math.round(areaM2 * base * finishMult * colorMult + handlePrice + mods);
        this.ui.updatePrice(this.price);
    }

    updateSummary() {
        const summary = `${this.config.typeLabel} · ${this.config.W}L × ${this.config.H}A × ${this.config.D}P · ${this.config.colorLabel} · ${this.config.finishLabel}`;
        this.ui.updateSummary(summary);
    }

    bindActionButtons() {
        const waNumber = '556732537898';

        const btnOrcamento = document.getElementById('btnOrcamento');
        if (btnOrcamento) {
            btnOrcamento.addEventListener('click', () => {
                const msg = this.projectService.generateWhatsAppMessage(this.config, this.price);
                window.open(`https://wa.me/${waNumber}?text=${msg}`, '_blank');
            });
        }

        const btnSalvar = document.getElementById('btnSalvar');
        if (btnSalvar) {
            btnSalvar.addEventListener('click', () => {
                const ok = this.projectService.saveLocal(this.config);
                this.ui.showToast(ok ? 'Projeto salvo neste navegador' : 'Não foi possível salvar');
            });
        }

        const btnLink = document.getElementById('btnLink');
        if (btnLink) {
            btnLink.addEventListener('click', () => {
                const url = this.projectService.generateShareUrl(this.config);
                try { history.replaceState(null, '', url); } catch(e) {}
                if (navigator.clipboard) {
                    navigator.clipboard.writeText(url).then(
                        () => this.ui.showToast('Link copiado!'),
                        () => this.ui.showToast('Link na barra de endereço')
                    );
                } else {
                    this.ui.showToast('Link na barra de endereço');
                }
            });
        }

        const btnImg = document.getElementById('btnImg');
        if (btnImg) {
            btnImg.addEventListener('click', () => {
                try {
                    const canvas = document.getElementById('cfgCanvas');
                    const url = canvas.toDataURL('image/png');
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = 'projeto-lc-moveis.png';
                    document.body.appendChild(a);
                    a.click();
                    document.body.removeChild(a);
                    this.ui.showToast('Imagem baixada');
                } catch(e) {
                    this.ui.showToast('Erro ao gerar imagem');
                }
            });
        }

        const btnLimpar = document.getElementById('btnLimpar');
        if (btnLimpar) {
            btnLimpar.addEventListener('click', () => {
                this.projectService.clearLocal();
                try { history.replaceState(null, '', location.pathname); } catch(e) {}
                this.config = this._getDefaultConfig();
                this.ui.updateUIFromConfig(this.config);
                this.buildFurniture();
                this.updatePrice();
                this.updateSummary();
                this.ui.showToast('Configuração reiniciada');
            });
        }
    }

    showError(msg) {
        const wrap = document.getElementById('canvasWrap');
        if (wrap) {
            wrap.innerHTML = `
                <div style="display:flex;flex-direction:column;align-items:center;justify-content:center;height:100%;padding:40px;text-align:center;color:var(--ink-muted);">
                    <i class="fas fa-exclamation-triangle" style="font-size:2rem;margin-bottom:16px;color:var(--error);"></i>
                    <p style="font-size:1rem;margin-bottom:12px;">${msg}</p>
                    <button onclick="location.reload()" style="padding:10px 20px;background:var(--ink);color:#fff;border:none;border-radius:8px;cursor:pointer;font-size:0.9rem;">
                        Tentar novamente
                    </button>
                </div>
            `;
        }
    }
}

const app = new ConfiguratorApp();
app.init();
window.__configuratorApp = app;
