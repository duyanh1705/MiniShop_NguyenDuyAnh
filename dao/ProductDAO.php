<?php
require_once __DIR__ . "/BaseDAO.php";
require_once __DIR__ . "/../models/Product.php";

class ProductDAO extends BaseDAO {
    public function __construct() {
        parent::__construct();
    }

    public function getAll(): array {
        $list = [];
        try {
            $sql = "SELECT * FROM products ORDER BY id DESC";
            $result = $this->executeQuery($sql);
            while ($row = $result->fetch_assoc()) {
                $product = new Product(
                    (int)$row["category_id"],
                    (int)$row["brand_id"],
                    $row["proname"],
                    $row["slug"],
                    (float)$row["price"],
                    (float)$row["discount_price"],
                    (int)$row["quantity"],
                    $row["image"],
                    $row["description"],
                    (int)$row["status"]
                );
                $product->id = (int)$row["id"];
                $product->createdAt = $row["created_at"];
                $product->updatedAt = $row["updated_at"];
                $list[] = $product;
            }
        } catch (Exception $e) {
            throw $e;
        }
        return $list;
    }

    public function findById(int $id): ?Product {
        try {
            $sql = "SELECT * FROM products WHERE id = ?";
            $stmt = $this->prepare($sql);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                $product = new Product(
                    (int)$row["category_id"],
                    (int)$row["brand_id"],
                    $row["proname"],
                    $row["slug"],
                    (float)$row["price"],
                    (float)$row["discount_price"],
                    (int)$row["quantity"],
                    $row["image"],
                    $row["description"],
                    (int)$row["status"]
                );
                $product->id = (int)$row["id"];
                $product->createdAt = $row["created_at"];
                $product->updatedAt = $row["updated_at"];
                return $product;
            }
        } catch (Exception $e) {
            throw $e;
        }
        return null;
    }

    public function insert(Product $product): bool {
        try {
            $sql = "INSERT INTO products (category_id, brand_id, proname, slug, price, discount_price, quantity, image, description, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $this->prepare($sql);
            $stmt->bind_param(
                "iissddissi",
                $product->categoryId,
                $product->brandId,
                $product->proName,
                $product->slug,
                $product->price,
                $product->discountPrice,
                $product->quantity,
                $product->image,
                $product->description,
                $product->status
            );
            return $stmt->execute();
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function update(Product $product): bool {
        try {
            $sql = "UPDATE products SET category_id = ?, brand_id = ?, proname = ?, slug = ?, price = ?, discount_price = ?, quantity = ?, image = ?, description = ?, status = ? WHERE id = ?";
            $stmt = $this->prepare($sql);
            $stmt->bind_param(
                "iissddissii",
                $product->categoryId,
                $product->brandId,
                $product->proName,
                $product->slug,
                $product->price,
                $product->discountPrice,
                $product->quantity,
                $product->image,
                $product->description,
                $product->status,
                $product->id
            );
            return $stmt->execute();
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function delete(int $id): bool {
        try {
            $sql = "DELETE FROM products WHERE id = ?";
            $stmt = $this->prepare($sql);
            $stmt->bind_param("i", $id);
            return $stmt->execute();
        } catch (Exception $e) {
            throw $e;
        }
    }
}
?>