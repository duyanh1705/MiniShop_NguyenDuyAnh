<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/../../../dao/CategoryDAO.php";
require_once __DIR__ . "/../../../models/Category.php";

$errors = [];
$cateName = "";
$slug = "";
$description = "";
$status = 1;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $cateName    = trim($_POST["cateName"] ?? "");
    $slug        = trim($_POST["slug"] ?? "");
    $description = trim($_POST["description"] ?? "");
    $status      = isset($_POST["status"]) ? (int)$_POST["status"] : 1;

    // 1. Validation dữ liệu text
    if (empty($cateName)) {
        $errors[] = "Tên danh mục không được để trống.";
    }

    if (empty($slug)) {
        $errors[] = "Slug không được để trống.";
    }

    // 2. Validation Hình ảnh Danh mục (Mục F - Lab 8)
    $fileName = $_FILES["image"]["name"] ?? "";
    $tmpName  = $_FILES["image"]["tmp_name"] ?? "";
    $fileSize = $_FILES["image"]["size"] ?? 0;
    $error    = $_FILES["image"]["error"] ?? 0;
    $image    = null;

    if (!empty($fileName)) {
        if ($error != UPLOAD_ERR_OK) {
            $errors[] = "Upload hình ảnh thất bại.";
        } else {
            $allowExtensions = ["jpg", "jpeg", "png", "gif", "webp"];
            $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

            if (!in_array($extension, $allowExtensions)) {
                $errors[] = "Chỉ cho phép file ảnh JPG, JPEG, PNG, GIF hoặc WEBP.";
            }

            if ($fileSize > 200 * 1024) {
                $errors[] = "Kích thước hình ảnh phải <= 200 KB.";
            }
        }
    }

    // 3. Tiến hành Upload & Lưu CSDL
    if (empty($errors)) {
        if (!empty($fileName)) {
            $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            $image = time() . "_" . $slug . "." . $extension;
            $uploadPath = __DIR__ . "/../../../uploads/categories/" . $image;
            move_uploaded_file($tmpName, $uploadPath);
        }

        $dao = new CategoryDAO();

        // Khởi tạo đối tượng Model Category bao gồm tham số $image
        $category = new Category($cateName, $slug, $image, $description, $status);

        if ($dao->insert($category)) {
            $_SESSION['success'] = "Thêm mới danh mục thành công!";
            header("Location: index.php");
            exit();
        } else {
            $errors[] = "Có lỗi xảy ra khi thêm danh mục vào cơ sở dữ liệu.";
        }
    }
}

$pageTitle = "Thêm mới danh mục";
ob_start();
?>

<div class="card shadow-sm border-0">
    <div class="card-header bg-primary text-white">
        <h5 class="m-0"><i class="fa-solid fa-plus me-2"></i>Thêm mới danh mục</h5>
    </div>
    <div class="card-body">

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

        <!-- Form bắt buộc có enctype="multipart/form-data" -->
        <form action="create.php" method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label class="form-label font-weight-bold">Tên danh mục <span class="text-danger">*</span></label>
                <input type="text" name="cateName" class="form-control" placeholder="Nhập tên danh mục..." value="<?= $cateName ?>">
            </div>

            <div class="mb-3">
                <label class="form-label font-weight-bold">Slug <span class="text-danger">*</span></label>
                <input type="text" name="slug" class="form-control" placeholder="nhap-ten-danh-muc" value="<?= $slug ?>">
            </div>

            <!-- Bổ sung Khung chọn Hình ảnh & Xem trước (Preview) -->
            <div class="mb-3">
                <label class="form-label font-weight-bold">Hình ảnh đại diện (<= 200KB)</label>
                        <div class="mb-2" id="preview"></div>
                        <input type="file" id="image" name="image" class="form-control" accept="image/*">
            </div>

            <div class="mb-3">
                <label class="form-label font-weight-bold">Mô tả</label>
                <textarea name="description" class="form-control" rows="4" placeholder="Nhập mô tả ngắn cho danh mục..."><?= $description ?></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label d-block font-weight-bold">Trạng thái</label>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="status" id="status1" value="1" <?= $status == 1 ? "checked" : "" ?>>
                    <label class="form-check-label" for="status1">Hiển thị / Hoạt động</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="status" id="status0" value="0" <?= $status == 0 ? "checked" : "" ?>>
                    <label class="form-check-label" for="status0">Ẩn / Ngừng hoạt động</label>
                </div>
            </div>

            <button type="submit" class="btn btn-primary me-2"><i class="fa-solid fa-floppy-disk me-1"></i>Lưu</button>
            <button type="reset" class="btn btn-warning me-2"><i class="fa-solid fa-rotate-left me-1"></i>Làm mới</button>
            <a href="index.php" class="btn btn-secondary"><i class="fa-solid fa-arrow-left me-1"></i>Quay lại</a>
        </form>
    </div>
</div>
<?php
$content = ob_get_clean();
include __DIR__ . "/../layouts/master.php";
?>