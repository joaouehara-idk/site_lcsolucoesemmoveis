/**
 * API Service — Communicates with the ConfiguratorController backend.
 * Handles catalog fetch, project save/load, and error handling.
 */
export class ApiService {
    constructor(debug) {
        this.debug = debug;
        this.baseUrl = this._detectBaseUrl();
    }

    _detectBaseUrl() {
        const path = window.location.pathname;
        const base = path.substring(0, path.lastIndexOf('/'));
        return base || '';
    }

    async fetchCatalog() {
        try {
            this.debug.step('[API] Fetching material catalog...');
            const resp = await fetch(`${this.baseUrl}/api/configurador/catalog`, {
                method: 'GET',
                headers: { 'Accept': 'application/json' },
                credentials: 'same-origin',
            });

            if (!resp.ok) {
                throw new Error(`HTTP ${resp.status}: ${resp.statusText}`);
            }

            const data = await resp.json();
            this.debug.step('[API] Catalog loaded');
            return data;
        } catch (err) {
            this.debug.warn('[API] Catalog fetch failed, using local defaults: ' + err.message);
            return null;
        }
    }

    async fetchHardwareCatalog() {
        try {
            this.debug.step('[API] Fetching hardware catalog...');
            const resp = await fetch(`${this.baseUrl}/api/configurador/hardware`, {
                method: 'GET',
                headers: { 'Accept': 'application/json' },
                credentials: 'same-origin',
            });

            if (!resp.ok) {
                throw new Error(`HTTP ${resp.status}: ${resp.statusText}`);
            }

            const data = await resp.json();
            this.debug.step('[API] Hardware catalog loaded');
            return data;
        } catch (err) {
            this.debug.warn('[API] Hardware fetch failed: ' + err.message);
            return null;
        }
    }

    async saveProject(projectData) {
        try {
            this.debug.step('[API] Saving project...');
            const resp = await fetch(`${this.baseUrl}/api/configurador/save`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
                credentials: 'same-origin',
                body: JSON.stringify(projectData),
            });

            if (!resp.ok) {
                throw new Error(`HTTP ${resp.status}: ${resp.statusText}`);
            }

            const data = await resp.json();
            this.debug.step(`[API] Project saved: ID=${data.projectId || data.id}`);
            return data;
        } catch (err) {
            this.debug.error('[API] Project save failed: ' + err.message, err);
            throw err;
        }
    }

    async loadProject(id) {
        try {
            this.debug.step(`[API] Loading project ${id}...`);
            const resp = await fetch(`${this.baseUrl}/api/configurador/load/${id}`, {
                method: 'GET',
                headers: { 'Accept': 'application/json' },
                credentials: 'same-origin',
            });

            if (!resp.ok) {
                throw new Error(`HTTP ${resp.status}: ${resp.statusText}`);
            }

            const data = await resp.json();
            this.debug.step('[API] Project loaded');
            return data;
        } catch (err) {
            this.debug.error('[API] Project load failed: ' + err.message, err);
            throw err;
        }
    }

    async deleteProject(id) {
        try {
            this.debug.step(`[API] Deleting project ${id}...`);
            const resp = await fetch(`${this.baseUrl}/api/configurador/delete/${id}`, {
                method: 'DELETE',
                credentials: 'same-origin',
            });

            if (!resp.ok) {
                throw new Error(`HTTP ${resp.status}: ${resp.statusText}`);
            }

            const data = await resp.json();
            this.debug.step('[API] Project deleted');
            return data;
        } catch (err) {
            this.debug.error('[API] Project delete failed: ' + err.message, err);
            throw err;
        }
    }
}
