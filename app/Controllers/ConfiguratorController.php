<?php

namespace App\Controllers;

use App\Core\Controller;

class ConfiguratorController extends Controller
{
    public function catalog()
    {
        $catalog = [
            'furniture' => [
                'guarda_roupa' => ['label' => 'Guarda-roupa planejado', 'category' => 'quarto', 'mounted' => false],
                'nicho' => ['label' => 'Nicho', 'category' => 'decorativo', 'mounted' => true],
                'aereo' => ['label' => 'Móvel Aéreo', 'category' => 'cozinha', 'mounted' => true],
                'estante' => ['label' => 'Estante', 'category' => 'sala', 'mounted' => false],
                'painel_tv' => ['label' => 'Painel para TV', 'category' => 'sala', 'mounted' => false],
                'cozinha' => ['label' => 'Armário de Cozinha', 'category' => 'cozinha', 'mounted' => false],
                'closet' => ['label' => 'Closet', 'category' => 'quarto', 'mounted' => false],
                'comoda' => ['label' => 'Cômoda', 'category' => 'quarto', 'mounted' => false],
            ],
            'colors' => [
                ['id' => 'branco', 'name' => 'Branco', 'hex' => '#ECEAE3', 'available_at_lc' => true],
                ['id' => 'offwhite', 'name' => 'Off-white', 'hex' => '#E8E0D3', 'available_at_lc' => true],
                ['id' => 'bege', 'name' => 'Bege', 'hex' => '#D8C3A5', 'available_at_lc' => true],
                ['id' => 'caramelo', 'name' => 'Caramelo', 'hex' => '#C8A87C', 'available_at_lc' => true],
                ['id' => 'madeira_clara', 'name' => 'Madeira Clara', 'hex' => '#C9A876', 'available_at_lc' => true],
                ['id' => 'nogueira', 'name' => 'Nogueira', 'hex' => '#6B5340', 'available_at_lc' => true],
                ['id' => 'cinza', 'name' => 'Cinza', 'hex' => '#9A9A9A', 'available_at_lc' => true],
                ['id' => 'grafite', 'name' => 'Grafite', 'hex' => '#4A4A4A', 'available_at_lc' => true],
                ['id' => 'preto', 'name' => 'Preto', 'hex' => '#2B2B2B', 'available_at_lc' => true],
            ],
            'finishes' => [
                ['id' => 'melamina', 'name' => 'Melamina'],
                ['id' => 'liso', 'name' => 'Liso (fosco)'],
                ['id' => 'texturizado', 'name' => 'Texturizado (madeira)'],
                ['id' => 'laca', 'name' => 'Laca (brilhante)'],
            ],
            'handles' => [
                ['id' => 'alca', 'name' => 'Alça', 'available_at_lc' => true],
                ['id' => 'botao', 'name' => 'Botão', 'available_at_lc' => true],
                ['id' => 'cava', 'name' => 'Cava (recesso)', 'available_at_lc' => true],
                ['id' => 'nenhum', 'name' => 'Nenhum', 'available_at_lc' => true],
            ],
            'led' => [
                ['id' => 'none', 'name' => 'Sem LED', 'available_at_lc' => true],
                ['id' => 'fita_interno', 'name' => 'Fita LED interna', 'available_at_lc' => true],
            ],
        ];

        return $this->json($catalog);
    }

    public function saveProject()
    {
        $raw = file_get_contents('php://input');
        $data = json_decode($raw, true);

        if (!$data || !isset($data['furnitureType'])) {
            return $this->json(['success' => false, 'error' => 'Invalid data'], 400);
        }

        $projectId = 'LC-' . date('Y') . '-' . strtoupper(substr(md5($raw . time()), 0, 6));

        try {
            $db = \App\Core\Database::getInstance();
            $db->query(
                "INSERT INTO saved_projects (project_id, furniture_type, configuration_json, created_at) VALUES (?, ?, ?, NOW())",
                [$projectId, $data['furnitureType'], json_encode($data)]
            );
        } catch (\Exception $e) {
            if (strpos($e->getMessage(), 'saved_projects') !== false || strpos($e->getMessage(), "doesn't exist") !== false) {
                return $this->json(['success' => true, 'project_id' => $projectId, 'warning' => 'Table not yet created']);
            }
            return $this->json(['success' => false, 'error' => 'Database error'], 500);
        }

        return $this->json(['success' => true, 'project_id' => $projectId]);
    }

    public function loadProject($id)
    {
        if (!$id || !preg_match('/^LC-\d{4}-[A-Z0-9]{6}$/', $id)) {
            return $this->json(['success' => false, 'error' => 'Invalid project ID'], 400);
        }

        try {
            $db = \App\Core\Database::getInstance();
            $project = $db->fetch(
                "SELECT * FROM saved_projects WHERE project_id = ?",
                [$id]
            );

            if (!$project) {
                return $this->json(['success' => false, 'error' => 'Project not found'], 404);
            }

            $config = json_decode($project['configuration_json'], true);
            return $this->json(['success' => true, 'project' => $config]);
        } catch (\Exception $e) {
            return $this->json(['success' => false, 'error' => 'Database error'], 500);
        }
    }
}
