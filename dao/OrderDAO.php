<?php
require_once __DIR__ . "/BaseDAO.php";
require_once __DIR__ . "/../models/Order.php";

class OrderDAO extends BaseDAO
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getAll(): array
    {
        $list = [];
        try {
            $sql = "SELECT * FROM orders ORDER BY id DESC";
            $result = $this->executeQuery($sql);
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
                $list[] = $order;
            }
        } catch (Exception $e) {
            throw $e;
        }
        return $list;
    }

    public function findById(int $id): ?Order
    {
        try {
            $sql = "SELECT * FROM orders WHERE id = ?";
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
                return $order;
            }
        } catch (Exception $e) {
            throw $e;
        }
        return null;
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
                // Lưu thêm tên khách hàng để hiển thị
                $order->customerName = $row["customer_name"] ?? 'Khách lẻ';
                $list[] = $order;
            }
        } catch (Exception $e) {
            throw $e;
        }
        return $list;
    }
}
