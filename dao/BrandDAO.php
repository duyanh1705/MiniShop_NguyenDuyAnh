<?php
require_once __DIR__ . "/BaseDAO.php";
require_once __DIR__ . "/../models/Brand.php";

class BrandDAO extends BaseDAO {
    public function __construct() {
        parent::__construct();
    }

    public function getAll(): array {
        $list = [];
        try {
            $sql = "SELECT * FROM brands ORDER BY brandname";
            $result = $this->executeQuery($sql);
            while ($row = $result->fetch_assoc()) {
                $brand = new Brand(
                    $row["brandname"],
                    $row["slug"],
                    $row["image"],
                    $row["description"],
                    (int)$row["status"]
                );
                $brand->id = (int)$row["id"];
                $brand->createdAt = $row["created_at"];
                $brand->updatedAt = $row["updated_at"];
                $list[] = $brand;
            }
        } catch (Exception $e) {
            throw $e;
        }
        return $list;
    }

    public function findById(int $id): ?Brand {
        try {
            $sql = "SELECT * FROM brands WHERE id = ?";
            $stmt = $this->prepare($sql);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                $brand = new Brand(
                    $row["brandname"],
                    $row["slug"],
                    $row["image"],
                    $row["description"],
                    (int)$row["status"]
                );
                $brand->id = (int)$row["id"];
                $brand->createdAt = $row["created_at"];
                $brand->updatedAt = $row["updated_at"];
                return $brand;
            }
        } catch (Exception $e) {
            throw $e;
        }
        return null;
    }

    public function insert(Brand $brand): bool {
        try {
            $sql = "INSERT INTO brands (brandname, slug, image, description, status) VALUES (?, ?, ?, ?, ?)";
            $stmt = $this->prepare($sql);
            $stmt->bind_param("ssssi", $brand->brandName, $brand->slug, $brand->image, $brand->description, $brand->status);
            return $stmt->execute();
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function update(Brand $brand): bool {
        try {
            $sql = "UPDATE brands SET brandname = ?, slug = ?, image = ?, description = ?, status = ? WHERE id = ?";
            $stmt = $this->prepare($sql);
            $stmt->bind_param("ssssii", $brand->brandName, $brand->slug, $brand->image, $brand->description, $brand->status, $brand->id);
            return $stmt->execute();
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function delete(int $id): bool {
        try {
            $sql = "DELETE FROM brands WHERE id = ?";
            $stmt = $this->prepare($sql);
            $stmt->bind_param("i", $id);
            return $stmt->execute();
        } catch (Exception $e) {
            throw $e;
        }
    }
}
?>