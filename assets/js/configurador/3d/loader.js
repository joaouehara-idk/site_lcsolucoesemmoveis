/**
 * GLTF Loader with fallback.
 * Attempts to load a GLB/GLTF model. If loading fails or is unavailable,
 * the caller should fall back to procedural geometry (handled by FurnitureBuilder).
 */
export class ModelLoader {
    constructor(THREE, debug) {
        this.THREE = THREE;
        this.debug = debug;
        this.gltfLoader = null;
        this._initGLTFLoader();
    }

    _initGLTFLoader() {
        try {
            if (typeof THREE.GLTFLoader === 'function') {
                this.gltfLoader = new THREE.GLTFLoader();
                this.debug.step('[3D] GLTFLoader available');
                return true;
            }

            const url = 'https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/loaders/GLTFLoader.js';
            this.debug.step(`[3D] GLTFLoader not bundled, will load from CDN: ${url}`);
            return false;
        } catch (err) {
            this.debug.warn('[3D] GLTFLoader init failed: ' + err.message);
            return false;
        }
    }

    /**
     * Load a GLB model from URL.
     * Returns a Promise that resolves to the model's scene or rejects on failure.
     */
    async loadGLB(url) {
        return new Promise((resolve, reject) => {
            if (!this.gltfLoader) {
                this.debug.warn('[3D] GLTFLoader not available, cannot load GLB');
                reject(new Error('GLTFLoader not available'));
                return;
            }

            this.debug.step(`[3D] Model loading... ${url}`);

            const timeout = setTimeout(() => {
                this.debug.error('[3D] GLB load timeout', new Error(`Timeout loading ${url}`));
                reject(new Error('GLB load timeout'));
            }, 10000);

            this.gltfLoader.load(
                url,
                (gltf) => {
                    clearTimeout(timeout);
                    this.debug.step('[3D] Model loaded');
                    this.debug.step(`[3D] Meshes: ${this._countMeshes(gltf.scene)}`);
                    this.debug.step(`[3D] Materials: ${this._countMaterials(gltf.scene)}`);
                    resolve(gltf.scene);
                },
                (xhr) => {
                    if (xhr.total > 0) {
                        const percent = Math.round((xhr.loaded / xhr.total) * 100);
                        if (percent % 25 === 0) {
                            this.debug.step(`[3D] GLB loading ${percent}%`);
                        }
                    }
                },
                (error) => {
                    clearTimeout(timeout);
                    this.debug.error('[3D] GLB load failed: ' + error.message, error);

                    const msg = document.createElement('div');
                    msg.className = 'cfg-error-message';
                    msg.innerHTML = `
                        <div class="error-box">
                            <h4>Não foi possível carregar o modelo 3D.</h4>
                            <p>${error.message}</p>
                            <button onclick="location.reload()" class="retry-btn">Tentar novamente</button>
                        </div>
                    `;
                    const wrap = document.getElementById('canvasWrap');
                    if (wrap) wrap.appendChild(msg);

                    reject(error);
                }
            );
        });
    }

    _countMeshes(obj) {
        let count = 0;
        obj.traverse((child) => {
            if (child.isMesh) count++;
        });
        return count;
    }

    _countMaterials(obj) {
        const materials = new Set();
        obj.traverse((child) => {
            if (child.isMesh && child.material) {
                if (Array.isArray(child.material)) {
                    child.material.forEach((m) => materials.add(m.uuid));
                } else {
                    materials.add(child.material.uuid);
                }
            }
        });
        return materials.size;
    }

    /**
     * Load GLB from URL using a dynamic import if the loader wasn't available at init.
     * Used as a recovery path when GLTFLoader wasn't imported initially.
     */
    async loadGLBDynamic(url, THREE) {
        try {
            const module = await import('https://cdn.jsdelivr.net/npm/three@0.128.0/examples/jsm/loaders/GLTFLoader.js');
            const GLTFLoader = module.GLTFLoader || module.default?.GLTFLoader;

            if (!GLTFLoader) {
                throw new Error('GLTFLoader class not found in module');
            }

            this.gltfLoader = new GLTFLoader();
            return await this.loadGLB(url);
        } catch (err) {
            this.debug.error('[3D] Dynamic GLB load failed', err);
            throw err;
        }
    }
}
