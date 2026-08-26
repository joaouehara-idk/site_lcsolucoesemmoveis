// Configurator 3D - All Modules Bundled
window.LC = window.LC || {};
window.LC.Configurator = window.LC.Configurator || {};

(function() {
'use strict';

/**
 * Debug / Diagnostic Logger for the 3D Configurator
 * Provides real-time diagnostic messages displayed in the UI overlay.
 */
window.LC.Configurator.DebugLogger = class {
    constructor(containerId = 'cfgDebug') {
        this.steps = [];
        this.steps = [];
        this.errors = [];
        this.enabled = true;
        this.initContainer();
    }

    initContainer() {
        const existing = document.getElementById(this.containerId);
        if (existing) {
            this.container = existing;
            return;
        }
        const div = document.createElement('div');
        div.id = this.containerId;
        div.className = 'cfg-debug';
        div.style.cssText = [
            'position:fixed',
            'bottom:12px',
            'right:12px',
            'z-index:9999',
            'background:rgba(26,23,20,0.92)',
            'color:#fff',
            'font-family:monospace',
            'font-size:0.72rem',
            'padding:8px 10px',
            'border-radius:6px',
            'max-width:320px',
            'max-height:240px',
            'overflow-y:auto',
            'backdrop-filter:blur(4px)',
            'box-shadow:0 4px 20px rgba(0,0,0,0.3)',
            'display:none',
        ].join(';');
        document.body.appendChild(div);
        this.container = div;
    }

    step(msg) {
        this.steps.push({ msg, ts: Date.now(), level: 'info' });
        this.log(msg, 'info');
    }

    info(msg) { this.log(msg, 'info'); }

    warn(msg) {
        this.errors.push({ msg, ts: Date.now(), level: 'warn' });
        this.log(msg, 'warn');
    }

    error(msg, err) {
        this.errors.push({ msg: err ? err.message : msg, ts: Date.now(), level: 'error' });
        if (err && console) console.error('[Configurator3D]', msg, err);
        else if (console) console.error('[Configurator3D]', msg);
        this.log(msg, 'error');
    }

    log(msg, level = 'info') {
        if (!this.enabled || !console) return;
        const prefix = '[3D]';
        switch (level) {
            case 'info':  console.log(prefix, msg); break;
            case 'warn':  console.warn(prefix, msg); break;
            case 'error': console.error(prefix, msg); break;
        }
        this.render();
    }

    render() {
        if (!this.container || !this.enabled) return;
        let html = '';
        const recent = this.steps.concat(this.errors).slice(-20);
        for (const s of recent) {
            const label = s.level === 'error' ? '✗' : s.level === 'warn' ? '!' : '✓';
            html += `<div style="margin:2px 0;color:${s.level === 'error' ? '#ff6b6b' : s.level === 'warn' ? '#ffd43b' : '#51cf66'};">${label} ${this.escapeHtml(s.msg)}</div>`;
        }
        this.container.innerHTML = html;
        this.show();
    }

    show() {
        if (this.container) this.container.style.display = 'block';
    }

    hide() {
        if (this.container) this.container.style.display = 'none';
    }

    escapeHtml(str) {
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;');
    }

    getSummary() {
        return {
            steps: this.steps.length,
            errors: this.errors.length,
            allSteps: this.steps.map(s => s.msg),
            allErrors: this.errors.map(e => e.msg),
        };
    }
}


/**
 * Geometry Utilities
 * Helper functions for 3D geometry calculations.
 * Requires THREE to be passed for methods that use THREE classes.
 */
window.LC.Configurator.GeometryUtils = class {
    static cmToMeters(cm) {
        return cm * 0.01;
    }

    static metersToCm(m) {
        return m * 100;
    }

    static clamp(value, min, max) {
        return Math.min(Math.max(value, min), max);
    }

    static lerp(a, b, t) {
        return a + (b - a) * t;
    }

    static degToRad(deg) {
        return deg * Math.PI / 180;
    }

    static radToDeg(rad) {
        return rad * 180 / Math.PI;
    }

    static getBoundingBox(THREE, object3D) {
        const box = new THREE.Box3().setFromObject(object3D);
        const size = new THREE.Vector3();
        box.getSize(size);
        const center = new THREE.Vector3();
        box.getCenter(center);
        return { box, size, center };
    }

    static fitCameraToGroup(THREE, camera, controls, group, distanceFactor = 1.5) {
        const { size, center } = this.getBoundingBox(THREE, group);
        const maxDim = Math.max(size.x, size.y, size.z);
        const fov = camera.fov * (Math.PI / 180);
        let distance = maxDim / (2 * Math.tan(fov / 2));
        distance *= distanceFactor;

        const dir = new THREE.Vector3();
        const eye = new THREE.Vector3();
        dir.subVectors(camera.position, controls.target).normalize();
        eye.copy(center).addScaledVector(dir, distance);

        camera.position.copy(eye);
        controls.target.copy(center);
        controls.update();
    }
}


/**
 * Furniture Type Catalog
 * Defines available furniture types with their constraints.
 * Structured for easy extension with new furniture types.
 *
 * IMPORTANT: This is a structural template. Real furniture models will be
 * added later (GLB files in /assets/models/furniture/). Each entry can
 * reference a GLB model; if none exists, the procedural builder is used.
 *
 * Convention: 1 Three.js unit = 1 meter. Dimensions shown in cm.
 */
window.LC.Configurator.FURNITURE_CATALOG = {
    guarda_roupa: {
        label: 'Guarda-roupa planejado',
        category: 'quarto',
        dimensions: { W: 180, H: 220, D: 55 },
        defaults: { doors: 2, shelves: 3, drawers: 0, dividerCount: 0 },
        limits: {
            minW: 40,  maxW: 400,
            minH: 30,  maxH: 300,
            minD: 20,  maxD: 80,
            maxDoors: 8, maxShelves: 10, maxDrawers: 6, maxDividers: 4,
        },
        mounted: false,
        backThickness: 0.8,
        modelPath: null,
        description: 'Ideal para quartos: 120–240 cm de largura.',
        componentSchema: {
            carcass: ['left_side', 'right_side', 'top_panel', 'bottom_panel', 'back_panel'],
            doors: ['pivot', 'panel', 'handle'],
            drawers: ['front', 'sides', 'bottom', 'back', 'handle'],
            shelves: 'shelf',
            dividers: 'divider',
            handles: ['alca', 'botao', 'cava', 'nenhum'],
            led: true,
        },
    },
    nicho: {
        label: 'Nicho',
        category: 'decorativo',
        dimensions: { W: 80, H: 80, D: 30 },
        defaults: { doors: 0, shelves: 3, drawers: 0, dividerCount: 0 },
        limits: {
            minW: 40, maxW: 200,
            minH: 30, maxH: 240,
            minD: 15, maxD: 50,
            maxDoors: 0, maxShelves: 8, maxDrawers: 0, maxDividers: 3,
        },
        mounted: true,
        backThickness: 0.6,
        modelPath: null,
        description: 'Suspenso. Altura comum: 40–120 cm.',
    },
    aereo: {
        label: 'Móvel Aéreo',
        category: 'cozinha',
        dimensions: { W: 120, H: 40, D: 30 },
        defaults: { doors: 2, shelves: 2, drawers: 0, dividerCount: 0 },
        limits: {
            minW: 60, maxW: 300,
            minH: 20, maxH: 90,
            minD: 20, maxD: 50,
            maxDoors: 6, maxShelves: 8, maxDrawers: 0, maxDividers: 3,
        },
        mounted: true,
        backThickness: 0.6,
        modelPath: null,
        description: 'Suspenso, acima de bancadas ou tanque.',
    },
    estante: {
        label: 'Estante',
        category: 'sala',
        dimensions: { W: 90, H: 200, D: 35 },
        defaults: { doors: 0, shelves: 5, drawers: 0, dividerCount: 0 },
        limits: {
            minW: 60, maxW: 200,
            minH: 60, maxH: 260,
            minD: 25, maxD: 50,
            maxDoors: 0, maxShelves: 12, maxDrawers: 0, maxDividers: 4,
        },
        mounted: false,
        backThickness: 0.8,
        modelPath: null,
        description: 'Largura 60–120 cm, altura até 240 cm.',
    },
    painel_tv: {
        label: 'Painel para TV',
        category: 'sala',
        dimensions: { W: 180, H: 50, D: 35 },
        defaults: { doors: 0, shelves: 2, drawers: 1, dividerCount: 0 },
        limits: {
            minW: 80, maxW: 300,
            minH: 30, maxH: 120,
            minD: 25, maxD: 50,
            maxDoors: 0, maxShelves: 6, maxDrawers: 3, maxDividers: 2,
        },
        mounted: false,
        backThickness: 0.8,
        modelPath: null,
        description: 'Altura 40–60 cm para base de TV.',
    },
    cozinha: {
        label: 'Armário de Cozinha',
        category: 'cozinha',
        dimensions: { W: 120, H: 90, D: 60 },
        defaults: { doors: 2, shelves: 2, drawers: 2, dividerCount: 1 },
        limits: {
            minW: 30, maxW: 400,
            minH: 30, maxH: 240,
            minD: 50, maxD: 80,
            maxDoors: 8, maxShelves: 8, maxDrawers: 8, maxDividers: 6,
        },
        mounted: false,
        backThickness: 0.8,
        modelPath: null,
        description: 'Bancada padrão: profundidade 60 cm.',
    },
    closet: {
        label: 'Closet',
        category: 'quarto',
        dimensions: { W: 240, H: 240, D: 60 },
        defaults: { doors: 3, shelves: 4, drawers: 2, dividerCount: 2 },
        limits: {
            minW: 120, maxW: 400,
            minH: 180, maxH: 300,
            minD: 50, maxD: 80,
            maxDoors: 12, maxShelves: 12, maxDrawers: 8, maxDividers: 6,
        },
        mounted: false,
        backThickness: 0.8,
        modelPath: null,
        description: 'Largura ampla: 200–360 cm.',
    },
    comoda: {
        label: 'Cômoda',
        category: 'quarto',
        dimensions: { W: 120, H: 80, D: 45 },
        defaults: { doors: 0, shelves: 0, drawers: 4, dividerCount: 0 },
        limits: {
            minW: 60, maxW: 200,
            minH: 30, maxH: 120,
            minD: 35, maxD: 60,
            maxDoors: 0, maxShelves: 4, maxDrawers: 10, maxDividers: 0,
        },
        mounted: false,
        backThickness: 0.8,
        modelPath: null,
        description: 'Altura 70–90 cm, 4–6 gavetas.',
    },
};

window.LC.Configurator.FURNITURE_CATEGORIES = [
    { id: 'quarto', label: 'Quarto' },
    { id: 'cozinha', label: 'Cozinha' },
    { id: 'sala', label: 'Sala' },
    { id: 'escritorio', label: 'Home Office' },
    { id: 'decorativo', label: 'Decorativo' },
    { id: 'comercial', label: 'Comercial' },
];

window.LC.Configurator.getFurnitureType = function(key) {
    return FURNITURE_CATALOG[key] || null;
}

window.LC.Configurator.getAllFurnitureTypes = function() {
    return Object.entries(FURNITURE_CATALOG).map(([key, def]) => ({
        key,
        ...def,
    }));
}


/**
 * Material Catalog Data
 *
 * STRUCTURE (ready for manufacturer data):
 *   brands → lines → patterns → textures → finishes → thicknesses → assets
 *
 * Current state: placeholder color palette. Real manufacturer data
 * (Greenplac, Duratex, Arauco, etc.) will populate this structure when
 * the data is confirmed by the LC team.
 *
 * Rule: "available_at_lc" must be true ONLY for materials the LC actually
 * stocks. Items with available_at_lc=false are "Referência de catálogo".
 */
window.LC.Configurator.MATERIAL_BRANDS = [
    {
        id: 'generic',
        name: 'Cor padrão LC',
        available_at_lc: true,
        description: 'Cores padrão oferecidas pela LC Soluções em Móveis',
    },
    {
        id: 'greenplac',
        name: 'Greenplac',
        available_at_lc: false,
        description: 'Fabricante de MDF — aguardando confirmação de disponibilidade na LC',
    },
    {
        id: 'duratex',
        name: 'Duratex',
        available_at_lc: false,
        description: 'Fabricante de MDF — aguardando confirmação de disponibilidade na LC',
    },
    {
        id: 'arauco',
        name: 'Arauco',
        available_at_lc: false,
        description: 'Fabricante de MDF — aguardando confirmação de disponibilidade na LC',
    },
    {
        id: 'guararapes',
        name: 'Guararapes',
        available_at_lc: false,
        description: 'Fabricante de MDF — aguardando confirmação de disponibilidade na LC',
    },
    {
        id: 'berneck',
        name: 'Berneck',
        available_at_lc: false,
        description: 'Fabricante de MDF — aguardando confirmação de disponibilidade na LC',
    },
    {
        id: 'eucatex',
        name: 'Eucatex',
        available_at_lc: false,
        description: 'Fabricante de MDF — aguardando confirmação de disponibilidade na LC',
    },
    {
        id: 'placas_do_brasil',
        name: 'Placas do Brasil',
        available_at_lc: false,
        description: 'Fabricante de MDF — aguardando confirmação de disponibilidade na LC',
    },
    {
        id: 'sudati',
        name: 'Sudati',
        available_at_lc: false,
        description: 'Fabricante de MDF — aguardando confirmação de disponibilidade na LC',
    },
    {
        id: 'floraplac',
        name: 'Floraplac / Flora',
        available_at_lc: false,
        description: 'Fabricante de MDF — aguardando confirmação de disponibilidade na LC',
    },
    {
        id: 'fibraplac',
        name: 'Fibraplac',
        available_at_lc: false,
        description: 'Fabricante de MDF — aguardando confirmação de disponibilidade na LC',
    },
];

// Material lines (mapped to brands)
window.LC.Configurator.MATERIAL_LINES = [
    // Generic LC standard colors
    { id: 'lc_padrao', brand_id: 'generic', name: 'Cores padrão LC', available_at_lc: true },
    // Greenplac lines (placeholder until confirmed)
    { id: 'greenplac_colore', brand_id: 'greenplac', name: 'Colore', available_at_lc: false },
    { id: 'greenplac_texture', brand_id: 'greenplac', name: 'Texture', available_at_lc: false },
    { id: 'greenplac_essenziale', brand_id: 'greenplac', name: 'Essenziale', available_at_lc: false },
    { id: 'greenplac_matiz', brand_id: 'greenplac', name: 'Matiz', available_at_lc: false },
    { id: 'greenplac_natural', brand_id: 'greenplac', name: 'Natural', available_at_lc: false },
    { id: 'greenplac_decore', brand_id: 'greenplac', name: 'Decore', available_at_lc: false },
    { id: 'greenplac_classicos', brand_id: 'greenplac', name: 'Clássicos', available_at_lc: false },
    { id: 'greenplac_mdf_green', brand_id: 'greenplac', name: 'MDF Green', available_at_lc: false },
    // Duratex lines (placeholder until confirmed)
    { id: 'duratex_essencial', brand_id: 'duratex', name: 'Essencial', available_at_lc: false },
    { id: 'duratex_design', brand_id: 'duratex', name: 'Design', available_at_lc: false },
    { id: 'duratex_cristallo', brand_id: 'duratex', name: 'Cristallo', available_at_lc: false },
    { id: 'duratex_velluto', brand_id: 'duratex', name: 'Velluto', available_at_lc: false },
    { id: 'duratex_trama', brand_id: 'duratex', name: 'Trama', available_at_lc: false },
    { id: 'duratex_conceito', brand_id: 'duratex', name: 'Conceito', available_at_lc: false },
    { id: 'duratex_acetinatta', brand_id: 'duratex', name: 'Acetinatta', available_at_lc: false },
    { id: 'duratex_you', brand_id: 'duratex', name: 'Duratex You', available_at_lc: false },
    // Arauco lines (placeholder until confirmed)
    { id: 'arauco_madeiras', brand_id: 'arauco', name: 'Madeiras', available_at_lc: false },
    { id: 'arauco_madeiras_brasileiras', brand_id: 'arauco', name: 'Madeiras Brasileiras', available_at_lc: false },
    { id: 'arauco_cores', brand_id: 'arauco', name: 'Cores', available_at_lc: false },
    { id: 'arauco_metais', brand_id: 'arauco', name: 'Metais', available_at_lc: false },
    { id: 'arauco_tecidos', brand_id: 'arauco', name: 'Tecidos', available_at_lc: false },
    { id: 'arauco_pedras', brand_id: 'arauco', name: 'Pedras', available_at_lc: false },
    // Arauco texture names (placeholder until confirmed)
    { id: 'arauco_bold', brand_id: 'arauco', name: 'Bold', available_at_lc: false },
    { id: 'arauco_chess', brand_id: 'arauco', name: 'Chess', available_at_lc: false },
    { id: 'arauco_couro', brand_id: 'arauco', name: 'Couro', available_at_lc: false },
    { id: 'arauco_dueto', brand_id: 'arauco', name: 'Dueto', available_at_lc: false },
    { id: 'arauco_fosco', brand_id: 'arauco', name: 'Fosco', available_at_lc: false },
    { id: 'arauco_liso', brand_id: 'arauco', name: 'Liso', available_at_lc: false },
    { id: 'arauco_matt', brand_id: 'arauco', name: 'Matt', available_at_lc: false },
    { id: 'arauco_nature', brand_id: 'arauco', name: 'Nature', available_at_lc: false },
    { id: 'arauco_poro', brand_id: 'arauco', name: 'Poro', available_at_lc: false },
    { id: 'arauco_sethos', brand_id: 'arauco', name: 'Sethos', available_at_lc: false },
    { id: 'arauco_trend', brand_id: 'arauco', name: 'Trend', available_at_lc: false },
    { id: 'arauco_tx', brand_id: 'arauco', name: 'TX', available_at_lc: false },
    { id: 'arauco_vert', brand_id: 'arauco', name: 'Vert', available_at_lc: false },
    // Guararapes, Berneck, Eucatex, etc. lines (placeholders)
    { id: 'guararapes_cores', brand_id: 'guararapes', name: 'Cores', available_at_lc: false },
    { id: 'guararapes_madeiras', brand_id: 'guararapes', name: 'Madeiras', available_at_lc: false },
    { id: 'berneck_mdf', brand_id: 'berneck', name: 'MDF', available_at_lc: false },
    { id: 'berneck_mdf_plus', brand_id: 'berneck', name: 'MDF PLUS', available_at_lc: false },
    { id: 'berneck_mdf_bp', brand_id: 'berneck', name: 'MDF BP', available_at_lc: false },
    { id: 'berneck_mdp', brand_id: 'berneck', name: 'MDP', available_at_lc: false },
    { id: 'berneck_hdf', brand_id: 'berneck', name: 'HDF', available_at_lc: false },
    { id: 'eucatex_bp', brand_id: 'eucatex', name: 'BP', available_at_lc: false },
    { id: 'eucatex_lacca', brand_id: 'eucatex', name: 'Lacca', available_at_lc: false },
    { id: 'eucatex_matt', brand_id: 'eucatex', name: 'Matt', available_at_lc: false },
    { id: 'eucatex_grafis', brand_id: 'eucatex', name: 'Grafis', available_at_lc: false },
    { id: 'eucatex_raizes', brand_id: 'eucatex', name: 'Raízes', available_at_lc: false },
    { id: 'eucatex_poro_supermatt', brand_id: 'eucatex', name: 'Poro Supermatt', available_at_lc: false },
];

window.LC.Configurator.FINISH_TYPES = [
    { id: 'melamina', name: 'Melamina', roughness: 0.75, metalness: 0.0, hasGrain: false },
    { id: 'texturizado', name: 'Texturizado (madeira)', roughness: 0.85, metalness: 0.0, hasGrain: true },
    { id: 'liso', name: 'Liso (fosco)', roughness: 0.55, metalness: 0.0, hasGrain: false },
    { id: 'laca', name: 'Laca (brilhante)', roughness: 0.15, metalness: 0.12, hasGrain: false },
    { id: 'madeira', name: 'Madeira natural', roughness: 0.8, metalness: 0.0, hasGrain: true },
    { id: 'acetinado', name: 'Acetinado', roughness: 0.45, metalness: 0.02, hasGrain: false },
];

window.LC.Configurator.COLOR_PRESETS = [
    { id: 'branco', name: 'Branco', hex: '#ECEAE3', finish: 'melamina', available_at_lc: true },
    { id: 'offwhite', name: 'Off-white', hex: '#E8E0D3', finish: 'melamina', available_at_lc: true },
    { id: 'bege', name: 'Bege', hex: '#D8C3A5', finish: 'melamina', available_at_lc: true },
    { id: 'caramelo', name: 'Caramelo', hex: '#C8A87C', finish: 'texturizado', available_at_lc: true },
    { id: 'madeira_clara', name: 'Madeira Clara', hex: '#C9A876', finish: 'texturizado', available_at_lc: true },
    { id: 'carvalho', name: 'Carvalho', hex: '#C9A876', finish: 'texturizado', available_at_lc: true },
    { id: 'nogueira', name: 'Nogueira', hex: '#6B5340', finish: 'texturizado', available_at_lc: true },
    { id: 'wenge', name: 'Wengué', hex: '#3A2E26', finish: 'texturizado', available_at_lc: true },
    { id: 'cinza', name: 'Cinza', hex: '#9A9A9A', finish: 'liso', available_at_lc: true },
    { id: 'grafite', name: 'Grafite', hex: '#4A4A4A', finish: 'liso', available_at_lc: true },
    { id: 'preto', name: 'Preto', hex: '#2B2B2B', finish: 'laco', available_at_lc: true },
];

window.LC.Configurator.MATERIAL_THICKNESSES = [
    { id: 6, label: '6 mm', available_at_lc: false },
    { id: 9, label: '9 mm', available_at_lc: false },
    { id: 12, label: '12 mm', available_at_lc: true },
    { id: 15, label: '15 mm', available_at_lc: true },
    { id: 18, label: '18 mm', available_at_lc: true },
    { id: 25, label: '25 mm', available_at_lc: false },
];

const COLOR_APPROX_WARNING =
    'As cores exibidas na tela são uma representação digital e podem apresentar diferenças em relação à amostra física. ' +
    'Para especificação final, considere a amostra física do fabricante.';


/**
 * Hardware Catalog Data
 *
 * STRUCTURE (ready for manufacturer data):
 *   brands → categories → products → variants → compatibility_rules
 *
 * Current state: generic handle types only. Real hardware data
 * (Blum, Hettich, Hafele, etc.) will populate this when confirmed.
 *
 * Rule: "available_at_lc" must be true ONLY for hardware the LC actually uses.
 */
window.LC.Configurator.HARDWARE_BRANDS = [
    {
        id: 'generic',
        name: 'Padrão LC',
        available_at_lc: true,
        description: 'Puxadores e ferragens padrão oferecidas pela LC',
    },
    { id: 'blum', name: 'Blum', available_at_lc: false, description: 'Aguardando confirmação' },
    { id: 'hettich', name: 'Hettich', available_at_lc: false, description: 'Aguardando confirmação' },
    { id: 'hafele', name: 'Häfele', available_at_lc: false, description: 'Aguardando confirmação' },
    { id: 'fgvtn', name: 'FGVTN', available_at_lc: false, description: 'Aguardando confirmação' },
    { id: 'bigfer', name: 'Bigfer', available_at_lc: false, description: 'Aguardando confirmação' },
    { id: 'renna', name: 'Renna', available_at_lc: false, description: 'Aguardando confirmação' },
    { id: 'rometal', name: 'Rometal', available_at_lc: false, description: 'Aguardando confirmação' },
    { id: 'ducasse', name: 'Ducasse', available_at_lc: false, description: 'Aguardando confirmação' },
];

window.LC.Configurator.HARDWARE_CATEGORIES = [
    { id: 'handles', name: 'Puxadores', icon: 'fa-hand-holding' },
    { id: 'hinges', name: 'dobradiças', icon: 'fa-cube' },
    { id: 'slides', name: 'corrediças', icon: 'fa-arrows-alt-h' },
    { id: 'tracks', name: 'trilhos', icon: 'fa-cube' },
    { id: 'lighting', name: 'iluminação', icon: 'fa-lightbulb' },
    { id: 'movimento', name: 'sistema de movimento', icon: 'fa-cog' },
];

window.LC.Configurator.HANDLE_TYPES = [
    {
        id: 'alca',
        name: 'Alça',
        brand_id: 'generic',
        available_at_lc: true,
        description: 'Puxador clássico em alça cilindrada.',
        material: 'aluminum',
        finish: 'preto',
        modelPath: null,
    },
    {
        id: 'botao',
        name: 'Botão',
        brand_id: 'generic',
        available_at_lc: true,
        description: 'Puxador embutido tipo botão.',
        material: 'aluminum',
        finish: 'preto',
        modelPath: null,
    },
    {
        id: 'cava',
        name: 'Cava (recesso)',
        brand_id: 'generic',
        available_at_lc: true,
        description: 'Puxador em cava reto com recesso na porta.',
        material: 'aluminum',
        finish: 'preto',
        modelPath: null,
    },
    {
        id: 'perfil',
        name: 'Perfil',
        brand_id: 'generic',
        available_at_lc: false,
        description: 'Puxador em perfil reto — pendente de especificação.',
        material: null,
        finish: null,
        modelPath: null,
    },
    {
        id: 'concha',
        name: 'Concha',
        brand_id: 'generic',
        available_at_lc: false,
        description: 'Puxador em formato de concha — pendente de especificação.',
        material: null,
        finish: null,
        modelPath: null,
    },
    {
        id: 'embutido',
        name: 'Embutido',
        brand_id: 'generic',
        available_at_lc: false,
        description: 'Puxador totalmente embutido — pendente de especificação.',
        material: null,
        finish: null,
        modelPath: null,
    },
    {
        id: 'nenhum',
        name: 'Nenhum',
        brand_id: 'generic',
        available_at_lc: true,
        description: 'Sem puxador (portas com fechamento automático ou cava puro).',
        material: null,
        finish: null,
        modelPath: null,
    },
];

window.LC.Configurator.HANDLE_MATERIALS = [
    { id: 'aluminio', name: 'Alumínio', available_at_lc: true },
    { id: 'inox', name: 'Inox', available_at_lc: false, description: 'Aguardando confirmação' },
    { id: 'zamac', name: 'Zamac', available_at_lc: false, description: 'Aguardando confirmação' },
];

window.LC.Configurator.HANDLE_FINISHES = [
    { id: 'preto', name: 'Preto', hex: '#1A1714', available_at_lc: true },
    { id: 'dourado', name: 'Dourado', hex: '#B8935A', available_at_lc: false, description: 'Aguardando confirmação' },
    { id: 'champanhe', name: 'Champanhe', hex: '#D4C8B0', available_at_lc: false, description: 'Aguardando confirmação' },
    { id: 'cromado', name: 'Cromado', hex: '#C0C0C0', available_at_lc: false, description: 'Aguardando confirmação' },
    { id: 'titanio', name: 'Titânio', hex: '#7A7A7A', available_at_lc: false, description: 'Aguardando confirmação' },
    { id: 'aco_escovado', name: 'Aço Escovado', hex: '#8A8A8A', available_at_lc: false, description: 'Aguardando confirmação' },
];

window.LC.Configurator.LED_OPTIONS = [
    { id: 'none', name: 'Sem LED', available_at_lc: true, modelPath: null },
    { id: 'fita_interno', name: 'Fita LED interna', available_at_lc: true, modelPath: null, description: 'Iluminação interna do guarda-roupa' },
    { id: 'perfil_inferior', name: 'Perfil inferior', available_at_lc: false, modelPath: null, description: 'Aguardando confirmação' },
    { id: 'nicho', name: 'Iluminação de nicho', available_at_lc: false, modelPath: null, description: 'Aguardando confirmação' },
    { id: 'sensor', name: 'Com sensor de presença', available_at_lc: false, modelPath: null, description: 'Aguardando confirmação' },
];

window.LC.Configurator.COMPATIBILITY_RULES = [
    {
        component_type: 'door',
        component_id: 'all',
        hardware_type: 'hinges',
        is_compatible: true,
        conditions: 'door_hinge_side = left|right',
    },
    {
        component_type: 'drawer',
        component_id: 'all',
        hardware_type: 'slides',
        is_compatible: true,
        conditions: 'drawer_width >= 20cm and drawer_depth >= 40cm',
    },
    {
        component_type: 'door',
        component_id: 'all',
        hardware_type: 'tracks',
        is_compatible: false,
        conditions: 'use sliding_door_system instead',
    },
    {
        component_type: 'drawer',
        component_id: 'all',
        hardware_type: 'handles',
        is_compatible: true,
        conditions: 'handle_type in [alca, botao, cava, perfil, concha]',
    },
];

window.LC.Configurator.getHandleById = function(id) {
    return HANDLE_TYPES.find(h => h.id === id) || null;
}

window.LC.Configurator.getAvailableHandles = function() {
    return HANDLE_TYPES.filter(h => h.available_at_lc);
}

window.LC.Configurator.checkCompatibility = function(componentType, componentId, hardwareType) {
    const rule = COMPATIBILITY_RULES.find(
        r => (r.component_id === componentId || r.component_id === 'all') &&
             r.component_type === componentType &&
             r.hardware_type === hardwareType
    );
    return rule ? rule.is_compatible : true;
}


/**
 * Scene Manager — Three.js core: Scene, Camera, Renderer, Controls, Animation loop, resize.
 * Handles all Three.js lifecycle concerns and camera preset views.
 * ENHANCED: IBL environment, studio lighting rig, soft shadows, adaptive quality.
 */
window.LC.Configurator.SceneManager = class {
    constructor(canvas, debug) {
        this.canvas = canvas;
        this.debug = debug;
        this.scene = null;
        this.camera = null;
        this.renderer = null;
        this.controls = null;
        this.furnitureGroup = null;
        this.lights = {};
        this.initialized = false;
        this.animateId = null;
        this.onBeforeRender = null;
        this.onResizeCallback = null;
        this.envMap = null;
        this.qualityLevel = 'high';
        this._detectQuality();
    }

    _detectQuality() {
        const isMobile = /Android|iPhone|iPad|iPod/i.test(navigator.userAgent);
        const lowMem = navigator.deviceMemory && navigator.deviceMemory <= 4;
        if (isMobile || lowMem) {
            this.qualityLevel = 'medium';
        }
        if (window.devicePixelRatio > 2) {
            this.qualityLevel = 'high';
        }
    }

    init(OrbitControlsClass, THREE) {
        if (this.initialized) return true;

        try {
            this.THREE = THREE;
            const dim = this._getCanvasSize();

            this.debug.step('[3D] Renderer initialization starting...');

            this.renderer = new THREE.WebGLRenderer({
                canvas: this.canvas,
                antialias: this.qualityLevel !== 'low',
                preserveDrawingBuffer: true,
                alpha: false,
                power: 'high-performance',
            });
            this.renderer.setSize(dim.w, dim.h);
            const dpr = this.qualityLevel === 'high' ? Math.min(window.devicePixelRatio, 2) :
                        this.qualityLevel === 'medium' ? Math.min(window.devicePixelRatio, 1.5) : 1;
            this.renderer.setPixelRatio(dpr);
            this.renderer.shadowMap.enabled = true;
            this.renderer.shadowMap.type = THREE.PCFSoftShadowMap;
            this.renderer.toneMapping = THREE.ACESFilmicToneMapping;
            this.renderer.toneMappingExposure = 1.05;
            this.renderer.outputEncoding = THREE.sRGBEncoding;
            this.debug.step('[3D] Renderer initialized (quality: ' + this.qualityLevel + ')');

            this.debug.step('[3D] Scene initialization starting...');
            this.scene = new THREE.Scene();
            this.scene.background = new THREE.Color(0xF6F1EB);
            this.debug.step('[3D] Scene initialized');

            this.debug.step('[3D] Camera initialization starting...');
            this.camera = new THREE.PerspectiveCamera(35, dim.w / dim.h, 0.01, 100);
            this.camera.position.set(3.5, 2.2, 3.5);
            this.debug.step('[3D] Camera initialized');

            this.debug.step('[3D] Controls initialization starting...');
            this.controls = new OrbitControlsClass(this.camera, this.renderer.domElement);
            this.controls.enableDamping = true;
            this.controls.dampingFactor = 0.06;
            this.controls.maxPolarAngle = Math.PI * 0.49;
            this.controls.minPolarAngle = Math.PI * 0.05;
            this.controls.minDistance = 0.8;
            this.controls.maxDistance = 12;
            this.controls.target.set(0, 1.0, 0);
            this.controls.update();
            this.debug.step('[3D] Controls initialized');

            this._setupEnvironmentMap(THREE);
            this._setupLights(THREE);
            this._setupEnvironment(THREE);

            this.furnitureGroup = new THREE.Group();
            this.scene.add(this.furnitureGroup);
            this.debug.step('[3D] Furniture group created');

            this.initialized = true;
            this.debug.step('[3D] Scene fully initialized — awaiting model');

            return true;
        } catch (err) {
            this.debug.error('Failed to initialize 3D scene', err);
            return false;
        }
    }

    _setupEnvironmentMap(THREE) {
        if (!THREE.PMREMGenerator) {
            this.debug.warn('PMREMGenerator not available — skipping IBL');
            return;
        }

        try {
            const pmremGenerator = new THREE.PMREMGenerator(this.renderer);
            pmremGenerator.compileEquirectangularShader();

            const envScene = new THREE.Scene();
            envScene.background = new THREE.Color(0xE8E0D8);

            const gradientCanvas = document.createElement('canvas');
            gradientCanvas.width = 512;
            gradientCanvas.height = 512;
            const ctx = gradientCanvas.getContext('2d');
            const gradient = ctx.createLinearGradient(0, 0, 0, 512);
            gradient.addColorStop(0, '#FFFFFF');
            gradient.addColorStop(0.4, '#F5F0EA');
            gradient.addColorStop(0.7, '#E8E0D8');
            gradient.addColorStop(1, '#D8D0C8');
            ctx.fillStyle = gradient;
            ctx.fillRect(0, 0, 512, 512);

            for (let i = 0; i < 8; i++) {
                const x = Math.random() * 512;
                const y = Math.random() * 256;
                const r = 30 + Math.random() * 80;
                const brightness = 240 + Math.random() * 15;
                const radGrad = ctx.createRadialGradient(x, y, 0, x, y, r);
                radGrad.addColorStop(0, `rgba(${brightness},${brightness},${brightness},0.4)`);
                radGrad.addColorStop(1, 'rgba(255,255,255,0)');
                ctx.fillStyle = radGrad;
                ctx.fillRect(x - r, y - r, r * 2, r * 2);
            }

            const envTexture = new THREE.CanvasTexture(gradientCanvas);
            envTexture.mapping = THREE.EquirectangularReflectionMapping;
            envTexture.needsUpdate = true;

            this.envMap = pmremGenerator.fromEquirectangular(envTexture).texture;
            this.scene.environment = this.envMap;

            envTexture.dispose();
            pmremGenerator.dispose();

            this.debug.step('[3D] Environment map (IBL) created');
        } catch (err) {
            this.debug.warn('Failed to create environment map: ' + err.message);
        }
    }

    _setupLights(THREE) {
        const ambient = new THREE.AmbientLight(0xffffff, 0.15);
        this.scene.add(ambient);
        this.lights.ambient = ambient;

        const hemi = new THREE.HemisphereLight(0xFFF8F0, 0xE0D8D0, 0.25);
        this.scene.add(hemi);
        this.lights.hemisphere = hemi;

        const keyLight = new THREE.DirectionalLight(0xFFF5E8, 1.2);
        keyLight.position.set(3, 6, 4);
        keyLight.castShadow = true;
        const shadowRes = this.qualityLevel === 'high' ? 2048 : 1024;
        keyLight.shadow.mapSize.width = shadowRes;
        keyLight.shadow.mapSize.height = shadowRes;
        keyLight.shadow.camera.near = 0.5;
        keyLight.shadow.camera.far = 20;
        keyLight.shadow.camera.left = -5;
        keyLight.shadow.camera.right = 5;
        keyLight.shadow.camera.top = 5;
        keyLight.shadow.camera.bottom = -5;
        keyLight.shadow.bias = -0.0003;
        keyLight.shadow.radius = this.qualityLevel === 'high' ? 6 : 3;
        this.scene.add(keyLight);
        this.lights.key = keyLight;

        const fillLight = new THREE.DirectionalLight(0xE8E0D8, 0.35);
        fillLight.position.set(-3, 3, -2);
        this.scene.add(fillLight);
        this.lights.fill = fillLight;

        const rimLight = new THREE.DirectionalLight(0xFFF8F0, 0.45);
        rimLight.position.set(-2, 4, -4);
        this.scene.add(rimLight);
        this.lights.rim = rimLight;

        const topLight = new THREE.PointLight(0xFFF8F0, 0.3, 10, 2);
        topLight.position.set(0, 5, 0);
        this.scene.add(topLight);
        this.lights.top = topLight;

        this.debug.step('[3D] Studio lighting rig complete (key/fill/rim/top + IBL)');
    }

    _setupEnvironment(THREE) {
        const floorMat = new THREE.MeshStandardMaterial({
            color: 0xE8E0D8,
            roughness: 0.85,
            metalness: 0.0,
            envMapIntensity: 0.4,
        });

        const floorGeo = new THREE.PlaneGeometry(30, 30);
        const floor = new THREE.Mesh(floorGeo, floorMat);
        floor.rotation.x = -Math.PI / 2;
        floor.position.y = -0.01;
        floor.receiveShadow = true;
        this.scene.add(floor);
        this.lights.floor = floor;

        const wallMat = new THREE.MeshStandardMaterial({
            color: 0xF5F0EA,
            roughness: 0.9,
            metalness: 0.0,
            envMapIntensity: 0.3,
        });

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

        this.debug.step('[3D] Studio environment complete (floor + walls)');
    }

    _getCanvasSize() {
        const wrap = document.getElementById('canvasWrap') || this.canvas.parentElement;
        if (!wrap) return { w: 600, h: 500 };
        const w = Math.max(wrap.clientWidth || 600, 300);
        const h = Math.max(wrap.clientHeight || 500, 300);
        return { w, h };
    }

    render() {
        if (!this.initialized) return;
        this.renderer.render(this.scene, this.camera);
    }

    animate(onPreRender) {
        const loop = () => {
            if (onPreRender) onPreRender();
            if (this.controls) this.controls.update();
            if (this.renderer && this.scene && this.camera) {
                this.renderer.render(this.scene, this.camera);
            }
            this.animateId = requestAnimationFrame(loop);
        };
        loop();
    }

    stopAnimate() {
        if (this.animateId) {
            cancelAnimationFrame(this.animateId);
            this.animateId = null;
        }
    }

    onResize() {
        if (!this.initialized) return;
        const dim = this._getCanvasSize();
        this.renderer.setSize(dim.w, dim.h);
        this.camera.aspect = dim.w / dim.h;
        this.camera.updateProjectionMatrix();
    }

    setCameraPreset(preset) {
        if (!this.camera || !this.controls) return;

        const target = new THREE.Vector3();
        if (this.furnitureGroup && this.furnitureGroup.children.length > 0) {
            const bbox = new THREE.Box3().setFromObject(this.furnitureGroup);
            bbox.getCenter(target);
            if (bbox.isEmpty()) target.set(0, 1, 0);
        } else {
            target.set(0, 1, 0);
        }

        let camPos;
        const offset = 4.5;
        switch (preset) {
            case 'front':
                camPos = new THREE.Vector3(target.x, target.y, target.z + offset);
                break;
            case 'side':
                camPos = new THREE.Vector3(target.x + offset, target.y, target.z);
                break;
            case 'top':
                camPos = new THREE.Vector3(target.x, target.y + offset, target.z + 0.01);
                break;
            case 'three_quarter':
            case 'reset':
            default:
                camPos = new THREE.Vector3(target.x + offset * 0.7, target.y + offset * 0.5, target.z + offset * 0.7);
                break;
        }

        this._animateCameraTo(camPos, target, 500);
    }

    _animateCameraTo(targetPos, lookTarget, duration) {
        const camera = this.camera;
        const controls = this.controls;
        const startPos = camera.position.clone();
        const startTarget = controls.target.clone();
        const startTime = performance.now();

        const easeInOutCubic = (t) => t < 0.5 ? 4 * t * t * t : 1 - Math.pow(-2 * t + 2, 3) / 2;

        const animate = (now) => {
            const elapsed = now - startTime;
            const t = Math.min(elapsed / duration, 1);
            const easeT = easeInOutCubic(t);

            camera.position.lerpVectors(startPos, targetPos, easeT);
            controls.target.lerpVectors(startTarget, lookTarget, easeT);
            controls.update();

            if (t < 1) {
                requestAnimationFrame(animate);
            }
        };

        requestAnimationFrame(animate);
    }

    dispose() {
        this.stopAnimate();
        if (this.renderer) {
            this.renderer.dispose();
            this.renderer = null;
        }
        if (this.scene) {
            this.scene.traverse((obj) => {
                if (obj.geometry) obj.geometry.dispose();
                if (obj.material) {
                    if (Array.isArray(obj.material)) {
                        obj.material.forEach((m) => m.dispose());
                    } else {
                        obj.material.dispose();
                    }
                }
            });
            this.scene = null;
        }
        this.initialized = false;
    }
}


/**
 * PBR Material System — Professional-grade materials for furniture rendering.
 * Features: realistic wood grain, proper metal materials, lacquer clearcoat simulation,
 * adaptive texture quality, and environment map integration.
 */
window.LC.Configurator.MaterialFactory = class {
    constructor(THREE, debug, envMap) {
        this.THREE = THREE;
        this.debug = debug;
        this.envMap = envMap;
        this.textureCache = new Map();
        this.materialCache = new Map();
        this.qualityLevel = this._detectQuality();
    }

    _detectQuality() {
        const isMobile = /Android|iPhone|iPad|iPod/i.test(navigator.userAgent);
        return isMobile ? 'medium' : 'high';
    }

    updateEnvMap(envMap) {
        this.envMap = envMap;
    }

    createMaterial(spec) {
        const cacheKey = this._hashSpec(spec);
        if (this.materialCache.has(cacheKey)) {
            return this.materialCache.get(cacheKey);
        }

        const mat = this._buildMaterial(spec);
        this.materialCache.set(cacheKey, mat);
        return mat;
    }

    _hashSpec(spec) {
        return [
            spec.baseColor || '#ffffff',
            spec.roughness ?? 0.5,
            spec.metalness ?? 0.0,
            spec.finish || 'matte',
            spec.grainDirection || 'none',
            spec.hasGrain ? '1' : '0',
        ].join('|');
    }

    _buildMaterial(spec) {
        const THREE = this.THREE;
        const baseColor = spec.baseColor || '#eceae3';
        const finish = spec.finish || 'matte';

        let roughness = spec.roughness ?? 0.5;
        let metalness = spec.metalness ?? 0.0;
        let color = baseColor;
        let map = null;
        let normalMap = null;
        let roughnessMap = null;

        if (spec.hasGrain || finish === 'texturizado' || finish === 'madeira') {
            const grainDir = spec.grainDirection || 'vertical';
            map = this._createWoodTexture(baseColor, grainDir);
            normalMap = this._createWoodNormal(baseColor, grainDir);
            roughnessMap = this._createWoodRoughness(baseColor);
            color = '#ffffff';
            roughness = finish === 'madeira' ? 0.82 : 0.78;
            metalness = 0.0;
        } else if (finish === 'laca') {
            roughness = 0.08;
            metalness = 0.05;
        } else if (finish === 'acetinado') {
            roughness = 0.35;
            metalness = 0.02;
        } else if (finish === 'melamina') {
            roughness = 0.72;
            metalness = 0.0;
        } else if (finish === 'liso') {
            roughness = 0.55;
            metalness = 0.0;
        }

        const params = {
            color: color,
            roughness: roughness,
            metalness: metalness,
            map: map,
            normalMap: normalMap,
            normalScale: new THREE.Vector2(spec.hasGrain ? 0.3 : 0.1, spec.hasGrain ? 0.3 : 0.1),
            roughnessMap: roughnessMap,
            aoMap: null,
            aoMapIntensity: 0.5,
            envMap: this.envMap,
            envMapIntensity: finish === 'laca' ? 0.8 : finish === 'acetinado' ? 0.5 : 0.3,
        };

        const material = new THREE.MeshStandardMaterial(params);

        if (map) {
            material.needsUpdate = true;
        }

        return material;
    }

    _createWoodTexture(hex, direction) {
        const cacheKey = `wood_${hex}_${direction}_${this.qualityLevel}`;
        if (this.textureCache.has(cacheKey)) {
            return this.textureCache.get(cacheKey);
        }

        const size = this.qualityLevel === 'high' ? 1024 : 512;
        const canvas = document.createElement('canvas');
        canvas.width = size;
        canvas.height = size;
        const ctx = canvas.getContext('2d');

        const r = parseInt(hex.slice(1, 3), 16);
        const g = parseInt(hex.slice(3, 5), 16);
        const b = parseInt(hex.slice(5, 7), 16);
        ctx.fillStyle = `rgb(${r},${g},${b})`;
        ctx.fillRect(0, 0, size, size);

        const numRings = 40;
        const ringSpacing = size / numRings;
        for (let i = 0; i < numRings; i++) {
            const centerX = size * (0.3 + Math.random() * 0.4);
            const centerY = size * (0.3 + Math.random() * 0.4);
            const radius = ringSpacing * (2 + Math.random() * 8);
            const variation = Math.random() * 20 - 10;
            ctx.strokeStyle = `rgba(${Math.max(0, Math.min(255, r + variation))},${Math.max(0, Math.min(255, g + variation))},${Math.max(0, Math.min(255, b + variation))},${0.08 + Math.random() * 0.06})`;
            ctx.lineWidth = 1 + Math.random() * 2;
            ctx.beginPath();
            ctx.arc(centerX, centerY, radius, 0, Math.PI * 2);
            ctx.stroke();
        }

        const numGrain = size * 3;
        for (let i = 0; i < numGrain; i++) {
            const grainR = r + (Math.random() * 14 - 7);
            const grainG = g + (Math.random() * 14 - 7);
            const grainB = b + (Math.random() * 14 - 7);
            ctx.strokeStyle = `rgba(${Math.max(0, Math.min(255, grainR))},${Math.max(0, Math.min(255, grainG))},${Math.max(0, Math.min(255, grainB))},${0.04 + Math.random() * 0.08})`;
            ctx.lineWidth = 0.5 + Math.random() * 1.5;
            const x = Math.random() * size;
            const y = Math.random() * size;
            const len = 40 + Math.random() * 150;
            ctx.beginPath();
            ctx.moveTo(x, y);
            const variance = Math.random() * 6 - 3;
            if (direction === 'horizontal') {
                ctx.lineTo(x + len, y + variance);
            } else {
                ctx.lineTo(x + variance, y + len);
            }
            ctx.stroke();
        }

        for (let i = 0; i < 200; i++) {
            const sx = Math.random() * size;
            const sy = Math.random() * size;
            const sr = Math.random() * 3 + 1;
            const sv = Math.random() * 15 - 7;
            ctx.fillStyle = `rgba(${Math.max(0, Math.min(255, r + sv))},${Math.max(0, Math.min(255, g + sv))},${Math.max(0, Math.min(255, b + sv))},${0.1 + Math.random() * 0.15})`;
            ctx.beginPath();
            ctx.ellipse(sx, sy, sr, sr * 0.3, 0, 0, Math.PI * 2);
            ctx.fill();
        }

        const tex = new this.THREE.CanvasTexture(canvas);
        tex.wrapS = this.THREE.RepeatWrapping;
        tex.wrapT = this.THREE.RepeatWrapping;
        tex.repeat.set(1.5, 1.5);
        tex.anisotropy = this.qualityLevel === 'high' ? 8 : 4;
        tex.needsUpdate = true;

        this.textureCache.set(cacheKey, tex);
        return tex;
    }

    _createWoodNormal(hex, direction) {
        const cacheKey = `wood_normal_${hex}_${direction}_${this.qualityLevel}`;
        if (this.textureCache.has(cacheKey)) {
            return this.textureCache.get(cacheKey);
        }

        const size = this.qualityLevel === 'high' ? 512 : 256;
        const canvas = document.createElement('canvas');
        canvas.width = size;
        canvas.height = size;
        const ctx = canvas.getContext('2d');
        const imgData = ctx.createImageData(size, size);
        const data = imgData.data;

        for (let y = 0; y < size; y++) {
            for (let x = 0; x < size; x++) {
                const idx = (y * size + x) * 4;
                const noise1 = Math.sin(x * 0.1) * Math.cos(y * 0.05) * 15;
                const noise2 = (Math.random() - 0.5) * 10;
                const noise3 = (Math.random() - 0.5) * 10;

                data[idx] = 128 + noise1 + noise2;
                data[idx + 1] = 128 + noise1 * 0.5 + noise3;
                data[idx + 2] = 128 + 20;
                data[idx + 3] = 255;
            }
        }
        ctx.putImageData(imgData, 0, 0);

        const tex = new this.THREE.CanvasTexture(canvas);
        tex.wrapS = this.THREE.RepeatWrapping;
        tex.wrapT = this.THREE.RepeatWrapping;
        tex.repeat.set(2, 2);
        tex.anisotropy = 4;
        tex.needsUpdate = true;

        this.textureCache.set(cacheKey, tex);
        return tex;
    }

    _createWoodRoughness(hex) {
        const cacheKey = `wood_rough_${hex}_${this.qualityLevel}`;
        if (this.textureCache.has(cacheKey)) {
            return this.textureCache.get(cacheKey);
        }

        const size = this.qualityLevel === 'high' ? 256 : 128;
        const canvas = document.createElement('canvas');
        canvas.width = size;
        canvas.height = size;
        const ctx = canvas.getContext('2d');
        const imgData = ctx.createImageData(size, size);
        const data = imgData.data;

        for (let i = 0; i < data.length; i += 4) {
            const val = 160 + Math.random() * 50;
            data[i] = val;
            data[i + 1] = val;
            data[i + 2] = val;
            data[i + 3] = 255;
        }
        ctx.putImageData(imgData, 0, 0);

        const tex = new this.THREE.CanvasTexture(canvas);
        tex.wrapS = this.THREE.RepeatWrapping;
        tex.wrapT = this.THREE.RepeatWrapping;
        tex.repeat.set(2, 2);
        tex.needsUpdate = true;

        this.textureCache.set(cacheKey, tex);
        return tex;
    }

    createMetalMaterial(hex, roughness = 0.2, metalness = 0.9) {
        const cacheKey = `metal_${hex}_${roughness}_${metalness}`;
        if (this.materialCache.has(cacheKey)) {
            return this.materialCache.get(cacheKey);
        }

        const params = {
            color: hex,
            roughness: roughness,
            metalness: metalness,
            envMap: this.envMap,
            envMapIntensity: 0.9,
            normalScale: new this.THREE.Vector2(0.3, 0.3),
        };

        const mat = new this.THREE.MeshStandardMaterial(params);
        this.materialCache.set(cacheKey, mat);
        return mat;
    }

    createBackMaterial(hex, textured = false) {
        const cacheKey = `back_${hex}_${textured}`;
        if (this.materialCache.has(cacheKey)) {
            return this.materialCache.get(cacheKey);
        }

        const params = {
            color: hex,
            roughness: textured ? 0.85 : 0.92,
            metalness: 0.0,
            side: this.THREE.FrontSide,
            envMap: this.envMap,
            envMapIntensity: 0.2,
        };

        if (textured) {
            params.map = this._createWoodTexture(hex, 'horizontal');
            params.color = '#ffffff';
        }

        const mat = new this.THREE.MeshStandardMaterial(params);
        this.materialCache.set(cacheKey, mat);
        return mat;
    }

    disposeAll() {
        for (const [key, tex] of this.textureCache) {
            if (tex.dispose) tex.dispose();
        }
        this.textureCache.clear();

        for (const [key, mat] of this.materialCache) {
            if (mat.dispose) mat.dispose();
        }
        this.materialCache.clear();
    }
}


/**
 * Furniture Builder — Procedural parametric furniture generation in Three.js.
 * Creates real geometry (not scale tricks) for carcass, doors, drawers,
 * shelves, dividers, handles, and LED strips based on centimeter dimensions.
 *
 * ARCHITECTURE: Zone-based interior layout
 * - Interior is divided into vertical zones (columns)
 * - Each zone can hold: shelves OR drawers (never both)
 * - Zones are allocated left-to-right: drawers first, then shelves
 * - This prevents any overlap between components
 *
 * Unit convention: 1 Three.js unit = 1 meter. Input dimensions are in cm.
 */
window.LC.Configurator.FurnitureBuilder = class {
    constructor(THREE, sceneManager, materialFactory, debug) {
        this.THREE = THREE;
        this.sceneManager = sceneManager;
        this.mats = materialFactory;
        this.debug = debug;

        this.furnitureGroup = sceneManager.furnitureGroup;
        this.components = new Map();
        this.materialRefs = new Map();
        this.zones = [];
    }

    /**
     * Build a complete wardrobe from configuration object.
     * @param {Object} config - { typeKey, W, H, D, doors, shelves, drawers, mounted, doorStyle, doorColor, finish, handleType, ledEnabled, interiorOpen, dividerCount }
     */
    build(config) {
        var errors = [];
        if (!config) errors.push('Config is null');
        if (!config.W || config.W < 40) errors.push('Invalid width');
        if (!config.H || config.H < 30) errors.push('Invalid height');
        if (!config.D || config.D < 20) errors.push('Invalid depth');
        if (config.doors < 0) errors.push('Invalid door count');

        if (errors.length) {
            this.debug.error('FurnitureBuilder.build validation failed: ' + errors.join(', '));
            return this._renderError();
        }

        try {
            this.debug.step('[Build] Clearing previous furniture...');
            this.clear();

            const s = 0.01;
            const w = config.W * s;
            const h = config.H * s;
            const d = config.D * s;
            const t = 0.018;
            const bt = config.backThickness ? config.backThickness * s : 0.008;
            const y0 = config.mounted ? 1.4 : 0;

            this._buildMaterials(config);
            this.debug.step(`[Build] Materials ready (color=${config.doorColor}, finish=${config.finish}, handle=${config.handleType})`);

            this.debug.step('[Build] Building carcass...');
            this._buildCarcass(w, h, d, t, bt, y0, config);

            this.debug.step('[Build] Building doors...');
            this._buildDoors(w, h, d, t, y0, config);

            this._calculateZones(w, h, d, t, y0, config);

            this.debug.step('[Build] Building drawers...');
            this._buildDrawersInZones(w, h, d, t, y0, config);

            this.debug.step('[Build] Building shelves...');
            this._buildShelvesInZones(w, h, d, t, y0, config);

            this.debug.step('[Build] Building dividers...');
            this._buildDividers(w, h, d, t, y0, config);

            if (config.ledEnabled) {
                this.debug.step('[Build] Building LED strips...');
                this._buildLED(w, h, d, t, y0, config);
            }

            if (config.mounted) {
                this._buildMountingRail(w, d, y0, t);
            }

            this._fitCamera(w, h, d, t, y0);
            this._addDimensionLabels(w, h, d, t, y0, config.W, config.H, config.D);

            const meshCount = this.furnitureGroup.children.length;
            const totalMeshes = this._countAllMeshes();
            this.debug.step(`[Build] Complete. Group children: ${meshCount}, total meshes: ${totalMeshes}`);
            this.debug.step('[3D] Model loaded');

        } catch (err) {
            this.debug.error('FurnitureBuilder.build failed', err);
            this._renderError();
        }
    }

    _buildMaterials(config) {
        const isWood = config.finish === 'texturizado' || config.finish === 'madeira' || config.hasGrain;
        const isLacquer = config.finish === 'laca';
        const isMatte = config.finish === 'liso';
        const isMelamine = config.finish === 'melamina';
        const isGlossy = config.finish === 'brilhante' || config.finish === 'acetinada';

        let doorRoughness, doorMetalness;
        if (isLacquer) {
            doorRoughness = 0.08;
            doorMetalness = 0.05;
        } else if (isGlossy) {
            doorRoughness = 0.15;
            doorMetalness = 0.02;
        } else if (isMatte) {
            doorRoughness = 0.55;
            doorMetalness = 0.0;
        } else if (isMelamine) {
            doorRoughness = 0.72;
            doorMetalness = 0.0;
        } else if (isWood) {
            doorRoughness = 0.82;
            doorMetalness = 0.0;
        } else {
            doorRoughness = 0.65;
            doorMetalness = 0.0;
        }

        const doorMat = this.mats.createMaterial({
            baseColor: config.doorColor,
            roughness: doorRoughness,
            metalness: doorMetalness,
            finish: config.finish,
            grainDirection: 'vertical',
            hasGrain: isWood,
        });
        this.materialRefs.set('door', doorMat);

        const sideMat = this.mats.createMaterial({
            baseColor: config.doorColor,
            roughness: Math.min(doorRoughness + 0.05, 1.0),
            metalness: doorMetalness,
            finish: config.finish,
            grainDirection: 'vertical',
            hasGrain: isWood && config.sideGrainDirection === 'vertical',
        });
        this.materialRefs.set('side', sideMat);

        const topMat = this.mats.createMaterial({
            baseColor: config.doorColor,
            roughness: Math.min(doorRoughness + 0.03, 1.0),
            metalness: doorMetalness,
            finish: config.finish,
            grainDirection: 'horizontal',
            hasGrain: isWood && config.topGrainDirection === 'horizontal',
        });
        this.materialRefs.set('top', topMat);

        const backMat = this.mats.createBackMaterial(config.backColor || '#D9D2C7', isWood);
        this.materialRefs.set('back', backMat);

        const drawerMat = this.mats.createMaterial({
            baseColor: config.doorColor,
            roughness: Math.min(doorRoughness + 0.02, 1.0),
            metalness: doorMetalness,
            finish: config.finish,
            grainDirection: 'vertical',
            hasGrain: isWood,
        });
        this.materialRefs.set('drawer', drawerMat);

        this.materialRefs.set('shelf', sideMat);
        this.materialRefs.set('divider', sideMat);

        this.materialRefs.set('handle', this.mats.createMetalMaterial(config.handleColor || '#6B5340'));
        this.materialRefs.set('hardware', this.mats.createMetalMaterial(config.hardwareColor || '#BBBBBB'));
        this.materialRefs.set('mountRail', this.mats.createMetalMaterial(config.hardwareColor || '#BBBBBB', 0.25, 0.8));
    }

    _buildCarcass(w, h, d, t, bt, y0, config) {
        const hw = w / 2;
        const hh = h / 2;
        const hd = d / 2;
        const bevel = 0.0015;

        const mat = this.materialRefs.get('side');

        this._addBox(-hw + t / 2, hh + y0, 0, t, h, d, mat, 'left_side', bevel);
        this._addBox(hw - t / 2, hh + y0, 0, t, h, d, mat, 'right_side', bevel);

        const topMat = this.materialRefs.get('top');
        this._addBox(0, h - t / 2 + y0, 0, w - t * 2, t, d, topMat, 'top_panel', bevel);

        this._addBox(0, t / 2 + y0, 0, w - t * 2, t, d, topMat, 'bottom_panel', bevel);

        const backDepth = bt;
        const backMat = this.materialRefs.get('back');
        this._addBox(0, hh + y0, -hd + backDepth / 2, w - t * 2, h - t * 2, backDepth, backMat, 'back_panel', 0);
    }

    _buildDoors(w, h, d, t, y0, config) {
        const { doors, doorStyle, doorColor, handleType, finish } = config;
        if (!doors || doors <= 0) return;

        const hw = w / 2;
        const hh = h / 2;
        const hd = d / 2;
        const doorH = h - t * 2 - 0.01;
        const gap = 0.006;
        const doorW = (w - t * 2 - gap * (doors - 1) - 0.02) / doors;

        for (let i = 0; i < doors; i++) {
            const pivot = new this.THREE.Group();
            pivot.name = `door_${i}`;
            pivot.userData = { type: 'door', index: i, open: false, maxOpen: 105, handleType };

            const slotX = -hw + t + 0.01 + i * (doorW + gap);
            const hingeSide = i < Math.ceil(doors / 2) ? 1 : -1;
            const hingeX = hingeSide === 1 ? slotX : slotX + doorW;

            pivot.position.set(hingeX, hh + y0, hd + 0.001);

            const panel = new this.THREE.Mesh(
                new this.THREE.BoxGeometry(doorW - 0.008, doorH, 0.018),
                this.materialRefs.get('door')
            );
            panel.position.set(hingeSide * doorW / 2, 0, 0);
            panel.castShadow = true;
            panel.receiveShadow = true;
            pivot.add(panel);

            this._addDoorHandle(panel, doorW, doorH, hingeSide, handleType, y0);

            this.furnitureGroup.add(pivot);
            this.components.set(`door_${i}`, pivot);
        }

        this.debug.step(`[Build] Doors: ${doors} (style=${doorStyle || 'rebatedor'}, handle=${handleType})`);
    }

    _addDoorHandle(doorPanel, doorW, doorH, dir, handleType, yOffset) {
        const handleMat = this.materialRefs.get('handle');
        const handleY = doorH * 0.25;
        const offsetX = dir * (doorW / 2 - 0.05);

        switch (handleType) {
            case 'alca': {
                const handle = new this.THREE.Mesh(
                    new this.THREE.BoxGeometry(0.02, 0.18, 0.02),
                    handleMat
                );
                handle.position.set(offsetX, -handleY, 0.018);
                handle.castShadow = true;
                doorPanel.add(handle);
                break;
            }
            case 'botao': {
                const handle = new this.THREE.Mesh(
                    new this.THREE.CylinderGeometry(0.015, 0.015, 0.04, 16),
                    handleMat
                );
                handle.position.set(offsetX, -handleY, 0.02);
                handle.castShadow = true;
                doorPanel.add(handle);
                break;
            }
            case 'cava': {
                const groove = new this.THREE.Mesh(
                    new this.THREE.BoxGeometry(0.06, 0.012, 0.008),
                    this.materialRefs.get('drawer')
                );
                groove.position.set(offsetX, -handleY, 0.018);
                groove.castShadow = true;
                doorPanel.add(groove);

                const handle = new this.THREE.Mesh(
                    new this.THREE.BoxGeometry(0.024, 0.024, 0.02),
                    handleMat
                );
                handle.position.set(offsetX, -handleY, 0.022);
                handle.castShadow = true;
                doorPanel.add(handle);
                break;
            }
            case 'perfil': {
                const perfil = new this.THREE.Mesh(
                    new this.THREE.BoxGeometry(0.008, 0.12, 0.025),
                    handleMat
                );
                perfil.position.set(dir * (doorW / 2 - 0.004), -handleY, 0.015);
                perfil.castShadow = true;
                doorPanel.add(perfil);
                break;
            }
            case 'concha': {
                const concha = new this.THREE.Mesh(
                    new this.THREE.SphereGeometry(0.025, 16, 16, 0, Math.PI),
                    handleMat
                );
                concha.position.set(offsetX, -handleY, 0.025);
                concha.castShadow = true;
                doorPanel.add(concha);
                break;
            }
            case 'embutido': {
                const slot = new this.THREE.Mesh(
                    new this.THREE.BoxGeometry(0.08, 0.02, 0.015),
                    this.materialRefs.get('drawer')
                );
                slot.position.set(offsetX, -handleY, 0.009);
                doorPanel.add(slot);
                break;
            }
            case 'touch':
            case 'nenhum':
            default:
                break;
        }
    }

    _addDrawerHandle(frontPanel, drawerW, drawerH, handleType) {
        const handleMat = this.materialRefs.get('handle');
        const handleY = 0;
        const offset = 0.04;

        switch (handleType) {
            case 'alca': {
                const handle = new this.THREE.Mesh(
                    new this.THREE.BoxGeometry(0.02, 0.06, 0.02),
                    handleMat
                );
                handle.position.set(0, handleY, offset);
                handle.castShadow = true;
                frontPanel.add(handle);
                break;
            }
            case 'botao': {
                const handle = new this.THREE.Mesh(
                    new this.THREE.CylinderGeometry(0.01, 0.01, 0.03, 16),
                    handleMat
                );
                handle.position.set(0, handleY, offset);
                frontPanel.add(handle);
                break;
            }
            case 'cava': {
                const groove = new this.THREE.Mesh(
                    new this.THREE.BoxGeometry(0.05, 0.01, 0.006),
                    this.materialRefs.get('drawer')
                );
                groove.position.set(0, handleY, offset);
                frontPanel.add(groove);

                const handle = new this.THREE.Mesh(
                    new this.THREE.BoxGeometry(0.024, 0.024, 0.01),
                    handleMat
                );
                handle.position.set(0, handleY, offset + 0.006);
                frontPanel.add(handle);
                break;
            }
            case 'perfil': {
                const perfil = new this.THREE.Mesh(
                    new this.THREE.BoxGeometry(0.12, 0.008, 0.02),
                    handleMat
                );
                perfil.position.set(0, handleY, offset);
                frontPanel.add(perfil);
                break;
            }
            case 'concha': {
                const concha = new this.THREE.Mesh(
                    new this.THREE.SphereGeometry(0.018, 16, 16, 0, Math.PI),
                    handleMat
                );
                concha.position.set(0, handleY, offset);
                frontPanel.add(concha);
                break;
            }
            case 'embutido': {
                const slot = new this.THREE.Mesh(
                    new this.THREE.BoxGeometry(0.06, 0.015, 0.01),
                    this.materialRefs.get('drawer')
                );
                slot.position.set(0, handleY, offset - 0.005);
                frontPanel.add(slot);
                break;
            }
            case 'touch':
            case 'nenhum':
            default:
                break;
        }
    }

    _buildDividers(w, h, d, t, y0, config) {
        const { dividerCount } = config;
        if (!dividerCount || dividerCount <= 0) return;

        const hw = w / 2;
        const hh = h / 2;
        const hd = d / 2;
        const bt = 0.008;
        const dividerW = 0.01;
        const innerH = h - t * 2;
        const slot = (w - t * 2) / (dividerCount + 1);

        const dividerMat = this.materialRefs.get('divider') || this.materialRefs.get('side');

        for (let k = 0; k < dividerCount; k++) {
            const dx = -hw + t + slot * (k + 1);
            const divider = new this.THREE.Mesh(
                new this.THREE.BoxGeometry(dividerW, innerH, d - bt),
                dividerMat
            );
            divider.position.set(dx, hh + y0, 0);
            divider.castShadow = true;
            divider.receiveShadow = true;
            this.furnitureGroup.add(divider);
            this.components.set(`divider_${k}`, divider);
        }

        this.debug.step(`[Build] Dividers: ${dividerCount}`);
    }

    /**
     * Calculate interior zones for drawers and shelves.
     * Zones are vertical columns. Drawers get left zones, shelves get right zones.
     * This ensures drawers and shelves NEVER overlap.
     */
    _calculateZones(w, h, d, t, y0, config) {
        const hw = w / 2;
        const innerW = w - t * 2 - 0.02;
        const drawers = config.drawers || 0;
        const shelves = config.shelves || 0;

        const drawerZoneCount = Math.min(drawers, 2);
        const shelfZoneCount = Math.max(1, shelves > 0 ? 1 : 0);

        const totalZones = drawerZoneCount + shelfZoneCount;
        const zoneWidth = innerW / totalZones;

        this.zones = [];

        for (let i = 0; i < drawerZoneCount; i++) {
            this.zones.push({
                type: 'drawer',
                index: i,
                xStart: -hw + t + 0.01 + i * zoneWidth,
                xEnd: -hw + t + 0.01 + (i + 1) * zoneWidth,
                width: zoneWidth - 0.01,
            });
        }

        for (let i = 0; i < shelfZoneCount; i++) {
            const zoneIdx = drawerZoneCount + i;
            this.zones.push({
                type: 'shelf',
                index: i,
                xStart: -hw + t + 0.01 + zoneIdx * zoneWidth,
                xEnd: -hw + t + 0.01 + (zoneIdx + 1) * zoneWidth,
                width: zoneWidth - 0.01,
            });
        }

        this.debug.step(`[Build] Zones: ${drawerZoneCount} drawer, ${shelfZoneCount} shelf`);
    }

    /**
     * Build drawers inside their allocated zones.
     * Each drawer zone can stack multiple drawers vertically.
     */
    _buildDrawersInZones(w, h, d, t, y0, config) {
        const { drawers, handleType } = config;
        if (!drawers || drawers <= 0) return;

        const drawerZones = this.zones.filter(z => z.type === 'drawer');
        if (drawerZones.length === 0) return;

        const hd = d / 2;
        const drawerD = Math.min(0.45, d - t * 2 - 0.02);

        const drawersPerZone = Math.ceil(drawers / drawerZones.length);
        let drawerIdx = 0;

        for (const zone of drawerZones) {
            const zoneDrawers = Math.min(drawersPerZone, drawers - drawerIdx);
            if (zoneDrawers <= 0) break;

            const zoneInnerH = h - t * 2 - 0.04;
            const drH = (zoneInnerH * 0.9) / zoneDrawers;
            const drawerW = zone.width;

            for (let g = 0; g < zoneDrawers; g++) {
                const gy = t + 0.02 + drH / 2 + g * drH + y0;

                const group = new this.THREE.Group();
                group.name = `drawer_${drawerIdx}`;
                group.userData = { type: 'drawer', index: drawerIdx };

                const front = new this.THREE.Mesh(
                    new this.THREE.BoxGeometry(drawerW, drH * 0.88, 0.016),
                    this.materialRefs.get('drawer')
                );
                front.position.set(0, 0, hd - 0.008);
                front.castShadow = true;
                front.receiveShadow = true;
                group.add(front);

                const sideMat = this.materialRefs.get('drawer');
                const thickness = 0.01;

                const leftSide = new this.THREE.Mesh(
                    new this.THREE.BoxGeometry(thickness, drH * 0.88, drawerD),
                    sideMat
                );
                leftSide.position.set(-drawerW / 2 + thickness / 2, 0, -hd + drawerD / 2 + 0.016);
                group.add(leftSide);

                const rightSide = leftSide.clone();
                rightSide.position.x = drawerW / 2 - thickness / 2;
                group.add(rightSide);

                const bottom = new this.THREE.Mesh(
                    new this.THREE.BoxGeometry(drawerW - thickness * 2, thickness, drawerD),
                    sideMat
                );
                bottom.position.y = -drH * 0.88 / 2;
                group.add(bottom);

                const back = new this.THREE.Mesh(
                    new this.THREE.BoxGeometry(drawerW - thickness * 2, drH * 0.88, 0.005),
                    sideMat
                );
                back.position.set(0, 0, -hd + drawerD / 2 - 0.003);
                group.add(back);

                this._addDrawerHandle(front, drawerW, drH * 0.88, handleType);

                const zoneCenterX = (zone.xStart + zone.xEnd) / 2;
                group.position.set(zoneCenterX, gy, 0);
                this.furnitureGroup.add(group);
                this.components.set(`drawer_${drawerIdx}`, group);

                drawerIdx++;
            }
        }

        this.debug.step(`[Build] Drawers: ${drawerIdx}`);
    }

    /**
     * Build shelves inside their allocated zones.
     * Shelves span the full width of shelf zones.
     */
    _buildShelvesInZones(w, h, d, t, y0, config) {
        const { shelves } = config;
        if (!shelves || shelves <= 0) return;

        const shelfZones = this.zones.filter(z => z.type === 'shelf');
        if (shelfZones.length === 0) return;

        const hd = d / 2;
        const bt = 0.008;
        const innerD = d - bt - t * 2;
        const shelfH = 0.014;

        for (const zone of shelfZones) {
            const shelfW = zone.width;

            for (let j = 0; j < shelves; j++) {
                const ratio = (j + 1) / (shelves + 1);
                const sy = (h - t * 2) * ratio - (h - t * 2) / 2 + y0;

                const shelf = new this.THREE.Mesh(
                    new this.THREE.BoxGeometry(shelfW, shelfH, innerD),
                    this.materialRefs.get('shelf')
                );
                const zoneCenterX = (zone.xStart + zone.xEnd) / 2;
                shelf.position.set(zoneCenterX, sy, -hd + t + innerD / 2);
                shelf.castShadow = true;
                shelf.receiveShadow = true;
                this.furnitureGroup.add(shelf);
                this.components.set(`shelf_${j}`, shelf);
            }
        }

        this.debug.step(`[Build] Shelves: ${shelves}`);
    }

    _buildLED(w, h, d, t, y0, config) {
        const THREE = this.THREE;
        const hw = w / 2;
        const hd = d / 2;
        const ledGroup = new THREE.Group();
        ledGroup.name = 'led_strip';
        ledGroup.userData = { type: 'led' };

        const ledColor = new THREE.Color(0x44aaff);
        const ledGlowColor = new THREE.Color(0x88ccff);

        const ledTubeMat = new THREE.MeshBasicMaterial({
            color: ledGlowColor,
            transparent: true,
            opacity: 0.9,
        });

        const tubeGeo = new THREE.CylinderGeometry(0.004, 0.004, w - t * 2, 8);
        const tube = new THREE.Mesh(tubeGeo, ledTubeMat);
        tube.rotation.z = Math.PI / 2;
        tube.position.set(0, t * 2 + y0, -hd + t * 2 + 0.01);
        ledGroup.add(tube);

        const glowMat = new THREE.MeshBasicMaterial({
            color: ledColor,
            transparent: true,
            opacity: 0.3,
        });
        const glowGeo = new THREE.CylinderGeometry(0.012, 0.012, w - t * 2, 8);
        const glow = new THREE.Mesh(glowGeo, glowMat);
        glow.rotation.z = Math.PI / 2;
        glow.position.copy(tube.position);
        ledGroup.add(glow);

        const pointLight = new THREE.PointLight(ledColor, 1.5, 4, 2);
        pointLight.position.set(0, t * 2 + y0 + 0.05, -hd + t * 2 + 0.02);
        ledGroup.add(pointLight);

        const spotLight = new THREE.SpotLight(ledGlowColor, 2.0, 5, Math.PI / 4, 0.4, 0.8);
        spotLight.position.set(0, t * 2 + y0 + 0.02, hd - t * 2 - 0.01);
        spotLight.target.position.set(0, t + y0, hd - t * 2 - 0.02);
        ledGroup.add(spotLight);
        ledGroup.add(spotLight.target);

        this.furnitureGroup.add(ledGroup);
        this.components.set('led', ledGroup);

        if (!ledGroup.userData._animateFn) {
            const animate = () => {
                if (this.components.has('led')) {
                    const time = Date.now() * 0.001;
                    const pulse = 0.5 + 0.5 * Math.sin(time * 2);
                    pointLight.intensity = 1.2 + pulse * 0.6;
                    tube.material.opacity = 0.7 + pulse * 0.3;
                    glow.material.opacity = 0.2 + pulse * 0.2;
                }
            };
            ledGroup.userData._animateFn = animate;
        }
    }

    _buildMountingRail(w, d, y0, t) {
        const railMat = this.materialRefs.get('mountRail');
        const railW = w - 0.04;
        const railH = 0.015;
        const railD = 0.02;

        const rail = new this.THREE.Mesh(
            new this.THREE.BoxGeometry(railW, railH, railD),
            railMat
        );
        rail.position.set(0, y0 - railH / 2 - 0.005, 0);
        rail.castShadow = true;
        rail.receiveShadow = true;
        this.furnitureGroup.add(rail);
        this.components.set('mount_rail', rail);
    }

    _addBox(x, y, z, w, h, d, material, name, bevelSize = 0.002) {
        const THREE = this.THREE;
        let geometry;
        if (bevelSize > 0 && w > bevelSize * 2 && h > bevelSize * 2 && d > bevelSize * 2) {
            geometry = this._createBevelBox(w, h, d, bevelSize);
        } else {
            geometry = new THREE.BoxGeometry(w, h, d);
        }
        const mesh = new THREE.Mesh(geometry, material);
        mesh.position.set(x, y, z);
        mesh.castShadow = true;
        mesh.receiveShadow = true;
        if (name) {
            mesh.name = name;
            this.components.set(name, mesh);
        }
        this.furnitureGroup.add(mesh);
        return mesh;
    }

    _createBevelBox(w, h, d, bevel) {
        const THREE = this.THREE;
        const shape = new THREE.Shape();
        const hw = w / 2;
        const hh = h / 2;
        const r = Math.min(bevel, hw * 0.5, hh * 0.5);

        shape.moveTo(-hw + r, -hh);
        shape.lineTo(hw - r, -hh);
        shape.quadraticCurveTo(hw, -hh, hw, -hh + r);
        shape.lineTo(hw, hh - r);
        shape.quadraticCurveTo(hw, hh, hw - r, hh);
        shape.lineTo(-hw + r, hh);
        shape.quadraticCurveTo(-hw, hh, -hw, hh - r);
        shape.lineTo(-hw, -hh + r);
        shape.quadraticCurveTo(-hw, -hh, -hw + r, -hh);

        const extrudeSettings = {
            depth: d - bevel * 2,
            bevelEnabled: true,
            bevelThickness: bevel,
            bevelSize: bevel,
            bevelSegments: 2,
            curveSegments: 4,
        };

        const geometry = new THREE.ExtrudeGeometry(shape, extrudeSettings);
        geometry.center();
        return geometry;
    }

    clear() {
        const group = this.furnitureGroup;
        while (group.children.length > 0) {
            const child = group.children[0];
            this._disposeObject(child);
            group.remove(child);
        }
        this.components.clear();
    }

    _disposeObject(obj) {
        if (obj.geometry) obj.geometry.dispose();
        if (obj.material) {
            if (Array.isArray(obj.material)) {
                obj.material.forEach((m) => m.dispose());
            } else {
                obj.material.dispose();
            }
        }
        if (obj.children) {
            obj.children.forEach((child) => this._disposeObject(child));
        }
    }

    _fitCamera(w, h, d, t, y0) {
        const target = new this.THREE.Vector3(0, h / 2 + y0, 0);
        const camera = this.sceneManager.camera;
        const controls = this.sceneManager.controls;

        if (camera && controls) {
            const maxDim = Math.max(w, h, d);
            const fov = camera.fov * (Math.PI / 180);
            const distance = (maxDim / (2 * Math.tan(fov / 2))) * 1.6;
            const camX = distance * 0.65;
            const camY = h / 2 + y0 + distance * 0.35;
            const camZ = distance * 0.65;

            this.sceneManager._animateCameraTo(
                new this.THREE.Vector3(camX, camY, camZ),
                target,
                400
            );
        }
    }

    /**
     * Add dimension labels showing measurements on the 3D model.
     */
    _addDimensionLabels(w, h, d, t, y0, configW, configH, configD) {
        const THREE = this.THREE;
        const labelGroup = new THREE.Group();
        labelGroup.name = 'dimension_labels';

        const createLabel = (text, x, y, z, color = '#1A1714') => {
            const canvas = document.createElement('canvas');
            canvas.width = 256;
            canvas.height = 64;
            const ctx = canvas.getContext('2d');
            ctx.fillStyle = 'rgba(255,255,255,0.9)';
            ctx.roundRect(0, 0, 256, 64, 8);
            ctx.fill();
            ctx.strokeStyle = color;
            ctx.lineWidth = 2;
            ctx.roundRect(0, 0, 256, 64, 8);
            ctx.stroke();
            ctx.fillStyle = color;
            ctx.font = 'bold 28px Inter, sans-serif';
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';
            ctx.fillText(text, 128, 32);

            const tex = new THREE.CanvasTexture(canvas);
            tex.needsUpdate = true;
            const mat = new THREE.SpriteMaterial({ map: tex, transparent: true, depthTest: false });
            const sprite = new THREE.Sprite(mat);
            sprite.position.set(x, y, z);
            sprite.scale.set(0.5, 0.125, 1);
            return sprite;
        };

        labelGroup.add(createLabel(`${configW} cm`, 0, y0 - 0.15, d / 2 + 0.05));
        labelGroup.add(createLabel(`${configH} cm`, w / 2 + 0.1, h / 2 + y0, 0));
        labelGroup.add(createLabel(`${configD} cm`, 0, y0 - 0.15, -d / 2 - 0.05));

        this.furnitureGroup.add(labelGroup);
        this.components.set('dimensions', labelGroup);
    }

    _renderError() {
        const THREE = this.THREE;
        const errorMat = new THREE.MeshStandardMaterial({
            color: 0xff6b6b,
            roughness: 0.4,
            metalness: 0.0,
        });

        const geo = new THREE.BoxGeometry(0.5, 0.5, 0.5);
        const mesh = new THREE.Mesh(geo, errorMat);
        mesh.name = 'error_cube';
        mesh.castShadow = true;
        mesh.receiveShadow = true;
        this.furnitureGroup.add(mesh);
        this.components.set('error', mesh);

        const textMat = new THREE.MeshBasicMaterial({
            color: 0xffffff,
            transparent: true,
            opacity: 0.9,
            side: THREE.DoubleSide,
        });

        this.debug.step('[3D] Model loading... fallback cube rendered');
    }

    _countAllMeshes() {
        let count = 0;
        this.furnitureGroup.traverse((child) => {
            if (child.isMesh) count++;
        });
        return count;
    }

    /**
     * Open/close all doors with smooth animation.
     * Uses pivot-based rotation around hinge axis.
     */
    setOpenDoors(open) {
        const doors = [];
        this.furnitureGroup.traverse((child) => {
            if (child.userData && child.userData.type === 'door') {
                doors.push(child);
            }
        });

        const duration = 600;
        const startTime = performance.now();

        const startRotations = doors.map(d => d.rotation.y);
        const targetRotations = doors.map((d) => {
            if (!open) return 0;
            const idx = d.userData.index;
            const dir = idx % 2 === 0 ? 1 : -1;
            return dir * this.THREE.MathUtils.degToRad(d.userData.maxOpen || 105);
        });

        const animate = (now) => {
            const elapsed = now - startTime;
            const t = Math.min(elapsed / duration, 1);
            const easeT = t < 0.5 ? 4 * t * t * t : 1 - Math.pow(-2 * t + 2, 3) / 2;

            for (let i = 0; i < doors.length; i++) {
                doors[i].rotation.y = startRotations[i] + (targetRotations[i] - startRotations[i]) * easeT;
                doors[i].userData.open = open;
            }

            if (t < 1) {
                requestAnimationFrame(animate);
            }
        };

        requestAnimationFrame(animate);
    }

    /**
     * Toggle door open/close on click.
     */
    toggleDoor(doorObj) {
        if (doorObj.userData && doorObj.userData.type === 'door') {
            this.setOpenDoors(!doorObj.userData.open);
        }
    }

    /**
     * Animate drawer slide out/in.
     */
    setDrawerOpen(drawerIndex, open) {
        const drawer = this.components.get(`drawer_${drawerIndex}`);
        if (!drawer || !drawer.userData) return;

        const duration = 400;
        const startTime = performance.now();
        const startZ = drawer.position.z;
        const slideAmount = 0.35;
        const targetZ = open ? startZ + slideAmount : drawer.userData.homeZ !== undefined ? drawer.userData.homeZ : 0;

        if (drawer.userData.homeZ === undefined) {
            drawer.userData.homeZ = startZ;
        }

        const animate = (now) => {
            const elapsed = now - startTime;
            const t = Math.min(elapsed / duration, 1);
            const easeT = t < 0.5 ? 2 * t * t : 1 - Math.pow(-2 * t + 2, 2) / 2;

            drawer.position.z = startZ + (targetZ - startZ) * easeT;
            drawer.userData.open = open;

            if (t < 1) {
                requestAnimationFrame(animate);
            }
        };

        requestAnimationFrame(animate);
    }

    /**
     * Toggle all drawers open/closed.
     */
    setAllDrawersOpen(open) {
        const drawers = [];
        this.components.forEach((comp, key) => {
            if (key.startsWith('drawer_')) {
                drawers.push(comp);
            }
        });

        drawers.forEach((drawer, idx) => {
            setTimeout(() => {
                this.setDrawerOpen(parseInt(drawer.userData.index), open);
            }, idx * 80);
        });
    }

    getLightCount() {
        let count = 0;
        this.furnitureGroup.traverse((child) => {
            if (child.isLight) count++;
        });
        return count;
    }

    getMaterialCount() {
        const materials = new Set();
        this.furnitureGroup.traverse((child) => {
            if (child.isMesh && child.material) {
                if (Array.isArray(child.material)) {
                    child.material.forEach((m) => materials.add(m.uuid));
                } else {
                    materials.add(child.material.uuid);
                }
            }
        });
        return materials.size;
    }
}


/**
 * Model Loader — GLTFLoader wrapper with procedural fallback.
 * Attempts to load GLB models; if unavailable, returns null so the
 * furniture builder can generate procedural geometry instead.
 */
window.LC.Configurator.ModelLoader = class {
    constructor(THREE, debug) {
        this.THREE = THREE;
        this.debug = debug;
        this.cache = new Map();
        this.loader = null;
        this._initLoader();
    }

    _initLoader() {
        try {
            if (typeof GLTFLoader !== 'undefined') {
                this.loader = new GLTFLoader();
                this.debug.step('[3D] GLTFLoader initialized');
            } else {
                this.debug.warn('GLTFLoader not loaded — using procedural geometry only');
            }
        } catch (err) {
            this.debug.error('Failed to init GLTFLoader', err);
        }
    }

    /**
     * Load a GLB model by path. Returns a Promise<THREE.Group|null>.
     */
    load(modelPath, cacheKey = null) {
        const key = cacheKey || modelPath;

        if (this.cache.has(key)) {
            this.debug.info(`[3D] Model loaded from cache: ${key}`);
            return Promise.resolve(this.cache.get(key).clone());
        }

        if (!this.loader) {
            this.debug.warn(`[3D] No loader available for: ${modelPath}`);
            return Promise.resolve(null);
        }

        return new Promise((resolve) => {
            this.loader.load(
                modelPath,
                (gltf) => {
                    const model = gltf.scene || gltf.scenes[0];
                    if (model) {
                        this.cache.set(key, model);
                        this.debug.step(`[3D] Model loaded: ${modelPath}`);
                        resolve(model.clone());
                    } else {
                        this.debug.warn(`[3D] Loaded model is empty: ${modelPath}`);
                        resolve(null);
                    }
                },
                (progress) => {
                    if (progress.lengthComputable) {
                        const pct = Math.round((progress.loaded / progress.total) * 100);
                        this.debug.info(`[3D] Loading ${modelPath}: ${pct}%`);
                    }
                },
                (error) => {
                    this.debug.error(`[3D] Failed to load model: ${modelPath}`, error);
                    resolve(null);
                }
            );
        });
    }

    disposeCache() {
        this.cache.clear();
    }
}


/**
 * UI Manager — coordinates all UI interactions and updates.
 * Connects DOM elements with the 3D builder and catalog data.
 */




window.LC.Configurator.UIManager = class {
    constructor(debug) {
        this.debug = debug;
        this.elements = {};
        this.state = {
            currentStep: 0,
            totalSteps: 6,
        };
        this.onConfigChange = null;
    }

    init() {
        this._cacheElements();
        this._bindEvents();
        this._initMaterialCatalog();
        this._initHardwareCatalog();
        this.debug.step('[UI] Manager initialized');
    }

    _cacheElements() {
        const ids = [
            'cfgType', 'cfgWidth', 'cfgHeight', 'cfgDepth',
            'cfgColor', 'cfgCustom', 'cfgFinish', 'cfgHandle',
            'cfgOpen', 'cfgLED',
            'valDoors', 'valShelves', 'valDrawers',
            'btnDoorsLess', 'btnDoorsMore',
            'btnShelvesLess', 'btnShelvesMore',
            'btnDrawersLess', 'btnDrawersMore',
            'btnResetCamera', 'btnFront', 'btnSide', 'btnTop',
            'btnOrcamento', 'btnSalvar', 'btnLink', 'btnImg', 'btnLimpar',
            'cfgPrice', 'cfgSummary', 'cfgTypeTitle', 'cfgHint',
            'stepDoors', 'stepShelves', 'stepDrawers', 'stepInterior', 'stepHardware',
            'canvasWrap', 'cfgCanvas', 'cfgToast',
            'materialGrid', 'hardwareGrid', 'colorGrid',
        ];
        for (const id of ids) {
            this.elements[id] = document.getElementById(id);
        }
    }

    _bindEvents() {
        const e = this.elements;

        if (e.cfgType) e.cfgType.addEventListener('change', () => this._emitChange());
        if (e.cfgColor) e.cfgColor.addEventListener('change', () => this._emitChange());
        if (e.cfgCustom) e.cfgCustom.addEventListener('input', () => this._emitChange());
        if (e.cfgFinish) e.cfgFinish.addEventListener('change', () => this._emitChange());
        if (e.cfgHandle) e.cfgHandle.addEventListener('change', () => this._emitChange());
        if (e.cfgOpen) e.cfgOpen.addEventListener('change', () => this._emitChange());
        if (e.cfgLED) e.cfgLED.addEventListener('change', () => this._emitChange());

        if (e.cfgWidth) e.cfgWidth.addEventListener('input', () => this._emitChange());
        if (e.cfgHeight) e.cfgHeight.addEventListener('input', () => this._emitChange());
        if (e.cfgDepth) e.cfgDepth.addEventListener('input', () => this._emitChange());

        if (e.btnDoorsLess) e.btnDoorsLess.addEventListener('click', () => this._adjustCounter('doors', -1));
        if (e.btnDoorsMore) e.btnDoorsMore.addEventListener('click', () => this._adjustCounter('doors', 1));
        if (e.btnShelvesLess) e.btnShelvesLess.addEventListener('click', () => this._adjustCounter('shelves', -1));
        if (e.btnShelvesMore) e.btnShelvesMore.addEventListener('click', () => this._adjustCounter('shelves', 1));
        if (e.btnDrawersLess) e.btnDrawersLess.addEventListener('click', () => this._adjustCounter('drawers', -1));
        if (e.btnDrawersMore) e.btnDrawersMore.addEventListener('click', () => this._adjustCounter('drawers', 1));

        if (e.btnResetCamera) e.btnResetCamera.addEventListener('click', () => this._fireCameraEvent('reset'));
        if (e.btnFront) e.btnFront.addEventListener('click', () => this._fireCameraEvent('front'));
        if (e.btnSide) e.btnSide.addEventListener('click', () => this._fireCameraEvent('side'));
        if (e.btnTop) e.btnTop.addEventListener('click', () => this._fireCameraEvent('top'));
    }

    _initMaterialCatalog() {
        const grid = this.elements.colorGrid;
        if (!grid) return;

        grid.innerHTML = '';
        for (const color of window.LC.Configurator.COLOR_PRESETS) {
            const btn = document.createElement('button');
            btn.className = 'swatch-btn';
            btn.title = `${color.name}${color.available_at_lc ? '' : ' (Referência de catálogo)'}`;
            btn.dataset.colorId = color.id;
            btn.innerHTML = `
                <span class="swatch-preview" style="background:${color.hex};"></span>
                <span class="swatch-name">${color.name}</span>
            `;
            btn.addEventListener('click', () => {
                if (this.elements.cfgColor) {
                    this.elements.cfgColor.value = color.id;
                    this._emitChange();
                }
            });
            grid.appendChild(btn);
        }
    }

    _initHardwareCatalog() {
        const grid = this.elements.hardwareGrid;
        if (!grid) return;

        grid.innerHTML = '';
        for (const handle of window.LC.Configurator.HANDLE_TYPES) {
            if (!handle.available_at_lc) continue;
            const btn = document.createElement('button');
            btn.className = 'hw-btn';
            btn.dataset.handleId = handle.id;
            btn.type = 'button';
            btn.innerHTML = `
                <span class="hw-name">${handle.name}</span>
                <span class="hw-desc">${handle.description || ''}</span>
            `;
            btn.addEventListener('click', (ev) => {
                ev.preventDefault();
                ev.stopPropagation();
                if (this.elements.cfgHandle) {
                    this.elements.cfgHandle.value = handle.id;
                    this._updateHardwareSelection(handle.id);
                    this._emitChange();
                }
            });
            grid.appendChild(btn);
        }
        this._updateHardwareSelection(this.elements.cfgHandle?.value || 'alca');
    }

    _updateHardwareSelection(selectedId) {
        const grid = this.elements.hardwareGrid;
        if (!grid) return;
        const buttons = grid.querySelectorAll('.hw-btn');
        buttons.forEach(btn => {
            if (btn.dataset.handleId === selectedId) {
                btn.classList.add('active');
            } else {
                btn.classList.remove('active');
            }
        });
    }

    _adjustCounter(type, delta) {
        if (this.onConfigChange) {
            this.onConfigChange({ type: 'counter', counter: type, delta });
        }
    }

    _emitChange() {
        if (this.onConfigChange) {
            this.onConfigChange({ type: 'config' });
        }
    }

    _fireCameraEvent(preset) {
        if (this.onConfigChange) {
            this.onConfigChange({ type: 'camera', preset });
        }
    }

    getConfigFromUI() {
        const e = this.elements;
        return {
            typeKey: e.cfgType?.value || 'guarda_roupa',
            W: parseInt(e.cfgWidth?.value) || 180,
            H: parseInt(e.cfgHeight?.value) || 220,
            D: parseInt(e.cfgDepth?.value) || 55,
            doors: parseInt(e.valDoors?.textContent) || 0,
            shelves: parseInt(e.valShelves?.textContent) || 0,
            drawers: parseInt(e.valDrawers?.textContent) || 0,
            doorColor: e.cfgColor?.value || 'caramelo',
            customColor: e.cfgCustom?.value || null,
            finish: e.cfgFinish?.value || 'melamina',
            handleType: e.cfgHandle?.value || 'alca',
            interiorOpen: e.cfgOpen?.checked || false,
            ledEnabled: e.cfgLED?.checked || false,
        };
    }

    updateUIFromConfig(config) {
        const e = this.elements;
        if (e.cfgType && config.typeKey) e.cfgType.value = config.typeKey;
        if (e.cfgWidth) e.cfgWidth.value = config.W;
        if (e.cfgHeight) e.cfgHeight.value = config.H;
        if (e.cfgDepth) e.cfgDepth.value = config.D;
        if (e.valDoors) e.valDoors.textContent = config.doors;
        if (e.valShelves) e.valShelves.textContent = config.shelves;
        if (e.valDrawers) e.valDrawers.textContent = config.drawers;
        if (e.cfgColor && config.doorColor) e.cfgColor.value = config.doorColor;
        if (e.cfgCustom && config.customColor) e.cfgCustom.value = config.customColor;
        if (e.cfgFinish && config.finish) e.cfgFinish.value = config.finish;
        if (e.cfgHandle && config.handleType) e.cfgHandle.value = config.handleType;
        if (e.cfgOpen) e.cfgOpen.checked = config.interiorOpen || false;
        if (e.cfgLED) e.cfgLED.checked = config.ledEnabled || false;
        if (e.cfgTypeTitle && config.typeLabel) e.cfgTypeTitle.textContent = config.typeLabel;
        if (e.cfgHint && config.hint) e.cfgHint.textContent = config.hint;
    }

    updatePrice(price) {
        if (this.elements.cfgPrice) {
            this.elements.cfgPrice.textContent = `A partir de R$ ${price.toLocaleString('pt-BR')}`;
        }
    }

    updateSummary(text) {
        if (this.elements.cfgSummary) {
            this.elements.cfgSummary.textContent = text;
        }
    }

    showToast(msg) {
        const toast = this.elements.cfgToast;
        if (!toast) return;
        toast.textContent = msg;
        toast.classList.add('show');
        setTimeout(() => toast.classList.remove('show'), 2000);
    }

    updateStepVisibility(typeKey, typeDef) {
        const e = this.elements;
        const max = typeDef?.limits || {};
        if (e.stepDoors) e.stepDoors.style.display = (max.maxDoors || 0) > 0 ? '' : 'none';
        if (e.stepShelves) e.stepShelves.style.display = (max.maxShelves || 0) > 0 ? '' : 'none';
        if (e.stepDrawers) e.stepDrawers.style.display = (max.maxDrawers || 0) > 0 ? '' : 'none';
        if (e.stepInterior) e.stepInterior.style.display = (max.maxDoors || 0) > 0 ? '' : 'none';
        if (e.stepHardware) e.stepHardware.style.display = (max.maxDoors || 0) > 0 ? '' : 'none';
    }
}


/**
 * API Service — communicates with the PHP backend for catalog data and project persistence.
 */
window.LC.Configurator.ApiService = class {
    constructor(debug) {
        this.debug = debug;
        this.baseUrl = '/api/configurador';
        this.csrfToken = null;
    }

    getCsrfToken() {
        const meta = document.querySelector('meta[name="csrf-token"]');
        if (meta) return meta.content;
        const input = document.querySelector('input[name="csrf_token"]');
        if (input) return input.value;
        return null;
    }

    async fetchCatalog() {
        try {
            const res = await fetch(`${this.baseUrl}/catalog`);
            if (!res.ok) throw new Error(`HTTP ${res.status}`);
            return await res.json();
        } catch (err) {
            this.debug.warn('Failed to fetch catalog from API — using local data');
            return null;
        }
    }

    async saveProject(projectData) {
        try {
            const token = this.getCsrfToken();
            const res = await fetch(`${this.baseUrl}/projects`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    ...(token ? { 'X-CSRF-Token': token } : {}),
                },
                body: JSON.stringify(projectData),
            });
            if (!res.ok) throw new Error(`HTTP ${res.status}`);
            return await res.json();
        } catch (err) {
            this.debug.error('Failed to save project to server', err);
            return { success: false, error: err.message };
        }
    }

    async loadProject(projectId) {
        try {
            const res = await fetch(`${this.baseUrl}/projects/${encodeURIComponent(projectId)}`);
            if (!res.ok) throw new Error(`HTTP ${res.status}`);
            return await res.json();
        } catch (err) {
            this.debug.error('Failed to load project from server', err);
            return null;
        }
    }
}


/**
 * Project Service — handles serialization, localStorage persistence, and server sync.
 */
window.LC.Configurator.ProjectService = class {
    constructor(apiService, debug) {
        this.api = apiService;
        this.debug = debug;
        this.storageKey = 'lc_configurador_project';
        this.version = '1.0.0';
    }

    serialize(config) {
        return {
            version: this.version,
            timestamp: Date.now(),
            furnitureType: config.typeKey,
            dimensions: { W: config.W, H: config.H, D: config.D },
            structure: {
                doors: config.doors,
                shelves: config.shelves,
                drawers: config.drawers,
                dividerCount: config.dividerCount || 0,
            },
            material: {
                color: config.doorColor,
                customColor: config.customColor,
                finish: config.finish,
                brand: config.materialBrand || 'generic',
                line: config.materialLine || 'lc_padrao',
            },
            hardware: {
                handleType: config.handleType,
                handleColor: config.handleColor || '#6B5340',
                hingeType: config.hingeType || 'padrao',
                slideType: config.slideType || 'telescopica',
            },
            extras: {
                led: config.ledEnabled || false,
                interiorOpen: config.interiorOpen || false,
                mounted: config.mounted || false,
            },
        };
    }

    deserialize(data) {
        if (!data || !data.version) return null;
        const d = data;
        return {
            typeKey: d.furnitureType || 'guarda_roupa',
            W: d.dimensions?.W || 180,
            H: d.dimensions?.H || 220,
            D: d.dimensions?.D || 55,
            doors: d.structure?.doors || 0,
            shelves: d.structure?.shelves || 0,
            drawers: d.structure?.drawers || 0,
            dividerCount: d.structure?.dividerCount || 0,
            doorColor: d.material?.color || 'caramelo',
            customColor: d.material?.customColor || null,
            finish: d.material?.finish || 'melamina',
            materialBrand: d.material?.brand || 'generic',
            materialLine: d.material?.line || 'lc_padrao',
            handleType: d.hardware?.handleType || 'alca',
            handleColor: d.hardware?.handleColor || '#6B5340',
            hingeType: d.hardware?.hingeType || 'padrao',
            slideType: d.hardware?.slideType || 'telescopica',
            ledEnabled: d.extras?.led || false,
            interiorOpen: d.extras?.interiorOpen || false,
            mounted: d.extras?.mounted || false,
        };
    }

    saveLocal(config) {
        try {
            const data = this.serialize(config);
            localStorage.setItem(this.storageKey, JSON.stringify(data));
            this.debug.step('[Save] Project saved to localStorage');
            return true;
        } catch (err) {
            this.debug.error('Failed to save to localStorage', err);
            return false;
        }
    }

    loadLocal() {
        try {
            const raw = localStorage.getItem(this.storageKey);
            if (!raw) return null;
            const data = JSON.parse(raw);
            return this.deserialize(data);
        } catch (err) {
            this.debug.error('Failed to load from localStorage', err);
            return null;
        }
    }

    clearLocal() {
        try {
            localStorage.removeItem(this.storageKey);
        } catch (err) {
            // ignore
        }
    }

    async saveToServer(config) {
        const data = this.serialize(config);
        return await this.api.saveProject(data);
    }

    generateShareUrl(config) {
        const data = this.serialize(config);
        const json = JSON.stringify(data);
        const encoded = btoa(unescape(encodeURIComponent(json)));
        return `${location.origin}${location.pathname}#p=${encoded}`;
    }

    loadFromHash() {
        const match = location.hash.match(/p=([^&]+)/);
        if (!match) return null;
        try {
            const json = decodeURIComponent(escape(atob(decodeURIComponent(match[1]))));
            const data = JSON.parse(json);
            return this.deserialize(data);
        } catch (err) {
            this.debug.error('Failed to load project from URL hash', err);
            return null;
        }
    }

    generateWhatsAppMessage(config, price) {
        const lines = [];
        lines.push('Olá! Montei um móvel no configurador da LC Soluções em Móveis:');
        lines.push('');
        lines.push(`*Móvel:* ${config.typeLabel || 'Guarda-roupa planejado'}`);
        lines.push(`*Medidas:* ${config.W} cm (L) × ${config.H} cm (A) × ${config.D} cm (P)`);
        lines.push(`*Cor:* ${config.colorLabel || config.doorColor}`);
        lines.push(`*Acabamento:* ${config.finishLabel || config.finish}`);
        lines.push(`*Puxador:* ${config.handleLabel || config.handleType}`);
        lines.push(`*Portas:* ${config.doors} | *Prateleiras:* ${config.shelves} | *Gavetas:* ${config.drawers}`);
        if (config.ledEnabled) lines.push(`*LED:* Sim`);
        if (price) lines.push(`*Estimativa:* a partir de R$ ${price.toLocaleString('pt-BR')}`);
        lines.push('');
        lines.push('Gostaria de mais informações sobre este projeto.');
        return encodeURIComponent(lines.join('\n'));
    }
}


})();










