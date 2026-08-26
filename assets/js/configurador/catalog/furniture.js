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
export const FURNITURE_CATALOG = {
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

export const FURNITURE_CATEGORIES = [
    { id: 'quarto', label: 'Quarto' },
    { id: 'cozinha', label: 'Cozinha' },
    { id: 'sala', label: 'Sala' },
    { id: 'escritorio', label: 'Home Office' },
    { id: 'decorativo', label: 'Decorativo' },
    { id: 'comercial', label: 'Comercial' },
];

export function getFurnitureType(key) {
    return FURNITURE_CATALOG[key] || null;
}

export function getAllFurnitureTypes() {
    return Object.entries(FURNITURE_CATALOG).map(([key, def]) => ({
        key,
        ...def,
    }));
}
