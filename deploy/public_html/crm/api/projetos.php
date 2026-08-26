<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/../includes/db_config.php';
$conn = getDbConnection();
$conn->set_charset('utf8mb4');

$action = $_GET['action'] ?? 'list';

// List projetos
if ($action === 'list') {
    $cliente_id = intval($_GET['cliente_id'] ?? 0);
    $status = $_GET['status'] ?? '';
    $limit = min(500, max(1, intval($_GET['limit'] ?? 100)));
    $offset = max(0, intval($_GET['offset'] ?? 0));

    $where = 'WHERE 1=1';
    $params = [];
    $types = '';

    if ($cliente_id) {
        $where .= ' AND p.cliente_id = ?';
        $params[] = $cliente_id;
        $types .= 'i';
    }
    if ($status) {
        $where .= ' AND p.status = ?';
        $params[] = $status;
        $types .= 's';
    }

    $total = 0;
    $stmt = $conn->prepare("SELECT COUNT(*) FROM projetos p $where");
    if ($params) $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $stmt->bind_result($total);
    $stmt->fetch();
    $stmt->close();

    $stmt = $conn->prepare("SELECT p.*, c.razao_social as cliente_nome, c.telefone as cliente_telefone, c.email as cliente_email 
        FROM projetos p 
        LEFT JOIN clientes c ON p.cliente_id = c.id 
        $where ORDER BY p.updated_at DESC LIMIT ? OFFSET ?");
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

// Save (add/update)
if ($action === 'save') {
    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input) {
        echo json_encode(['success' => false, 'error' => 'Dados inválidos']);
        exit;
    }

    $id = intval($input['id'] ?? 0);
    $cliente_id = intval($input['cliente_id'] ?? 0);
    $descricao = $input['descricao'] ?? '';
    $tipo = $input['tipo'] ?? 'moveis_planejados';
    $valor = $input['valor'] ? floatval($input['valor']) : null;
    $data_inicio = $input['data_inicio'] ?? null;
    $data_conclusao = $input['data_conclusao'] ?? null;
    $status = $input['status'] ?? 'orcamento';
    $observacoes = $input['observacoes'] ?? '';

    if (!$cliente_id) {
        echo json_encode(['success' => false, 'error' => 'Selecione um cliente']);
        exit;
    }

    if ($id > 0) {
        $stmt = $conn->prepare("UPDATE projetos SET cliente_id=?, descricao=?, tipo=?, valor=?, data_inicio=?, data_conclusao=?, status=?, observacoes=? WHERE id=?");
        $stmt->bind_param('issdssssi', $cliente_id, $descricao, $tipo, $valor, $data_inicio, $data_conclusao, $status, $observacoes, $id);
        $stmt->execute();

        // Update cliente status to 'projeto' if project was added
        if ($status !== 'cancelado') {
            $newStatus = $status === 'concluido' ? 'concluido' : 'projeto';
            $stmt2 = $conn->prepare("UPDATE clientes SET status = ? WHERE id = ? AND status NOT IN ('concluido', 'inativo')");
            $stmt2->bind_param('si', $newStatus, $cliente_id);
            $stmt2->execute();
            $stmt2->close();
        }

        echo json_encode(['success' => true, 'message' => 'Projeto atualizado', 'id' => $id]);
    } else {
        $stmt = $conn->prepare("INSERT INTO projetos (cliente_id, descricao, tipo, valor, data_inicio, data_conclusao, status, observacoes) VALUES (?,?,?,?,?,?,?,?)");
        $stmt->bind_param('issdssss', $cliente_id, $descricao, $tipo, $valor, $data_inicio, $data_conclusao, $status, $observacoes);
        $stmt->execute();
        $newId = $conn->insert_id;

        // Update cliente status
        if ($status !== 'cancelado') {
            $newStatus = $status === 'concluido' ? 'concluido' : 'projeto';
            $stmt2 = $conn->prepare("UPDATE clientes SET status = ? WHERE id = ? AND status NOT IN ('concluido', 'inativo')");
            $stmt2->bind_param('si', $newStatus, $cliente_id);
            $stmt2->execute();
            $stmt2->close();
        }

        echo json_encode(['success' => true, 'message' => 'Projeto cadastrado', 'id' => $newId]);
    }
    exit;
}

// Delete
if ($action === 'delete') {
    $id = intval($_GET['id'] ?? 0);
    $stmt = $conn->prepare("DELETE FROM projetos WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    echo json_encode(['success' => true, 'message' => 'Projeto removido']);
    exit;
}

// Stats
if ($action === 'stats') {
    $total_clientes = $conn->query("SELECT COUNT(*) FROM clientes")->fetch_row()[0];
    $leads = $conn->query("SELECT COUNT(*) FROM clientes WHERE status = 'lead'")->fetch_row()[0];
    $contato = $conn->query("SELECT COUNT(*) FROM clientes WHERE status = 'contato'")->fetch_row()[0];
    $em_projeto = $conn->query("SELECT COUNT(*) FROM clientes WHERE status = 'projeto'")->fetch_row()[0];
    $concluidos = $conn->query("SELECT COUNT(*) FROM clientes WHERE status = 'concluido'")->fetch_row()[0];
    $total_projetos = $conn->query("SELECT COUNT(*) FROM projetos")->fetch_row()[0];
    $projetos_concluidos = $conn->query("SELECT COUNT(*) FROM projetos WHERE status = 'concluido'")->fetch_row()[0];
    $receita = $conn->query("SELECT COALESCE(SUM(valor), 0) FROM projetos WHERE status = 'concluido'")->fetch_row()[0];

    echo json_encode(['success' => true, 'data' => [
        'total_clientes' => intval($total_clientes),
        'leads' => intval($leads),
        'contato' => intval($contato),
        'em_projeto' => intval($em_projeto),
        'concluidos' => intval($concluidos),
        'total_projetos' => intval($total_projetos),
        'projetos_concluidos' => intval($projetos_concluidos),
        'receita' => floatval($receita),
    ]]);
    exit;
}

