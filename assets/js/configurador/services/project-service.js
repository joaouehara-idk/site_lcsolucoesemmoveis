/**
 * Project Service — Save/load project configurations.
 * Uses localStorage as primary store, with optional API backup.
 */
export class ProjectService {
    constructor(apiService, debug) {
        this.api = apiService;
        this.debug = debug;
        this.storageKey = 'lc_configurator_project';
        this.current = null;
    }

    createDefault() {
        return {
            furnitureType: 'guarda_roupa',
            dimensions: { W: 180, H: 220, D: 55 },
            structure: {
                doors: 2,
                shelves: 3,
                drawers: 0,
                dividers: 0,
                doorStyle: 'rebatedor',
                mounted: false,
            },
            finish: {
                color: 'caramelo',
                customColor: null,
                materialFinish: 'melamina',
                backColor: '#D9D2C7',
            },
            hardware: {
                handleType: 'alca',
                handleMaterial: 'aluminio',
                handleFinish: 'preto',
                handleColor: '#1A1714',
                hardwareColor: '#BBBBBB',
            },
            extras: {
                led: false,
                ledType: 'none',
            },
            view: {
                cameraPreset: 'reset',
                doorsOpen: false,
            },
            configurationVersion: '1.0.0',
        };
    }

    getCurrent() {
        if (!this.current) {
            this.current = this.loadFromStorage() || this.createDefault();
        }
        return this.current;
    }

    saveToStorage(project) {
        try {
            const serialized = JSON.stringify(project);
            localStorage.setItem(this.storageKey, serialized);
            this.current = project;
            this.debug.step('[Project] Saved to localStorage');
        } catch (err) {
            this.debug.error('[Project] localStorage save failed: ' + err.message, err);
        }
    }

    loadFromStorage() {
        try {
            const data = localStorage.getItem(this.storageKey);
            if (!data) return null;
            const project = JSON.parse(data);
            this.current = project;
            this.debug.step('[Project] Loaded from localStorage');
            return project;
        } catch (err) {
            this.debug.error('[Project] localStorage load failed: ' + err.message, err);
            return null;
        }
    }

    clearStorage() {
        try {
            localStorage.removeItem(this.storageKey);
            this.current = this.createDefault();
            this.debug.step('[Project] Storage cleared');
        } catch (err) {
            this.debug.error('[Project] localStorage clear failed', err);
        }
    }

    serializeToUrl(project) {
        try {
            const json = JSON.stringify(project);
            const compressed = this._compressToBase64(json);
            return compressed;
        } catch (err) {
            this.debug.error('[Project] Serialization failed', err);
            return null;
        }
    }

    deserializeFromUrl(str) {
        try {
            const json = this._decompressFromBase64(str);
            const project = JSON.parse(json);
            this.current = project;
            this.debug.step('[Project] Loaded from URL hash');
            return project;
        } catch (err) {
            this.debug.error('[Project] URL deserialization failed', err);
            return null;
        }
    }

    _compressToBase64(str) {
        return btoa(encodeURIComponent(str).replace(/%([0-9A-F]{2})/g, (m, p1) => String.fromCharCode('0x' + p1)));
    }

    _decompressFromBase64(b64) {
        const binary = atob(b64);
        const bytes = new Uint8Array(binary.length);
        for (let i = 0; i < binary.length; i++) {
            bytes[i] = binary.charCodeAt(i);
        }
        return decodeURIComponent(String.fromCharCode.apply(null, bytes));
    }

    async saveToServer(project, projectName = 'Meu Projeto') {
        if (!this.api) {
            this.debug.warn('[Project] API service not available');
            return null;
        }

        const payload = {
            name: projectName,
            configuration: project,
            configuration_version: '1.0.0',
        };

        return await this.api.saveProject(payload);
    }

    async loadFromServer(id) {
        if (!this.api) {
            this.debug.warn('[Project] API service not available');
            return null;
        }

        const data = await this.api.loadProject(id);
        if (data && data.configuration) {
            this.current = data.configuration;
            return data.configuration;
        }
        return null;
    }

    generateWhatsAppMessage(project) {
        const cfg = project;
        const dim = cfg.dimensions;
        const str = cfg.structure;
        const fin = cfg.finish;
        const hw = cfg.hardware;
        const ext = cfg.extras;
        const furnitureLabel = this._getFurnitureLabel(cfg.furnitureType);

        const lines = [
            '*Projeto LC Soluções em Móveis*',
            '',
            `*Móvel:* ${furnitureLabel}`,
            `*Medidas:* ${dim.W} cm (L) x ${dim.H} cm (A) x ${dim.D} cm (P)`,
            `*Portas:* ${str.doors || 0}`,
            `*Gavetas:* ${str.drawers || 0}`,
            `*Prateleiras:* ${str.shelves || 0}`,
            `*Divisórias:* ${str.dividers || 0}`,
            '',
            '*Acabamento:*',
            `• Cor: ${fin.customColor || fin.color || 'Padrão'}`,
            `• Acabamento: ${this._getFinishLabel(fin.materialFinish)}`,
            `• Puxador: ${this._getHandleLabel(hw.handleType)}`,
            `• LED: ${ext.led ? 'Sim (' + this._getLedLabel(ext.ledType) + ')' : 'Não'}`,
            '',
            '*Projeto salvo no configurador 3D da LC*',
        ];

        return encodeURIComponent(lines.join('\n'));
    }

    _getFurnitureLabel(type) {
        const labels = {
            guarda_roupa: 'Guarda-roupa Planejado',
            nicho: 'Nicho',
            aereo: 'Móvel Aéreo',
            estante: 'Estante',
            painel_tv: 'Painel para TV',
            cozinha: 'Armário de Cozinha',
            closet: 'Closet',
            comoda: 'Cômoda',
        };
        return labels[type] || type;
    }

    _getFinishLabel(finish) {
        const labels = {
            melamina: 'Melamina',
            liso: 'Liso (fosco)',
            texturizado: 'Texturizado (madeira)',
            laca: 'Laca (brilhante)',
            madeira: 'Madeira natural',
            acetinado: 'Acetinado',
        };
        return labels[finish] || finish;
    }

    _getHandleLabel(handle) {
        const labels = {
            alca: 'Alça',
            botao: 'Botão',
            cava: 'Cava (recesso)',
            perfil: 'Perfil',
            concha: 'Concha',
            embutido: 'Embutido',
            nenhum: 'Nenhum',
        };
        return labels[handle] || handle;
    }

    _getLedLabel(led) {
        const labels = {
            none: 'Nenhum',
            fita_interno: 'Fita LED interna',
            perfil_inferior: 'Perfil inferior',
            nicho: 'Iluminação de nicho',
            sensor: 'Com sensor de presença',
        };
        return labels[led] || led;
    }
}
