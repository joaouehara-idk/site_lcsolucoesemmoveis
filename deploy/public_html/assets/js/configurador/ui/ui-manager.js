/**
 * UI Manager — coordinates all UI interactions and updates.
 * Connects DOM elements with the 3D builder and catalog data.
 */
import { COLOR_PRESETS, FINISH_TYPES, MATERIAL_BRANDS } from '../catalog/materials.js';
import { HANDLE_TYPES, LED_OPTIONS } from '../catalog/hardware.js';
import { FURNITURE_CATALOG } from '../catalog/furniture.js';

export class UIManager {
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
        for (const color of COLOR_PRESETS) {
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
        for (const handle of HANDLE_TYPES) {
            if (!handle.available_at_lc) continue;
            const btn = document.createElement('button');
            btn.className = 'hw-btn';
            btn.dataset.handleId = handle.id;
            btn.innerHTML = `
                <span class="hw-name">${handle.name}</span>
                <span class="hw-desc">${handle.description || ''}</span>
            `;
            btn.addEventListener('click', () => {
                if (this.elements.cfgHandle) {
                    this.elements.cfgHandle.value = handle.id;
                    this._emitChange();
                }
            });
            grid.appendChild(btn);
        }
    }

    _adjustCounter(type, delta) {
        if (this.onConfigChange) {
            this.onConfigChange({ type: 'counter', counter: type, delta });
        }
    }
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
