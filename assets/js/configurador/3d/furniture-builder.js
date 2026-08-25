/**
 * Furniture Builder — Procedural parametric furniture generation in Three.js.
 * Creates real geometry (not scale tricks) for carcass, doors, drawers,
 * shelves, dividers, handles, and LED strips based on centimeter dimensions.
 *
 * Unit convention: 1 Three.js unit = 1 meter. Input dimensions are in cm.
 */
export class FurnitureBuilder {
    constructor(THREE, sceneManager, materialFactory, debug) {
        this.THREE = THREE;
        this.sceneManager = sceneManager;
        this.mats = materialFactory;
        this.debug = debug;

        this.furnitureGroup = sceneManager.furnitureGroup;
        this.components = new Map();
        this.materialRefs = new Map();
    }

    /**
     * Build a complete wardrobe from configuration object.
     * @param {Object} config - { typeKey, W, H, D, doors, shelves, drawers, mounted, doorStyle, doorColor, finish, handleType, ledEnabled, interiorOpen, dividerCount }
     */
    build(config) {
        const errors = [];
        if (!config) errors.push('Config is null');
        if (!config.W || config.W < 40) errors.push('Invalid width');
        if (!config.H || config.H < 30) errors.push('Invalid height');
        if (!config.D || config.D < 20) errors.push('Invalid depth');
        if (config.doors < 0) errors.push('Invalid door count');

        if (errors.length) {
            this.debug.error('FurnitureBuilder.build validation failed: ' + errors.join(', '));
            return this._renderError();
        }

        try {
            this.debug.step('[Build] Clearing previous furniture...');
            this.clear();

            const s = 0.01;
            const w = config.W * s;
            const h = config.H * s;
            const d = config.D * s;
            const t = 0.018;
            const bt = config.backThickness ? config.backThickness * s : 0.008;
            const y0 = config.mounted ? 1.4 : 0;

            this._buildMaterials(config);
            this.debug.step(`[Build] Materials ready (color=${config.doorColor}, finish=${config.finish}, handle=${config.handleType})`);

            this.debug.step('[Build] Building carcass...');
            this._buildCarcass(w, h, d, t, bt, y0, config);

            this.debug.step('[Build] Building doors...');
            this._buildDoors(w, h, d, t, y0, config);

            this.debug.step('[Build] Building drawers...');
            this._buildDrawers(w, h, d, t, y0, config);

            this.debug.step('[Build] Building shelves...');
            this._buildShelves(w, h, d, t, y0, config);

            this.debug.step('[Build] Building dividers...');
            this._buildDividers(w, h, d, t, y0, config);

            this._buildHandles(w, h, d, t, y0, config);

            if (config.ledEnabled) {
                this.debug.step('[Build] Building LED strips...');
                this._buildLED(w, h, d, t, y0, config);
            }

            if (config.mounted) {
                this._buildMountingRail(w, d, y0, t);
            }

            this._fitCamera(w, h, d, t, y0);

            const meshCount = this.furnitureGroup.children.length;
            const totalMeshes = this._countAllMeshes();
            this.debug.step(`[Build] Complete. Group children: ${meshCount}, total meshes: ${totalMeshes}`);
            this.debug.step('[3D] Model loaded');

        } catch (err) {
            this.debug.error('FurnitureBuilder.build failed', err);
            this._renderError();
        }
    }

    _buildMaterials(config) {
        const isWood = config.finish === 'texturizado' || config.finish === 'madeira' || config.hasGrain;
        const isLacquer = config.finish === 'laca';
        const isMatte = config.finish === 'liso';
        const isMelamine = config.finish === 'melamina';

        const doorMat = this.mats.createMaterial({
            baseColor: config.doorColor,
            roughness: isLacquer ? 0.12 : (isMatte ? 0.55 : (isMelamine ? 0.75 : 0.85)),
            metalness: isLacquer ? 0.12 : 0.0,
            finish: config.finish,
            grainDirection: 'vertical',
            hasGrain: isWood,
        });
        this.materialRefs.set('door', doorMat);

        const sideMat = this.mats.createMaterial({
            baseColor: config.doorColor,
            roughness: isLacquer ? 0.2 : (isMatte ? 0.55 : 0.75),
            metalness: isLacquer ? 0.08 : 0.0,
            finish: config.finish,
            grainDirection: 'vertical',
            hasGrain: isWood && config.sideGrainDirection === 'vertical',
        });
        this.materialRefs.set('side', sideMat);

        const topMat = this.mats.createMaterial({
            baseColor: config.doorColor,
            roughness: isLacquer ? 0.2 : (isMatte ? 0.55 : 0.75),
            metalness: isLacquer ? 0.08 : 0.0,
            finish: config.finish,
            grainDirection: 'horizontal',
            hasGrain: isWood && config.topGrainDirection === 'horizontal',
        });
        this.materialRefs.set('top', topMat);

        const backMat = this.mats.createBackMaterial(config.backColor || '#D9D2C7', isWood);
        this.materialRefs.set('back', backMat);

        const drawerMat = this.mats.createMaterial({
            baseColor: config.doorColor,
            roughness: isLacquer ? 0.15 : (isMatte ? 0.55 : 0.75),
            metalness: isLacquer ? 0.10 : 0.0,
            finish: config.finish,
            grainDirection: 'vertical',
            hasGrain: isWood,
        });
        this.materialRefs.set('drawer', drawerMat);

        this.materialRefs.set('shelf', sideMat);
        this.materialRefs.set('divider', sideMat);

        this.materialRefs.set('handle', this.mats.createMetalMaterial(config.handleColor || '#6B5340'));
        this.materialRefs.set('hardware', this.mats.createMetalMaterial(config.hardwareColor || '#BBBBBB'));
        this.materialRefs.set('mountRail', this.mats.createMetalMaterial(config.hardwareColor || '#BBBBBB', 0.25, 0.8));
    }

    _buildCarcass(w, h, d, t, bt, y0, config) {
        const hw = w / 2;
        const hh = h / 2;
        const hd = d / 2;

        const mat = this.materialRefs.get('side');

        this._addBox(-hw + t / 2, hh + y0, 0, t, h, d, mat, 'left_side');
        this._addBox(hw - t / 2, hh + y0, 0, t, h, d, mat, 'right_side');

        const topMat = this.materialRefs.get('top');
        this._addBox(0, h - t / 2 + y0, 0, w - t * 2, t, d, topMat, 'top_panel');

        this._addBox(0, t / 2 + y0, 0, w - t * 2, t, d, topMat, 'bottom_panel');

        const backDepth = bt;
        const backMat = this.materialRefs.get('back');
        this._addBox(0, hh + y0, -hd + backDepth / 2, w - t * 2, h - t * 2, backDepth, backMat, 'back_panel');
    }

    _buildDoors(w, h, d, t, y0, config) {
        const { doors, doorStyle, doorColor, handleType, finish } = config;
        if (!doors || doors <= 0) return;

        const hw = w / 2;
        const hh = h / 2;
        const hd = d / 2;
        const doorH = h - t * 2 - 0.01;
        const gap = 0.006;
        const doorW = (w - t * 2 - gap * (doors - 1) - 0.02) / doors;

        for (let i = 0; i < doors; i++) {
            const pivot = new this.THREE.Group();
            pivot.name = `door_${i}`;
            pivot.userData = { type: 'door', index: i, open: false, maxOpen: 105, handleType };

            const slotX = -hw + t + 0.01 + i * (doorW + gap);
            const hingeSide = i < Math.ceil(doors / 2) ? 1 : -1;
            const hingeX = hingeSide === 1 ? slotX : slotX + doorW;

            pivot.position.set(hingeX, hh + y0, hd + 0.001);

            const panel = new this.THREE.Mesh(
                new this.THREE.BoxGeometry(doorW - 0.008, doorH, 0.018),
                this.materialRefs.get('door')
            );
            panel.position.set(hingeSide * doorW / 2, 0, 0);
            panel.castShadow = true;
            panel.receiveShadow = true;
            pivot.add(panel);

            this._addDoorHandle(panel, doorW, doorH, hingeSide, handleType, y0);

            this.furnitureGroup.add(pivot);
            this.components.set(`door_${i}`, pivot);
        }

        this.debug.step(`[Build] Doors: ${doors} (style=${doorStyle || 'rebatedor'}, handle=${handleType})`);
    }

    _addDoorHandle(doorPanel, doorW, doorH, dir, handleType, yOffset) {
        const handleMat = this.materialRefs.get('handle');
        const handleY = doorH * 0.25;

        if (handleType === 'alca') {
            const handle = new this.THREE.Mesh(
                new this.THREE.BoxGeometry(0.02, 0.18, 0.02),
                handleMat
            );
            handle.position.set(dir * (doorW / 2 - 0.05), -handleY, 0.018);
            handle.castShadow = true;
            doorPanel.add(handle);
        } else if (handleType === 'botao') {
            const handle = new this.THREE.Mesh(
                new this.THREE.BoxGeometry(0.03, 0.03, 0.04),
                handleMat
            );
            handle.position.set(dir * (doorW / 2 - 0.06), -handleY, 0.018);
            handle.castShadow = true;
            doorPanel.add(handle);
        } else if (handleType === 'cava') {
            const groove = new this.THREE.Mesh(
                new this.THREE.BoxGeometry(0.06, 0.012, 0.008),
                this.materialRefs.get('drawer')
            );
            groove.position.set(dir * (doorW / 2 - 0.06), -handleY, 0.018);
            groove.castShadow = true;
            doorPanel.add(groove);

            const handle = new this.THREE.Mesh(
                new this.THREE.BoxGeometry(0.024, 0.024, 0.02),
                handleMat
            );
            handle.position.set(dir * (doorW / 2 - 0.06), -handleY, 0.022);
            handle.castShadow = true;
            doorPanel.add(handle);
        }
    }

    _buildDrawers(w, h, d, t, y0, config) {
        const { drawers, handleType } = config;
        if (!drawers || drawers <= 0) return;

        const hw = w / 2;
        const hh = h / 2;
        const hd = d / 2;
        const dzH = h * 0.35;
        const drH = (dzH * 0.85) / drawers;
        const drawerW = w - t * 2 - 0.02;
        const drawerD = 0.45;

        for (let g = 0; g < drawers; g++) {
            const gy = t + 0.03 + drH / 2 + g * drH + y0;

            const group = new this.THREE.Group();
            group.name = `drawer_${g}`;
            group.userData = { type: 'drawer', index: g };

            const front = new this.THREE.Mesh(
                new this.THREE.BoxGeometry(drawerW, drH * 0.85, 0.016),
                this.materialRefs.get('drawer')
            );
            front.position.set(0, 0, hd - 0.008);
            front.castShadow = true;
            front.receiveShadow = true;
            group.add(front);

            const sideMat = this.materialRefs.get('drawer');
            const thickness = 0.01;
            group.add(new this.THREE.Mesh(
                new this.THREE.BoxGeometry(drawerW, drH * 0.85, thickness),
                sideMat
            ));

            const leftSide = new this.THREE.Mesh(
                new this.THREE.BoxGeometry(thickness, drH * 0.85, drawerD),
                sideMat
            );
            leftSide.position.set(-drawerW / 2 + thickness / 2, 0, -hd + drawerD / 2 + 0.016);
            group.add(leftSide);

            const rightSide = leftSide.clone();
            rightSide.position.x = drawerW / 2 - thickness / 2;
            group.add(rightSide);

            const bottom = new this.THREE.Mesh(
                new this.THREE.BoxGeometry(drawerW - thickness * 2, thickness, drawerD),
                sideMat
            );
            bottom.position.y = -drH * 0.85 / 2;
            group.add(bottom);

            this._addDrawerHandle(front, drawerW, drH * 0.85, handleType);

            group.position.set(0, gy, 0);
            this.furnitureGroup.add(group);
            this.components.set(`drawer_${g}`, group);
        }

        this.debug.step(`[Build] Drawers: ${drawers}`);
    }

    _addDrawerHandle(frontPanel, drawerW, drawerH, handleType) {
        const handleMat = this.materialRefs.get('handle');
        const handleY = 0;
        const offset = 0.04;

        if (handleType === 'alca') {
            const handle = new this.THREE.Mesh(
                new this.THREE.BoxGeometry(0.02, 0.06, 0.02),
                handleMat
            );
            handle.position.set(0, handleY, offset);
            handle.castShadow = true;
            frontPanel.add(handle);
        } else if (handleType === 'botao') {
            const handle = new this.THREE.Mesh(
                new this.THREE.BoxGeometry(0.02, 0.02, 0.03),
                handleMat
            );
            handle.position.set(0, handleY, offset);
            frontPanel.add(handle);
        } else if (handleType === 'cava') {
            const groove = new this.THREE.Mesh(
                new this.THREE.BoxGeometry(0.05, 0.01, 0.006),
                this.materialRefs.get('drawer')
            );
            groove.position.set(0, handleY, offset);
            frontPanel.add(groove);

            const handle = new this.THREE.Mesh(
                new this.THREE.BoxGeometry(0.024, 0.024, 0.01),
                handleMat
            );
            handle.position.set(0, handleY, offset + 0.006);
            frontPanel.add(handle);
        }
    }

    _buildShelves(w, h, d, t, y0, config) {
        const { shelves } = config;
        if (!shelves || shelves <= 0) return;

        const hw = w / 2;
        const hd = d / 2;
        const bt = 0.008;
        const innerW = w - t * 2 - 0.01;
        const innerD = d - bt - t * 2;
        const shelfH = 0.014;

        const doorCount = config.doors || 0;
        const shelfOffset = doorCount > 0 ? t + 0.02 : 0;

        for (let j = 0; j < shelves; j++) {
            const ratio = (j + 1) / (shelves + 1);
            const sy = (h - t * 2) * ratio - (h - t * 2) / 2 + y0;

            const shelf = new this.THREE.Mesh(
                new this.THREE.BoxGeometry(innerW, shelfH, innerD),
                this.materialRefs.get('shelf')
            );
            shelf.position.set(0, sy, -hd + t + innerD / 2);
            shelf.castShadow = true;
            shelf.receiveShadow = true;
            this.furnitureGroup.add(shelf);
            this.components.set(`shelf_${j}`, shelf);
        }

        this.debug.step(`[Build] Shelves: ${shelves}`);
    }

    _buildDividers(w, h, d, t, y0, config) {
        const { dividerCount } = config;
        if (!dividerCount || dividerCount <= 0) return;

        const hw = w / 2;
        const hh = h / 2;
        const hd = d / 2;
        const bt = 0.008;
        const dividerW = 0.01;
        const innerH = h - t * 2;
        const slot = (w - t * 2) / (dividerCount + 1);

        const dividerMat = this.materialRefs.get('divider') || this.materialRefs.get('side');

        for (let k = 0; k < dividerCount; k++) {
            const dx = -hw + t + slot * (k + 1);
            const divider = new this.THREE.Mesh(
                new this.THREE.BoxGeometry(dividerW, innerH, d - bt),
                dividerMat
            );
            divider.position.set(dx, hh + y0, 0);
            divider.castShadow = true;
            divider.receiveShadow = true;
            this.furnitureGroup.add(divider);
            this.components.set(`divider_${k}`, divider);
        }

        this.debug.step(`[Build] Dividers: ${dividerCount}`);
    }

    _buildLED(w, h, d, t, y0, config) {
        const THREE = this.THREE;
        const hw = w / 2;
        const hd = d / 2;
        const ledGroup = new THREE.Group();
        ledGroup.name = 'led_strip';
        ledGroup.userData = { type: 'led' };

        const ledColor = new THREE.Color(0x44aaff);
        const ledMaterial = new THREE.MeshBasicMaterial({
            color: ledColor,
            transparent: true,
            opacity: 0.0,
        });

        const ledTubeMat = new THREE.MeshBasicMaterial({
            color: ledColor,
            transparent: true,
            opacity: 0.6,
        });

        const tubeGeo = new THREE.CylinderGeometry(0.002, 0.002, w - t * 2, 8);
        const tube = new THREE.Mesh(tubeGeo, ledTubeMat);
        tube.rotation.z = Math.PI / 2;
        tube.position.set(0, t * 2 + y0, -hd + t * 2 + 0.01);
        ledGroup.add(tube);

        const pointLight = new THREE.PointLight(ledColor, 0.6, 3, 2);
        pointLight.position.set(0, t * 2 + y0 + 0.05, -hd + t * 2 + 0.02);
        ledGroup.add(pointLight);

        const spotLight = new THREE.SpotLight(ledColor, 0.8, 4, Math.PI / 4, 0.4, 0.8);
        spotLight.position.set(0, t * 2 + y0 + 0.02, hd - t * 2 - 0.01);
        spotLight.target.position.set(0, t + y0, hd - t * 2 - 0.02);
        ledGroup.add(spotLight);
        ledGroup.add(spotLight.target);

        this.furnitureGroup.add(ledGroup);
        this.components.set('led', ledGroup);

        const ambient = this.sceneManager.lights.ambient;
        const originalIntensity = ambient.intensity;
        const dirLight = this.sceneManager.lights.main;
        const originalDirIntensity = dirLight.intensity;

        if (!ledGroup.userData._animateFn) {
            const animate = () => {
                if (this.components.has('led')) {
                    const time = Date.now() * 0.001;
                    const pulse = 0.5 + 0.5 * Math.sin(time * 2);
                    pointLight.intensity = 0.6 + pulse * 0.3;
                    tube.material.opacity = 0.4 + pulse * 0.2;
                }
            };
            ledGroup.userData._animateFn = animate;
        }

        this._originalAmbient = originalIntensity;
        this._originalDir = originalDirIntensity;
    }

    _buildMountingRail(w, d, y0, t) {
        const railMat = this.materialRefs.get('mountRail');
        const railW = w - 0.04;
        const railH = 0.015;
        const railD = 0.02;

        const rail = new this.THREE.Mesh(
            new this.THREE.BoxGeometry(railW, railH, railD),
            railMat
        );
        rail.position.set(0, y0 - railH / 2 - 0.005, 0);
        rail.castShadow = true;
        rail.receiveShadow = true;
        this.furnitureGroup.add(rail);
        this.components.set('mount_rail', rail);
    }

    _addBox(x, y, z, w, h, d, material, name) {
        const mesh = new this.THREE.Mesh(
            new this.THREE.BoxGeometry(w, h, d),
            material
        );
        mesh.position.set(x, y, z);
        mesh.castShadow = true;
        mesh.receiveShadow = true;
        if (name) {
            mesh.name = name;
            this.components.set(name, mesh);
        }
        this.furnitureGroup.add(mesh);
        return mesh;
    }

    clear() {
        const group = this.furnitureGroup;
        while (group.children.length > 0) {
            const child = group.children[0];
            this._disposeObject(child);
            group.remove(child);
        }
        this.components.clear();
    }

    _disposeObject(obj) {
        if (obj.geometry) obj.geometry.dispose();
        if (obj.material) {
            if (Array.isArray(obj.material)) {
                obj.material.forEach((m) => m.dispose());
            } else {
                obj.material.dispose();
            }
        }
        if (obj.children) {
            obj.children.forEach((child) => this._disposeObject(child));
        }
    }

    _fitCamera(w, h, d, t, y0) {
        const target = new this.THREE.Vector3(0, h / 2 + y0, 0);
        const camera = this.sceneManager.camera;
        const controls = this.sceneManager.controls;

        if (camera && controls) {
            const maxDim = Math.max(w, h, d);
            const fov = camera.fov * (Math.PI / 180);
            const distance = (maxDim / (2 * Math.tan(fov / 2))) * 1.8;
            const camX = distance * 0.7;
            const camY = distance * 0.6;
            const camZ = distance * 0.7;

            this._animateCamera(camera, controls, target, camX, camY, camZ);
        }
    }

    _animateCamera(camera, controls, target, x, y, z) {
        const start = { x: camera.position.x, y: camera.position.y, z: camera.position.z };
        const startX = start.x, startY = start.y, startZ = start.z;
        const duration = 600;
        const startTime = performance.now();

        const step = (now) => {
            const elapsed = now - startTime;
            const t = Math.min(elapsed / duration, 1);
            const easeT = 1 - Math.pow(1 - t, 3);

            camera.position.set(
                startX + (x - startX) * easeT,
                startY + (y - startY) * easeT,
                startZ + (z - startZ) * easeT
            );
            controls.target.lerp(target, easeT);
            controls.update();

            if (t < 1) {
                requestAnimationFrame(step);
            }
        };

        requestAnimationFrame(step);
    }

    _renderError() {
        const THREE = this.THREE;
        const errorMat = new THREE.MeshStandardMaterial({
            color: 0xff6b6b,
            roughness: 0.4,
            metalness: 0.0,
        });

        const geo = new THREE.BoxGeometry(0.5, 0.5, 0.5);
        const mesh = new THREE.Mesh(geo, errorMat);
        mesh.name = 'error_cube';
        mesh.castShadow = true;
        mesh.receiveShadow = true;
        this.furnitureGroup.add(mesh);
        this.components.set('error', mesh);

        const textMat = new THREE.MeshBasicMaterial({
            color: 0xffffff,
            transparent: true,
            opacity: 0.9,
            side: THREE.DoubleSide,
        });

        this.debug.step('[3D] Model loading... fallback cube rendered');
    }

    _countAllMeshes() {
        let count = 0;
        this.furnitureGroup.traverse((child) => {
            if (child.isMesh) count++;
        });
        return count;
    }

    /**
     * Open/close all doors.
     */
    setOpenDoors(open) {
        this.furnitureGroup.traverse((child) => {
            if (child.userData && child.userData.type === 'door' && child.userData.maxOpen) {
                if (open) {
                    const idx = child.userData.index;
                    const dir = idx % 2 === 0 ? 1 : -1;
                    child.rotation.y = -dir * this.THREE.MathUtils.degToRad(child.userData.maxOpen);
                } else {
                    child.rotation.y = 0;
                }
            }
        });
    }

    getLightCount() {
        let count = 0;
        this.furnitureGroup.traverse((child) => {
            if (child.isLight) count++;
        });
        return count;
    }

    getMaterialCount() {
        const materials = new Set();
        this.furnitureGroup.traverse((child) => {
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
}
