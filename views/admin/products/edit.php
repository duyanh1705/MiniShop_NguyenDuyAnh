<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/../../../dao/ProductDAO.php";
require_once __DIR__ . "/../../../dao/CategoryDAO.php";
require_once __DIR__ . "/../../../dao/BrandDAO.php";
require_once __DIR__ . "/../../../models/Product.php";

$productDAO = new ProductDAO();
$categoryDAO = new CategoryDAO();
$brandDAO = new BrandDAO();

// 1. Nhận productId từ URL
$id = (int)($_GET["id"] ?? 0);
$product = $productDAO->findById($id);

if (!$product) {
    $_SESSION['error'] = "Không tìm thấy sản phẩm.";
    header("Location: index.php");
    exit();
}

// 2. Xử lý XÓA 1 ÁNH GALLERY CỤ THỂ (Mục G - Làm thêm)[cite: 2]
if (isset($_GET["action"]) && $_GET["action"] === "delete_gallery") {
    $imageId = (int)($_GET["image_id"] ?? 0);
    if ($imageId > 0) {
        $productDAO->deleteImage($imageId);
        $_SESSION['success'] = "Xóa ảnh phụ gallery thành công!";
        header("Location: edit.php?id={$id}");
        exit();
    }
}

$categories = $categoryDAO->getAll();
$brands = $brandDAO->getAll();
$galleryImages = $productDAO->getImagesByProductId($id);
$errors = [];

// 3. Đọc dữ liệu từ Form khi submit (Mục C)[cite: 2]
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

    // Validation cơ bản
    if (empty($proName)) { $errors[] = "Tên sản phẩm không được để trống."; }
    if (empty($slug)) { $errors[] = "Slug không được để trống."; }
    if ($categoryId == 0) { $errors[] = "Vui lòng chọn danh mục."; }
    if ($brandId == 0) { $errors[] = "Vui lòng chọn thương hiệu."; }
    if ($price <= 0) { $errors[] = "Giá bán phải lớn hơn 0."; }
    if ($quantity < 0) { $errors[] = "Số lượng không hợp lệ."; }

    // Xử lý CẬP NHẬT HÌNH ẢNH ĐẠI DIỆN CHÍNH (Mục C)[cite: 2]
    $fileName = $_FILES["image"]["name"] ?? "";
    $tmpName  = $_FILES["image"]["tmp_name"] ?? "";
    $fileSize = $_FILES["image"]["size"] ?? 0;
    $error    = $_FILES["image"]["error"] ?? 0;
    $imageName = $product->image; // Mặc định giữ nguyên hình ảnh cũ[cite: 2]

    // Có chọn hình ảnh mới[cite: 2]
    if (!empty($fileName)) {
        if ($error != UPLOAD_ERR_OK) {
            $errors[] = "Upload hình ảnh mới thất bại.";
        } else {
            $allowExtensions = ["jpg", "jpeg", "png", "gif", "webp"];
            $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

            if (!in_array($extension, $allowExtensions)) {
                $errors[] = "Chỉ cho phép file ảnh JPG, JPEG, PNG, GIF hoặc WEBP.";
            }

            if ($fileSize > 200 * 1024) {
                $errors[] = "Kích thước hình ảnh mới phải <= 200 KB.";
            }

            if (empty($errors)) {
                // Đổi tên file mới: time_slug.extension[cite: 2]
                $imageName = time() . "_" . $slug . "." . $extension;
                $uploadDir = __DIR__ . "/../../../uploads/products/";

                // Xóa hình ảnh cũ khỏi thư mục uploads/products/ nếu tồn tại (Mục C)[cite: 2]
                if (!empty($product->image)) {
                    $oldImagePath = $uploadDir . $product->image;
                    if (file_exists($oldImagePath)) {
                        @unlink($oldImagePath); // Xóa file cũ[cite: 2]
                    }
                }

                // Upload hình ảnh mới[cite: 2]
                move_uploaded_file($tmpName, $uploadDir . $imageName);
            }
        }
    }

    // Nếu dữ liệu hợp lệ -> Cập nhật CSDL
    if (empty($errors)) {
        $product->proName       = $proName;
        $product->slug          = $slug;
        $product->categoryId    = $categoryId;
        $product->brandId       = $brandId;
        $product->price         = $price;
        $product->discountPrice = $discountPrice;
        $product->quantity      = $quantity;
        $product->image         = $imageName; // Cập nhật tên file mới vào CSDL[cite: 2]
        $product->description   = $description;
        $product->status        = $status;

        if ($productDAO->update($product)) {
            // Upload bổ sung thêm các ảnh Gallery phụ nếu người dùng chọn chọn thêm[cite: 2]
            if (isset($_FILES["images"]) && !empty($_FILES["images"]["name"][0])) {
                $galleryFiles = $_FILES["images"];
                $totalGallery = count($galleryFiles["name"]);

                for ($i = 0; $i < $totalGallery; $i++) {
                    $gName = $galleryFiles["name"][$i];
                    $gTmp  = $galleryFiles["tmp_name"][$i];
                    $gErr  = $galleryFiles["error"][$i];
                    $gSize = $galleryFiles["size"][$i];

                    if ($gErr == UPLOAD_ERR_OK && $gSize <= 200 * 1024) {
                        $gExt = strtolower(pathinfo($gName, PATHINFO_EXTENSION));
                        if (in_array($gExt, ["jpg", "jpeg", "png", "gif", "webp"])) {
                            $gImageName = time() . "_" . $i . "_" . $slug . "." . $gExt;
                            if (move_uploaded_file($gTmp, __DIR__ . "/../../../uploads/products/" . $gImageName)) {
                                $productDAO->insertImage($product->id, $gImageName);
                            }
                        }
                    }
                }
            }

            $_SESSION['success'] = "Cập nhật thông tin và hình ảnh sản phẩm thành công!";
            header("Location: index.php"); // Sau khi cập nhật thành công, chuyển về index.php[cite: 2]
            exit();
        } else {
            $errors[] = "Cập nhật sản phẩm vào cơ sở dữ liệu thất bại.";
        }
    }
}

$pageTitle = "Cập nhật sản phẩm";
ob_start();
?>

<div class="card shadow-sm border-0">
    <div class="card-header bg-warning text-dark">
        <h5 class="m-0"><i class="fa-solid fa-pen-to-square me-2"></i>Cập nhật sản phẩm</h5>
    </div>
    <div class="card-body">

        <!-- Thông báo lỗi Validation -->
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

        <form action="edit.php?id=<?= $product->id ?>" method="POST" enctype="multipart/form-data">

            <div class="mb-3">
                <label class="form-label font-weight-bold">Tên sản phẩm <span class="text-danger">*</span></label>
                <input type="text" name="proName" class="form-control" value="<?= $product->proName ?>">
            </div>

            <div class="mb-3">
                <label class="form-label font-weight-bold">Slug <span class="text-danger">*</span></label>
                <input type="text" name="slug" class="form-control" value="<?= $product->slug ?>">
            </div>

            <!-- Tự động chọn (selected) đúng Danh mục và Thương hiệu hiện tại -->
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label font-weight-bold">Danh mục <span class="text-danger">*</span></label>
                    <select name="categoryId" class="form-select">
                        <?php foreach ($categories as $item): ?>
                            <option value="<?= $item->id ?>" <?= $item->id == $product->categoryId ? "selected" : "" ?>>
                                <?= $item->cateName ?? $item->catename ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label font-weight-bold">Thương hiệu <span class="text-danger">*</span></label>
                    <select name="brandId" class="form-select">
                        <?php foreach ($brands as $item): ?>
                            <option value="<?= $item->id ?>" <?= $item->id == $product->brandId ? "selected" : "" ?>>
                                <?= $item->brandName ?? $item->brandname ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label font-weight-bold">Giá bán (VNĐ) <span class="text-danger">*</span></label>
                    <input type="number" name="price" class="form-control" value="<?= $product->price ?>">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label font-weight-bold">Giá khuyến mãi (VNĐ)</label>
                    <input type="number" name="discountPrice" class="form-control" value="<?= $product->discountPrice ?>">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label font-weight-bold">Số lượng tồn kho <span class="text-danger">*</span></label>
                    <input type="number" name="quantity" class="form-control" value="<?= $product->quantity ?>">
                </div>
            </div>

            <!-- QUẢN LÝ ÁNH ĐẠI DIỆN VÀ GALLERY (Mục C & E.6)[cite: 2] -->
            <div class="row mb-3">
                <!-- Hiển thị hình ảnh hiện tại & chọn hình ảnh mới (Mục C)[cite: 2] -->
                <div class="col-md-6">
                    <label class="form-label font-weight-bold d-block">Hình ảnh đại diện hiện tại:</label>
                    <div class="mb-2" id="preview">
                        <?php if (!empty($product->image) && file_exists(__DIR__ . "/../../../uploads/products/" . $product->image)): ?>
                            <img src="../../../uploads/products/<?= $product->image ?>" alt="<?= $product->proName ?>" class="img-thumbnail" width="150">
                        <?php else: ?>
                            <span class="text-muted small">Chưa có hình ảnh</span>
                        <?php endif; ?>
                    </div>
                    <label class="form-label font-weight-bold">Chọn hình ảnh mới (nếu muốn đổi):</label>
                    <input type="file" id="image" name="image" class="form-control" accept="image/*">
                </div>

                <!-- Thêm ảnh Gallery phụ mới[cite: 2] -->
                <div class="col-md-6">
                    <label class="form-label font-weight-bold d-block">Bộ sưu tập ảnh (Gallery phụ):</label>
                    <div class="mb-2" id="gallery_preview"></div>
                    <label class="form-label font-weight-bold">Chọn thêm ảnh Gallery mới (nếu có):</label>
                    <input type="file" id="images" name="images[]" class="form-control" accept="image/*" multiple>
                </div>
            </div>

            <!-- HIỂN THỊ DANH SÁCH ÁNH GALLERY HIỆN CÓ VÀ NÚT XÓA TỪNG ÁNH (MỤC G - LÀM THÊM)[cite: 2] -->
            <?php if (!empty($galleryImages)): ?>
                <div class="mb-3">
                    <label class="form-label font-weight-bold d-block">Danh sách ảnh phụ Gallery hiện tại (Nhấn nút Xóa để gỡ):</label>
                    <div class="d-flex flex-wrap gap-2">
                        <?php foreach ($galleryImages as $gImg): ?>
                            <div class="text-center border p-1 rounded bg-light">
                                <img src="../../../uploads/products/<?= $gImg["image"] ?>" class="img-thumbnail d-block mb-1" width="100" style="height: 80px; object-fit: cover;">
                                <a href="edit.php?id=<?= $product->id ?>&action=delete_gallery&image_id=<?= $gImg["id"] ?>" 
                                   class="btn btn-sm btn-danger py-0 px-2 w-100" 
                                   onclick="return confirm('Bạn có chắc muốn xóa ảnh phụ này?');">
                                    <i class="fa-solid fa-trash me-1"></i>Xóa
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <div class="mb-3">
                <label class="form-label font-weight-bold">Mô tả sản phẩm</label>
                <textarea name="description" class="form-control" rows="4"><?= $product->description ?? '' ?></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label d-block font-weight-bold">Trạng thái</label>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="status" id="st1" value="1" <?= $product->status == 1 ? "checked" : "" ?>>
                    <label class="form-check-label" for="st1">Đang bán</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="status" id="st0" value="0" <?= $product->status == 0 ? "checked" : "" ?>>
                    <label class="form-check-label" for="st0">Ngừng bán</label>
                </div>
            </div>

            <button type="submit" class="btn btn-primary me-2"><i class="fa-solid fa-floppy-disk me-1"></i>Lưu thay đổi</button>
            <a href="index.php" class="btn btn-secondary"><i class="fa-solid fa-arrow-left me-1"></i>Quay lại</a>
        </form>
    </div>
</div>
<?php
$content = ob_get_clean();
include __DIR__ . "/../layouts/master.php";
?>