/**
 * Project Service — handles serialization, localStorage persistence, and server sync.
 */
export class ProjectService {
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
