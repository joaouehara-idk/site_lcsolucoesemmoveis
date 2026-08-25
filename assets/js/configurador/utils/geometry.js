/**
 * Geometry Utilities
 * Helper functions for 3D geometry calculations.
 */
export class GeometryUtils {
    static cmToMeters(cm) {
        return cm * 0.01;
    }

    static metersToCm(m) {
        return m * 100;
    }

    static clamp(value, min, max) {
        return Math.min(Math.max(value, min), max);
    }

    static lerp(a, b, t) {
        return a + (b - a) * t;
    }

    static degToRad(deg) {
        return deg * Math.PI / 180;
    }

    static radToDeg(rad) {
        return rad * 180 / Math.PI;
    }

    static getBoundingBox(object3D) {
        const box = new THREE.Box3().setFromObject(object3D);
        const size = new THREE.Vector3();
        box.getSize(size);
        const center = new THREE.Vector3();
        box.getCenter(center);
        return { box, size, center };
    }

    static fitCameraToGroup(camera, controls, group, distanceFactor = 1.5) {
        const { size, center } = this.getBoundingBox(group);
        const maxDim = Math.max(size.x, size.y, size.z);
        const fov = camera.fov * (Math.PI / 180);
        let distance = maxDim / (2 * Math.tan(fov / 2));
        distance *= distanceFactor;

        const dir = new THREE.Vector3();
        const eye = new THREE.Vector3();
        const up = new THREE.Vector3(0, 1, 0);

        dir.subVectors(camera.position, controls.target).normalize();
        eye.copy(center).addScaledVector(dir, distance);

        camera.position.copy(eye);
        controls.target.copy(center);
        controls.update();
    }
}
