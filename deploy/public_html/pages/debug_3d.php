<?php
require_once ROOT_PATH . '/includes/config.php';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Debug 3D</title>
    <style>
        body { margin: 20px; font-family: monospace; background: #f5f5f5; }
        .step { padding: 10px; margin: 10px 0; border-left: 4px solid #ccc; background: white; }
        .pass { border-color: #28a745; }
        .fail { border-color: #dc3545; }
        #canvas { width: 100%; height: 300px; background: #000; display: block; margin-top: 10px; }
    </style>
</head>
<body>
    <h1>Debug Three.js + WebGL</h1>
    <div id="debug"></div>
    <canvas id="canvas"></canvas>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
    <script>
        const debug = document.getElementById('debug');
        
        function log(msg, isError) {
            const div = document.createElement('div');
            div.className = 'step ' + (isError ? 'fail' : 'pass');
            div.textContent = (isError ? '❌ ' : '✅ ') + msg;
            debug.appendChild(div);
        }

        try {
            // Test 1: Three.js loaded?
            if (typeof THREE === 'undefined') {
                log('Three.js NÃO carregou da CDN', true);
                throw new Error('Parando');
            }
            log('Three.js carregado (r' + THREE.REVISION + ')');
            log('OrbitControls: ' + (typeof THREE.OrbitControls !== 'undefined' ? 'disponível' : 'NÃO disponível'));

            // Test 2: WebGL support?
            const testCanvas = document.createElement('canvas');
            const gl = testCanvas.getContext('webgl') || testCanvas.getContext('experimental-webgl');
            if (!gl) {
                log('WebGL NÃO suportado neste navegador', true);
                throw new Error('Parando');
            }
            log('WebGL suportado: ' + gl.getParameter(gl.RENDERER), false);

            // Test 3: Render simple cube
            const canvas = document.getElementById('canvas');
            const scene = new THREE.Scene();
            scene.background = new THREE.Color(0xF6F1EB);

            const camera = new THREE.PerspectiveCamera(45, canvas.clientWidth / 300, 0.1, 100);
            camera.position.set(2, 2, 2);

            const renderer = new THREE.WebGLRenderer({ canvas: canvas, antialias: true });
            renderer.setSize(canvas.clientWidth, 300);
            renderer.setClearColor(0xF6F1EB);
            log('Renderer criado: ' + renderer.info.render.frame, false);

            // Lights
            scene.add(new THREE.AmbientLight(0xffffff, 0.6));
            const dirLight = new THREE.DirectionalLight(0xffffff, 1);
            dirLight.position.set(5, 10, 5);
            scene.add(dirLight);
            log('Luzes adicionadas', false);

            // Simple cube
            const geo = new THREE.BoxGeometry(1, 1, 1);
            const mat = new THREE.MeshPhongMaterial({ color: 0xC5A253 });
            const cube = new THREE.Mesh(geo, mat);
            scene.add(cube);
            log('Cubo criado e adicionado à cena', false);

            function animate() {
                requestAnimationFrame(animate);
                cube.rotation.x += 0.01;
                cube.rotation.y += 0.01;
                renderer.render(scene, camera);
            }
            animate();
            log('Animação iniciada - cubo girando', false);

        } catch (e) {
            log('Erro geral: ' + e.message, true);
            console.error(e);
        }
    </script>
</body>
</html>
