/**
 * Hardware Selector UI Module.
 * Handles handle type, material, finish, and LED selection.
 * Updates hardware visibility in the 3D model.
 */
export class HardwareSelector {
    constructor(debug, projectService) {
        this.debug = debug;
        this.projectService = projectService;
        this.handleEl = document.getElementById('cfgHandle') || null;
        this.ledEl = document.getElementById('cfgLed') || null;
        this._bindEvents();
    }

    _bindEvents() {
        if (this.handleEl) {
            this.handleEl.addEventListener('change', (e) => {
                const value = e.target.value;
                const project = this.projectService.getCurrent();
                project.hardware.handleType = value;
                project.hardware.handleMaterial = 'aluminio';
                project.hardware.handleFinish = 'preto';
                this.projectService.saveToStorage(project);
                this.debug.step(`[UI] Handle changed to: ${value}`);
                window.dispatchEvent(new CustomEvent('configChanged', {
                    detail: { section: 'hardware', target: 'handles' }
                }));
            });
        }

        if (this.ledEl) {
            this.ledEl.addEventListener('change', (e) => {
                const value = e.target.value;
                const project = this.projectService.getCurrent();
                project.extras.led = value !== 'none';
                project.extras.ledType = value;
                this.projectService.saveToStorage(project);
                this.debug.step(`[UI] LED changed to: ${value}`);
                window.dispatchEvent(new CustomEvent('configChanged', {
                    detail: { section: 'extras', target: 'led' }
                }));
            });
        }
    }

    populate(hardwareCatalog) {
        if (this.handleEl) {
            const currentValue = this.handleEl.value;
            this.handleEl.innerHTML = '';

            const handles = hardwareCatalog?.handles || [
                { id: 'alca', available_at_lc: true, name: 'Alça' },
                { id: 'botao', available_at_lc: true, name: 'Botão' },
                { id: 'cava', available_at_lc: true, name: 'Cava (recesso)' },
                { id: 'perfil', available_at_lc: false, name: 'Perfil' },
                { id: 'concha', available_at_lc: false, name: 'Concha' },
                { id: 'embutido', available_at_lc: false, name: 'Embutido' },
                { id: 'nenhum', available_at_lc: true, name: 'Nenhum' },
            ];

            handles.filter(h => h.available_at_lc).forEach((h) => {
                const opt = document.createElement('option');
                opt.value = h.id;
                opt.textContent = h.name;
                this.handleEl.appendChild(opt);
            });

            if (currentValue) this.handleEl.value = currentValue;
        }

        if (this.ledEl) {
            const ledOptions = [
                { id: 'none', name: 'Sem LED' },
                { id: 'fita_interno', name: 'Fita LED interna' },
                { id: 'perfil_inferior', name: 'Perfil inferior', avail: false },
                { id: 'nicho', name: 'Iluminação de nicho', avail: false },
                { id: 'sensor', name: 'Com sensor de presença', avail: false },
            ];

            this.ledEl.innerHTML = '';
            ledOptions.forEach((opt) => {
                if (opt.avail === false) return;
                const el = document.createElement('option');
                el.value = opt.id;
                el.textContent = opt.name;
                this.ledEl.appendChild(el);
            });
        }
    }

    updateFromProject(project) {
        if (this.handleEl) {
            this.handleEl.value = project.hardware.handleType || 'alca';
        }
        if (this.ledEl) {
            this.ledEl.value = project.extras.ledType || 'none';
        }
    }
}
