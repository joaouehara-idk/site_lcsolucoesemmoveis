/**
 * Configurator 3D - Main Application
 * Uses global LC namespace for maximum browser compatibility.
 * Requires: THREE (global), THREE.OrbitControls, THREE.GLTFLoader
 */
(function() {
    'use strict';

    // Namespace
    window.LC = window.LC || {};
    window.LC.Configurator = window.LC.Configurator || {};

    function ConfiguratorApp() {
        this.debug = null;
        this.initialized = false;
        this.config = this._getDefaultConfig();
        this.price = 0;
    }

    ConfiguratorApp.prototype._getDefaultConfig = function() {
        return {
            typeKey: 'guarda_roupa',
            W: 180, H: 220, D: 55,
            doors: 2, shelves: 3, drawers: 0, dividerCount: 0,
            doorColor: 'caramelo', customColor: null, finish: 'melamina',
            materialBrand: 'generic', materialLine: 'lc_padrao',
            handleType: 'alca', handleColor: '#6B5340',
            hingeType: 'padrao', slideType: 'telescopica',
            ledEnabled: false, interiorOpen: false, mounted: false,
            typeLabel: 'Guarda-roupa planejado',
            colorLabel: 'Caramelo', finishLabel: 'Melamina', handleLabel: 'Alça',
            hint: 'Ideal para quartos: 120–240 cm de largura.'
        };
    };

    ConfiguratorApp.prototype.init = function() {
        var self = this;
        self.debug = new window.LC.Configurator.DebugLogger('cfgDebug');
        self.debug.step('[App] Initializing Configurator 3D...');

        try {
            if (typeof THREE === 'undefined') {
                self.showError('Three.js não carregou. Verifique sua conexão.');
                return;
            }

            self.debug.step('[App] THREE.js detected: v' + THREE.REVISION);

            self.ui = new window.LC.Configurator.UIManager(self.debug);
            self.sceneManager = new window.LC.Configurator.SceneManager(document.getElementById('cfgCanvas'), self.debug);
            self.materialFactory = new window.LC.Configurator.MaterialFactory(THREE, self.debug, self.sceneManager.envMap);
            self.modelLoader = new window.LC.Configurator.ModelLoader(THREE, self.debug);
            self.apiService = new window.LC.Configurator.ApiService(self.debug);
            self.projectService = new window.LC.Configurator.ProjectService(self.apiService, self.debug);

            var OrbitControlsClass = window.THREE && window.THREE.OrbitControls;
            if (!OrbitControlsClass) {
                self.debug.error('OrbitControls not found');
                self.showError('Controles 3D não disponíveis.');
                return;
            }

            var success = self.sceneManager.init(OrbitControlsClass, THREE);
            if (!success) {
                self.showError('Não foi possível inicializar o visualizador 3D.');
                return;
            }

            self.builder = new window.LC.Configurator.FurnitureBuilder(THREE, self.sceneManager, self.materialFactory, self.debug);

            self.ui.init();
            self.ui.onConfigChange = function(event) { self.handleUIChange(event); };

            self.sceneManager.animate(function() {
                var led = self.builder.components.get('led');
                if (led && led.userData._animateFn) {
                    led.userData._animateFn();
                }
            });

            window.addEventListener('resize', function() { self.sceneManager.onResize(); });

            self.loadInitialConfig();
            self.buildFurniture();
            self.updatePrice();
            self.updateSummary();
            self.bindActionButtons();
            self.bindClickInteraction();

            self.initialized = true;
            self.debug.step('[App] Configurator ready');

        } catch (err) {
            self.debug.error('Fatal initialization error', err);
            self.showError('Ocorreu um erro ao carregar o configurador: ' + err.message);
        }
    };

    ConfiguratorApp.prototype.loadInitialConfig = function() {
        var fromHash = this.projectService.loadFromHash();
        if (fromHash) {
            this.config = Object.assign({}, this.config, fromHash);
            this.syncTypeDefaults();
            this.ui.updateUIFromConfig(this.config);
            this.debug.step('[App] Loaded project from URL');
            return;
        }
        var fromStorage = this.projectService.loadLocal();
        if (fromStorage) {
            this.config = Object.assign({}, this.config, fromStorage);
            this.syncTypeDefaults();
            this.ui.updateUIFromConfig(this.config);
            this.debug.step('[App] Loaded project from localStorage');
        }
    };

    ConfiguratorApp.prototype.syncTypeDefaults = function() {
        var typeDef = window.LC.Configurator.FURNITURE_CATALOG[this.config.typeKey];
        if (!typeDef) return;
        this.config.mounted = typeDef.mounted;
        this.config.typeLabel = typeDef.label;
        this.config.hint = typeDef.description;
        this.ui.updateStepVisibility(this.config.typeKey, typeDef);
    };

    ConfiguratorApp.prototype.handleUIChange = function(event) {
        if (!this.initialized) return;
        if (event.type === 'config') {
            this.readConfigFromUI();
            this.buildFurniture();
            this.updatePrice();
            this.updateSummary();
        } else if (event.type === 'counter') {
            var maxDef = window.LC.Configurator.FURNITURE_CATALOG[this.config.typeKey];
            var maxKey = event.counter === 'doors' ? 'maxDoors' :
                        event.counter === 'shelves' ? 'maxShelves' : 'maxDrawers';
            var max = (maxDef && maxDef.limits && maxDef.limits[maxKey]) || 0;
            var current = this.config[event.counter] || 0;
            var next = current + event.delta;
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
    };

    ConfiguratorApp.prototype.readConfigFromUI = function() {
        var uiCfg = this.ui.getConfigFromUI();
        this.config = Object.assign({}, this.config, uiCfg);
        this.syncTypeDefaults();

        var colorDef = this._findColor(this.config.doorColor);
        this.config.colorLabel = (colorDef && colorDef.name) || this.config.doorColor;

        var finishDef = this._findFinish(this.config.finish);
        this.config.finishLabel = (finishDef && finishDef.name) || this.config.finish;

        var handleDef = this._findHandle(this.config.handleType);
        this.config.handleLabel = (handleDef && handleDef.name) || this.config.handleType;
    };

    ConfiguratorApp.prototype._findColor = function(id) {
        var colors = window.LC.Configurator.COLOR_PRESETS;
        for (var i = 0; i < colors.length; i++) {
            if (colors[i].id === id) return colors[i];
        }
        return null;
    };

    ConfiguratorApp.prototype._findFinish = function(id) {
        var finishes = window.LC.Configurator.FINISH_TYPES;
        for (var i = 0; i < finishes.length; i++) {
            if (finishes[i].id === id) return finishes[i];
        }
        return null;
    };

    ConfiguratorApp.prototype._findHandle = function(id) {
        var handles = window.LC.Configurator.HANDLE_TYPES;
        for (var i = 0; i < handles.length; i++) {
            if (handles[i].id === id) return handles[i];
        }
        return null;
    };

    ConfiguratorApp.prototype.buildFurniture = function() {
        this.builder.build({
            typeKey: this.config.typeKey,
            W: this.config.W, H: this.config.H, D: this.config.D,
            doors: this.config.doors, shelves: this.config.shelves,
            drawers: this.config.drawers, dividerCount: this.config.dividerCount || 0,
            mounted: this.config.mounted,
            doorColor: this.config.customColor || this._getColorHex(this.config.doorColor),
            finish: this.config.finish,
            hasGrain: this.config.finish === 'texturizado' || this.config.finish === 'madeira',
            handleType: this.config.handleType,
            handleColor: this.config.handleColor || '#6B5340',
            ledEnabled: this.config.ledEnabled,
            interiorOpen: this.config.interiorOpen
        });
        this.builder.setOpenDoors(this.config.interiorOpen);
    };

    ConfiguratorApp.prototype._getColorHex = function(colorId) {
        var c = this._findColor(colorId);
        return (c && c.hex) || '#C8A87C';
    };

    ConfiguratorApp.prototype.updatePrice = function() {
        var areaM2 = (this.config.W * this.config.H) / 10000;
        var base = 420;
        var finishMult = { melamina: 1.0, liso: 1.05, texturizado: 1.1, laca: 1.4, madeira: 1.15, acetinado: 1.25 }[this.config.finish] || 1;
        var colorMult = (['preto', 'wenge', 'grafite'].indexOf(this.config.doorColor) >= 0) ? 1.12 : 1.0;
        var handlePrice = { alca: 25, botao: 15, cava: 35, perfil: 45, concha: 30, embutido: 20, nenhum: 0 }[this.config.handleType] || 0;
        var mods = this.config.doors * 45 + this.config.shelves * 18 + this.config.drawers * 85 + (this.config.ledEnabled ? 120 : 0);
        this.price = Math.round(areaM2 * base * finishMult * colorMult + handlePrice + mods);
        this.ui.updatePrice(this.price);
    };

    ConfiguratorApp.prototype.updateSummary = function() {
        var summary = this.config.typeLabel + ' · ' + this.config.W + 'L × ' + this.config.H + 'A × ' + this.config.D + 'P · ' + this.config.colorLabel + ' · ' + this.config.finishLabel;
        this.ui.updateSummary(summary);
    };

    ConfiguratorApp.prototype.bindActionButtons = function() {
        var self = this;
        var waNumber = '556732537898';

        var btnOrcamento = document.getElementById('btnOrcamento');
        if (btnOrcamento) {
            btnOrcamento.addEventListener('click', function() {
                var msg = self.projectService.generateWhatsAppMessage(self.config, self.price);
                window.open('https://wa.me/' + waNumber + '?text=' + msg, '_blank');
            });
        }

        var btnSalvar = document.getElementById('btnSalvar');
        if (btnSalvar) {
            btnSalvar.addEventListener('click', function() {
                var ok = self.projectService.saveLocal(self.config);
                self.ui.showToast(ok ? 'Projeto salvo neste navegador' : 'Não foi possível salvar');
            });
        }

        var btnLink = document.getElementById('btnLink');
        if (btnLink) {
            btnLink.addEventListener('click', function() {
                var url = self.projectService.generateShareUrl(self.config);
                try { history.replaceState(null, '', url); } catch(e) {}
                if (navigator.clipboard) {
                    navigator.clipboard.writeText(url).then(
                        function() { self.ui.showToast('Link copiado!'); },
                        function() { self.ui.showToast('Link na barra de endereço'); }
                    );
                } else {
                    self.ui.showToast('Link na barra de endereço');
                }
            });
        }

        var btnImg = document.getElementById('btnImg');
        if (btnImg) {
            btnImg.addEventListener('click', function() {
                try {
                    var canvas = document.getElementById('cfgCanvas');
                    var url = canvas.toDataURL('image/png');
                    var a = document.createElement('a');
                    a.href = url; a.download = 'projeto-lc-moveis.png';
                    document.body.appendChild(a); a.click(); document.body.removeChild(a);
                    self.ui.showToast('Imagem baixada');
                } catch(e) { self.ui.showToast('Erro ao gerar imagem'); }
            });
        }

        var btnLimpar = document.getElementById('btnLimpar');
        if (btnLimpar) {
            btnLimpar.addEventListener('click', function() {
                self.projectService.clearLocal();
                try { history.replaceState(null, '', location.pathname); } catch(e) {}
                self.config = self._getDefaultConfig();
                self.ui.updateUIFromConfig(self.config);
                self.buildFurniture();
                self.updatePrice();
                self.updateSummary();
                self.ui.showToast('Configuração reiniciada');
            });
        }

        var btnToggleDoors = document.getElementById('btnToggleDoors');
        if (btnToggleDoors) {
            btnToggleDoors.addEventListener('click', function() {
                var anyOpen = false;
                self.builder.components.forEach(function(comp) {
                    if (comp.userData && comp.userData.type === 'door' && comp.userData.open) {
                        anyOpen = true;
                    }
                });
                self.builder.setOpenDoors(!anyOpen);
                self.config.interiorOpen = !anyOpen;
            });
        }

        var btnToggleDrawers = document.getElementById('btnToggleDrawers');
        if (btnToggleDrawers) {
            btnToggleDrawers.addEventListener('click', function() {
                var anyOpen = false;
                self.builder.components.forEach(function(comp) {
                    if (comp.userData && comp.userData.type === 'drawer' && comp.userData.open) {
                        anyOpen = true;
                    }
                });
                self.builder.setAllDrawersOpen(!anyOpen);
            });
        }

        var presetBtns = document.querySelectorAll('.preset-btn');
        presetBtns.forEach(function(btn) {
            btn.addEventListener('click', function() {
                var preset = btn.dataset.preset;
                self.applyPreset(preset);
                presetBtns.forEach(function(b) { b.classList.remove('active'); });
                btn.classList.add('active');
            });
        });
    };

    ConfiguratorApp.prototype.applyPreset = function(preset) {
        var presets = {
            casal: { W: 180, H: 220, D: 55, doors: 2, shelves: 3, drawers: 0, doorColor: 'caramelo', finish: 'melamina', handleType: 'alca' },
            solteiro: { W: 120, H: 200, D: 50, doors: 1, shelves: 2, drawers: 0, doorColor: 'branco', finish: 'melamina', handleType: 'botao' },
            closet: { W: 240, H: 240, D: 60, doors: 4, shelves: 4, drawers: 0, doorColor: 'nogueira', finish: 'texturizado', handleType: 'perfil' },
            comoda: { W: 140, H: 100, D: 45, doors: 0, shelves: 0, drawers: 4, doorColor: 'freijo', finish: 'melamina', handleType: 'alca' }
        };

        var p = presets[preset];
        if (!p) return;

        this.config = Object.assign({}, this.config, p);
        this.syncTypeDefaults();
        this.ui.updateUIFromConfig(this.config);
        this.buildFurniture();
        this.updatePrice();
        this.updateSummary();
        this.ui.showToast('Preset aplicado: ' + preset);
    };

    ConfiguratorApp.prototype.bindClickInteraction = function() {
        var self = this;
        var canvas = document.getElementById('cfgCanvas');
        if (!canvas) return;

        var raycaster = new THREE.Raycaster();
        var mouse = new THREE.Vector2();
        var downPos = null;
        var hoveredObj = null;
        var selectedObj = null;

        canvas.addEventListener('pointerdown', function(e) {
            downPos = { x: e.clientX, y: e.clientY };
        });

        canvas.addEventListener('pointermove', function(e) {
            var rect = canvas.getBoundingClientRect();
            mouse.x = ((e.clientX - rect.left) / rect.width) * 2 - 1;
            mouse.y = -((e.clientY - rect.top) / rect.height) * 2 + 1;

            raycaster.setFromCamera(mouse, self.sceneManager.camera);
            var intersects = raycaster.intersectObjects(self.builder.furnitureGroup.children, true);

            var found = null;
            if (intersects.length > 0) {
                var obj = intersects[0].object;
                var root = obj;
                while (root.parent && root.parent !== self.builder.furnitureGroup) {
                    root = root.parent;
                }
                if (root.userData && (root.userData.type === 'door' || root.userData.type === 'drawer')) {
                    found = root;
                }
            }

            if (found !== hoveredObj) {
                if (hoveredObj && hoveredObj !== selectedObj) {
                    self._setHighlight(hoveredObj, false);
                }

                hoveredObj = found;

                if (hoveredObj) {
                    self._setHighlight(hoveredObj, true);
                }
            }

            canvas.style.cursor = found ? 'pointer' : 'grab';
        });

        canvas.addEventListener('pointerup', function(e) {
            if (!downPos) return;
            var dx = e.clientX - downPos.x;
            var dy = e.clientY - downPos.y;
            if (Math.sqrt(dx * dx + dy * dy) > 5) {
                downPos = null;
                return;
            }
            downPos = null;

            var rect = canvas.getBoundingClientRect();
            mouse.x = ((e.clientX - rect.left) / rect.width) * 2 - 1;
            mouse.y = -((e.clientY - rect.top) / rect.height) * 2 + 1;

            raycaster.setFromCamera(mouse, self.sceneManager.camera);
            var intersects = raycaster.intersectObjects(self.builder.furnitureGroup.children, true);

            if (intersects.length > 0) {
                var obj = intersects[0].object;
                var root = obj;
                while (root.parent && root.parent !== self.builder.furnitureGroup) {
                    root = root.parent;
                }

                if (root.userData && root.userData.type === 'door') {
                    if (selectedObj && selectedObj !== root) {
                        self._setSelected(selectedObj, false);
                    }
                    self.builder.toggleDoor(root);
                    self.config.interiorOpen = root.userData.open;
                    self._setSelected(root, true);
                    selectedObj = root;
                } else if (root.userData && root.userData.type === 'drawer') {
                    var idx = root.userData.index;
                    var isOpen = !root.userData.open;
                    self.builder.setDrawerOpen(idx, isOpen);
                    self._setSelected(root, true);
                    selectedObj = root;
                }
            }
        });
    };

    ConfiguratorApp.prototype._setHighlight = function(obj, enabled) {
        var emissiveColor = 0x333333;
        var emissiveIntensity = enabled ? 0.3 : 0;

        obj.traverse(function(child) {
            if (child.isMesh && child.material && child.material.emissive) {
                child.material.emissive.setHex(emissiveColor);
                child.material.emissiveIntensity = emissiveIntensity;
            }
        });
    };

    ConfiguratorApp.prototype._setSelected = function(obj, enabled) {
        obj.traverse(function(child) {
            if (child.isMesh && child.material) {
                if (enabled) {
                    child.material.emissive.setHex(0x444444);
                    child.material.emissiveIntensity = 0.15;
                } else {
                    child.material.emissive.setHex(0x000000);
                    child.material.emissiveIntensity = 0;
                }
            }
        });
    };

    ConfiguratorApp.prototype.showError = function(msg) {
        var wrap = document.getElementById('canvasWrap');
        if (wrap) {
            wrap.innerHTML = '<div style="display:flex;flex-direction:column;align-items:center;justify-content:center;height:100%;padding:40px;text-align:center;color:var(--ink-muted);">' +
                '<i class="fas fa-exclamation-triangle" style="font-size:2rem;margin-bottom:16px;color:#B84A4A;"></i>' +
                '<p style="font-size:1rem;margin-bottom:12px;">' + msg + '</p>' +
                '<button onclick="location.reload()" style="padding:10px 20px;background:#1A1714;color:#fff;border:none;border-radius:8px;cursor:pointer;font-size:0.9rem;">Tentar novamente</button>' +
                '</div>';
        }
    };

    // Initialize when DOM is ready
    function boot() {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', function() {
                var app = new ConfiguratorApp();
                app.init();
                window.__configuratorApp = app;
            });
        } else {
            var app = new ConfiguratorApp();
            app.init();
            window.__configuratorApp = app;
        }
    }

    boot();
})();
