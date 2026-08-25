/**
 * API Service — communicates with the PHP backend for catalog data and project persistence.
 */
export class ApiService {
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
