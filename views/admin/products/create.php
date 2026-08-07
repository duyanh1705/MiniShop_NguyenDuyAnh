<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/../../../dao/ProductDAO.php";
require_once __DIR__ . "/../../../dao/CategoryDAO.php";
require_once __DIR__ . "/../../../dao/BrandDAO.php";
require_once __DIR__ . "/../../../models/Product.php";

$categoryDAO = new CategoryDAO();
$brandDAO = new BrandDAO();

// Lấy danh sách Categories và Brands đổ vào thẻ <select>
$categories = $categoryDAO->getAll();
$brands = $brandDAO->getAll();

$errors = [];
$proName = "";
$slug = "";
$categoryId = 0;
$brandId = 0;
$price = 0;
$discountPrice = 0;
$quantity = 0;
$description = "";
$status = 1;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $proName       = trim($_POST["proName"] ?? "");
    $slug          = trim($_POST["slug"] ?? "");
    $categoryId    = (int)($_POST["categoryId"] ?? 0);
    $brandId       = (int)($_POST["brandId"] ?? 0);
    $price         = (float)($_POST["price"] ?? 0);
    $discountPrice = (float)($_POST["discountPrice"] ?? 0);
    $quantity      = (int)($_POST["quantity"] ?? 0);
    $description   = trim($_POST["description"] ?? "");
    $status        = (int)($_POST["status"] ?? 1);

    // 1. Validation thông tin text (Mục B.7 trong Lab 8)
    if (empty($proName)) {
        $errors[] = "Tên sản phẩm không được để trống.";
    }
    if (empty($slug)) {
        $errors[] = "Slug không được để trống.";
    }
    if ($categoryId == 0) {
        $errors[] = "Vui lòng chọn danh mục.";
    }
    if ($brandId == 0) {
        $errors[] = "Vui lòng chọn thương hiệu.";
    }
    if ($price <= 0) {
        $errors[] = "Giá bán phải lớn hơn 0.";
    }
    if ($quantity < 0) {
        $errors[] = "Số lượng không hợp lệ.";
    }

    // 2. Validation Hình ảnh đại diện (Mục B.6 & B.7 trong Lab 8)
    $fileName = $_FILES["image"]["name"] ?? "";
    $tmpName  = $_FILES["image"]["tmp_name"] ?? "";
    $fileSize = $_FILES["image"]["size"] ?? 0;
    $error    = $_FILES["image"]["error"] ?? 0;
    $image    = "";

    if (!empty($fileName)) {
        if ($error != UPLOAD_ERR_OK) {
            $errors[] = "Upload hình ảnh không thành công.";
        } else {
            $allowExtensions = ["jpg", "jpeg", "png", "gif", "webp"];
            $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

            if (!in_array($extension, $allowExtensions)) {
                $errors[] = "Chỉ cho phép file JPG, JPEG, PNG hoặc WEBP.";
            }

            // Giới hạn 200 KB (Mục B.7)
            if ($fileSize > 200 * 1024) {
                $errors[] = "Kích thước hình ảnh <= 200 KB.";
            }
        }
    }

    // 3. Thực hiện Upload và Lưu CSDL (Mục B.9 & B.10 trong Lab 8)
    if (empty($errors)) {
        // Upload ảnh đại diện chính chuẩn theo Lab (Mục B.9)
        if (!empty($fileName)) {
            $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            // Đổi tên file: time_slug.jpg
            $image = time() . "_" . $slug . "." . $extension;
            // Đường dẫn lưu hình ảnh
            $uploadPath = __DIR__ . "/../../../uploads/products/" . $image;
            // Upload hình ảnh
            move_uploaded_file($tmpName, $uploadPath);
        }

        $dao = new ProductDAO();

        $product = new Product(
            $categoryId,
            $brandId,
            $proName,
            $slug,
            $price,
            $discountPrice,
            $quantity,
            $image,
            $description,
            $status
        );

        if ($dao->insert($product)) {
            // Lấy ID vừa tạo để lưu gallery
            $productId = $dao->getLastInsertId();

            // 4. Upload bộ sưu tập ảnh Gallery (Mục E.4 & E.5 trong Lab 8)
            if (isset($_FILES["images"]) && !empty($_FILES["images"]["name"][0])) {
                $galleryFiles = $_FILES["images"];
                $totalFiles = count($galleryFiles["name"]);

                for ($i = 0; $i < $totalFiles; $i++) {
                    $gName = $galleryFiles["name"][$i];
                    $gTmp  = $galleryFiles["tmp_name"][$i];
                    $gErr  = $galleryFiles["error"][$i];
                    $gSize = $galleryFiles["size"][$i];

                    if ($gErr == UPLOAD_ERR_OK && $gSize <= 200 * 1024) {
                        $gExt = strtolower(pathinfo($gName, PATHINFO_EXTENSION));
                        if (in_array($gExt, ["jpg", "jpeg", "png", "gif", "webp"])) {
                            $gImageName = time() . "_" . $i . "_" . $slug . "." . $gExt;
                            $gUploadPath = __DIR__ . "/../../../uploads/products/" . $gImageName;
                            
                            if (move_uploaded_file($gTmp, $gUploadPath)) {
                                $dao->insertImage($productId, $gImageName); // (Mục E.5)
                            }
                        }
                    }
                }
            }

            $_SESSION['success'] = "Thêm mới sản phẩm thành công!";
            header("Location: index.php");
            exit();
        } else {
            $errors[] = "Thêm sản phẩm thất bại.";
        }
    }
}

$pageTitle = "Thêm mới sản phẩm";
ob_start();
?>

<div class="card shadow-sm border-0">
    <div class="card-header bg-primary text-white">
        <h5 class="m-0"><i class="fa-solid fa-plus me-2"></i>Thêm mới sản phẩm</h5>
    </div>
    <div class="card-body">

        <!-- Hiển thị lỗi Validation (Mục B.8) -->
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul class="mb-0 ps-3">
                    <?php foreach ($errors as $err): ?>
                        <li><?= $err ?></li>
                    <?php endforeach; ?>
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Form bắt buộc dùng enctype="multipart/form-data" (Mục B.1) -->
        <form action="create.php" method="POST" enctype="multipart/form-data">

            <div class="mb-3">
                <label class="form-label font-weight-bold">Tên sản phẩm <span class="text-danger">*</span></label>
                <input type="text" name="proName" class="form-control" placeholder="Nhập tên sản phẩm..." value="<?= $proName ?>">
            </div>

            <div class="mb-3">
                <label class="form-label font-weight-bold">Slug <span class="text-danger">*</span></label>
                <input type="text" name="slug" class="form-control" placeholder="nhap-ten-san-pham" value="<?= $slug ?>">
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label font-weight-bold">Danh mục <span class="text-danger">*</span></label>
                    <select name="categoryId" class="form-select">
                        <option value="0">-- Chọn danh mục --</option>
                        <?php foreach ($categories as $item): ?>
                            <option value="<?= $item->id ?>" <?= $categoryId == $item->id ? "selected" : "" ?>>
                                <?= $item->cateName ?? $item->catename ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label font-weight-bold">Thương hiệu <span class="text-danger">*</span></label>
                    <select name="brandId" class="form-select">
                        <option value="0">-- Chọn thương hiệu --</option>
                        <?php foreach ($brands as $item): ?>
                            <option value="<?= $item->id ?>" <?= $brandId == $item->id ? "selected" : "" ?>>
                                <?= $item->brandName ?? $item->brandname ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label font-weight-bold">Giá bán (VNĐ) <span class="text-danger">*</span></label>
                    <input type="number" name="price" class="form-control" placeholder="0" value="<?= $price ?>">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label font-weight-bold">Giá khuyến mãi (VNĐ)</label>
                    <input type="number" name="discountPrice" class="form-control" placeholder="0" value="<?= $discountPrice ?>">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label font-weight-bold">Số lượng tồn kho <span class="text-danger">*</span></label>
                    <input type="number" name="quantity" class="form-control" placeholder="0" value="<?= $quantity ?>">
                </div>
            </div>

            <!-- Vùng Upload ảnh đại diện & Gallery xem trước (Mục B.2 & Mục E.2) -->
            <div class="row mb-3">
                <div class="col-md-6 mb-3">
                    <label class="form-label font-weight-bold">Hình ảnh đại diện (<= 200KB)</label>
                    <div class="text-center mb-2" id="preview"></div>
                    <input type="file" id="image" name="image" class="form-control" accept="image/*">
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label font-weight-bold">Bộ sưu tập hình ảnh (Gallery):</label>
                    <div class="text-center mb-2" id="gallery_preview"></div>
                    <input type="file" name="images[]" id="images" class="form-control" accept="image/*" multiple>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label font-weight-bold">Mô tả sản phẩm</label>
                <textarea name="description" class="form-control" rows="4" placeholder="Nhập mô tả sản phẩm..."><?= $description ?></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label d-block font-weight-bold">Trạng thái</label>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="status" id="st1" value="1" <?= $status == 1 ? "checked" : "" ?>>
                    <label class="form-check-label" for="st1">Đang bán</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="status" id="st0" value="0" <?= $status == 0 ? "checked" : "" ?>>
                    <label class="form-check-label" for="st0">Ngừng bán</label>
                </div>
            </div>

            <button type="submit" class="btn btn-primary me-2"><i class="fa-solid fa-floppy-disk me-1"></i>Lưu sản phẩm</button>
            <button type="reset" class="btn btn-warning me-2"><i class="fa-solid fa-rotate-left me-1"></i>Làm mới</button>
            <a href="index.php" class="btn btn-secondary"><i class="fa-solid fa-arrow-left me-1"></i>Quay lại</a>
        </form>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . "/../layouts/master.php";
?>