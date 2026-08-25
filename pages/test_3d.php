<?php
require_once ROOT_PATH . '/includes/config.php';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Teste 3D</title>
    <style>
        body { margin: 0; background: #f0f0f0; font-family: sans-serif; padding: 20px; }
        #canvas { width: 100%; height: 400px; background: #000; display: block; }
        .status { padding: 10px; margin: 10px 0; border-radius: 4px; }
        .ok { background: #d4edda; color: #155724; }
        .error { background: #f8d7da; color: #721c24; }
    </style>
</head>
<body>
    <h1>Teste Three.js</h1>
    <div id="status"></div>
    <canvas id="canvas"></canvas>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
    <script>
        const status = document.getElementById('status');
        
        function showStatus(msg, isError) {
            status.innerHTML = '<div class="status ' + (isError ? 'error' : 'ok') + '">' + msg + '</div>';
        }

        try {
            if (typeof THREE === 'undefined') {
                throw new Error('Three.js não carregou da CDN');
            }
            showStatus('Three.js carregado: ' + THREE.REVISION, false);

            const canvas = document.getElementById('canvas');
            const scene = new THREE.Scene();
            scene.background = new THREE.Color(0xF6F1EB);

            const camera = new THREE.PerspectiveCamera(45, canvas.clientWidth / 400, 0.1, 100);
            camera.position.set(2, 2, 2);

            const renderer = new THREE.WebGLRenderer({ canvas: canvas, antialias: true });
            renderer.setSize(canvas.clientWidth, 400);
            renderer.setClearColor(0xF6F1EB);

            const light = new THREE.DirectionalLight(0xffffff, 1);
            light.position.set(5, 10, 5);
            scene.add(light);
            scene.add(new THREE.AmbientLight(0xffffff, 0.5));

            const geo = new THREE.BoxGeometry(1, 1, 1);
            const mat = new THREE.MeshPhongMaterial({ color: 0xC5A253 });
            const cube = new THREE.Mesh(geo, mat);
            scene.add(cube);

            function animate() {
                requestAnimationFrame(animate);
                cube.rotation.x += 0.01;
                cube.rotation.y += 0.01;
                renderer.render(scene, camera);
            }
            animate();
            showStatus('Visualizador 3D funcionando!', false);

        } catch (e) {
            showStatus('Erro: ' + e.message, true);
            console.error(e);
        }
    </script>
</body>
</html>
