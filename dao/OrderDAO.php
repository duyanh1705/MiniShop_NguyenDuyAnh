<?php
require_once __DIR__ . "/BaseDAO.php";
require_once __DIR__ . "/../models/Order.php";

class OrderDAO extends BaseDAO
{
    public function __construct()
    {
        parent::__construct();
    }

    // 1. Hiển thị danh sách đơn hàng có JOIN Khách hàng & Nhân viên + Tìm kiếm (Mục E.2 & E.5)
    public function getAll(string $keyword = ""): array
    {
        $list = [];
        try {
            $sql = "SELECT o.*, 
                           c.fullname AS customer_name, 
                           u.fullname AS user_name 
                    FROM orders o
                    LEFT JOIN customers c ON o.customer_id = c.id
                    LEFT JOIN users u ON o.user_id = u.id";

            if (!empty($keyword)) {
                $sql .= " WHERE o.order_code LIKE ? OR c.fullname LIKE ?";
            }

            $sql .= " ORDER BY o.id DESC";

            $stmt = $this->prepare($sql);

            if (!empty($keyword)) {
                $search = "%{$keyword}%";
                $stmt->bind_param("ss", $search, $search);
            }

            $stmt->execute();
            $result = $stmt->get_result();

            while ($row = $result->fetch_assoc()) {
                $order = new Order(
                    (int)$row["customer_id"],
                    $row["user_id"] ? (int)$row["user_id"] : null,
                    $row["order_code"],
                    (float)$row["total_amount"],
                    $row["note"],
                    (int)$row["status"]
                );
                $order->id = (int)$row["id"];
                $order->createdAt = $row["created_at"];
                $order->updatedAt = $row["updated_at"];

                // Thuộc tính hiển thị tên từ JOIN (cần khai báo trong Order.php)
                $order->customerName = $row["customer_name"] ?? 'Khách lẻ';
                $order->userName = $row["user_name"] ?? 'Hệ thống';

                $list[] = $order;
            }
        } catch (Exception $e) {
            throw $e;
        }
        return $list;
    }

    // 2. Lấy thông tin đơn hàng theo ID kèm JOIN Khách hàng & Nhân viên (Mục E.3)
    public function findById(int $id): ?Order
    {
        try {
            $sql = "SELECT o.*, 
                           c.fullname AS customer_name, 
                           c.phone AS customer_phone, 
                           c.address AS customer_address, 
                           u.fullname AS user_name 
                    FROM orders o
                    LEFT JOIN customers c ON o.customer_id = c.id
                    LEFT JOIN users u ON o.user_id = u.id
                    WHERE o.id = ?";

            $stmt = $this->prepare($sql);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($row = $result->fetch_assoc()) {
                $order = new Order(
                    (int)$row["customer_id"],
                    $row["user_id"] ? (int)$row["user_id"] : null,
                    $row["order_code"],
                    (float)$row["total_amount"],
                    $row["note"],
                    (int)$row["status"]
                );
                $order->id = (int)$row["id"];
                $order->createdAt = $row["created_at"];
                $order->updatedAt = $row["updated_at"];

                // Gán thông tin khách hàng và nhân viên lấy từ JOIN
                $order->customerName = $row["customer_name"] ?? 'Khách lẻ';
                $order->customerPhone = $row["customer_phone"] ?? 'N/A';
                $order->customerAddress = $row["customer_address"] ?? 'N/A';
                $order->userName = $row["user_name"] ?? 'Hệ thống';

                return $order;
            }
        } catch (Exception $e) {
            throw $e;
        }
        return null;
    }

    // 3. Lấy danh sách sản phẩm thuộc đơn hàng cho mô hình Master - Detail (Mục E.3)
    public function getOrderDetails(int $orderId): array
    {
        $list = [];
        try {
            $sql = "SELECT od.*, p.proname AS productName 
                    FROM order_details od
                    INNER JOIN products p ON od.product_id = p.id
                    WHERE od.order_id = ?";

            $stmt = $this->prepare($sql);
            $stmt->bind_param("i", $orderId);
            $stmt->execute();
            $result = $stmt->get_result();

            while ($row = $result->fetch_assoc()) {
                $list[] = $row;
            }
        } catch (Exception $e) {
            throw $e;
        }
        return $list;
    }

    // 4. Cập nhật trạng thái đơn hàng (Mục E.4)
    public function updateStatus(int $orderId, int $status): bool
    {
        try {
            $sql = "UPDATE orders SET status = ? WHERE id = ?";
            $stmt = $this->prepare($sql);
            $stmt->bind_param("ii", $status, $orderId);
            return $stmt->execute();
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function insert(Order $order): bool
    {
        try {
            $sql = "INSERT INTO orders (customer_id, user_id, order_code, total_amount, note, status) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $this->prepare($sql);
            $stmt->bind_param("iisdsi", $order->customerId, $order->userId, $order->orderCode, $order->totalAmount, $order->note, $order->status);
            return $stmt->execute();
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function update(Order $order): bool
    {
        try {
            $sql = "UPDATE orders SET customer_id = ?, user_id = ?, order_code = ?, total_amount = ?, note = ?, status = ? WHERE id = ?";
            $stmt = $this->prepare($sql);
            $stmt->bind_param("iisdsii", $order->customerId, $order->userId, $order->orderCode, $order->totalAmount, $order->note, $order->status, $order->id);
            return $stmt->execute();
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function delete(int $id): bool
    {
        try {
            $sql = "DELETE FROM orders WHERE id = ?";
            $stmt = $this->prepare($sql);
            $stmt->bind_param("i", $id);
            return $stmt->execute();
        } catch (Exception $e) {
            throw $e;
        }
    }

    // Lấy 5 đơn hàng mới nhất cho Dashboard (Yêu cầu Câu G)
    public function getLatestOrders(int $limit = 5): array
    {
        $list = [];
        try {
            $sql = "SELECT o.*, c.fullname AS customer_name 
                    FROM orders o 
                    LEFT JOIN customers c ON o.customer_id = c.id 
                    ORDER BY o.id DESC LIMIT ?";
            $stmt = $this->prepare($sql);
            $stmt->bind_param("i", $limit);
            $stmt->execute();
            $result = $stmt->get_result();
            while ($row = $result->fetch_assoc()) {
                $order = new Order(
                    (int)$row["customer_id"],
                    $row["user_id"] ? (int)$row["user_id"] : null,
                    $row["order_code"],
                    (float)$row["total_amount"],
                    $row["note"],
                    (int)$row["status"]
                );
                $order->id = (int)$row["id"];
                $order->createdAt = $row["created_at"];
                $order->customerName = $row["customer_name"] ?? 'Khách lẻ';
                $list[] = $order;
            }
        } catch (Exception $e) {
            throw $e;
        }
        return $list;
    }
}
?>