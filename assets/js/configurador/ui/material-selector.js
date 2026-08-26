/**
 * Material Selector UI Module.
 * Handles material/brand/line/filter selection and preview updates.
 */
export class MaterialSelector {
    constructor(debug, projectService) {
        this.debug = debug;
        this.projectService = projectService;
        this.element = document.getElementById('cfgMaterial') || null;
        this.previewEl = document.getElementById('matPreview') || null;
        this.detailEl = document.getElementById('matInfo') || null;
        this._bindEvents();
    }

    _bindEvents() {
        if (!this.element) return;

        this.element.addEventListener('change', (e) => {
            const value = e.target.value;
            const project = this.projectService.getCurrent();
            project.finish.color = value;
            project.finish.customColor = null;
            this.projectService.saveToStorage(project);
            this.debug.step(`[UI] Material changed to: ${value}`);
            this._updatePreview(value);
            this._updateInfo(value);
            window.dispatchEvent(new CustomEvent('configChanged', { detail: { section: 'material' } }));
        });
    }

    _updatePreview(colorKey) {
        if (this.previewEl) {
            const colors = {
                branco: '#ECEAE3', marfim: '#EAE3D5', areia: '#D8C7A8',
                amendola: '#C8A87C', caramelo: '#C8A87C', offwhite: '#E8E0D3',
                bege: '#D8C3A5', madeira: '#C9A876', carvalho: '#C9A876',
                nogueira: '#6B5340', wenge: '#3A2E26', concreto: '#B8B5AE',
                cinza: '#9A9A9A', grafite: '#4A4A4A', preto: '#2B2B2B',
            };
            this.previewEl.style.backgroundColor = colors[colorKey] || '#C8A87C';
        }
    }

    _updateInfo(colorKey) {
        if (!this.detailEl) return;
        const info = {
            branco: { name: 'Branco', finish: 'Melamina' },
            caramelo: { name: 'Caramelo', finish: 'Texturizado' },
            carvalho: { name: 'Carvalho', finish: 'Madeira natural' },
            preto: { name: 'Preto', finish: 'Laca' },
        };
        const d = info[colorKey] || { name: colorKey, finish: 'Padrão' };
        this.detailEl.innerHTML = `
            <span class="mat-name">${d.name}</span>
            <span class="mat-detail">${d.finish}</span>
        `;
    }

    populate(materialCatalog) {
        if (!this.element) return;
        this.element.innerHTML = '';

        const defaultOption = document.createElement('option');
        defaultOption.value = 'caramelo';
        defaultOption.textContent = 'Caramelo (Texturizado)';
        this.element.appendChild(defaultOption);

        if (materialCatalog && materialCatalog.patterns) {
            materialCatalog.patterns.forEach((pattern) => {
                const opt = document.createElement('option');
                opt.value = pattern.id;
                opt.textContent = `${pattern.name} (${pattern.brand_name || 'Catálogo'})`;
                if (!pattern.available_at_lc) opt.style.fontStyle = 'italic';
                this.element.appendChild(opt);
            });
        }
    }

    updateFromProject(project) {
        if (!this.element) return;
        this.element.value = project.finish.color || 'caramelo';
        this._updatePreview(project.finish.color || 'caramelo');
    }
}
