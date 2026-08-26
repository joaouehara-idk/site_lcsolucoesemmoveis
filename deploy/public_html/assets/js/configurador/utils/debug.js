/**
 * Debug / Diagnostic Logger for the 3D Configurator
 * Provides real-time diagnostic messages displayed in the UI overlay.
 */
export class DebugLogger {
    constructor(containerId = 'cfgDebug') {
        this.steps = [];
        this.errors = [];
        this.enabled = true;
        this.containerId = containerId;
        this.container = null;
        this.initContainer();
    }

    initContainer() {
        const existing = document.getElementById(this.containerId);
        if (existing) {
            this.container = existing;
            return;
        }
        const div = document.createElement('div');
        div.id = this.containerId;
        div.className = 'cfg-debug';
        div.style.cssText = [
            'position:fixed',
            'bottom:12px',
            'right:12px',
            'z-index:9999',
            'background:rgba(26,23,20,0.92)',
            'color:#fff',
            'font-family:monospace',
            'font-size:0.72rem',
            'padding:8px 10px',
            'border-radius:6px',
            'max-width:320px',
            'max-height:240px',
            'overflow-y:auto',
            'backdrop-filter:blur(4px)',
            'box-shadow:0 4px 20px rgba(0,0,0,0.3)',
            'display:none',
        ].join(';');
        document.body.appendChild(div);
        this.container = div;
    }

    step(msg) {
        this.steps.push({ msg, ts: Date.now() });
        this.log(msg, 'info');
    }

    info(msg) { this.log(msg, 'info'); }

    warn(msg) {
        this.errors.push({ msg, ts: Date.now() });
        this.log(msg, 'warn');
    }

    error(msg, err) {
        this.errors.push({ msg: err ? err.message : msg, ts: Date.now() });
        if (err && console) console.error('[Configurator3D]', msg, err);
        else if (console) console.error('[Configurator3D]', msg);
        this.log(msg, 'error');
    }

    log(msg, level = 'info') {
        if (!this.enabled || !console) return;
        const prefix = '[3D]';
        switch (level) {
            case 'info':  console.log(prefix, msg); break;
            case 'warn':  console.warn(prefix, msg); break;
            case 'error': console.error(prefix, msg); break;
        }
        this.render();
    }

    render() {
        if (!this.container || !this.enabled) return;
        let html = '';
        const recent = this.steps.concat(this.errors).slice(-20);
        for (const s of recent) {
            const label = s.level === 'error' ? '✗' : s.level === 'warn' ? '!' : '✓';
            html += `<div style="margin:2px 0;color:${s.level === 'error' ? '#ff6b6b' : s.level === 'warn' ? '#ffd43b' : '#51cf66'};">${label} ${this.escapeHtml(s.msg)}</div>`;
        }
        this.container.innerHTML = html;
        this.show();
    }

    show() {
        if (this.container) this.container.style.display = 'block';
    }

    hide() {
        if (this.container) this.container.style.display = 'none';
    }

    escapeHtml(str) {
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;');
    }

    getSummary() {
        return {
            steps: this.steps.length,
            errors: this.errors.length,
            allSteps: this.steps.map(s => s.msg),
            allErrors: this.errors.map(e => e.msg),
        };
    }
}
