/**
 * LC Soluções em Móveis — Professional 3D Furniture Configurator
 * Version 2.0 — Compatible with Three.js r128+ global build
 */

(function() {
    'use strict';

    // ============================================================
    // UNIT SYSTEM
    // ============================================================
    const UnitSystem = {
        cmToMeters: (cm) => cm * 0.01,
        metersToCm: (m) => m * 100,
    };

    // ============================================================
    // CATALOG DATA (sincronizado com FurnitureTemplates em furniture-engine.js)
    const FURNITURE_CATALOG = {
        guarda_roupa: {
            label: 'Guarda-roupa planejado',
            dimensions: { width: 180, height: 220, depth: 55 },
            defaults: { doors: 2, shelves: 3, drawers: 0, dividers: 0 },
            limits: { minW: 40, maxW: 400, minH: 30, maxH: 300, minD: 20, maxD: 80, maxDoors: 8, maxShelves: 10, maxDrawers: 6, maxDividers: 4 },
            mounted: false,
            description: 'Ideal para quartos: 120–240 cm de largura.',
        },
        closet: {
            label: 'Closet',
            dimensions: { width: 240, height: 240, depth: 60 },
            defaults: { doors: 3, shelves: 4, drawers: 2, dividers: 1 },
            limits: { minW: 120, maxW: 400, minH: 180, maxH: 300, minD: 50, maxD: 80, maxDoors: 12, maxShelves: 12, maxDrawers: 8, maxDividers: 6 },
            mounted: false,
            description: 'Largura ampla: 200–360 cm.',
        },
        comoda: {
            label: 'Cômoda',
            dimensions: { width: 120, height: 80, depth: 45 },
            defaults: { doors: 0, shelves: 0, drawers: 4, dividers: 0 },
            limits: { minW: 60, maxW: 200, minH: 30, maxH: 120, minD: 35, maxD: 60, maxDoors: 0, maxShelves: 4, maxDrawers: 10, maxDividers: 0 },
            mounted: false,
            description: 'Altura 70–90 cm, 4–6 gavetas.',
        },
        cozinha_base: {
            label: 'Armário Cozinha Base',
            dimensions: { width: 80, height: 72, depth: 56 },
            defaults: { doors: 2, shelves: 1, drawers: 0, dividers: 0 },
            limits: { minW: 30, maxW: 400, minH: 30, maxH: 240, minD: 50, maxD: 80, maxDoors: 8, maxShelves: 8, maxDrawers: 8, maxDividers: 4 },
            mounted: false,
            description: 'Bancada padrão: profundidade 60 cm.',
        },
        cozinha_aereo: {
            label: 'Armário Cozinha Aéreo',
            dimensions: { width: 80, height: 70, depth: 35 },
            defaults: { doors: 2, shelves: 1, drawers: 0, dividers: 0 },
            limits: { minW: 30, maxW: 300, minH: 30, maxH: 120, minD: 25, maxD: 50, maxDoors: 6, maxShelves: 6, maxDrawers: 0, maxDividers: 4 },
            mounted: true,
            description: 'Suspenso, acima de bancadas.',
        },
        estante: {
            label: 'Estante',
            dimensions: { width: 90, height: 200, depth: 35 },
            defaults: { doors: 0, shelves: 5, drawers: 0, dividers: 0 },
            limits: { minW: 60, maxW: 200, minH: 60, maxH: 260, minD: 25, maxD: 50, maxDoors: 0, maxShelves: 12, maxDrawers: 0, maxDividers: 4 },
            mounted: false,
            description: 'Largura 60–120 cm, altura até 240 cm.',
        },
        painel_tv: {
            label: 'Painel para TV',
            dimensions: { width: 180, height: 50, depth: 35 },
            defaults: { doors: 0, shelves: 2, drawers: 1, dividers: 0 },
            limits: { minW: 80, maxW: 300, minH: 30, maxH: 120, minD: 25, maxD: 50, maxDoors: 0, maxShelves: 6, maxDrawers: 3, maxDividers: 4 },
            mounted: false,
            description: 'Altura 40–60 cm para base de TV.',
        },
        nicho: {
            label: 'Nicho',
            dimensions: { width: 80, height: 80, depth: 30 },
            defaults: { doors: 0, shelves: 3, drawers: 0, dividers: 0 },
            limits: { minW: 40, maxW: 200, minH: 30, maxH: 240, minD: 15, maxD: 50, maxDoors: 0, maxShelves: 8, maxDrawers: 0, maxDividers: 4 },
            mounted: true,
            description: 'Suspenso. Altura comum: 40–120 cm.',
        },
    };

    const COLOR_PRESETS = [
        { id: 'branco', name: 'Branco', hex: '#ECEAE3' },
        { id: 'offwhite', name: 'Off-white', hex: '#E8E0D3' },
        { id: 'bege', name: 'Bege', hex: '#D8C3A5' },
        { id: 'caramelo', name: 'Caramelo', hex: '#C8A87C' },
        { id: 'madeira_clara', name: 'Madeira Clara', hex: '#C9A876' },
        { id: 'carvalho', name: 'Carvalho', hex: '#C9A876' },
        { id: 'nogueira', name: 'Nogueira', hex: '#6B5340' },
        { id: 'wenge', name: 'Wengué', hex: '#3A2E26' },
        { id: 'cinza', name: 'Cinza', hex: '#9A9A9A' },
        { id: 'grafite', name: 'Grafite', hex: '#4A4A4A' },
        { id: 'preto', name: 'Preto', hex: '#2B2B2B' },
    ];

    const FINISH_TYPES = [
        { id: 'melamina', name: 'Melamina', roughness: 0.72 },
        { id: 'liso', name: 'Liso (fosco)', roughness: 0.55 },
        { id: 'texturizado', name: 'Texturizado (madeira)', roughness: 0.78 },
        { id: 'laca', name: 'Laca (brilhante)', roughness: 0.08 },
    ];

    const HANDLE_TYPES = [
        { id: 'alca', name: 'Alça' },
        { id: 'botao', name: 'Botão' },
        { id: 'cava', name: 'Cava' },
        { id: 'perfil', name: 'Perfil' },
        { id: 'concha', name: 'Concha' },
        { id: 'embutido', name: 'Embutido' },
        { id: 'nenhum', name: 'Nenhum' },
    ];

    // ============================================================
    // CONFIGURATOR STATE
    // ============================================================
    class ConfiguratorState {
        constructor() {
            this.listeners = [];
            this._state = this._getDefault();
        }

        _getDefault() {
            return {
                furnitureType: 'guarda_roupa',
                dimensions: { width: 180, height: 220, depth: 55 },
                structure: { doors: 2, drawers: 0, shelves: 3 },
                material: { color: 'caramelo', customColor: null, finish: 'melamina' },
                hardware: { handle: 'alca' },
                lighting: { enabled: false },
                view: { doorsOpen: false, drawersOpen: false },
                mounted: false,
                typeLabel: 'Guarda-roupa planejado',
                colorLabel: 'Caramelo',
                finishLabel: 'Melamina',
                handleLabel: 'Alça',
                hint: 'Ideal para quartos: 120–240 cm de largura.',
            };
        }

        get() { return JSON.parse(JSON.stringify(this._state)); }

        set(changes) {
            this._state = { ...this._state, ...changes };
            this._notify();
        }

        subscribe(fn) { this.listeners.push(fn); }

        _notify() {
            this.listeners.forEach(fn => fn(this.get()));
        }

        serialize() {
            return { version: 2, updatedAt: new Date().toISOString(), state: this.get() };
        }

        deserialize(data) {
            if (!data || !data.state) return;
            this._state = { ...this._getDefault(), ...data.state };
        }
    }

    // ============================================================
    // MATERIAL ENGINE
    // ============================================================
    class MaterialEngine {
        constructor() {
            this.textureCache = new Map();
            this.materialCache = new Map();
        }

        getMaterial(spec) {
            const key = `${spec.color}_${spec.finish}_${spec.hasGrain ? 1 : 0}`;
            if (this.materialCache.has(key)) return this.materialCache.get(key);

            const finish = FINISH_TYPES.find(f => f.id === spec.finish) || FINISH_TYPES[0];
            const isWood = spec.hasGrain || spec.finish === 'texturizado';

            const params = {
                color: spec.color || '#ECEAE3',
                roughness: finish.roughness,
                metalness: 0.0,
            };

            if (isWood) {
                params.map = this._createWoodTexture(spec.color, spec.grainDir || 'vertical');
                params.color = '#ffffff';
            }

            const mat = new THREE.MeshStandardMaterial(params);
            this.materialCache.set(key, mat);
            return mat;
        }

        getMetalMaterial(hex = '#6B5340') {
            const key = `metal_${hex}`;
            if (this.materialCache.has(key)) return this.materialCache.get(key);
            const mat = new THREE.MeshStandardMaterial({ color: hex, roughness: 0.25, metalness: 0.9 });
            this.materialCache.set(key, mat);
            return mat;
        }

        _createWoodTexture(hex, direction) {
            const cacheKey = `wood_${hex}_${direction}`;
            if (this.textureCache.has(cacheKey)) return this.textureCache.get(cacheKey);

            const size = 512;
            const canvas = document.createElement('canvas');
            canvas.width = size;
            canvas.height = size;
            const ctx = canvas.getContext('2d');

            const r = parseInt(hex.slice(1, 3), 16);
            const g = parseInt(hex.slice(3, 5), 16);
            const b = parseInt(hex.slice(5, 7), 16);
            ctx.fillStyle = `rgb(${r},${g},${b})`;
            ctx.fillRect(0, 0, size, size);

            for (let i = 0; i < 25; i++) {
                const cx = size * (0.2 + Math.random() * 0.6);
                const cy = size * (0.2 + Math.random() * 0.6);
                const radius = 20 + Math.random() * 80;
                const v = Math.random() * 16 - 8;
                ctx.strokeStyle = `rgba(${r + v},${g + v},${b + v},0.05)`;
                ctx.lineWidth = 1 + Math.random() * 2;
                ctx.beginPath();
                ctx.arc(cx, cy, radius, 0, Math.PI * 2);
                ctx.stroke();
            }

            for (let i = 0; i < 1200; i++) {
                const v = Math.random() * 10 - 5;
                ctx.strokeStyle = `rgba(${r + v},${g + v},${b + v},${0.03 + Math.random() * 0.05})`;
                ctx.lineWidth = 0.5 + Math.random() * 1;
                const x = Math.random() * size;
                const y = Math.random() * size;
                const len = 30 + Math.random() * 100;
                ctx.beginPath();
                ctx.moveTo(x, y);
                if (direction === 'horizontal') {
                    ctx.lineTo(x + len, y + (Math.random() * 3 - 1.5));
                } else {
                    ctx.lineTo(x + (Math.random() * 3 - 1.5), y + len);
                }
                ctx.stroke();
            }

            const tex = new THREE.CanvasTexture(canvas);
            tex.wrapS = tex.wrapT = THREE.RepeatWrapping;
            tex.repeat.set(1.5, 1.5);
            tex.anisotropy = 4;
            this.textureCache.set(cacheKey, tex);
            return tex;
        }

        dispose() {
            this.textureCache.forEach(t => t.dispose());
            this.materialCache.forEach(m => m.dispose());
            this.textureCache.clear();
            this.materialCache.clear();
        }
    }

    // ============================================================
    // RENDERING ENGINE
    // ============================================================
    class RenderingEngine {
        constructor(canvas) {
            this.canvas = canvas;
            this.renderer = null;
            this.scene = null;
            this.camera = null;
            this.controls = null;
            this.furnitureGroup = null;
            this.lights = {};
            this._animCallbacks = [];
            this._animId = null;
        }

        init() {
            const dim = this._getCanvasSize();

            this.renderer = new THREE.WebGLRenderer({
                canvas: this.canvas,
                antialias: true,
                preserveDrawingBuffer: true,
            });
            this.renderer.setSize(dim.w, dim.h);
            this.renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
            this.renderer.shadowMap.enabled = true;
            this.renderer.shadowMap.type = THREE.PCFSoftShadowMap;
            this.renderer.toneMapping = THREE.ACESFilmicToneMapping;
            this.renderer.toneMappingExposure = 1.05;

            this.scene = new THREE.Scene();
            this.scene.background = new THREE.Color(0xF6F1EB);

            this.camera = new THREE.PerspectiveCamera(35, dim.w / dim.h, 0.01, 100);
            this.camera.position.set(3.5, 2.2, 3.5);

            this.controls = new THREE.OrbitControls(this.camera, this.renderer.domElement);
            this.controls.enableDamping = true;
            this.controls.dampingFactor = 0.06;
            this.controls.maxPolarAngle = Math.PI * 0.49;
            this.controls.minPolarAngle = Math.PI * 0.05;
            this.controls.minDistance = 0.8;
            this.controls.maxDistance = 12;
            this.controls.target.set(0, 1.0, 0);
            this.controls.update();

            this._setupLights();
            this._setupEnvironment();

            this.furnitureGroup = new THREE.Group();
            this.scene.add(this.furnitureGroup);

            return true;
        }

        _setupLights() {
            this.lights.ambient = new THREE.AmbientLight(0xffffff, 0.2);
            this.scene.add(this.lights.ambient);

            this.lights.hemisphere = new THREE.HemisphereLight(0xFFF8F0, 0xE0D8D0, 0.3);
            this.scene.add(this.lights.hemisphere);

            this.lights.key = new THREE.DirectionalLight(0xFFF5E8, 1.0);
            this.lights.key.position.set(3, 6, 4);
            this.lights.key.castShadow = true;
            this.lights.key.shadow.mapSize.width = 2048;
            this.lights.key.shadow.mapSize.height = 2048;
            this.lights.key.shadow.camera.near = 0.5;
            this.lights.key.shadow.camera.far = 20;
            this.lights.key.shadow.camera.left = -5;
            this.lights.key.shadow.camera.right = 5;
            this.lights.key.shadow.camera.top = 5;
            this.lights.key.shadow.camera.bottom = -5;
            this.lights.key.shadow.bias = -0.0003;
            this.lights.key.shadow.radius = 4;
            this.scene.add(this.lights.key);

            this.lights.fill = new THREE.DirectionalLight(0xE8E0D8, 0.35);
            this.lights.fill.position.set(-3, 3, -2);
            this.scene.add(this.lights.fill);

            this.lights.rim = new THREE.DirectionalLight(0xFFF8F0, 0.45);
            this.lights.rim.position.set(-2, 4, -4);
            this.scene.add(this.lights.rim);
        }

        _setupEnvironment() {
            const floorMat = new THREE.MeshStandardMaterial({ color: 0xE8E0D8, roughness: 0.85, metalness: 0.0 });
            const floor = new THREE.Mesh(new THREE.PlaneGeometry(30, 30), floorMat);
            floor.rotation.x = -Math.PI / 2;
            floor.position.y = -0.01;
            floor.receiveShadow = true;
            this.scene.add(floor);

            const wallMat = new THREE.MeshStandardMaterial({ color: 0xF5F0EA, roughness: 0.9, metalness: 0.0 });
            const wallGeo = new THREE.PlaneGeometry(12, 5);
            const wall = new THREE.Mesh(wallGeo, wallMat);
            wall.position.set(0, 2.5, -3.5);
            wall.receiveShadow = true;
            this.scene.add(wall);

            const sideWall = new THREE.Mesh(wallGeo, wallMat);
            sideWall.rotation.y = Math.PI / 2;
            sideWall.position.set(-3.5, 2.5, 0);
            sideWall.receiveShadow = true;
            this.scene.add(sideWall);
        }

        _getCanvasSize() {
            const wrap = document.getElementById('canvasWrap') || this.canvas.parentElement;
            if (!wrap) return { w: 600, h: 500 };
            return { w: Math.max(wrap.clientWidth || 600, 300), h: Math.max(wrap.clientHeight || 500, 300) };
        }

        onResize() {
            if (!this.renderer) return;
            const dim = this._getCanvasSize();
            this.renderer.setSize(dim.w, dim.h);
            this.camera.aspect = dim.w / dim.h;
            this.camera.updateProjectionMatrix();
        }

        setCameraPreset(preset) {
            if (!this.camera || !this.controls) return;

            const target = new THREE.Vector3();
            if (this.furnitureGroup.children.length > 0) {
                const bbox = new THREE.Box3().setFromObject(this.furnitureGroup);
                bbox.getCenter(target);
                if (bbox.isEmpty()) target.set(0, 1, 0);
            }

            let camPos;
            const offset = 4;
            switch (preset) {
                case 'front': camPos = new THREE.Vector3(target.x, target.y, target.z + offset); break;
                case 'side': camPos = new THREE.Vector3(target.x + offset, target.y, target.z); break;
                case 'top': camPos = new THREE.Vector3(target.x, target.y + offset, target.z + 0.01); break;
                default: camPos = new THREE.Vector3(target.x + offset * 0.7, target.y + offset * 0.5, target.z + offset * 0.7); break;
            }

            this._animateCameraTo(camPos, target, 500);
        }

        _animateCameraTo(targetPos, lookTarget, duration) {
            const camera = this.camera;
            const controls = this.controls;
            const startPos = camera.position.clone();
            const startTarget = controls.target.clone();
            const startTime = performance.now();
            const ease = (t) => t < 0.5 ? 4 * t * t * t : 1 - Math.pow(-2 * t + 2, 3) / 2;

            const animate = (now) => {
                const t = Math.min((now - startTime) / duration, 1);
                const et = ease(t);
                camera.position.lerpVectors(startPos, targetPos, et);
                controls.target.lerpVectors(startTarget, lookTarget, et);
                controls.update();
                if (t < 1) requestAnimationFrame(animate);
            };
            requestAnimationFrame(animate);
        }

        frameCameraTo(box) {
            const center = new THREE.Vector3();
            const size = new THREE.Vector3();
            box.getCenter(center);
            box.getSize(size);
            const maxDim = Math.max(size.x, size.y, size.z);
            const fov = this.camera.fov * (Math.PI / 180);
            const distance = (maxDim / (2 * Math.tan(fov / 2))) * 1.5;

            // Posicionar a câmera em um ângulo que centraliza o móvel
            const camPos = new THREE.Vector3(
                center.x + distance * 0.5,
                center.y + distance * 0.2,
                center.z + distance * 0.85
            );
            this._animateCameraTo(camPos, center, 400);
        }

        addAnimCallback(fn) { this._animCallbacks.push(fn); }

        startLoop() {
            const loop = () => {
                this._animCallbacks.forEach(fn => fn());
                if (this.controls) this.controls.update();
                this.renderer.render(this.scene, this.camera);
                this._animId = requestAnimationFrame(loop);
            };
            loop();
        }

        clearFurniture() {
            while (this.furnitureGroup.children.length > 0) {
                const child = this.furnitureGroup.children[0];
                this.furnitureGroup.remove(child);
                child.traverse((obj) => {
                    if (obj.geometry) obj.geometry.dispose();
                    if (obj.material) {
                        if (Array.isArray(obj.material)) obj.material.forEach(m => m.dispose());
                        else obj.material.dispose();
                    }
                });
            }
        }
    }

    // ============================================================
    // FURNITURE ENGINE (Adapter para LCFurnitureEngine)
    // ============================================================
    class FurnitureEngine {
        constructor(renderingEngine, materialEngine) {
            this.renderingEngine = renderingEngine;
            this.materialEngine = materialEngine;
            this.rendererAdapter = new LCFurnitureRendererAdapter(materialEngine);
            this.components = new Map();
            this.doors = [];
            this.drawers = [];
            this.lastResult = null;
        }

        build(config) {
            this.renderingEngine.clearFurniture();
            this.components.clear();
            this.doors = [];
            this.drawers = [];

            const group = this.renderingEngine.furnitureGroup;

            // Converter dimensões de cm para mm (LCFurnitureEngine usa mm)
            const dimensionsMm = {
                width: Math.round((config.dimensions.width || 180) * 10),
                height: Math.round((config.dimensions.height || 220) * 10),
                depth: Math.round((config.dimensions.depth || 55) * 10),
            };

            // Criar definição de móvel para o LCFurnitureEngine
            const furnitureDef = {
                type: config.furnitureType || 'guarda_roupa',
                dimensions: dimensionsMm,
                structure: {
                    doors: config.structure?.doors || 0,
                    drawers: config.structure?.drawers || 0,
                    shelves: config.structure?.shelves || 0,
                    dividers: config.structure?.dividers || 0,
                },
                material: {
                    finish: config.material?.finish || 'melamina',
                    color: config.material?.color || 'caramelo',
                    customColor: config.material?.customColor || null,
                },
                hardware: {
                    hinge: config.hardware?.hinge || 'soft_close',
                    slide: config.hardware?.slide || 'telescopica_soft',
                    handle: config.hardware?.handle || 'alca',
                },
                mounted: config.mounted || false,
            };

            // Construir usando LCFurnitureEngine
            const engine = new LCFurnitureEngine.FurnitureEngine();
            const result = engine.build(furnitureDef);
            this.lastResult = result;

            if (!result.valid) {
                console.warn('FurnitureEngine erros:', result.errors);
                return result;
            }

            // Separar partes por tipo
            const structureParts = result.parts.filter(p => ['side', 'top', 'bottom', 'back', 'divider'].includes(p.type));
            const shelfParts = result.parts.filter(p => p.type === 'shelf');
            const doorParts = result.parts.filter(p => p.type === 'door');
            const drawerParts = result.parts.filter(p => ['drawer_front', 'drawer_side', 'drawer_bottom'].includes(p.type));

            // Renderizar partes estruturais
            structureParts.forEach(part => {
                const material = this.rendererAdapter.getMaterialForPart(part);
                const mesh = this.rendererAdapter.createMeshFromPart(part, material);
                if (mesh) {
                    group.add(mesh);
                    this.components.set(part.name, mesh);
                }
            });

            // Renderizar prateleiras
            shelfParts.forEach(part => {
                const material = this.rendererAdapter.getMaterialForPart(part);
                const mesh = this.rendererAdapter.createMeshFromPart(part, material);
                if (mesh) {
                    group.add(mesh);
                    this.components.set(part.name, mesh);
                }
            });

            // Renderizar portas com pivôs
            this.doors = this.rendererAdapter.renderDoors(doorParts, group);

            // Renderizar gavetas (agrupadas: frente + laterais + fundo)
            this.drawers = this.rendererAdapter.renderDrawers(drawerParts, group);

            // Atualizar câmera
            const bbox = new THREE.Box3().setFromObject(group);
            if (isFinite(bbox.min.x)) {
                this.renderingEngine.frameCameraTo(bbox);
            }

            return result;
        }

        // Obter último resultado do LCFurnitureEngine
        getLastResult() {
            return this.lastResult;
        }

        // Obter cut list
        getCutList() {
            if (!this.lastResult) return [];
            return this.lastResult.parts.map(part => ({
                name: part.name,
                type: part.type,
                width: Math.round(part.width),
                height: Math.round(part.height),
                thickness: part.thickness,
                material: part.material?.type || 'mdf',
                finish: part.finish,
                color: part.color,
                grainDirection: part.grainDirection,
            }));
        }

        // Obter lista de ferragens
        getHardwareList() {
            if (!this.lastResult) return [];
            return this.lastResult.hardware.map(hw => ({
                name: hw.name,
                type: hw.type,
                subType: hw.subType,
                quantity: hw.quantity,
            }));
        }

        // Obter área total
        getTotalAreaM2() {
            if (!this.lastResult) return 0;
            return this.lastResult.parts.reduce((sum, part) => {
                return sum + (part.width / 1000) * (part.height / 1000);
            }, 0);
        }

        _addPanel(parent, x, y, z, w, h, d, material, name) {
            // Validate all parameters to prevent NaN errors
            x = isFinite(x) ? x : 0;
            y = isFinite(y) ? y : 0;
            z = isFinite(z) ? z : 0;
            w = isFinite(w) && w > 0 ? w : 0.001;
            h = isFinite(h) && h > 0 ? h : 0.001;
            d = isFinite(d) && d > 0 ? d : 0.001;

            const mesh = new THREE.Mesh(new THREE.BoxGeometry(w, h, d), material);
            mesh.position.set(x, y, z);
            mesh.castShadow = true;
            mesh.receiveShadow = true;
            mesh.name = name;
            parent.add(mesh);
            this.components.set(name, mesh);
            return mesh;
        }

        _buildDoors(parent, config, W, H, D, T, y0, panelMat, handleMat) {
            const doorCount = config.structure.doors;
            const doorH = Math.max(0.01, H - T * 2 - 0.01);
            const gap = 0.006;
            const doorW = Math.max(0.01, (W - T * 2 - gap * (doorCount - 1) - 0.02) / doorCount);

            for (let i = 0; i < doorCount; i++) {
                const pivot = new THREE.Group();
                pivot.name = `door_${i}`;

                const slotX = -W / 2 + T + 0.01 + i * (doorW + gap);
                const hingeSide = i < Math.ceil(doorCount / 2) ? 1 : -1;
                const hingeX = hingeSide === 1 ? slotX : slotX + doorW;

                pivot.position.set(hingeX, y0 + H / 2, D / 2 + 0.001);

                const doorMesh = new THREE.Mesh(new THREE.BoxGeometry(doorW - 0.008, doorH, 0.018), panelMat);
                doorMesh.position.set(hingeSide * doorW / 2, 0, 0);
                doorMesh.castShadow = true;
                doorMesh.receiveShadow = true;
                pivot.add(doorMesh);

                if (config.hardware.handle !== 'nenhum') {
                    this._addHandle(doorMesh, doorW, doorH, hingeSide, config.hardware.handle, handleMat);
                }

                pivot.userData = { type: 'door', index: i, open: false, maxOpen: 105 * Math.PI / 180 };
                parent.add(pivot);
                this.doors.push(pivot);
                this.components.set(`door_${i}`, pivot);
            }
        }

        _addHandle(parent, doorW, doorH, dir, handleType, handleMat) {
            const handleY = doorH * 0.25;
            const offsetX = dir * (doorW / 2 - 0.05);

            switch (handleType) {
                case 'alca': {
                    const h = new THREE.Mesh(new THREE.BoxGeometry(0.02, 0.16, 0.02), handleMat);
                    h.position.set(offsetX, -handleY, 0.018);
                    parent.add(h);
                    break;
                }
                case 'botao': {
                    const h = new THREE.Mesh(new THREE.CylinderGeometry(0.012, 0.012, 0.035, 16), handleMat);
                    h.position.set(offsetX, -handleY, 0.02);
                    parent.add(h);
                    break;
                }
                case 'cava': {
                    const h = new THREE.Mesh(new THREE.BoxGeometry(0.06, 0.01, 0.008), handleMat);
                    h.position.set(offsetX, -handleY, 0.018);
                    parent.add(h);
                    break;
                }
                case 'perfil': {
                    const h = new THREE.Mesh(new THREE.BoxGeometry(0.008, 0.12, 0.02), handleMat);
                    h.position.set(dir * (doorW / 2 - 0.004), -handleY, 0.015);
                    parent.add(h);
                    break;
                }
                case 'concha': {
                    const h = new THREE.Mesh(new THREE.SphereGeometry(0.02, 16, 16, 0, Math.PI), handleMat);
                    h.position.set(offsetX, -handleY, 0.022);
                    parent.add(h);
                    break;
                }
                case 'embutido': {
                    const h = new THREE.Mesh(new THREE.BoxGeometry(0.07, 0.015, 0.012), handleMat);
                    h.position.set(offsetX, -handleY, 0.009);
                    parent.add(h);
                    break;
                }
            }
        }

        _buildDrawers(parent, config, W, H, D, T, y0, panelMat, handleMat) {
            const drawerCount = config.structure.drawers;
            const innerH = Math.max(0.01, H - T * 2 - 0.04);
            const drawerH = Math.max(0.01, (innerH * 0.9) / drawerCount);
            const drawerW = Math.max(0.01, W - T * 2 - 0.02);
            const drawerD = Math.max(0.01, Math.min(0.45, D - T * 2 - 0.02));

            for (let i = 0; i < drawerCount; i++) {
                const group = new THREE.Group();
                group.name = `drawer_${i}`;

                const gy = y0 + T + 0.02 + drawerH / 2 + i * drawerH;

                const front = new THREE.Mesh(new THREE.BoxGeometry(drawerW, drawerH * 0.88, 0.016), panelMat);
                front.position.set(0, 0, D / 2 - 0.008);
                front.castShadow = true;
                front.receiveShadow = true;
                group.add(front);

                const sideT = 0.01;
                const leftSide = new THREE.Mesh(new THREE.BoxGeometry(sideT, drawerH * 0.88, drawerD), panelMat);
                leftSide.position.set(-drawerW / 2 + sideT / 2, 0, -D / 2 + drawerD / 2 + 0.016);
                group.add(leftSide);

                const rightSide = leftSide.clone();
                rightSide.position.x = drawerW / 2 - sideT / 2;
                group.add(rightSide);

                const bottom = new THREE.Mesh(new THREE.BoxGeometry(drawerW - sideT * 2, sideT, drawerD), panelMat);
                bottom.position.y = -drawerH * 0.88 / 2;
                group.add(bottom);

                if (config.hardware.handle !== 'nenhum') {
                    this._addDrawerHandle(front, drawerW, drawerH * 0.88, config.hardware.handle, handleMat);
                }

                group.position.set(0, gy, 0);
                group.userData = { type: 'drawer', index: i, open: false };
                parent.add(group);
                this.drawers.push(group);
                this.components.set(`drawer_${i}`, group);
            }
        }

        _addDrawerHandle(front, drawerW, drawerH, handleType, handleMat) {
            const offset = 0.04;
            switch (handleType) {
                case 'alca': {
                    const h = new THREE.Mesh(new THREE.BoxGeometry(0.02, 0.06, 0.02), handleMat);
                    h.position.set(0, 0, offset);
                    front.add(h);
                    break;
                }
                case 'botao': {
                    const h = new THREE.Mesh(new THREE.CylinderGeometry(0.01, 0.01, 0.03, 16), handleMat);
                    h.position.set(0, 0, offset);
                    front.add(h);
                    break;
                }
                case 'cava': {
                    const h = new THREE.Mesh(new THREE.BoxGeometry(0.05, 0.008, 0.006), handleMat);
                    h.position.set(0, 0, offset);
                    front.add(h);
                    break;
                }
                case 'perfil': {
                    const h = new THREE.Mesh(new THREE.BoxGeometry(0.12, 0.008, 0.02), handleMat);
                    h.position.set(0, 0, offset);
                    front.add(h);
                    break;
                }
                case 'concha': {
                    const h = new THREE.Mesh(new THREE.SphereGeometry(0.018, 16, 16, 0, Math.PI), handleMat);
                    h.position.set(0, 0, offset);
                    front.add(h);
                    break;
                }
                case 'embutido': {
                    const h = new THREE.Mesh(new THREE.BoxGeometry(0.06, 0.012, 0.01), handleMat);
                    h.position.set(0, 0, offset - 0.005);
                    front.add(h);
                    break;
                }
            }
        }

        _buildShelves(parent, config, W, H, D, T, BT, y0, panelMat) {
            const shelfCount = config.structure.shelves;
            const innerD = Math.max(0.01, D - BT - T * 2);
            const innerH = Math.max(0.01, H - T * 2);

            for (let j = 0; j < shelfCount; j++) {
                const ratio = (j + 1) / (shelfCount + 1);
                const sy = y0 + T + innerH * ratio - innerH / 2;
                const shelfW = Math.max(0.01, W - T * 2 - 0.01);

                const shelf = new THREE.Mesh(new THREE.BoxGeometry(shelfW, 0.014, innerD), panelMat);
                shelf.position.set(0, sy, -D / 2 + T + innerD / 2);
                shelf.castShadow = true;
                shelf.receiveShadow = true;
                shelf.name = `shelf_${j}`;
                parent.add(shelf);
                this.components.set(`shelf_${j}`, shelf);
            }
        }

        _buildLED(parent, W, H, D, T, y0) {
            const ledGroup = new THREE.Group();
            ledGroup.name = 'led_strip';

            const ledColor = new THREE.Color(0xFFF5E0);
            const ledMat = new THREE.MeshBasicMaterial({ color: ledColor, transparent: true, opacity: 0.85 });

            const tube = new THREE.Mesh(new THREE.CylinderGeometry(0.004, 0.004, W - T * 2, 8), ledMat);
            tube.rotation.z = Math.PI / 2;
            tube.position.set(0, y0 + T * 2 + 0.02, -D / 2 + T * 2 + 0.01);
            ledGroup.add(tube);

            const light = new THREE.PointLight(ledColor, 1.2, 3, 2);
            light.position.copy(tube.position);
            light.position.y += 0.03;
            ledGroup.add(light);

            parent.add(ledGroup);
            this.components.set('led', ledGroup);
        }

        setDoorsOpen(open) {
            this.doors.forEach((door, i) => {
                const dir = i % 2 === 0 ? 1 : -1;
                const target = open ? dir * door.userData.maxOpen : 0;
                this._animateDoor(door, target, 500);
                door.userData.open = open;
            });
        }

        toggleDoor(door) {
            const newOpen = !door.userData.open;
            const dir = door.userData.index % 2 === 0 ? 1 : -1;
            const target = newOpen ? dir * door.userData.maxOpen : 0;
            this._animateDoor(door, target, 500);
            door.userData.open = newOpen;
        }

        _animateDoor(door, target, duration) {
            const start = door.rotation.y;
            const startTime = performance.now();
            const ease = (t) => t < 0.5 ? 4 * t * t * t : 1 - Math.pow(-2 * t + 2, 3) / 2;

            const animate = (now) => {
                const t = Math.min((now - startTime) / duration, 1);
                door.rotation.y = start + (target - start) * ease(t);
                if (t < 1) requestAnimationFrame(animate);
            };
            requestAnimationFrame(animate);
        }

        setDrawerOpen(index, open) {
            const drawer = this.drawers[index];
            if (!drawer) return;
            const targetZ = open ? 0.35 : 0;
            this._animateDrawer(drawer, targetZ, 400);
            drawer.userData.open = open;
        }

        setAllDrawersOpen(open) {
            this.drawers.forEach((drawer, i) => {
                setTimeout(() => this.setDrawerOpen(i, open), i * 80);
            });
        }

        _animateDrawer(drawer, targetZ, duration) {
            const startZ = drawer.position.z;
            const startTime = performance.now();
            const ease = (t) => t < 0.5 ? 2 * t * t : 1 - Math.pow(-2 * t + 2, 2) / 2;

            const animate = (now) => {
                const t = Math.min((now - startTime) / duration, 1);
                drawer.position.z = startZ + (targetZ - startZ) * ease(t);
                if (t < 1) requestAnimationFrame(animate);
            };
            requestAnimationFrame(animate);
        }
    }

    // ============================================================
    // INTERACTION MANAGER
    // ============================================================
    class InteractionManager {
        constructor(renderingEngine, furnitureEngine) {
            this.renderingEngine = renderingEngine;
            this.furnitureEngine = furnitureEngine;
            this.raycaster = new THREE.Raycaster();
            this.mouse = new THREE.Vector2();
            this.hoveredObj = null;
            this.selectedObj = null;
            this.downPos = null;
            this._bindEvents();
        }

        _bindEvents() {
            const canvas = this.renderingEngine.canvas;
            const self = this;
            canvas.addEventListener('pointerdown', (e) => { 
                self.downPos = { x: e.clientX, y: e.clientY }; 
            });
            canvas.addEventListener('pointermove', (e) => self._handleHover(e));
            canvas.addEventListener('pointerup', (e) => self._handleClick(e));
        }

        _handleHover(e) {
            const canvas = this.renderingEngine.canvas;
            const rect = canvas.getBoundingClientRect();
            this.mouse.x = ((e.clientX - rect.left) / rect.width) * 2 - 1;
            this.mouse.y = -((e.clientY - rect.top) / rect.height) * 2 + 1;

            this.raycaster.setFromCamera(this.mouse, this.renderingEngine.camera);
            const intersects = this.raycaster.intersectObjects(this.renderingEngine.furnitureGroup.children, true);

            let found = null;
            if (intersects.length > 0) {
                let root = intersects[0].object;
                while (root.parent && root.parent !== this.renderingEngine.furnitureGroup) root = root.parent;
                if (root.userData && (root.userData.type === 'door' || root.userData.type === 'drawer')) found = root;
            }

            if (found !== this.hoveredObj) {
                if (this.hoveredObj && this.hoveredObj !== this.selectedObj) this._setHighlight(this.hoveredObj, false);
                this.hoveredObj = found;
                if (this.hoveredObj) this._setHighlight(this.hoveredObj, true);
            }
            canvas.style.cursor = found ? 'pointer' : 'grab';
        }

        _handleClick(e) {
            if (!this.downPos) return;
            const dx = e.clientX - this.downPos.x;
            const dy = e.clientY - this.downPos.y;
            if (Math.sqrt(dx * dx + dy * dy) > 5) { this.downPos = null; return; }
            this.downPos = null;

            const canvas = this.renderingEngine.canvas;
            const rect = canvas.getBoundingClientRect();
            this.mouse.x = ((e.clientX - rect.left) / rect.width) * 2 - 1;
            this.mouse.y = -((e.clientY - rect.top) / rect.height) * 2 + 1;

            this.raycaster.setFromCamera(this.mouse, this.renderingEngine.camera);
            const intersects = this.raycaster.intersectObjects(this.renderingEngine.furnitureGroup.children, true);

            if (intersects.length > 0) {
                let root = intersects[0].object;
                while (root.parent && root.parent !== this.renderingEngine.furnitureGroup) root = root.parent;

                if (root.userData && root.userData.type === 'door') {
                    this.furnitureEngine.toggleDoor(root);
                    if (this.selectedObj && this.selectedObj !== root) this._setSelected(this.selectedObj, false);
                    this._setSelected(root, true);
                    this.selectedObj = root;
                } else if (root.userData && root.userData.type === 'drawer') {
                    const idx = root.userData.index;
                    const isOpen = !root.userData.open;
                    this.furnitureEngine.setDrawerOpen(idx, isOpen);
                    this._setSelected(root, true);
                     this.selectedObj = root;
                }
            }
        }

        _setHighlight(obj, enabled) {
            obj.traverse((child) => {
                if (child.isMesh && child.material && child.material.emissive) {
                    child.material.emissive.setHex(enabled ? 0x333333 : 0x000000);
                    child.material.emissiveIntensity = enabled ? 0.3 : 0;
                }
            });
        }

        _setSelected(obj, enabled) {
            obj.traverse((child) => {
                if (child.isMesh && child.material && child.material.emissive) {
                    child.material.emissive.setHex(enabled ? 0x444444 : 0x000000);
                    child.material.emissiveIntensity = enabled ? 0.15 : 0;
                }
            });
        }
    }

    // ============================================================
    // PERSISTENCE MANAGER
    // ============================================================
    class PersistenceManager {
        constructor(state, furnitureEngine) {
            this.state = state;
            this.furnitureEngine = furnitureEngine;
            this.storageKey = 'lc_configurador_v3';
        }

        save() {
            try {
                const stateData = this.state.serialize();
                const engineData = this.furnitureEngine.getLastResult();
                const data = {
                    version: '3.0',
                    state: stateData.state,
                    engineering: engineData ? {
                        parts: engineData.parts.map(p => p.toJSON ? p.toJSON() : p),
                        hardware: engineData.hardware.map(h => h.toJSON ? h.toJSON() : h),
                        valid: engineData.valid,
                        errors: engineData.errors,
                        warnings: engineData.warnings,
                    } : null,
                };
                localStorage.setItem(this.storageKey, JSON.stringify(data));
                return true;
            } catch (e) { console.warn('Save error:', e); return false; }
        }

        load() {
            try {
                const raw = localStorage.getItem(this.storageKey);
                if (!raw) return null;
                return JSON.parse(raw);
            } catch (e) { return null; }
        }

        clear() { localStorage.removeItem(this.storageKey); }

        generateShareUrl() {
            try {
                const stateData = this.state.serialize();
                const json = JSON.stringify(stateData);
                const encoded = btoa(unescape(encodeURIComponent(json)));
                return `${location.origin}${location.pathname}#p=${encoded}`;
            } catch (e) { return location.href; }
        }

        loadFromHash() {
            const match = location.hash.match(/p=([^&]+)/);
            if (!match) return null;
            try {
                const json = decodeURIComponent(escape(atob(decodeURIComponent(match[1]))));
                return JSON.parse(json);
            } catch (e) { return null; }
        }

        generateWhatsAppMessage() {
            const s = this.state.get();
            const eng = this.furnitureEngine.getLastResult();
            const lines = [
                'Olá! Montei um móvel no configurador da LC Soluções em Móveis:',
                '',
                `*Móvel:* ${s.typeLabel}`,
                `*Medidas:* ${s.dimensions.width} × ${s.dimensions.height} × ${s.dimensions.depth} cm`,
                `*Portas:* ${s.structure.doors} | *Gavetas:* ${s.structure.drawers} | *Prateleiras:* ${s.structure.shelves}`,
                `*Cor:* ${s.colorLabel}`,
                `*Acabamento:* ${s.finishLabel}`,
                `*Puxador:* ${s.handleLabel}`,
            ];
            if (s.lighting.enabled) lines.push('*LED:* Sim');
            if (eng && eng.valid) {
                lines.push('', `*Peças:* ${eng.parts.length} itens`);
                lines.push(`*Área MDF:* ${eng.parts.reduce((sum, p) => sum + (p.width / 1000) * (p.height / 1000), 0).toFixed(2)} m²`);
                const hwSummary = eng.hardware.map(h => `${h.name}: ${h.quantity}x`).join(', ');
                if (hwSummary) lines.push(`*Ferragens:* ${hwSummary}`);
            }
            lines.push('', 'Gostaria de mais informações sobre este projeto.');
            return encodeURIComponent(lines.join('\n'));
        }
    }

    // ============================================================
    // UI MANAGER
    // ============================================================
    class UIManager {
        constructor(state) {
            this.state = state;
            this.elements = {};
            this.onConfigChange = null;
            this._cacheElements();
            this._bindEvents();
            this._initCatalogs();
        }

        _cacheElements() {
            const ids = [
                'cfgType', 'cfgWidth', 'cfgHeight', 'cfgDepth',
                'cfgCustom', 'cfgFinish', 'cfgOpen', 'cfgLED',
                'valDoors', 'valShelves', 'valDrawers', 'valDividers',
                'btnDoorsLess', 'btnDoorsMore', 'btnShelvesLess', 'btnShelvesMore',
                'btnDrawersLess', 'btnDrawersMore', 'btnDividersLess', 'btnDividersMore',
                'btnResetCamera', 'btnFront',
                'btnSide', 'btnTop', 'btnOrcamento', 'btnSalvar', 'btnLink',
                'btnImg', 'btnLimpar', 'cfgSummary', 'cfgTypeTitle', 'cfgHint',
                'stepDoors', 'stepShelves', 'stepDrawers', 'stepDividers', 'colorGrid', 'hardwareGrid',
                'cfgToast', 'btnToggleDoors', 'btnToggleDrawers',
            ];
            ids.forEach(id => { this.elements[id] = document.getElementById(id); });
        }

        _bindEvents() {
            const e = this.elements;
            const emit = () => this.onConfigChange && this.onConfigChange({ type: 'config' });

            if (e.cfgType) e.cfgType.addEventListener('change', emit);
            if (e.cfgCustom) e.cfgCustom.addEventListener('input', emit);
            if (e.cfgFinish) e.cfgFinish.addEventListener('change', emit);
            if (e.cfgOpen) e.cfgOpen.addEventListener('change', emit);
            if (e.cfgLED) e.cfgLED.addEventListener('change', emit);
            if (e.cfgWidth) e.cfgWidth.addEventListener('input', emit);
            if (e.cfgHeight) e.cfgHeight.addEventListener('input', emit);
            if (e.cfgDepth) e.cfgDepth.addEventListener('input', emit);

            if (e.btnDoorsLess) e.btnDoorsLess.addEventListener('click', () => this._adjustCounter('doors', -1));
            if (e.btnDoorsMore) e.btnDoorsMore.addEventListener('click', () => this._adjustCounter('doors', 1));
            if (e.btnShelvesLess) e.btnShelvesLess.addEventListener('click', () => this._adjustCounter('shelves', -1));
            if (e.btnShelvesMore) e.btnShelvesMore.addEventListener('click', () => this._adjustCounter('shelves', 1));
            if (e.btnDrawersLess) e.btnDrawersLess.addEventListener('click', () => this._adjustCounter('drawers', -1));
            if (e.btnDrawersMore) e.btnDrawersMore.addEventListener('click', () => this._adjustCounter('drawers', 1));
            if (e.btnDividersLess) e.btnDividersLess.addEventListener('click', () => this._adjustCounter('dividers', -1));
            if (e.btnDividersMore) e.btnDividersMore.addEventListener('click', () => this._adjustCounter('dividers', 1));

            if (e.btnResetCamera) e.btnResetCamera.addEventListener('click', () => this._fireCamera('three_quarter'));
            if (e.btnFront) e.btnFront.addEventListener('click', () => this._fireCamera('front'));
            if (e.btnSide) e.btnSide.addEventListener('click', () => this._fireCamera('side'));
            if (e.btnTop) e.btnTop.addEventListener('click', () => this._fireCamera('top'));
        }

        _adjustCounter(type, delta) {
            if (this.onConfigChange) this.onConfigChange({ type: 'counter', counter: type, delta });
        }

        _fireCamera(preset) {
            if (this.onConfigChange) this.onConfigChange({ type: 'camera', preset });
        }

        _initCatalogs() {
            const colorGrid = this.elements.colorGrid;
            if (colorGrid) {
                colorGrid.innerHTML = '';
                COLOR_PRESETS.forEach(color => {
                    const btn = document.createElement('button');
                    btn.className = 'swatch-btn';
                    btn.title = color.name;
                    btn.dataset.colorId = color.id;
                    btn.innerHTML = `<span class="swatch-preview" style="background:${color.hex};"></span><span class="swatch-name">${color.name}</span>`;
                    btn.addEventListener('click', () => {
                        if (this.elements.cfgCustom) {
                            this.elements.cfgCustom.value = color.hex;
                            this.onConfigChange && this.onConfigChange({ type: 'config' });
                        }
                    });
                    colorGrid.appendChild(btn);
                });
            }

            const hwGrid = this.elements.hardwareGrid;
            if (hwGrid) {
                hwGrid.innerHTML = '';
                HANDLE_TYPES.forEach(handle => {
                    const btn = document.createElement('button');
                    btn.className = 'hw-btn';
                    btn.dataset.handleId = handle.id;
                    btn.type = 'button';
                    btn.innerHTML = `<span class="hw-name">${handle.name}</span>`;
                    btn.addEventListener('click', (ev) => {
                        ev.preventDefault();
                        ev.stopPropagation();
                        this._updateHardwareSelection(handle.id);
                        this.onConfigChange && this.onConfigChange({ type: 'config' });
                    });
                    hwGrid.appendChild(btn);
                });
                this._updateHardwareSelection('alca');
            }
        }

        _updateHardwareSelection(selectedId) {
            const grid = this.elements.hardwareGrid;
            if (!grid) return;
            grid.querySelectorAll('.hw-btn').forEach(btn => {
                btn.classList.toggle('active', btn.dataset.handleId === selectedId);
            });
        }

        getConfigFromUI() {
            const e = this.elements;
            const s = this.state.get();
            const activeHandle = e.hardwareGrid?.querySelector('.hw-btn.active');
            const handleId = activeHandle?.dataset.handleId || s.hardware.handle;

            return {
                furnitureType: e.cfgType?.value || s.furnitureType,
                dimensions: {
                    width: parseInt(e.cfgWidth?.value) || s.dimensions.width,
                    height: parseInt(e.cfgHeight?.value) || s.dimensions.height,
                    depth: parseInt(e.cfgDepth?.value) || s.dimensions.depth,
                },
                structure: {
                    doors: parseInt(e.valDoors?.textContent) || 0,
                    drawers: parseInt(e.valDrawers?.textContent) || 0,
                    shelves: parseInt(e.valShelves?.textContent) || 0,
                    dividers: parseInt(e.valDividers?.textContent) || 0,
                },
                material: {
                    color: s.material.color,
                    customColor: e.cfgCustom?.value || null,
                    finish: e.cfgFinish?.value || s.material.finish,
                },
                hardware: { handle: handleId },
                lighting: { enabled: e.cfgLED?.checked || false },
                view: {
                    doorsOpen: e.cfgOpen?.checked || false,
                    drawersOpen: s.view.drawersOpen,
                },
            };
        }

        updateUIFromConfig(config) {
            const e = this.elements;
            if (e.cfgType && config.furnitureType) e.cfgType.value = config.furnitureType;
            if (e.cfgWidth) e.cfgWidth.value = config.dimensions.width;
            if (e.cfgHeight) e.cfgHeight.value = config.dimensions.height;
            if (e.cfgDepth) e.cfgDepth.value = config.dimensions.depth;
            if (e.valDoors) e.valDoors.textContent = config.structure.doors;
            if (e.valShelves) e.valShelves.textContent = config.structure.shelves;
            if (e.valDrawers) e.valDrawers.textContent = config.structure.drawers;
            if (e.valDividers) e.valDividers.textContent = config.structure.dividers || 0;
            if (e.cfgCustom && config.material.customColor) e.cfgCustom.value = config.material.customColor;
            if (e.cfgFinish && config.material.finish) e.cfgFinish.value = config.material.finish;
            if (e.cfgOpen) e.cfgOpen.checked = config.view.doorsOpen || false;
            if (e.cfgLED) e.cfgLED.checked = config.lighting.enabled || false;
            if (e.cfgTypeTitle && config.typeLabel) e.cfgTypeTitle.textContent = config.typeLabel;
            if (e.cfgHint && config.hint) e.cfgHint.textContent = config.hint;
            this._updateHardwareSelection(config.hardware.handle);
        }

        updateSummary(text) {
            if (this.elements.cfgSummary) this.elements.cfgSummary.textContent = text;
        }

        showToast(msg) {
            const toast = this.elements.cfgToast;
            if (!toast) return;
            toast.textContent = msg;
            toast.classList.add('show');
            setTimeout(() => toast.classList.remove('show'), 2000);
        }

        updateStepVisibility(typeKey) {
            const e = this.elements;
            const typeDef = FURNITURE_CATALOG[typeKey];
            if (!typeDef) return;
            const max = typeDef.limits;
            if (e.stepDoors) e.stepDoors.style.display = (max.maxDoors || 0) > 0 ? '' : 'none';
            if (e.stepShelves) e.stepShelves.style.display = (max.maxShelves || 0) > 0 ? '' : 'none';
            if (e.stepDrawers) e.stepDrawers.style.display = (max.maxDrawers || 0) > 0 ? '' : 'none';
        }
    }

    // ============================================================
    // MAIN APPLICATION
    // ============================================================
    class ConfiguratorApp {
        constructor() {
            this.state = new ConfiguratorState();
            this.initialized = false;
        }

        init() {
            const canvas = document.getElementById('cfgCanvas');
            if (!canvas) return;

            this.renderingEngine = new RenderingEngine(canvas);
            this.renderingEngine.init();

            this.materialEngine = new MaterialEngine();
            this.furnitureEngine = new FurnitureEngine(this.renderingEngine, this.materialEngine);
            this.interaction = new InteractionManager(this.renderingEngine, this.furnitureEngine);
            this.ui = new UIManager(this.state);
            this.ui.onConfigChange = (event) => this.handleUIChange(event);
            this.persistence = new PersistenceManager(this.state, this.furnitureEngine);

            this.loadInitialConfig();
            this.buildFurniture();
            this.updateSummary();
            this.bindActionButtons();

            this.renderingEngine.startLoop();
            window.addEventListener('resize', () => this.renderingEngine.onResize());

            this.initialized = true;
        }

        loadInitialConfig() {
            const fromHash = this.persistence.loadFromHash();
            if (fromHash) {
                this.state.deserialize(fromHash);
                this.syncTypeDefaults();
                this.ui.updateUIFromConfig(this.state.get());
                return;
            }
            const fromStorage = this.persistence.load();
            if (fromStorage) {
                this.state.deserialize(fromStorage);
                this.syncTypeDefaults();
                this.ui.updateUIFromConfig(this.state.get());
            }
        }

        syncTypeDefaults() {
            const s = this.state.get();
            const typeDef = FURNITURE_CATALOG[s.furnitureType];
            if (!typeDef) return;

            // Atualizar dimensões e estrutura com padrões do template
            this.state.set({
                mounted: typeDef.mounted,
                typeLabel: typeDef.label,
                hint: typeDef.description,
                dimensions: { ...typeDef.dimensions },
                structure: {
                    doors: typeDef.defaults.doors || 0,
                    drawers: typeDef.defaults.drawers || 0,
                    shelves: typeDef.defaults.shelves || 0,
                    dividers: typeDef.defaults.dividers || 0,
                },
            });
            this.ui.updateStepVisibility(s.furnitureType);
            this.ui.updateUIFromConfig(this.state.get());
        }

        handleUIChange(event) {
            if (!this.initialized) return;

            if (event.type === 'counter') {
                const s = this.state.get();
                const typeDef = FURNITURE_CATALOG[s.furnitureType];
                const maxKey = event.counter === 'doors' ? 'maxDoors' : event.counter === 'shelves' ? 'maxShelves' : event.counter === 'drawers' ? 'maxDrawers' : 'maxDividers';
                const max = typeDef?.limits?.[maxKey] || 0;
                const current = s.structure[event.counter] || 0;
                const next = current + event.delta;
                if (next >= 0 && next <= max) {
                    this.state.set({ structure: { ...s.structure, [event.counter]: next } });
                    this.ui.updateUIFromConfig(this.state.get());
                    this.buildFurniture();
                    this.updateSummary();
                }
            } else if (event.type === 'camera') {
                this.renderingEngine.setCameraPreset(event.preset);
            } else {
                this.readConfigFromUI();
                this.buildFurniture();
                this.updateSummary();
            }
        }

        readConfigFromUI() {
            const uiConfig = this.ui.getConfigFromUI();
            const s = this.state.get();

            const colorDef = COLOR_PRESETS.find(c => c.hex === uiConfig.material.customColor);
            const colorLabel = colorDef?.name || COLOR_PRESETS.find(c => c.id === s.material.color)?.name || 'Personalizado';
            const finishDef = FINISH_TYPES.find(f => f.id === uiConfig.material.finish);
            const finishLabel = finishDef?.name || uiConfig.material.finish;
            const handleDef = HANDLE_TYPES.find(f => f.id === uiConfig.hardware.handle);
            const handleLabel = handleDef?.name || uiConfig.hardware.handle;

            // Se mudou o tipo, sincronizar padrões
            if (uiConfig.furnitureType !== s.furnitureType) {
                this.state.set({ ...uiConfig, colorLabel, finishLabel, handleLabel });
                this.syncTypeDefaults();
            } else {
                this.state.set({ ...uiConfig, colorLabel, finishLabel, handleLabel });
            }
        }

        buildFurniture() {
            const config = this.state.get();
            const result = this.furnitureEngine.build(config);
            if (config.view.doorsOpen) this.furnitureEngine.setDoorsOpen(true);
            this.lastEngineResult = result;
            this.updateEngineeringPanel(result);
        }

        updateEngineeringPanel(result) {
            if (!result || !result.valid) return;

            // Atualizar lista de peças
            const partsList = document.getElementById('partsList');
            const partsCount = document.getElementById('partsCount');
            if (partsList) {
                partsList.innerHTML = '';
                result.parts.forEach(part => {
                    const item = document.createElement('div');
                    item.className = 'engineering-item';
                    item.innerHTML = `
                        <span class="part-name">${part.name}</span>
                        <span class="part-dims">${Math.round(part.width)}×${Math.round(part.height)}×${part.thickness}mm</span>
                    `;
                    partsList.appendChild(item);
                });
                if (partsCount) partsCount.textContent = `${result.parts.length} itens`;
            }

            // Atualizar lista de ferragens
            const hardwareList = document.getElementById('hardwareList');
            const hardwareCount = document.getElementById('hardwareCount');
            if (hardwareList) {
                hardwareList.innerHTML = '';
                result.hardware.forEach(hw => {
                    const item = document.createElement('div');
                    item.className = 'engineering-item';
                    item.innerHTML = `
                        <span class="hw-name">${hw.name}</span>
                        <span class="hw-qty">${hw.quantity}x</span>
                    `;
                    hardwareList.appendChild(item);
                });
                if (hardwareCount) hardwareCount.textContent = `${result.hardware.length} itens`;
            }

            // Atualizar área total
            const totalArea = document.getElementById('totalArea');
            if (totalArea) {
                const area = result.parts.reduce((sum, p) => sum + (p.width / 1000) * (p.height / 1000), 0);
                totalArea.textContent = `${area.toFixed(2)} m²`;
            }
        }

        updateSummary() {
            const s = this.state.get();
            const summary = `${s.typeLabel} · ${s.dimensions.width}L × ${s.dimensions.height}A × ${s.dimensions.depth}P · ${s.colorLabel} · ${s.finishLabel}`;
            this.ui.updateSummary(summary);
        }

        bindActionButtons() {
            const waNumber = '556732537898';

            document.getElementById('btnOrcamento')?.addEventListener('click', () => {
                const msg = this.persistence.generateWhatsAppMessage();
                window.open('https://wa.me/' + waNumber + '?text=' + msg, '_blank');
            });

            document.getElementById('btnSalvar')?.addEventListener('click', () => {
                const ok = this.persistence.save();
                this.ui.showToast(ok ? 'Projeto salvo neste navegador' : 'Não foi possível salvar');
            });

            document.getElementById('btnLink')?.addEventListener('click', () => {
                const url = this.persistence.generateShareUrl();
                try { history.replaceState(null, '', url); } catch(e) {}
                navigator.clipboard?.writeText(url).then(
                    () => this.ui.showToast('Link copiado!'),
                    () => this.ui.showToast('Link na barra de endereço')
                );
            });

            document.getElementById('btnImg')?.addEventListener('click', () => {
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
                } catch(e) { this.ui.showToast('Erro ao gerar imagem'); }
            });

            document.getElementById('btnLimpar')?.addEventListener('click', () => {
                this.persistence.clear();
                try { history.replaceState(null, '', location.pathname); } catch(e) {}
                this.state = new ConfiguratorState();
                this.ui.updateUIFromConfig(this.state.get());
                this.buildFurniture();
                this.updateSummary();
                this.ui.showToast('Configuração reiniciada');
            });

            document.getElementById('btnToggleDoors')?.addEventListener('click', () => {
                const s = this.state.get();
                const anyOpen = this.furnitureEngine.doors.some(d => d.userData.open);
                this.furnitureEngine.setDoorsOpen(!anyOpen);
                this.state.set({ view: { ...s.view, doorsOpen: !anyOpen } });
            });

            document.getElementById('btnToggleDrawers')?.addEventListener('click', () => {
                const s = this.state.get();
                const anyOpen = this.furnitureEngine.drawers.some(d => d.userData.open);
                this.furnitureEngine.setAllDrawersOpen(!anyOpen);
                this.state.set({ view: { ...s.view, drawersOpen: !anyOpen } });
            });
        }
    }

    // ============================================================
    // BOOT
    // ============================================================
    function boot() {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => {
                window.__configuratorApp = new ConfiguratorApp();
                window.__configuratorApp.init();
            });
        } else {
            window.__configuratorApp = new ConfiguratorApp();
            window.__configuratorApp.init();
        }
    }

    // Wait for THREE to be available
    function waitForTHREE() {
        if (typeof THREE !== 'undefined' && THREE.WebGLRenderer && THREE.OrbitControls) {
            boot();
        } else {
            setTimeout(waitForTHREE, 100);
        }
    }

    waitForTHREE();

})();