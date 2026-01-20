<?php
header('Content-Type: application/json');
require_once 'db.php';

$action = $_GET['action'] ?? '';

switch ($action) {
    case 'get_products':
        try {
            $stmt = $pdo->query("SELECT * FROM products ORDER BY created_at DESC");
            $products = $stmt->fetchAll();
            echo json_encode($products);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
        break;
    case 'search_products':
        $keyword = $_GET['keyword'] ?? '';
        try {
            // Tim kiem san pham theo ten hoac mo ta
            $stmt = $pdo->prepare("SELECT * FROM products WHERE name LIKE ? OR description LIKE ? ORDER BY created_at DESC");
            $stmt->execute(["%$keyword%", "%$keyword%"]);
            $products = $stmt->fetchAll();
            echo json_encode($products);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
        break;

    case 'add_to_cart':
        // Bắt đầu session để lưu giỏ hàng (Yêu cầu Ngày 4)
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $id = $_GET['id'] ?? '';
        if ($id) {
            if (!isset($_SESSION['cart'])) { $_SESSION['cart'] = []; }
            // Tăng số lượng sản phẩm trong giỏ
            $_SESSION['cart'][$id] = ($_SESSION['cart'][$id] ?? 0) + 1;
            echo json_encode(['success' => true, 'cart_count' => array_sum($_SESSION['cart'])]);
        }
        break;
    case 'create_order':
        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data || !isset($data['customer_name'], $data['items'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid data']);
            break;
        }

        try {
            $pdo->beginTransaction();

            $stmt = $pdo->prepare("INSERT INTO orders (customer_name, total_price, status) VALUES (?, ?, 'new')");
            $stmt->execute([$data['customer_name'], $data['total_price']]);
            $orderId = $pdo->lastInsertId();

            $stmtItem = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity) VALUES (?, ?, ?)");
            foreach ($data['items'] as $item) {
                $stmtItem->execute([$orderId, $item['id'], $item['quantity']]);
            }

            $pdo->commit();
            echo json_encode(['success' => true, 'order_id' => $orderId]);
        } catch (PDOException $e) {
            $pdo->rollBack();
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
        break;

    default:
        http_response_code(404);
        echo json_encode(['error' => 'Action not found']);
        break;
}
?>