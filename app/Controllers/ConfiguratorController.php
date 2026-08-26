<?php
/**
 * ConfiguratorController
 * API endpoints for the 3D Furniture Configurator.
 * Serves catalog data, saves/loads projects.
 */

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Middlewares\AuthMiddleware;
use App\Middlewares\CsrfMiddleware;

class ConfiguratorController extends Controller {

    private function getDb() {
        return Database::getInstance();
    }

    /**
     * GET /api/configurador/catalog
     * Returns furniture types, material brands, lines, finishes, thicknesses.
     */
    public function catalog() {
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: ' . (isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '*'));
        header('Access-Control-Allow-Methods: GET, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, X-Requested-With');

        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(200);
            exit;
        }

        try {
            $db = $this->getDb();

            $brands = $db->fetchAll("
                SELECT id, name, description, available_at_lc
                FROM configurator_material_brands
                ORDER BY name
            ");

            $lines = $db->fetchAll("
                SELECT id, brand_id, name, available_at_lc
                FROM configurator_material_lines
                ORDER BY brand_id, name
            ");

            $patterns = $db->fetchAll("
                SELECT p.id, p.line_id, p.name, p.category, p.base_color,
                       p.roughness, p.metalness, p.texture_url, p.normal_url,
                       p.roughness_map_url, p.preview_url, p.application_image_url,
                       p.thickness_mm, p.available_at_lc, p.reference,
                       b.name as brand_name, l.name as line_name
                FROM configurator_material_patterns p
                LEFT JOIN configurator_material_lines l ON p.line_id = l.id
                LEFT JOIN configurator_material_brands b ON l.brand_id = b.id
                ORDER BY p.name
            ");

            $finishes = $db->fetchAll("
                SELECT id, name, slug, roughness, metalness, has_grain, description
                FROM configurator_material_finishes
                ORDER BY id
            ");

            $thicknesses = $db->fetchAll("
                SELECT id, thickness_mm, label, available_at_lc
                FROM configurator_material_thicknesses
                ORDER BY thickness_mm
            ");

            $furnitureTypes = $db->fetchAll("
                SELECT id, key_name, label, category, dimensions_json, description, model_path
                FROM configurator_furniture_types
                ORDER BY category, label
            ");

            $parsedFurniture = [];
            foreach ($furnitureTypes as $ft) {
                $dims = json_decode($ft['dimensions_json'], true);
                if (!$dims) {
                    $dims = ['W' => 180, 'H' => 220, 'D' => 55];
                }
                $parsedFurniture[] = [
                    'key' => $ft['key_name'],
                    'label' => $ft['label'],
                    'category' => $ft['category'],
                    'dimensions' => $dims,
                    'description' => $ft['description'],
                    'modelPath' => $ft['model_path'],
                ];
            }

            echo json_encode([
                'brands' => $brands,
                'lines' => $lines,
                'patterns' => $patterns,
                'finishes' => $finishes,
                'thicknesses' => $thicknesses,
                'furnitureTypes' => $parsedFurniture,
                'colorWarning' => 'As cores exibidas na tela são uma representação digital e podem apresentar diferenças em relação à amostra física. Para especificação final, considere a amostra física do fabricante.',
            ]);
        } catch (\Exception $e) {
            $this->json(['error' => 'Failed to load catalog: ' . $e->getMessage()], 500);
        }
    }

    /**
     * GET /api/configurador/hardware
     * Returns hardware brands, handle types, LED options.
     */
    public function hardware() {
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, X-Requested-With');

        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(200);
            exit;
        }

        try {
            $db = $this->getDb();

            $brands = $db->fetchAll("
                SELECT id, name, available_at_lc, description
                FROM configurator_hardware_brands
                ORDER BY name
            ");

            $handles = $db->fetchAll("
                SELECT id, brand_id, name, model_code, material, finish_hex,
                       available_at_lc, description, model_path
                FROM configurator_handles
                ORDER BY name
            ");

            $ledOptions = $db->fetchAll("
                SELECT id, name, available_at_lc, description, model_path
                FROM configurator_led_options
                ORDER BY id
            ");

            echo json_encode([
                'brands' => $brands,
                'handles' => array_values(array_filter($handles, fn($h) => $h['available_at_lc'] == 1)),
                'allHandles' => $handles,
                'ledOptions' => array_values(array_filter($ledOptions, fn($l) => $l['available_at_lc'] == 1)),
                'allLedOptions' => $ledOptions,
            ]);
        } catch (\Exception $e) {
            $this->json(['error' => 'Failed to load hardware: ' . $e->getMessage()], 500);
        }
    }

    /**
     * POST /api/configurador/save
     * Saves a project configuration to MySQL.
     * Requires authentication (admin or public session).
     */
    public function saveProject() {
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: ' . (isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '*'));
        header('Access-Control-Allow-Methods: POST, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, X-Requested-With, X-CSRF-Token');

        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(200);
            exit;
        }

        $input = file_get_contents('php://input');
        $data = json_decode($input, true);

        if (!$data) {
            $this->json(['error' => 'Invalid JSON data'], 400);
        }

        if (empty($data['configuration'])) {
            $this->json(['error' => 'Missing configuration data'], 400);
        }

        $name = $data['name'] ?? 'Projeto sem nome';
        $config = json_encode($data['configuration']);
        $configVersion = $data['configuration_version'] ?? '1.0.0';
        $userId = $_SESSION['user_id'] ?? null;

        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? ($_SERVER['REMOTE_ADDR'] ?? '0.0.0.0');
        $ip = trim(explode(',', $ip)[0]);

        try {
            $db = $this->getDb();
            $stmt = $db->prepare("
                INSERT INTO configurator_projects
                    (user_id, name, configuration, configuration_version, ip_address, created_at)
                VALUES (?, ?, ?, ?, ?, NOW())
            ");
            $stmt->execute([$userId, $name, $config, $configVersion, $ip]);
            $projectId = $db->lastInsertId();

            $shareId = $this->generateShareId($projectId);
            $db->query("UPDATE configurator_projects SET share_id = ? WHERE id = ?", [$shareId, $projectId]);

            $this->json([
                'success' => true,
                'projectId' => $projectId,
                'shareId' => $shareId,
                'url' => BASE_URL . '/configurador/p/' . $shareId,
            ]);
        } catch (\Exception $e) {
            $this->json(['error' => 'Save failed: ' . $e->getMessage()], 500);
        }
    }

    /**
     * GET /api/configurador/load/{id}
     * Loads a saved project by share ID or numeric ID.
     */
    public function loadProject($id) {
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');

        try {
            $db = $this->getDb();
            $stmt = $db->prepare("
                SELECT id, name, configuration, configuration_version, created_at
                FROM configurator_projects
                WHERE share_id = ? OR id = ?
                LIMIT 1
            ");
            $stmt->execute([$id, $id]);
            $project = $stmt->fetch();

            if (!$project) {
                $this->json(['error' => 'Project not found'], 404);
            }

            $config = json_decode($project['configuration'], true);
            if (!$config) {
                $this->json(['error' => 'Invalid configuration data'], 500);
            }

            $this->json([
                'id' => $project['id'],
                'name' => $project['name'],
                'configuration' => $config,
                'configuration_version' => $project['configuration_version'],
                'created_at' => $project['created_at'],
            ]);
        } catch (\Exception $e) {
            $this->json(['error' => 'Load failed: ' . $e->getMessage()], 500);
        }
    }

    /**
     * DELETE /api/configurador/delete/{id}
     */
    public function deleteProject($id) {
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: DELETE, OPTIONS');

        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(200);
            exit;
        }

        try {
            $db = $this->getDb();
            $stmt = $db->prepare("DELETE FROM configurator_projects WHERE share_id = ? OR id = ?");
            $stmt->execute([$id, $id]);
            $this->json(['success' => true, 'deleted' => $stmt->rowCount()]);
        } catch (\Exception $e) {
            $this->json(['error' => 'Delete failed: ' . $e->getMessage()], 500);
        }
    }

    private function generateShareId($projectId) {
        $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
        $short = '';
        for ($i = 0; $i < 6; $i++) {
            $short .= $chars[random_int(0, strlen($chars) - 1)];
        }
        return 'LC-' . $short . '-' . date('ym', strtotime('now')) . '-' . $projectId;
    }
}
