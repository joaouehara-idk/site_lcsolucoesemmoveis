/**
 * Dimension Panel UI Module.
 * Handles furniture type selection and dimension inputs (W/H/D in cm).
 * Updates the 3D model when any dimension changes.
 */
export class DimensionPanel {
    constructor(debug, projectService, furnitureCatalog) {
        this.debug = debug;
        this.projectService = projectService;
        this.furnitureCatalog = furnitureCatalog;

        this.typeEl = document.getElementById('cfgType') || null;
        this.titleEl = document.getElementById('cfgTypeTitle') || null;
        this.hintEl = document.getElementById('cfgHint') || null;

        this.widthEl = document.getElementById('cfgWidth') || null;
        this.heightEl = document.getElementById('cfgHeight') || null;
        this.depthEl = document.getElementById('cfgDepth') || null;

        this.doorsEl = document.getElementById('valDoors') || null;
        this.shelvesEl = document.getElementById('valShelves') || null;
        this.drawersEl = document.getElementById('valDrawers') || null;
        this.dividersEl = document.getElementById('valDividers') || null;

        this.stepDoors = document.getElementById('stepDoors') || null;
        this.stepShelves = document.getElementById('stepShelves') || null;
        this.stepDrawers = document.getElementById('stepDrawers') || null;
        this.stepInterior = document.getElementById('stepInterior') || null;
        this.stepHardware = document.getElementById('stepHardware') || null;
        this.stepLed = document.getElementById('stepLed') || null;
        this.stepDividers = document.getElementById('stepDividers') || null;

        this.openCheckbox = document.getElementById('cfgOpen') || null;

        this._bindEvents();
    }

    _getFurnitureType(key) {
        return this.furnitureCatalog[key] || null;
    }

    _bindEvents() {
        if (this.typeEl) {
            this.typeEl.addEventListener('change', () => {
                const value = this.typeEl.value;
                const project = this.projectService.getCurrent();
                project.furnitureType = value;

                const furnitureType = this._getFurnitureType(value);
                if (furnitureType) {
                    project.dimensions = { ...furnitureType.dimensions };
                    project.structure.mounted = furnitureType.mounted;
                    project.structure.doors = furnitureType.defaults.doors;
                    project.structure.shelves = furnitureType.defaults.shelves;
                    project.structure.drawers = furnitureType.defaults.drawers;
                    project.structure.dividers = furnitureType.defaults.dividerCount || 0;
                }

                this.projectService.saveToStorage(project);
                this._updateTypeUI(value);
                this._updateAllDisplays();
                window.dispatchEvent(new CustomEvent('configChanged', {
                    detail: { section: 'type', type: value }
                }));
            });
        }

        const dimHandler = () => {
            const project = this.projectService.getCurrent();
            if (this.widthEl) project.dimensions.W = parseInt(this.widthEl.value) || project.dimensions.W;
            if (this.heightEl) project.dimensions.H = parseInt(this.heightEl.value) || project.dimensions.H;
            if (this.depthEl) project.dimensions.D = parseInt(this.depthEl.value) || project.dimensions.D;
            this.projectService.saveToStorage(project);
            window.dispatchEvent(new CustomEvent('configChanged', {
                detail: { section: 'dimensions' }
            }));
        };

        [this.widthEl, this.heightEl, this.depthEl].forEach((el) => {
            if (el) {
                el.addEventListener('input', dimHandler);
                el.addEventListener('change', dimHandler);
            }
        });

        const counterHandler = (lessEl, moreEl, valEl, field) => {
            if (!lessEl || !moreEl || !valEl) return;
            lessEl.addEventListener('click', () => {
                const project = this.projectService.getCurrent();
                if (project.structure[field] > 0) {
                    project.structure[field]--;
                    this.projectService.saveToStorage(project);
                    valEl.textContent = project.structure[field];
                    window.dispatchEvent(new CustomEvent('configChanged', {
                        detail: { section: 'structure', field }
                    }));
                }
            });
            moreEl.addEventListener('click', () => {
                const project = this.projectService.getCurrent();
                project.structure[field]++;
                this.projectService.saveToStorage(project);
                valEl.textContent = project.structure[field];
                window.dispatchEvent(new CustomEvent('configChanged', {
                    detail: { section: 'structure', field }
                }));
            });
        };

        counterHandler(
            document.getElementById('btnDoorsLess'),
            document.getElementById('btnDoorsMore'),
            this.doorsEl, 'doors'
        );
        counterHandler(
            document.getElementById('btnShelvesLess'),
            document.getElementById('btnShelvesMore'),
            this.shelvesEl, 'shelves'
        );
        counterHandler(
            document.getElementById('btnDrawersLess'),
            document.getElementById('btnDrawersMore'),
            this.drawersEl, 'drawers'
        );
        counterHandler(
            document.getElementById('btnDividersLess'),
            document.getElementById('btnDividersMore'),
            this.dividersEl, 'dividers'
        );

        if (this.openCheckbox) {
            this.openCheckbox.addEventListener('change', () => {
                const project = this.projectService.getCurrent();
                project.view.doorsOpen = this.openCheckbox.checked;
                this.projectService.saveToStorage(project);
                window.dispatchEvent(new CustomEvent('configChanged', {
                    detail: { section: 'view' }
                }));
            });
        }
    }

    _updateTypeUI(typeKey) {
        const furniture = this._getFurnitureType(typeKey);
        if (!furniture) {
            if (this.titleEl) this.titleEl.textContent = 'Configurador';
            if (this.hintEl) this.hintEl.textContent = '';
            return;
        }

        if (this.titleEl) this.titleEl.textContent = furniture.label;
        if (this.hintEl) this.hintEl.textContent = furniture.description || '';

        if (this.widthEl) this.widthEl.value = furniture.dimensions.W;
        if (this.heightEl) this.heightEl.value = furniture.dimensions.H;
        if (this.depthEl) this.depthEl.value = furniture.dimensions.D;

        const hasDoors = furniture.limits.maxDoors > 0;
        const hasShelves = furniture.limits.maxShelves > 0;
        const hasDrawers = furniture.limits.maxDrawers > 0;
        const hasDividers = furniture.limits.maxDividers > 0;
        const hasHardware = hasDoors;
        const hasLed = hasDoors && furniture.category !== 'decorativo';

        if (this.stepDoors) this.stepDoors.style.display = hasDoors ? '' : 'none';
        if (this.stepShelves) this.stepShelves.style.display = hasShelves ? '' : 'none';
        if (this.stepDrawers) this.stepDrawers.style.display = hasDrawers ? '' : 'none';
        if (this.stepDividers) this.stepDividers.style.display = hasDividers ? '' : 'none';
        if (this.stepInterior) this.stepInterior.style.display = hasDoors ? '' : 'none';
        if (this.stepHardware) this.stepHardware.style.display = hasHardware ? '' : 'none';
        if (this.stepLed) this.stepLed.style.display = hasLed ? '' : 'none';

        const project = this.projectService.getCurrent();
        if (project.structure.doors > furniture.limits.maxDoors) project.structure.doors = furniture.limits.maxDoors;
        if (project.structure.shelves > furniture.limits.maxShelves) project.structure.shelves = furniture.limits.maxShelves;
        if (project.structure.drawers > furniture.limits.maxDrawers) project.structure.drawers = furniture.limits.maxDrawers;
        if (project.structure.dividers > furniture.limits.maxDividers) project.structure.dividers = furniture.limits.maxDividers;
        this.projectService.saveToStorage(project);
    }

    _updateAllDisplays() {
        const project = this.projectService.getCurrent();
        if (this.doorsEl) this.doorsEl.textContent = project.structure.doors;
        if (this.shelvesEl) this.shelvesEl.textContent = project.structure.shelves;
        if (this.drawersEl) this.drawersEl.textContent = project.structure.drawers;
        if (this.dividersEl) this.dividersEl.textContent = project.structure.dividers;
        if (this.openCheckbox) this.openCheckbox.checked = project.view.doorsOpen;
    }

    populateTypes() {
        if (!this.typeEl) return;
        this.typeEl.innerHTML = '';

        Object.entries(this.furnitureCatalog).forEach(([key, def]) => {
            const opt = document.createElement('option');
            opt.value = key;
            opt.textContent = def.label;
            this.typeEl.appendChild(opt);
        });
    }

    loadFromProject(project) {
        if (this.typeEl) this.typeEl.value = project.furnitureType || 'guarda_roupa';
        if (this.widthEl) this.widthEl.value = project.dimensions.W;
        if (this.heightEl) this.heightEl.value = project.dimensions.H;
        if (this.depthEl) this.depthEl.value = project.dimensions.D;
        this._updateTypeUI(project.furnitureType || 'guarda_roupa');
        this._updateAllDisplays();
    }
}
