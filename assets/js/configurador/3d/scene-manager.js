/**
 * Scene Manager — Three.js core: Scene, Camera, Renderer, Controls, Animation loop, resize.
 * Handles all Three.js lifecycle concerns and camera preset views.
 */
export class SceneManager {
    constructor(canvas, debug) {
        this.canvas = canvas;
        this.debug = debug;
        this.scene = null;
        this.camera = null;
        this.renderer = null;
        this.controls = null;
        this.furnitureGroup = null;
        this.lights = {};
        this.initialized = false;
        this.animateId = null;
        this.onBeforeRender = null;
        this.onResizeCallback = null;
    }

    init(OrbitControlsClass, THREE) {
        if (this.initialized) return true;

        try {
            this.THREE = THREE;
            const dim = this._getCanvasSize();

            this.debug.step('[3D] Renderer initialization starting...');

            this.renderer = new THREE.WebGLRenderer({
                canvas: this.canvas,
                antialias: true,
                preserveDrawingBuffer: true,
                alpha: false,
                power: 'high-performance',
            });
            this.renderer.setSize(dim.w, dim.h);
            this.renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
            this.renderer.shadowMap.enabled = true;
            this.renderer.shadowMap.type = THREE.PCFSoftShadowMap;
            this.renderer.toneMapping = THREE.ACESFilmicToneMapping;
            this.renderer.toneMappingExposure = 1.1;
            this.renderer.outputEncoding = THREE.sRGBEncoding;
            this.debug.step('[3D] Renderer initialized');

            this.debug.step('[3D] Scene initialization starting...');
            this.scene = new THREE.Scene();
            this.scene.background = new THREE.Color(0xF6F1EB);
            this.debug.step('[3D] Scene initialized');

            this.debug.step('[3D] Camera initialization starting...');
            this.camera = new THREE.PerspectiveCamera(35, dim.w / dim.h, 0.01, 100);
            this.camera.position.set(3.5, 2.2, 3.5);
            this.debug.step('[3D] Camera initialized');

            this.debug.step('[3D] Controls initialization starting...');
            this.controls = new OrbitControlsClass(this.camera, this.renderer.domElement);
            this.controls.enableDamping = true;
            this.controls.dampingFactor = 0.08;
            this.controls.maxPolarAngle = Math.PI * 0.48;
            this.controls.minPolarAngle = Math.PI * 0.05;
            this.controls.minDistance = 1.0;
            this.controls.maxDistance = 15;
            this.controls.target.set(0, 1.1, 0);
            this.controls.update();
            this.debug.step('[3D] Controls initialized');

            this._setupLights(THREE);
            this._setupEnvironment(THREE);

            this.furnitureGroup = new THREE.Group();
            this.scene.add(this.furnitureGroup);
            this.debug.step('[3D] Furniture group created');

            this.initialized = true;
            this.debug.step('[3D] Scene fully initialized — awaiting model');

            return true;
        } catch (err) {
            this.debug.error('Failed to initialize 3D scene', err);
            return false;
        }
    }

    _setupLights(THREE) {
        const ambient = new THREE.AmbientLight(0xffffff, 0.4);
        this.scene.add(ambient);
        this.lights.ambient = ambient;

        const hemi = new THREE.HemisphereLight(0xffffff, 0xE8E0D8, 0.5);
        this.scene.add(hemi);
        this.lights.hemisphere = hemi;

        const dirLight = new THREE.DirectionalLight(0xffffff, 0.95);
        dirLight.position.set(4, 8, 5);
        dirLight.castShadow = true;
        dirLight.shadow.mapSize.width = 1024;
        dirLight.shadow.mapSize.height = 1024;
        dirLight.shadow.camera.near = 0.5;
        dirLight.shadow.camera.far = 25;
        dirLight.shadow.camera.left = -6;
        dirLight.shadow.camera.right = 6;
        dirLight.shadow.camera.top = 6;
        dirLight.shadow.camera.bottom = -6;
        dirLight.shadow.bias = -0.001;
        this.scene.add(dirLight);
        this.lights.main = dirLight;

        const fillLight = new THREE.DirectionalLight(0xE8E0D8, 0.35);
        fillLight.position.set(-4, 4, -3);
        this.scene.add(fillLight);
        this.lights.fill = fillLight;

        this.debug.step('[3D] Lighting setup complete (3 lights + hemisphere)');
    }

    _setupEnvironment(THREE) {
        const floorMat = new THREE.MeshStandardMaterial({
            color: 0xEDE7DF,
            roughness: 0.92,
            metalness: 0.0,
        });

        const floorGeo = new THREE.PlaneGeometry(40, 40);
        const floor = new THREE.Mesh(floorGeo, floorMat);
        floor.rotation.x = -Math.PI / 2;
        floor.position.y = -0.01;
        floor.receiveShadow = true;
        this.scene.add(floor);
        this.lights.floor = floor;

        const wallMat = new THREE.MeshStandardMaterial({
            color: 0xFAF7F4,
            roughness: 0.95,
            metalness: 0.0,
        });

        const wallGeo = new THREE.PlaneGeometry(16, 6);
        const wall = new THREE.Mesh(wallGeo, wallMat);
        wall.position.set(0, 3, -4);
        wall.receiveShadow = true;
        this.scene.add(wall);

        const sideWall = new THREE.Mesh(wallGeo, wallMat);
        sideWall.rotation.y = Math.PI / 2;
        sideWall.position.set(-4, 3, 0);
        sideWall.receiveShadow = true;
        this.scene.add(sideWall);

        this.debug.step('[3D] Environment setup complete (floor + walls)');
    }

    _getCanvasSize() {
        const wrap = document.getElementById('canvasWrap') || this.canvas.parentElement;
        if (!wrap) return { w: 600, h: 500 };
        const w = Math.max(wrap.clientWidth || 600, 300);
        const h = Math.max(wrap.clientHeight || 500, 300);
        return { w, h };
    }

    render() {
        if (!this.initialized) return;
        this.renderer.render(this.scene, this.camera);
    }

    animate(onPreRender) {
        const loop = () => {
            if (onPreRender) onPreRender();
            if (this.controls) this.controls.update();
            if (this.renderer && this.scene && this.camera) {
                this.renderer.render(this.scene, this.camera);
            }
            this.animateId = requestAnimationFrame(loop);
        };
        loop();
    }

    stopAnimate() {
        if (this.animateId) {
            cancelAnimationFrame(this.animateId);
            this.animateId = null;
        }
    }

    onResize() {
        if (!this.initialized) return;
        const dim = this._getCanvasSize();
        this.renderer.setSize(dim.w, dim.h);
        this.camera.aspect = dim.w / dim.h;
        this.camera.updateProjectionMatrix();
    }

    setCameraPreset(preset) {
        if (!this.camera || !this.controls) return;

        const target = new THREE.Vector3();
        const bbox = new THREE.Box3().setFromObject(this.furnitureGroup);
        bbox.getCenter(target);
        if (bbox.isEmpty()) target.set(0, 1, 0);

        const f = () => {
            this.controls.target.copy(target);
            this.controls.update();
        };

        switch (preset) {
            case 'front':
                this.camera.position.set(target.x, target.y, target.z + 5);
                f();
                break;
            case 'side':
                this.camera.position.set(target.x + 5, target.y, target.z);
                f();
                break;
            case 'top':
                this.camera.position.set(target.x, target.y + 5, target.z + 0.01);
                f();
                break;
            case 'reset':
            default:
                this.camera.position.set(3.5, 2.2, 3.5);
                f();
                break;
        }
    }

    dispose() {
        this.stopAnimate();
        if (this.renderer) {
            this.renderer.dispose();
            this.renderer = null;
        }
        if (this.scene) {
            this.scene.traverse((obj) => {
                if (obj.geometry) obj.geometry.dispose();
                if (obj.material) {
                    if (Array.isArray(obj.material)) {
                        obj.material.forEach((m) => m.dispose());
                    } else {
                        obj.material.dispose();
                    }
                }
            });
            this.scene = null;
        }
        this.initialized = false;
    }
}
