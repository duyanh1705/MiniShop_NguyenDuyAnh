<?php
require_once __DIR__ . "/BaseDAO.php";
require_once __DIR__ . "/../models/Category.php";
class CategoryDAO extends BaseDAO
{
    public function __construct()
    {
        parent::__construct();
    }
    // Lấy tất cả danh mục
    public function getAll(): array
    {
        $list = [];
        try {
            // Hạn chế dùng SELECT * (nên chỉ định rõ cột cần truy vấn)
            $sql = "SELECT * FROM categories ORDER BY catename";
            $result = $this->executeQuery($sql);
            while ($row = $result->fetch_assoc()) {
                $category = new Category(
                    $row["catename"],
                    $row["slug"],
                    $row["image"],
                    $row["description"],
                    $row["status"]
                );
                $category->id = $row["id"];
                $category->createdAt = $row["created_at"];
                $category->updatedAt = $row["updated_at"];
                $list[] = $category;
            }
        } catch (Exception $e) {
            throw $e;
        }
        return $list;
    }
    // Tìm theo ID
    public function findById(int $id): ?Category
    {
        try {
            $sql = "SELECT * FROM categories WHERE id=?";
            $stmt = $this->prepare($sql);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                $category = new Category(
                    $row["name"],
                    $row["slug"],
                    $row["image"],
                    $row["description"],
                    $row["status"]
                );
                $category->id = $row["id"];
                $category->createdAt = $row["created_at"];
                $category->updatedAt = $row["updated_at"];
                return $category;
            }
        } catch (Exception $e) {
            throw $e;
        }
        return null;
    }
    // Thêm danh mục
    public function insert(Category $category): bool
    {
        try {
            $sql = "INSERT INTO categories(catename,slug,image,description,status)
VALUES(?,?,?,?,?)";
            $stmt = $this->prepare($sql);
            $stmt->bind_param(
                "ssssi",
                $category->name,
                $category->slug,
                $category->image,
                $category->description,
                $category->status
            );
            return $stmt->execute();
        } catch (Exception $e) {
            throw $e;
        }
    }
    // Cập nhật danh mục
    public function update(Category $category): bool
    {
        try {
            $sql = "UPDATE categories
SET
catename=?,
slug=?,
image=?,
description=?,
status=?
WHERE id=?";
            $stmt = $this->prepare($sql);
            $stmt->bind_param(
                "ssssii",
                $category->name,
                $category->slug,
                $category->image,
                $category->description,
                $category->status,
                $category->id
            );
            return $stmt->execute();
        } catch (Exception $e) {
            throw $e;
        }
    }
    // Xóa danh mục
    public function delete(int $id): bool
    {
        try {
            $sql = "DELETE FROM categories WHERE id=?";
            $stmt = $this->prepare($sql);
            $stmt->bind_param("i", $id);
            return $stmt->execute();
        } catch (Exception $e) {
            throw $e;
        }
    }
}
