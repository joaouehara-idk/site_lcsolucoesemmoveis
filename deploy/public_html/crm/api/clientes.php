<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/../includes/db_config.php';
$conn = getDbConnection();
$conn->set_charset('utf8mb4');

$action = $_GET['action'] ?? 'list';

// List clientes
if ($action === 'list') {
    $status = $_GET['status'] ?? '';
    $q = trim($_GET['q'] ?? '');
    $limit = min(500, max(1, intval($_GET['limit'] ?? 100)));
    $offset = max(0, intval($_GET['offset'] ?? 0));

    $where = 'WHERE 1=1';
    $params = [];
    $types = '';

    if ($status) {
        $where .= ' AND c.status = ?';
        $params[] = $status;
        $types .= 's';
    }
    if ($q) {
        $where .= ' AND (c.razao_social LIKE ? OR c.nome_fantasia LIKE ? OR c.telefone LIKE ? OR c.email LIKE ? OR c.cnpj LIKE ?)';
        $like = "%$q%";
        $params = array_merge($params, [$like, $like, $like, $like, $like]);
        $types .= 'sssss';
    }

    $total = 0;
    $stmt = $conn->prepare("SELECT COUNT(*) FROM clientes c $where");
    if ($params) $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $stmt->bind_result($total);
    $stmt->fetch();
    $stmt->close();

    $stmt = $conn->prepare("SELECT c.*, 
        (SELECT COUNT(*) FROM projetos p WHERE p.cliente_id = c.id) as total_projetos,
        (SELECT SUM(p.valor) FROM projetos p WHERE p.cliente_id = c.id AND p.status = 'concluido') as total_gasto
        FROM clientes c $where ORDER BY c.updated_at DESC LIMIT ? OFFSET ?");
    $params[] = $limit;
    $params[] = $offset;
    $types .= 'ii';
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    echo json_encode(['success' => true, 'total' => $total, 'records' => $rows]);
    exit;
}

// Get single
if ($action === 'get') {
    $id = intval($_GET['id'] ?? 0);
    $stmt = $conn->prepare("SELECT c.*, 
        (SELECT COUNT(*) FROM projetos p WHERE p.cliente_id = c.id) as total_projetos,
        (SELECT SUM(p.valor) FROM projetos p WHERE p.cliente_id = c.id AND p.status = 'concluido') as total_gasto
        FROM clientes c WHERE c.id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    if ($row) {
        echo json_encode(['success' => true, 'record' => $row]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Cliente não encontrado']);
    }
    exit;
}

// Add / Update
if ($action === 'save' || $action === 'add') {
    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input) {
        echo json_encode(['success' => false, 'error' => 'Dados inválidos']);
        exit;
    }

    $id = intval($input['id'] ?? 0);
    $razao_social = $input['razao_social'] ?? '';
    $nome_fantasia = $input['nome_fantasia'] ?? '';
    $cnpj = $input['cnpj'] ?? '';
    $cpf = $input['cpf'] ?? '';
    $telefone = $input['telefone'] ?? '';
    $email = $input['email'] ?? '';
    $status = $input['status'] ?? 'lead';
    $origem = $input['origem'] ?? 'manual';
    $contato_inicial = $input['contato_inicial'] ?? date('Y-m-d');
    $observacoes = $input['observacoes'] ?? '';

    if ($id > 0) {
        $stmt = $conn->prepare("UPDATE clientes SET razao_social=?, nome_fantasia=?, cnpj=?, cpf=?, telefone=?, email=?, status=?, origem=?, contato_inicial=?, observacoes=? WHERE id=?");
        $stmt->bind_param('ssssssssssi', $razao_social, $nome_fantasia, $cnpj, $cpf, $telefone, $email, $status, $origem, $contato_inicial, $observacoes, $id);
        $stmt->execute();
        echo json_encode(['success' => true, 'message' => 'Cliente atualizado', 'id' => $id]);
    } else {
        $stmt = $conn->prepare("INSERT INTO clientes (razao_social, nome_fantasia, cnpj, cpf, telefone, email, status, origem, contato_inicial, observacoes) VALUES (?,?,?,?,?,?,?,?,?,?)");
        $stmt->bind_param('ssssssssss', $razao_social, $nome_fantasia, $cnpj, $cpf, $telefone, $email, $status, $origem, $contato_inicial, $observacoes);
        $stmt->execute();
        $newId = $conn->insert_id;
        echo json_encode(['success' => true, 'message' => 'Cliente cadastrado', 'id' => $newId]);
    }
    exit;
}

// Delete
if ($action === 'delete') {
    $id = intval($_GET['id'] ?? 0);
    $stmt = $conn->prepare("DELETE FROM clientes WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    echo json_encode(['success' => true, 'message' => 'Cliente removido']);
    exit;
}

// Auto-import from segments (clients who might have contacted LC)
// This checks email_tracking for opens/clicks and creates cliente entries
if ($action === 'auto_import') {
    $limit = intval($_GET['limit'] ?? 50);
    $imported = 0;

    // Get contacts who opened/clicked emails but aren't in clientes yet
    $rows = $conn->query("
        SELECT DISTINCT t.email, t.nome, t.campaign_tag 
        FROM email_tracking t 
        LEFT JOIN clientes c ON t.email = c.email 
        WHERE c.id IS NULL AND t.email IS NOT NULL AND t.email != ''
        AND (t.event_type = 'open' OR t.event_type = 'click')
        ORDER BY t.ts DESC 
        LIMIT $limit
    ");

    while ($row = $rows->fetch_assoc()) {
        $nome = $row['nome'] ?: explode('@', $row['email'])[0];
        $stmt = $conn->prepare("INSERT IGNORE INTO clientes (razao_social, email, origem, status, contato_inicial, observacoes) VALUES (?, ?, 'email', 'lead', CURDATE(), ?)");
        $obs = "Importado automaticamente via email - campanha: " . ($row['campaign_tag'] ?? 'N/A');
        $stmt->bind_param('sss', $nome, $row['email'], $obs);
        $stmt->execute();
        if ($stmt->affected_rows > 0) $imported++;
    }

    echo json_encode(['success' => true, 'imported' => $imported, 'message' => "$imported clientes importados do email tracking"]);
    exit;
}

// Stats
if ($action === 'stats') {
    $total_clientes = $conn->query("SELECT COUNT(*) FROM clientes")->fetch_row()[0];
    $leads = $conn->query("SELECT COUNT(*) FROM clientes WHERE status = 'lead'")->fetch_row()[0];
    $contato = $conn->query("SELECT COUNT(*) FROM clientes WHERE status = 'contato'")->fetch_row()[0];
    $em_projeto = $conn->query("SELECT COUNT(*) FROM clientes WHERE status = 'projeto'")->fetch_row()[0];
    $concluidos = $conn->query("SELECT COUNT(*) FROM clientes WHERE status = 'concluido'")->fetch_row()[0];
    $receita = $conn->query("SELECT COALESCE(SUM(valor), 0) FROM projetos WHERE status = 'concluido'")->fetch_row()[0];

    echo json_encode(['success' => true, 'data' => [
        'total_clientes' => intval($total_clientes),
        'leads' => intval($leads),
        'contato' => intval($contato),
        'em_projeto' => intval($em_projeto),
        'concluidos' => intval($concluidos),
        'receita' => floatval($receita),
    ]]);
    exit;
}

