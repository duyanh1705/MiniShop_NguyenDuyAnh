<?php
require_once __DIR__ . "/BaseDAO.php";
require_once __DIR__ . "/../models/Product.php";

class ProductDAO extends BaseDAO
{
    public function __construct()
    {
        parent::__construct();
    }

    // 1. Cập nhật JOIN 3 bảng và hỗ trợ Tìm kiếm cho getAll()
    public function getAll(string $keyword = ""): array
    {
        $list = [];
        try {
            $sql = "SELECT p.*, 
                           c.catename AS cateName, 
                           b.brandname AS brandName 
                    FROM products p
                    INNER JOIN categories c ON p.category_id = c.id
                    INNER JOIN brands b ON p.brand_id = b.id";

            if (!empty($keyword)) {
                $sql .= " WHERE p.proname LIKE ? OR c.catename LIKE ? OR b.brandname LIKE ?";
            }

            $sql .= " ORDER BY p.id DESC";

            $stmt = $this->prepare($sql);

            if (!empty($keyword)) {
                $search = "%{$keyword}%";
                $stmt->bind_param("sss", $search, $search, $search);
            }

            $stmt->execute();
            $result = $stmt->get_result();

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

                $product->cateName = $row["cateName"];
                $product->brandName = $row["brandName"];

                $list[] = $product;
            }
        } catch (Exception $e) {
            throw $e;
        }
        return $list;
    }

    // 2. Cập nhật JOIN cho findById()
    public function findById(int $id): ?Product
    {
        try {
            $sql = "SELECT p.*, 
                           c.catename AS cateName, 
                           b.brandname AS brandName 
                    FROM products p
                    INNER JOIN categories c ON p.category_id = c.id
                    INNER JOIN brands b ON p.brand_id = b.id
                    WHERE p.id = ?";

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

                $product->cateName = $row["cateName"];
                $product->brandName = $row["brandName"];

                return $product;
            }
        } catch (Exception $e) {
            throw $e;
        }
        return null;
    }

    public function insert(Product $product): bool
    {
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

    public function update(Product $product): bool
    {
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

    // 3. Xóa sản phẩm + Xóa tất cả ảnh vật lý (Ảnh chính & Album Gallery)
    public function delete(int $id): bool
    {
        try {
            // Lấy thông tin sản phẩm và danh sách ảnh gallery phụ trước khi xóa
            $product = $this->findById($id);
            if ($product) {
                $uploadDir = __DIR__ . "/../../uploads/products/";

                // A. Xóa tất cả file ảnh phụ trong album Gallery
                $galleryImages = $this->getImagesByProductId($id);
                foreach ($galleryImages as $gImg) {
                    $gFilePath = $uploadDir . $gImg["image"];
                    if (!empty($gImg["image"]) && file_exists($gFilePath)) {
                        @unlink($gFilePath);
                    }
                }

                // B. Xóa file ảnh đại diện chính của sản phẩm
                if (!empty($product->image)) {
                    $mainImagePath = $uploadDir . $product->image;
                    if (file_exists($mainImagePath)) {
                        @unlink($mainImagePath);
                    }
                }
            }

            // C. Thực thi xóa sản phẩm trong CSDL (Bảng product_images sẽ tự động xóa nếu có ON DELETE CASCADE)
            $sql = "DELETE FROM products WHERE id = ?";
            $stmt = $this->prepare($sql);
            $stmt->bind_param("i", $id);
            return $stmt->execute();
        } catch (Exception $e) {
            throw $e;
        }
    }

    // 4. Thêm ảnh phụ Gallery (Mục E.1 trong Lab 8)
    public function insertImage(int $productId, string $image): bool
    {
        try {
            $sql = "INSERT INTO product_images (product_id, image) VALUES (?, ?)";
            $stmt = $this->prepare($sql);
            $stmt->bind_param("is", $productId, $image);
            return $stmt->execute();
        } catch (Exception $e) {
            throw $e;
        }
    }

    // 5. Lấy tất cả ảnh phụ của 1 sản phẩm (Mục E.1 trong Lab 8)
    public function getImagesByProductId(int $productId): array
    {
        $list = [];
        try {
            $sql = "SELECT * FROM product_images WHERE product_id = ?";
            $stmt = $this->prepare($sql);
            $stmt->bind_param("i", $productId);
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

    // 6. Xóa 1 ảnh phụ Gallery theo ID ảnh và xóa file vật lý (Mục E.1 & G trong Lab 8)
    public function deleteImage(int $id): bool
    {
        try {
            $sqlSelect = "SELECT image FROM product_images WHERE id = ?";
            $stmtSel = $this->prepare($sqlSelect);
            $stmtSel->bind_param("i", $id);
            $stmtSel->execute();
            $res = $stmtSel->get_result();
            if ($row = $res->fetch_assoc()) {
                $filePath = __DIR__ . "/../../uploads/products/" . $row["image"];
                if (!empty($row["image"]) && file_exists($filePath)) {
                    @unlink($filePath);
                }
            }

            $sqlDelete = "DELETE FROM product_images WHERE id = ?";
            $stmtDel = $this->prepare($sqlDelete);
            $stmtDel->bind_param("i", $id);
            return $stmtDel->execute();
        } catch (Exception $e) {
            throw $e;
        }
    }
}
?>