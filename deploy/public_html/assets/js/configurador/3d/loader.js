/**
 * Model Loader — GLTFLoader wrapper with procedural fallback.
 * Attempts to load GLB models; if unavailable, returns null so the
 * furniture builder can generate procedural geometry instead.
 */
export class ModelLoader {
    constructor(THREE, debug) {
        this.THREE = THREE;
        this.debug = debug;
        this.cache = new Map();
        this.loader = null;
        this._initLoader();
    }

    _initLoader() {
        try {
            if (typeof GLTFLoader !== 'undefined') {
                this.loader = new GLTFLoader();
                this.debug.step('[3D] GLTFLoader initialized');
            } else {
                this.debug.warn('GLTFLoader not loaded — using procedural geometry only');
            }
        } catch (err) {
            this.debug.error('Failed to init GLTFLoader', err);
        }
    }

    /**
     * Load a GLB model by path. Returns a Promise<THREE.Group|null>.
     */
    load(modelPath, cacheKey = null) {
        const key = cacheKey || modelPath;

        if (this.cache.has(key)) {
            this.debug.info(`[3D] Model loaded from cache: ${key}`);
            return Promise.resolve(this.cache.get(key).clone());
        }

        if (!this.loader) {
            this.debug.warn(`[3D] No loader available for: ${modelPath}`);
            return Promise.resolve(null);
        }

        return new Promise((resolve) => {
            this.loader.load(
                modelPath,
                (gltf) => {
                    const model = gltf.scene || gltf.scenes[0];
                    if (model) {
                        this.cache.set(key, model);
                        this.debug.step(`[3D] Model loaded: ${modelPath}`);
                        resolve(model.clone());
                    } else {
                        this.debug.warn(`[3D] Loaded model is empty: ${modelPath}`);
                        resolve(null);
                    }
                },
                (progress) => {
                    if (progress.lengthComputable) {
                        const pct = Math.round((progress.loaded / progress.total) * 100);
                        this.debug.info(`[3D] Loading ${modelPath}: ${pct}%`);
                    }
                },
                (error) => {
                    this.debug.error(`[3D] Failed to load model: ${modelPath}`, error);
                    resolve(null);
                }
            );
        });
    }

    disposeCache() {
        this.cache.clear();
    }
}
