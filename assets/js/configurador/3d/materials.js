/**
 * PBR Material System
 * Creates physically-based materials with texture support (color map, normal, roughness, AO, grain).
 * Falls back to procedural canvas textures when real assets aren't available.
 */
export class MaterialFactory {
    constructor(THREE, debug) {
        this.THREE = THREE;
        this.debug = debug;
        this.textureCache = new Map();
        this.materialCache = new Map();
    }

    /**
     * Create or retrieve a cached PBR material.
     * @param {Object} spec - { baseColor, roughness, metalness, finish, grainDirection }
     */
    createMaterial(spec) {
        const cacheKey = this._hashSpec(spec);
        if (this.materialCache.has(cacheKey)) {
            return this.materialCache.get(cacheKey);
        }

        const mat = this._buildMaterial(spec);
        this.materialCache.set(cacheKey, mat);
        return mat;
    }

    _hashSpec(spec) {
        return [
            spec.baseColor || '#ffffff',
            spec.roughness ?? 0.5,
            spec.metalness ?? 0.0,
            spec.finish || 'matte',
            spec.grainDirection || 'none',
            spec.hasGrain ? '1' : '0',
        ].join('|');
    }

    _buildMaterial(spec) {
        const THREE = this.THREE;
        const baseColor = spec.baseColor || '#eceae3';
        const finish = spec.finish || 'matte';

        let roughness = spec.roughness ?? 0.5;
        let metalness = spec.metalness ?? 0.0;
        let color = baseColor;
        let map = null;
        let normalMap = null;
        let roughnessMap = null;

        if (spec.hasGrain || finish === 'texturizado' || finish === 'madeira') {
            const grainDir = spec.grainDirection || 'vertical';
            map = this._createGrainTexture(baseColor, grainDir);
            normalMap = this._createNormalFromGrain(baseColor, grainDir);
            roughnessMap = this._createRoughnessMap(baseColor);
            color = '#ffffff';
            roughness = 0.85;
            metalness = 0.0;
        }

        const params = {
            color: color,
            roughness: roughness,
            metalness: metalness,
            map: map,
            normalMap: normalMap,
            normalScale: new THREE.Vector2(0.5, 0.5),
            roughnessMap: roughnessMap,
            aoMap: null,
            aoMapIntensity: 0.6,
            envMap: null,
            envMapIntensity: 0.3,
        };

        const material = new THREE.MeshStandardMaterial(params);

        if (map) {
            material.needsUpdate = true;
        }

        return material;
    }

    _createGrainTexture(hex, direction) {
        const cacheKey = `grain_${hex}_${direction}`;
        if (this.textureCache.has(cacheKey)) {
            return this.textureCache.get(cacheKey);
        }

        const canvas = document.createElement('canvas');
        canvas.width = 512;
        canvas.height = 512;
        const ctx = canvas.getContext('2d');

        const r = parseInt(hex.slice(1, 3), 16);
        const g = parseInt(hex.slice(3, 5), 16);
        const b = parseInt(hex.slice(5, 7), 16);
        ctx.fillStyle = `rgb(${r},${g},${b})`;
        ctx.fillRect(0, 0, 512, 512);

        const numGrain = 2000;
        for (let i = 0; i < numGrain; i++) {
            const grainR = r + (Math.random() * 16 - 8);
            const grainG = g + (Math.random() * 16 - 8);
            const grainB = b + (Math.random() * 16 - 8);
            ctx.strokeStyle = `rgba(${Math.max(0, Math.min(255, grainR))},${Math.max(0, Math.min(255, grainG))},${Math.max(0, Math.min(255, grainB))},0.12)`;
            ctx.lineWidth = 0.5 + Math.random() * 1.2;
            const x = Math.random() * 512;
            const y = Math.random() * 512;
            const len = 30 + Math.random() * 120;
            ctx.beginPath();
            ctx.moveTo(x, y);
            const variance = Math.random() * 4 - 2;
            if (direction === 'horizontal') {
                ctx.lineTo(x + len, y + variance);
            } else {
                ctx.lineTo(x + variance, y + len);
            }
            ctx.stroke();
        }

        const tex = new this.THREE.CanvasTexture(canvas);
        tex.wrapS = this.THREE.RepeatWrapping;
        tex.wrapT = this.THREE.RepeatWrapping;
        tex.repeat.set(2, 2);
        tex.anisotropy = Math.min(this.THREE.MAX_ANISOTROPY || 4, 4);
        tex.needsUpdate = true;

        this.textureCache.set(cacheKey, tex);
        return tex;
    }

    _createNormalFromGrain(hex, direction) {
        const cacheKey = `normal_${hex}_${direction}`;
        if (this.textureCache.has(cacheKey)) {
            return this.textureCache.get(cacheKey);
        }

        const canvas = document.createElement('canvas');
        canvas.width = 256;
        canvas.height = 256;
        const ctx = canvas.getContext('2d');
        const imgData = ctx.createImageData(256, 256);
        const data = imgData.data;

        for (let i = 0; i < data.length; i += 4) {
            const rndX = (Math.random() - 0.5) * 2;
            const rndY = (Math.random() - 0.5) * 2;
            const rndZ = Math.random();
            data[i] = 128 + rndX * 30;
            data[i + 1] = 128 + rndY * 30;
            data[i + 2] = 128 + rndZ * 20;
            data[i + 3] = 255;
        }
        ctx.putImageData(imgData, 0, 0);

        const tex = new this.THREE.CanvasTexture(canvas);
        tex.wrapS = this.THREE.RepeatWrapping;
        tex.wrapT = this.THREE.RepeatWrapping;
        tex.repeat.set(4, 4);
        tex.anisotropy = 2;
        tex.needsUpdate = true;

        this.textureCache.set(cacheKey, tex);
        return tex;
    }

    _createRoughnessMap(hex) {
        const cacheKey = `rough_${hex}`;
        if (this.textureCache.has(cacheKey)) {
            return this.textureCache.get(cacheKey);
        }

        const canvas = document.createElement('canvas');
        canvas.width = 256;
        canvas.height = 256;
        const ctx = canvas.getContext('2d');
        const imgData = ctx.createImageData(256, 256);
        const data = imgData.data;

        for (let i = 0; i < data.length; i += 4) {
            const val = 180 + Math.random() * 40;
            data[i] = val;
            data[i + 1] = val;
            data[i + 2] = val;
            data[i + 3] = 255;
        }
        ctx.putImageData(imgData, 0, 0);

        const tex = new this.THREE.CanvasTexture(canvas);
        tex.wrapS = this.THREE.RepeatWrapping;
        tex.wrapT = this.THREE.RepeatWrapping;
        tex.repeat.set(2, 2);
        tex.needsUpdate = true;

        this.textureCache.set(cacheKey, tex);
        return tex;
    }

    /**
     * Create a metal/hardware material.
     */
    createMetalMaterial(hex, roughness = 0.2, metalness = 0.85) {
        const cacheKey = `metal_${hex}_${roughness}_${metalness}`;
        if (this.materialCache.has(cacheKey)) {
            return this.materialCache.get(cacheKey);
        }

        const params = {
            color: hex,
            roughness: roughness,
            metalness: metalness,
            normalScale: new this.THREE.Vector2(0.8, 0.8),
        };

        const mat = new this.THREE.MeshStandardMaterial(params);
        this.materialCache.set(cacheKey, mat);
        return mat;
    }

    /**
     * Create a back-panel material (typically textured cardboard or painted MDF).
     */
    createBackMaterial(hex, textured = false) {
        const cacheKey = `back_${hex}_${textured}`;
        if (this.materialCache.has(cacheKey)) {
            return this.materialCache.get(cacheKey);
        }

        const params = {
            color: hex,
            roughness: textured ? 0.85 : 0.92,
            metalness: 0.0,
            side: this.THREE.FrontSide,
        };

        if (textured) {
            params.map = this._createGrainTexture(hex, 'horizontal');
            params.color = '#ffffff';
        }

        const mat = new this.THREE.MeshStandardMaterial(params);
        this.materialCache.set(cacheKey, mat);
        return mat;
    }

    disposeAll() {
        for (const [key, tex] of this.textureCache) {
            if (tex.dispose) tex.dispose();
        }
        this.textureCache.clear();

        for (const [key, mat] of this.materialCache) {
            if (mat.dispose) mat.dispose();
        }
        this.materialCache.clear();
    }
}
