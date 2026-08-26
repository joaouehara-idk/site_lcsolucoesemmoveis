/**
 * LC Soluções em Móveis — Furniture Engine 1.0
 * Parametric CAD / Manufacturing Core
 * Unidade oficial: MILÍMETRO (mm)
 */
(function() {
    'use strict';

    const UnitSystem = {
        mmToCm: (mm) => mm / 10,
        mmToM: (mm) => mm / 1000,
        cmToMm: (cm) => cm * 10,
        mToMm: (m) => m * 1000,
        roundToMm: (value) => Math.round(value * 1000) / 1000,
        formatMm: (mm) => {
            if (mm >= 1000) return `${(mm / 1000).toFixed(2)} m`;
            if (mm >= 10) return `${(mm / 10).toFixed(1)} cm`;
            return `${mm.toFixed(1)} mm`;
        },
        isValidDimension: (value) => isFinite(value) && value > 0,
    };

    const ToleranceSystem = {
        construction: 0.5, assembly: 1.0, manufacturing: 0.3, visual: 0.1,
        doorGap: 3, drawerGap: 3, shelfGap: 2, backPanelRecess: 7, edgeBanding: 0.5,
    };

    const MaterialCatalog = {
        materials: {
            mdf: { id: 'mdf', label: 'MDF', density: 750, standardThicknesses: [6, 9, 12, 15, 18, 25], sheetWidth: 1830, sheetHeight: 2750 },
            mdp: { id: 'mdp', label: 'MDP', density: 700, standardThicknesses: [12, 15, 18, 25], sheetWidth: 1830, sheetHeight: 2750 },
        },
        finishes: {
            melamina: { id: 'melamina', label: 'Melamina', roughness: 0.72 },
            liso: { id: 'liso', label: 'Liso (fosco)', roughness: 0.55 },
            texturizado: { id: 'texturizado', label: 'Texturizado (madeira)', roughness: 0.78 },
            laca: { id: 'laca', label: 'Laca (brilhante)', roughness: 0.08 },
        },
        colors: {
            branco: { id: 'branco', label: 'Branco', hex: '#ECEAE3' },
            caramelo: { id: 'caramelo', label: 'Caramelo', hex: '#C8A87C' },
            carvalho: { id: 'carvalho', label: 'Carvalho', hex: '#C9A876' },
            nogueira: { id: 'nogueira', label: 'Nogueira', hex: '#6B5340' },
            preto: { id: 'preto', label: 'Preto', hex: '#2B2B2B' },
        },
    };

    const HardwareCatalog = {
        hinges: {
            soft_close: { id: 'soft_close', label: 'Dobradiça Soft-Close', openingAngle: 110, cupDiameter: 35, minDoorWidth: 250, maxDoorWidth: 900, minDoorHeight: 250, maxDoorHeight: 2400, maxDoorWeight: 12 },
            convencional: { id: 'convencional', label: 'Dobradiça Convencional', openingAngle: 95, cupDiameter: 35, minDoorWidth: 250, maxDoorWidth: 900, minDoorHeight: 250, maxDoorHeight: 2400, maxDoorWeight: 8 },
        },
        slides: {
            telescopica_soft: { id: 'telescopica_soft', label: 'Corrediça Telescópica Soft-Close', nominalLength: 500, sideClearance: 12.7, minimumCabinetDepth: 400, maximumCabinetDepth: 800, loadCapacity: 45 },
            telescopica: { id: 'telescopica', label: 'Corrediça Telescópica', nominalLength: 500, sideClearance: 12.7, minimumCabinetDepth: 400, maximumCabinetDepth: 800, loadCapacity: 40 },
        },
        handles: {
            alca: { id: 'alca', label: 'Alça', type: 'bar', length: 128, centerDistance: 128, height: 30 },
            botao: { id: 'botao', label: 'Botão', type: 'knob', diameter: 25, height: 35 },
            nenhum: { id: 'nenhum', label: 'Nenhum', type: 'none' },
        },
        getHingeQuantity: (doorHeightMm) => {
            if (doorHeightMm <= 900) return 2;
            if (doorHeightMm <= 1200) return 3;
            if (doorHeightMm <= 1600) return 4;
            if (doorHeightMm <= 2000) return 5;
            return 6;
        },
        validateSlideCompatibility: (slideType, cabinetDepthMm) => {
            const slide = HardwareCatalog.slides[slideType];
            if (!slide) return { valid: false, reason: 'Corrediça não encontrada' };
            if (cabinetDepthMm < slide.minimumCabinetDepth) return { valid: false, reason: `Profundidade mínima: ${slide.minimumCabinetDepth}mm` };
            if (cabinetDepthMm > slide.maximumCabinetDepth) return { valid: false, reason: `Profundidade máxima: ${slide.maximumCabinetDepth}mm` };
            return { valid: true };
        },
    };

    const FurnitureTemplates = {
        guarda_roupa: {
            id: 'guarda_roupa', label: 'Guarda-roupa', category: 'quarto',
            defaultDimensions: { width: 1800, height: 2200, depth: 550 },
            defaultStructure: { doors: 2, drawers: 0, shelves: 3, dividers: 0 },
            limits: { minWidth: 400, maxWidth: 4000, minHeight: 300, maxHeight: 3000, minDepth: 200, maxDepth: 800, maxDoors: 8, maxShelves: 10, maxDrawers: 6, maxDividers: 4 },
            defaultMaterials: { structure: { type: 'mdf', thickness: 18 }, top: { type: 'mdf', thickness: 18 }, bottom: { type: 'mdf', thickness: 18 }, back: { type: 'mdf', thickness: 6 }, door: { type: 'mdf', thickness: 18 }, drawer: { type: 'mdf', thickness: 18 }, drawerBottom: { type: 'mdf', thickness: 6 }, shelf: { type: 'mdf', thickness: 18 } },
            defaultHardware: { hinge: 'soft_close', slide: 'telescopica_soft', handle: 'alca' },
            mounted: false, description: 'Ideal para quartos: 1200-2400mm de largura.',
        },
        closet: {
            id: 'closet', label: 'Closet', category: 'quarto',
            defaultDimensions: { width: 2400, height: 2400, depth: 600 },
            defaultStructure: { doors: 3, drawers: 2, shelves: 4, dividers: 1 },
            limits: { minWidth: 1200, maxWidth: 4000, minHeight: 1800, maxHeight: 3000, minDepth: 500, maxDepth: 800, maxDoors: 12, maxShelves: 12, maxDrawers: 8, maxDividers: 6 },
            defaultMaterials: { structure: { type: 'mdf', thickness: 18 }, top: { type: 'mdf', thickness: 18 }, bottom: { type: 'mdf', thickness: 18 }, back: { type: 'mdf', thickness: 6 }, door: { type: 'mdf', thickness: 18 }, drawer: { type: 'mdf', thickness: 18 }, drawerBottom: { type: 'mdf', thickness: 6 }, shelf: { type: 'mdf', thickness: 18 } },
            defaultHardware: { hinge: 'soft_close', slide: 'telescopica_soft', handle: 'alca' },
            mounted: false, description: 'Largura ampla: 2000-3600mm.',
        },
        comoda: {
            id: 'comoda', label: 'Cômoda', category: 'quarto',
            defaultDimensions: { width: 1200, height: 800, depth: 450 },
            defaultStructure: { doors: 0, drawers: 4, shelves: 0, dividers: 0 },
            limits: { minWidth: 600, maxWidth: 2000, minHeight: 300, maxHeight: 1200, minDepth: 350, maxDepth: 600, maxDoors: 0, maxShelves: 4, maxDrawers: 10, maxDividers: 0 },
            defaultMaterials: { structure: { type: 'mdf', thickness: 18 }, top: { type: 'mdf', thickness: 18 }, bottom: { type: 'mdf', thickness: 18 }, back: { type: 'mdf', thickness: 6 }, door: { type: 'mdf', thickness: 18 }, drawer: { type: 'mdf', thickness: 18 }, drawerBottom: { type: 'mdf', thickness: 6 }, shelf: { type: 'mdf', thickness: 18 } },
            defaultHardware: { hinge: 'soft_close', slide: 'telescopica_soft', handle: 'alca' },
            mounted: false, description: 'Altura 700-900mm, 4-6 gavetas.',
        },
        cozinha_base: {
            id: 'cozinha_base', label: 'Armário de Cozinha Base', category: 'cozinha',
            defaultDimensions: { width: 800, height: 720, depth: 560 },
            defaultStructure: { doors: 2, drawers: 0, shelves: 1, dividers: 0 },
            limits: { minWidth: 300, maxWidth: 4000, minHeight: 300, maxHeight: 2400, minDepth: 500, maxDepth: 800, maxDoors: 8, maxShelves: 8, maxDrawers: 8, maxDividers: 4 },
            defaultMaterials: { structure: { type: 'mdp', thickness: 18 }, top: { type: 'mdf', thickness: 25 }, bottom: { type: 'mdp', thickness: 18 }, back: { type: 'mdf', thickness: 6 }, door: { type: 'mdf', thickness: 18 }, drawer: { type: 'mdf', thickness: 18 }, drawerBottom: { type: 'mdf', thickness: 6 }, shelf: { type: 'mdf', thickness: 18 } },
            defaultHardware: { hinge: 'soft_close', slide: 'telescopica_soft', handle: 'alca' },
            mounted: false, hasPlinth: true, plinthHeight: 150, description: 'Bancada padrão: profundidade 600mm.',
        },
        estante: {
            id: 'estante', label: 'Estante', category: 'sala',
            defaultDimensions: { width: 900, height: 2000, depth: 350 },
            defaultStructure: { doors: 0, drawers: 0, shelves: 5, dividers: 0 },
            limits: { minWidth: 600, maxWidth: 2000, minHeight: 600, maxHeight: 2600, minDepth: 250, maxDepth: 500, maxDoors: 0, maxShelves: 12, maxDrawers: 0, maxDividers: 4 },
            defaultMaterials: { structure: { type: 'mdf', thickness: 18 }, top: { type: 'mdf', thickness: 18 }, bottom: { type: 'mdf', thickness: 18 }, back: { type: 'mdf', thickness: 6 }, door: { type: 'mdf', thickness: 18 }, drawer: { type: 'mdf', thickness: 18 }, drawerBottom: { type: 'mdf', thickness: 6 }, shelf: { type: 'mdf', thickness: 18 } },
            defaultHardware: { hinge: 'soft_close', slide: 'telescopica_soft', handle: 'alca' },
            mounted: false, description: 'Largura 600-1200mm, altura até 2400mm.',
        },
        painel_tv: {
            id: 'painel_tv', label: 'Painel para TV', category: 'sala',
            defaultDimensions: { width: 1800, height: 500, depth: 350 },
            defaultStructure: { doors: 0, drawers: 1, shelves: 2, dividers: 0 },
            limits: { minWidth: 800, maxWidth: 3000, minHeight: 300, maxHeight: 1200, minDepth: 250, maxDepth: 500, maxDoors: 0, maxShelves: 6, maxDrawers: 3, maxDividers: 4 },
            defaultMaterials: { structure: { type: 'mdf', thickness: 18 }, top: { type: 'mdf', thickness: 18 }, bottom: { type: 'mdf', thickness: 18 }, back: { type: 'mdf', thickness: 6 }, door: { type: 'mdf', thickness: 18 }, drawer: { type: 'mdf', thickness: 18 }, drawerBottom: { type: 'mdf', thickness: 6 }, shelf: { type: 'mdf', thickness: 18 } },
            defaultHardware: { hinge: 'soft_close', slide: 'telescopica_soft', handle: 'alca' },
            mounted: false, description: 'Altura 400-600mm para base de TV.',
        },
    };

    class PartDefinition {
        constructor(params) {
            this.id = params.id || `part_${Date.now()}_${Math.random().toString(36).substr(2, 9)}`;
            this.name = params.name || 'Peça sem nome';
            this.type = params.type || 'panel';
            this.width = params.width || 0;
            this.height = params.height || 0;
            this.thickness = params.thickness || 18;
            this.material = params.material || { type: 'mdf', thickness: 18 };
            this.finish = params.finish || 'melamina';
            this.color = params.color || 'caramelo';
            this.grainDirection = params.grainDirection || 'none';
            this.edgeBanding = params.edgeBanding || { top: false, bottom: false, left: false, right: false };
            this.position = params.position || { x: 0, y: 0, z: 0 };
            this.quantity = params.quantity || 1;
            this.moduleId = params.moduleId || null;
            this.notes = params.notes || '';
        }
        getAreaM2() { return UnitSystem.roundToMm((this.width / 1000) * (this.height / 1000)); }
        toJSON() { return { id: this.id, name: this.name, type: this.type, width: this.width, height: this.height, thickness: this.thickness, material: this.material, finish: this.finish, color: this.color, grainDirection: this.grainDirection, quantity: this.quantity, moduleId: this.moduleId, notes: this.notes }; }
    }

    class HardwareInstance {
        constructor(params) {
            this.id = params.id || `hw_${Date.now()}_${Math.random().toString(36).substr(2, 9)}`;
            this.type = params.type; this.subType = params.subType; this.name = params.name || 'Ferragem';
            this.quantity = params.quantity || 1; this.partId = params.partId || null; this.moduleId = params.moduleId || null;
        }
        toJSON() { return { id: this.id, type: this.type, subType: this.subType, name: this.name, quantity: this.quantity, partId: this.partId, moduleId: this.moduleId }; }
    }

    class ConstraintEngine {
        constructor() { this.constraints = []; this.warnings = []; this.errors = []; }
        clear() { this.constraints = []; this.warnings = []; this.errors = []; }
        addConstraint(type, message, severity = 'info') { this.constraints.push({ type, message, severity, timestamp: Date.now() }); if (severity === 'warning') this.warnings.push(message); if (severity === 'error') this.errors.push(message); }
        validateDimensions(dimensions, limits) { if (dimensions.width < limits.minWidth) this.addConstraint('dimension', `Largura mínima: ${limits.minWidth}mm`, 'error'); if (dimensions.width > limits.maxWidth) this.addConstraint('dimension', `Largura máxima: ${limits.maxWidth}mm`, 'error'); if (dimensions.height < limits.minHeight) this.addConstraint('dimension', `Altura mínima: ${limits.minHeight}mm`, 'error'); if (dimensions.height > limits.maxHeight) this.addConstraint('dimension', `Altura máxima: ${limits.maxHeight}mm`, 'error'); if (dimensions.depth < limits.minDepth) this.addConstraint('dimension', `Profundidade mínima: ${limits.minDepth}mm`, 'error'); if (dimensions.depth > limits.maxDepth) this.addConstraint('dimension', `Profundidade máxima: ${limits.maxDepth}mm`, 'error'); }
        validateStructure(structure, limits) { if (structure.doors > limits.maxDoors) this.addConstraint('structure', `Máximo de portas: ${limits.maxDoors}`, 'error'); if (structure.drawers > limits.maxDrawers) this.addConstraint('structure', `Máximo de gavetas: ${limits.maxDrawers}`, 'error'); if (structure.shelves > limits.maxShelves) this.addConstraint('structure', `Máximo de prateleiras: ${limits.maxShelves}`, 'error'); if (structure.dividers > limits.maxDividers) this.addConstraint('structure', `Máximo de divisórias: ${limits.maxDividers}`, 'error'); }
        validateHardwareCompatibility(parts, hardware) { const doors = parts.filter(p => p.type === 'door'); doors.forEach(door => { const hinge = HardwareCatalog.hinges[hardware.hinge]; if (hinge) { if (door.width < hinge.minDoorWidth || door.width > hinge.maxDoorWidth) this.addConstraint('hardware', `Porta ${door.name}: largura ${door.width}mm incompatível`, 'warning'); if (door.height < hinge.minDoorHeight || door.height > hinge.maxDoorHeight) this.addConstraint('hardware', `Porta ${door.name}: altura ${door.height}mm incompatível`, 'warning'); } }); }
        validateCollisions(parts) { const shelves = parts.filter(p => p.type === 'shelf'); const tops = parts.filter(p => p.type === 'top'); if (tops.length > 0 && shelves.length > 0) { const moduleWidth = Math.max(...tops.map(p => p.width)); shelves.forEach(shelf => { if (shelf.width > moduleWidth) this.addConstraint('collision', `Prateleira ${shelf.name} excede largura do módulo`, 'error'); }); } }
        hasErrors() { return this.errors.length > 0; }
        hasWarnings() { return this.warnings.length > 0; }
        getResult() { return { valid: !this.hasErrors(), errors: this.errors, warnings: this.warnings, constraints: this.constraints }; }
    }

    class FurnitureEngine {
        constructor() { this.constraintEngine = new ConstraintEngine(); this.parts = []; this.hardware = []; this.modules = []; this.calculationTrace = []; }
        trace(step, description, value, formula) { this.calculationTrace.push({ step, description, value, formula, timestamp: Date.now() }); }

        build(furnitureDefinition) {
            this.parts = []; this.hardware = []; this.modules = []; this.calculationTrace = []; this.constraintEngine.clear();
            const def = furnitureDefinition; const template = FurnitureTemplates[def.type];
            if (!template) { this.constraintEngine.addConstraint('definition', `Template não encontrado: ${def.type}`, 'error'); return this.getResult(); }
            this.constraintEngine.validateDimensions(def.dimensions, template.limits);
            this.constraintEngine.validateStructure(def.structure, template.limits);
            if (this.constraintEngine.hasErrors()) return this.getResult();
            const mainModule = { id: 'main', dimensions: { ...def.dimensions }, structure: { ...def.structure }, materials: { ...template.defaultMaterials }, hardware: { ...template.defaultHardware, ...def.hardware } };
            this.modules.push(mainModule);
            this._generateStructureParts(mainModule, def, template);
            this._generateBackPanel(mainModule, def, template);
            this._generateShelves(mainModule, def, template);
            this._generateDividers(mainModule, def, template);
            this._generateDoors(mainModule, def, template);
            this._generateDrawers(mainModule, def, template);
            this._generateHardware(mainModule, def, template);
            this.constraintEngine.validateCollisions(this.parts);
            this.constraintEngine.validateHardwareCompatibility(this.parts, def.hardware);
            return this.getResult();
        }

        _generateStructureParts(module, def, template) {
            const dims = def.dimensions; const mats = template.defaultMaterials; const T = mats.structure.thickness; const y0 = def.mounted ? 1400 : 0;
            // Centro do móvel na origem X=0
            const centerX = dims.width / 2;
            // Laterais (verticais)
            this.parts.push(new PartDefinition({ name: 'Lateral esquerda', type: 'side', width: T, height: dims.height, thickness: dims.depth, material: mats.structure, finish: def.material.finish, color: def.material.color, grainDirection: 'vertical', edgeBanding: { top: true, bottom: true, left: true, right: false }, position: { x: -centerX + T / 2, y: y0 + dims.height / 2, z: 0 }, moduleId: module.id }));
            this.parts.push(new PartDefinition({ name: 'Lateral direita', type: 'side', width: T, height: dims.height, thickness: dims.depth, material: mats.structure, finish: def.material.finish, color: def.material.color, grainDirection: 'vertical', edgeBanding: { top: true, bottom: true, left: false, right: true }, position: { x: centerX - T / 2, y: y0 + dims.height / 2, z: 0 }, moduleId: module.id }));
            // Tampo (horizontal - em cima)
            this.parts.push(new PartDefinition({ name: 'Tampo', type: 'top', width: dims.width, height: mats.top.thickness, thickness: dims.depth, material: mats.top, finish: def.material.finish, color: def.material.color, grainDirection: 'horizontal', edgeBanding: { top: false, bottom: false, left: true, right: true }, position: { x: 0, y: y0 + dims.height - mats.top.thickness / 2, z: 0 }, moduleId: module.id }));
            // Base (horizontal - embaixo)
            this.parts.push(new PartDefinition({ name: 'Base', type: 'bottom', width: dims.width, height: mats.bottom.thickness, thickness: dims.depth, material: mats.bottom, finish: def.material.finish, color: def.material.color, grainDirection: 'horizontal', edgeBanding: { top: false, bottom: false, left: true, right: true }, position: { x: 0, y: y0 + mats.bottom.thickness / 2, z: 0 }, moduleId: module.id }));
        }

        _generateBackPanel(module, def, template) {
            const dims = def.dimensions; const mats = template.defaultMaterials; const T = mats.structure.thickness; const BT = mats.back.thickness; const y0 = def.mounted ? 1400 : 0;
            const backWidth = dims.width - T * 2; const backHeight = dims.height - mats.top.thickness - mats.bottom.thickness;
            if (backWidth > 0 && backHeight > 0) { this.parts.push(new PartDefinition({ name: 'Fundo', type: 'back', width: backWidth, height: backHeight, thickness: BT, material: mats.back, finish: def.material.finish, color: def.material.color, grainDirection: 'none', edgeBanding: { top: false, bottom: false, left: false, right: false }, position: { x: 0, y: y0 + mats.bottom.thickness + backHeight / 2, z: -dims.depth / 2 + BT / 2 + ToleranceSystem.backPanelRecess }, moduleId: module.id })); }
        }

        _generateShelves(module, def, template) {
            const dims = def.dimensions; const mats = template.defaultMaterials; const T = mats.structure.thickness; const shelfCount = def.structure.shelves; if (shelfCount <= 0) return;
            const y0 = def.mounted ? 1400 : 0; const innerWidth = dims.width - T * 2 - ToleranceSystem.shelfGap * 2; const innerHeight = dims.height - mats.top.thickness - mats.bottom.thickness; const shelfDepth = dims.depth - ToleranceSystem.backPanelRecess - ToleranceSystem.shelfGap; const spacing = innerHeight / (shelfCount + 1);
            for (let i = 0; i < shelfCount; i++) { const shelfY = y0 + mats.bottom.thickness + spacing * (i + 1); this.parts.push(new PartDefinition({ name: `Prateleira ${i + 1}`, type: 'shelf', width: innerWidth, height: mats.shelf.thickness, thickness: shelfDepth, material: mats.shelf, finish: def.material.finish, color: def.material.color, grainDirection: 'horizontal', edgeBanding: { top: false, bottom: false, left: true, right: true }, position: { x: 0, y: shelfY, z: 0 }, moduleId: module.id })); }
            this.trace('shelf', 'Largura da prateleira', innerWidth, `${dims.width} - ${T} * 2 - ${ToleranceSystem.shelfGap} * 2`);
        }

        _generateDividers(module, def, template) {
            const dims = def.dimensions; const mats = template.defaultMaterials; const T = mats.structure.thickness; const dividerCount = def.structure.dividers; if (dividerCount <= 0) return;
            const y0 = def.mounted ? 1400 : 0; const innerHeight = dims.height - mats.top.thickness - mats.bottom.thickness; const dividerDepth = dims.depth - ToleranceSystem.backPanelRecess; const spacing = dims.width / (dividerCount + 1);
            for (let i = 0; i < dividerCount; i++) { const divX = spacing * (i + 1) - dims.width / 2; this.parts.push(new PartDefinition({ name: `Divisória ${i + 1}`, type: 'divider', width: T, height: innerHeight, thickness: dividerDepth, material: mats.structure, finish: def.material.finish, color: def.material.color, grainDirection: 'vertical', edgeBanding: { top: false, bottom: false, left: false, right: false }, position: { x: divX, y: y0 + mats.bottom.thickness + innerHeight / 2, z: 0 }, moduleId: module.id })); }
        }

        _generateDoors(module, def, template) {
            const dims = def.dimensions; const mats = template.defaultMaterials; const T = mats.structure.thickness; const doorCount = def.structure.doors; if (doorCount <= 0) return;
            const y0 = def.mounted ? 1400 : 0; const innerWidth = dims.width - T * 2; const doorHeight = dims.height - mats.top.thickness - mats.bottom.thickness - ToleranceSystem.doorGap * 2; const gapTotal = ToleranceSystem.doorGap * (doorCount + 1); const doorWidth = (innerWidth - gapTotal) / doorCount;
            this.trace('door', 'Largura da porta', doorWidth, `(${innerWidth} - ${gapTotal}) / ${doorCount}`);
            for (let i = 0; i < doorCount; i++) { const doorX = -innerWidth / 2 + ToleranceSystem.doorGap * (i + 1) + doorWidth * i + doorWidth / 2; this.parts.push(new PartDefinition({ name: `Porta ${i + 1}`, type: 'door', width: doorWidth, height: doorHeight, thickness: mats.door.thickness, material: mats.door, finish: def.material.finish, color: def.material.color, grainDirection: 'vertical', edgeBanding: { top: true, bottom: true, left: true, right: true }, position: { x: doorX, y: y0 + mats.bottom.thickness + ToleranceSystem.doorGap + doorHeight / 2, z: dims.depth / 2 - mats.door.thickness / 2 }, moduleId: module.id })); }
        }

        _generateDrawers(module, def, template) {
            const dims = def.dimensions; const mats = template.defaultMaterials; const T = mats.structure.thickness; const drawerCount = def.structure.drawers; if (drawerCount <= 0) return;
            const y0 = def.mounted ? 1400 : 0; const slide = HardwareCatalog.slides[module.hardware.slide] || HardwareCatalog.slides.telescopica_soft; const innerWidth = dims.width - T * 2 - slide.sideClearance * 2; const innerHeight = dims.height - mats.top.thickness - mats.bottom.thickness; const gapTotal = ToleranceSystem.drawerGap * (drawerCount + 1); const drawerHeight = (innerHeight - gapTotal) / drawerCount; const drawerDepth = Math.min(dims.depth - 100, slide.nominalLength);
            this.trace('drawer', 'Largura da gaveta', innerWidth, `${dims.width} - ${T} * 2 - ${slide.sideClearance} * 2`);
            for (let i = 0; i < drawerCount; i++) { const drawerY = y0 + mats.bottom.thickness + ToleranceSystem.drawerGap * (i + 1) + drawerHeight * i + drawerHeight / 2; this.parts.push(new PartDefinition({ name: `Frente gaveta ${i + 1}`, type: 'drawer_front', width: innerWidth, height: drawerHeight - ToleranceSystem.drawerGap, thickness: mats.door.thickness, material: mats.door, finish: def.material.finish, color: def.material.color, grainDirection: 'horizontal', edgeBanding: { top: true, bottom: true, left: true, right: true }, position: { x: 0, y: drawerY, z: dims.depth / 2 - mats.door.thickness / 2 }, moduleId: module.id })); this.parts.push(new PartDefinition({ name: `Lateral gaveta ${i + 1} esq`, type: 'drawer_side', width: T, height: drawerHeight * 0.8, thickness: drawerDepth, material: mats.drawer, finish: def.material.finish, color: def.material.color, grainDirection: 'vertical', edgeBanding: { top: false, bottom: false, left: false, right: false }, position: { x: -innerWidth / 2 + T / 2, y: drawerY, z: dims.depth / 2 - mats.door.thickness / 2 - drawerDepth / 2 }, moduleId: module.id })); this.parts.push(new PartDefinition({ name: `Lateral gaveta ${i + 1} dir`, type: 'drawer_side', width: T, height: drawerHeight * 0.8, thickness: drawerDepth, material: mats.drawer, finish: def.material.finish, color: def.material.color, grainDirection: 'vertical', edgeBanding: { top: false, bottom: false, left: false, right: false }, position: { x: innerWidth / 2 - T / 2, y: drawerY, z: dims.depth / 2 - mats.door.thickness / 2 - drawerDepth / 2 }, moduleId: module.id })); this.parts.push(new PartDefinition({ name: `Fundo gaveta ${i + 1}`, type: 'drawer_bottom', width: innerWidth - T * 2, height: mats.drawerBottom.thickness, thickness: drawerDepth, material: mats.drawerBottom, finish: def.material.finish, color: def.material.color, grainDirection: 'none', edgeBanding: { top: false, bottom: false, left: false, right: false }, position: { x: 0, y: drawerY - drawerHeight * 0.4 + mats.drawerBottom.thickness / 2, z: dims.depth / 2 - mats.door.thickness / 2 - drawerDepth / 2 }, moduleId: module.id })); }
        }

        _generateHardware(module, def, template) {
            const doors = this.parts.filter(p => p.type === 'door'); const drawers = this.parts.filter(p => p.type === 'drawer_front'); const shelves = this.parts.filter(p => p.type === 'shelf');
            if (doors.length > 0) { const hinge = HardwareCatalog.hinges[module.hardware.hinge]; const hingeQty = HardwareCatalog.getHingeQuantity(doors[0].height) * doors.length; this.hardware.push(new HardwareInstance({ type: 'hinge', subType: module.hardware.hinge, name: hinge ? hinge.label : 'Dobradiça', quantity: hingeQty, moduleId: module.id })); }
            if (drawers.length > 0) { const slide = HardwareCatalog.slides[module.hardware.slide]; this.hardware.push(new HardwareInstance({ type: 'slide', subType: module.hardware.slide, name: slide ? slide.label : 'Corrediça', quantity: drawers.length, moduleId: module.id })); }
            const handleCount = doors.length + drawers.length; if (handleCount > 0 && module.hardware.handle !== 'nenhum') { const handle = HardwareCatalog.handles[module.hardware.handle]; this.hardware.push(new HardwareInstance({ type: 'handle', subType: module.hardware.handle, name: handle ? handle.label : 'Puxador', quantity: handleCount, moduleId: module.id })); }
            if (shelves.length > 0) { this.hardware.push(new HardwareInstance({ type: 'accessory', subType: 'prateleira_pin', name: 'Suporte de Prateleira', quantity: shelves.length * 4, moduleId: module.id })); }
        }

        getResult() { return { valid: !this.constraintEngine.hasErrors(), errors: this.constraintEngine.errors, warnings: this.constraintEngine.warnings, parts: this.parts, hardware: this.hardware, modules: this.modules, calculationTrace: this.calculationTrace }; }

        getCutList() { return this.parts.map(part => ({ name: part.name, type: part.type, width: UnitSystem.roundToMm(part.width), height: UnitSystem.roundToMm(part.height), thickness: part.thickness, material: part.material, finish: part.finish, color: part.color, grainDirection: part.grainDirection, quantity: part.quantity })); }

        getHardwareList() { return this.hardware.map(hw => ({ name: hw.name, type: hw.type, subType: hw.subType, quantity: hw.quantity })); }

        getTotalAreaM2() { return this.parts.reduce((sum, part) => sum + part.getAreaM2() * part.quantity, 0); }

        serialize() { return { version: '1.0.0', template: this.modules[0] ? this.modules[0].template : null, dimensions: this.modules[0] ? this.modules[0].dimensions : null, structure: this.modules[0] ? this.modules[0].structure : null, parts: this.parts.map(p => p.toJSON()), hardware: this.hardware.map(h => h.toJSON()) }; }
    }

    // Exportar para uso global
    window.LCFurnitureEngine = {
        UnitSystem,
        ToleranceSystem,
        MaterialCatalog,
        HardwareCatalog,
        FurnitureTemplates,
        PartDefinition,
        HardwareInstance,
        ConstraintEngine,
        FurnitureEngine,
        createFurniture: (type, dimensions, structure, material, hardware) => {
            const engine = new FurnitureEngine();
            const template = FurnitureTemplates[type];
            if (!template) return { valid: false, errors: ['Template não encontrado'] };
            const def = {
                type,
                dimensions: dimensions || template.defaultDimensions,
                structure: structure || template.defaultStructure,
                material: material || { finish: 'melamina', color: 'caramelo' },
                hardware: hardware || template.defaultHardware,
                mounted: template.mounted,
            };
            return engine.build(def);
        },
    };

})();
