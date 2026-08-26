/**
 * LC Soluções em Móveis — Furniture Engine to Three.js Adapter
 * Converte PartDefinition em Three.js Meshes
 */
(function() {
    'use strict';

    class FurnitureRendererAdapter {
        constructor(materialEngine) {
            this.materialEngine = materialEngine;
        }

        // Converter mm para metros (Three.js usa metros)
        mm_to_m(value) {
            return value / 1000;
        }

        // Converter PartDefinition para Three.js Mesh
        createMeshFromPart(part, material) {
            const w = this.mm_to_m(part.width);
            const h = this.mm_to_m(part.height);
            const d = this.mm_to_m(part.thickness);

            // Validar dimensões
            if (!isFinite(w) || !isFinite(h) || !isFinite(d) || w <= 0 || h <= 0 || d <= 0) {
                console.warn(`Dimensões inválidas para ${part.name}: ${part.width}x${part.height}x${part.thickness}`);
                return null;
            }

            const geometry = new THREE.BoxGeometry(w, h, d);
            const mesh = new THREE.Mesh(geometry, material);
            mesh.position.set(
                this.mm_to_m(part.position.x),
                this.mm_to_m(part.position.y),
                this.mm_to_m(part.position.z)
            );
            mesh.castShadow = true;
            mesh.receiveShadow = true;
            mesh.name = part.name;
            mesh.userData = {
                type: part.type,
                partId: part.id,
                width: part.width,
                height: part.height,
                thickness: part.thickness,
            };

            return mesh;
        }

        // Obter material para a parte
        getMaterialForPart(part) {
            const colorHex = this._getColorHex(part.color);
            const isWood = part.finish === 'texturizado';
            const grainDir = part.grainDirection === 'horizontal' ? 'horizontal' : 'vertical';

            return this.materialEngine.getMaterial({
                color: colorHex,
                finish: part.finish,
                hasGrain: isWood,
                grainDir: grainDir,
            });
        }

        // Obter cor hex
        _getColorHex(colorId) {
            const colors = {
                branco: '#ECEAE3', offwhite: '#E8E0D3', bege: '#D8C3A5',
                caramelo: '#C8A87C', carvalho: '#C9A876', nogueira: '#6B5340',
                wenge: '#3A2E26', cinza: '#9A9A9A', grafite: '#4A4A4A', preto: '#2B2B2B',
            };
            return colors[colorId] || '#C8A87C';
        }

        // Renderizar todas as partes no grupo
        renderParts(parts, furnitureGroup) {
            const interactiveObjects = [];

            parts.forEach(part => {
                const material = this.getMaterialForPart(part);
                const mesh = this.createMeshFromPart(part, material);

                if (mesh) {
                    furnitureGroup.add(mesh);

                    // Adicionar à lista de objetos interativos se for porta ou gaveta
                    if (part.type === 'door' || part.type === 'drawer_front') {
                        mesh.userData.partType = part.type;
                        mesh.userData.partName = part.name;
                        interactiveObjects.push(mesh);
                    }
                }
            });

            return interactiveObjects;
        }

        // Criar pivô para porta (para animação de abertura)
        createDoorPivot(part, material) {
            const pivot = new THREE.Group();
            pivot.name = `door_pivot_${part.name}`;

            const mesh = this.createMeshFromPart(part, material);
            if (!mesh) return null;

            // Posicionar a porta no centro (sem offset de pivô por enquanto)
            mesh.position.set(0, 0, 0);

            pivot.add(mesh);
            pivot.position.set(
                this.mm_to_m(part.position.x),
                this.mm_to_m(part.position.y),
                this.mm_to_m(part.position.z)
            );

            pivot.userData = {
                type: 'door',
                partId: part.id,
                open: false,
                maxOpen: 105 * Math.PI / 180,
            };

            return pivot;
        }

        // Renderizar portas com pivôs
        renderDoors(doorParts, furnitureGroup) {
            const doorPivots = [];

            doorParts.forEach(part => {
                const material = this.getMaterialForPart(part);
                const pivot = this.createDoorPivot(part, material);

                if (pivot) {
                    furnitureGroup.add(pivot);
                    doorPivots.push(pivot);
                }
            });

            return doorPivots;
        }

        // Renderizar gavetas (agrupadas: frente + laterais + fundo)
        renderDrawers(allDrawerParts, furnitureGroup) {
            const drawers = [];

            // Agrupar parts por gaveta (baseado no número no nome)
            const drawerGroups = {};
            allDrawerParts.forEach(part => {
                const match = part.name.match(/gaveta (\d+)/);
                if (match) {
                    const idx = parseInt(match[1]);
                    if (!drawerGroups[idx]) drawerGroups[idx] = [];
                    drawerGroups[idx].push(part);
                }
            });

            // Criar um Group para cada gaveta
            Object.keys(drawerGroups).sort((a, b) => a - b).forEach((key, arrayIndex) => {
                const parts = drawerGroups[key];
                const group = new THREE.Group();
                group.name = `drawer_${arrayIndex}`;

                parts.forEach(part => {
                    const material = this.getMaterialForPart(part);
                    const mesh = this.createMeshFromPart(part, material);
                    if (mesh) {
                        group.add(mesh);
                    }
                });

                group.userData = {
                    type: 'drawer',
                    index: arrayIndex,
                    open: false,
                };

                furnitureGroup.add(group);
                drawers.push(group);
            });

            return drawers;
        }
    }

    // Exportar
    window.LCFurnitureRendererAdapter = FurnitureRendererAdapter;

})();
