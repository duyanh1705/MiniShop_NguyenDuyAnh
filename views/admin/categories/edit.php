<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/../../../dao/CategoryDAO.php";

$dao = new CategoryDAO();
$id = (int)($_GET["id"] ?? 0);

// Gọi DAO lấy đối tượng Model Category theo ID
$category = $dao->findById($id);

if (!$category) {
    $_SESSION['error'] = "Không tìm thấy danh mục cần cập nhật.";
    header("Location: index.php");
    exit();
}

$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $cateName    = trim($_POST["cateName"] ?? "");
    $slug        = trim($_POST["slug"] ?? "");
    $description = trim($_POST["description"] ?? "");
    $status      = (int)($_POST["status"] ?? 1);

    // 1. Validation dữ liệu text
    if (empty($cateName)) {
        $errors[] = "Tên danh mục không được để trống.";
    }

    if (empty($slug)) {
        $errors[] = "Slug không được để trống.";
    }

    // 2. Validation & Upload Hình ảnh Danh mục (Câu F - Lab 8)
    $fileName = $_FILES["image"]["name"] ?? "";
    $tmpName  = $_FILES["image"]["tmp_name"] ?? "";
    $fileSize = $_FILES["image"]["size"] ?? 0;
    $error    = $_FILES["image"]["error"] ?? 0;
    $imageName = $category->image; // Giữ nguyên tên ảnh cũ làm mặc định

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
                $imageName = time() . "_" . $slug . "." . $extension;
                $uploadDir = __DIR__ . "/../../../uploads/categories/";

                // Xóa file hình ảnh cũ nếu tồn tại
                if (!empty($category->image)) {
                    $oldPath = $uploadDir . $category->image;
                    if (file_exists($oldPath)) {
                        @unlink($oldPath);
                    }
                }

                // Di chuyển file mới vào thư mục uploads/categories/
                move_uploaded_file($tmpName, $uploadDir . $imageName);
            }
        }
    }

    if (empty($errors)) {
        // Cập nhật giá trị vào thuộc tính đối tượng Model
        if (property_exists($category, 'cateName')) {
            $category->cateName = $cateName;
        } else {
            $category->catename = $cateName;
        }
        
        $category->slug        = $slug;
        $category->image       = $imageName; // Cập nhật tên file hình ảnh mới
        $category->description = $description;
        $category->status      = $status;

        // Truyền đối tượng Model vào hàm update()
        if ($dao->update($category)) {
            $_SESSION['success'] = "Cập nhật danh mục thành công!";
            header("Location: index.php");
            exit();
        } else {
            $errors[] = "Cập nhật thất bại. Vui lòng thử lại.";
        }
    }
}

$pageTitle = "Cập nhật danh mục";
ob_start();
?>

<div class="card shadow-sm border-0">
    <div class="card-header bg-warning text-dark">
        <h5 class="m-0"><i class="fa-solid fa-pen-to-square me-2"></i>Cập nhật danh mục</h5>
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

        <!-- Thêm enctype="multipart/form-data" để hỗ trợ upload file -->
        <form action="edit.php?id=<?= $category->id ?>" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="categoryId" value="<?= $category->id ?>">

            <div class="mb-3">
                <label class="form-label font-weight-bold">Tên danh mục <span class="text-danger">*</span></label>
                <input type="text" name="cateName" class="form-control" value="<?= $category->cateName ?? $category->catename ?>">
            </div>

            <div class="mb-3">
                <label class="form-label font-weight-bold">Slug <span class="text-danger">*</span></label>
                <input type="text" name="slug" class="form-control" value="<?= $category->slug ?>">
            </div>

            <!-- Bổ sung vùng Hiển thị Ảnh hiện tại & Chọn Ảnh mới (Mục F - Lab 8) -->
            <div class="mb-3">
                <label class="form-label font-weight-bold d-block">Hình ảnh đại diện hiện tại:</label>
                <div class="mb-2" id="preview">
                    <?php if (!empty($category->image) && file_exists(__DIR__ . "/../../../uploads/categories/" . $category->image)): ?>
                        <img src="../../../uploads/categories/<?= $category->image ?>" alt="<?= $category->catename ?>" class="img-thumbnail" width="120">
                    <?php else: ?>
                        <span class="text-muted small">Chưa có hình ảnh</span>
                    <?php endif; ?>
                </div>
                <label class="form-label font-weight-bold">Chọn hình ảnh mới (nếu muốn đổi):</label>
                <input type="file" id="image" name="image" class="form-control" accept="image/*">
            </div>

            <div class="mb-3">
                <label class="form-label font-weight-bold">Mô tả</label>
                <textarea name="description" class="form-control" rows="4"><?= $category->description ?? '' ?></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label d-block font-weight-bold">Trạng thái</label>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="status" id="status1" value="1" <?= $category->status == 1 ? "checked" : "" ?>>
                    <label class="form-check-label" for="status1">Hiển thị / Hoạt động</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="status" id="status0" value="0" <?= $category->status == 0 ? "checked" : "" ?>>
                    <label class="form-check-label" for="status0">Ẩn / Ngừng hoạt động</label>
                </div>
            </div>

            <button type="submit" class="btn btn-primary me-2"><i class="fa-solid fa-pen me-1"></i>Cập nhật</button>
            <button type="reset" class="btn btn-warning me-2"><i class="fa-solid fa-rotate-left me-1"></i>Làm mới</button>
            <a href="index.php" class="btn btn-secondary"><i class="fa-solid fa-arrow-left me-1"></i>Quay lại</a>
        </form>
    </div>
</div>
<?php
$content = ob_get_clean();
include __DIR__ . "/../layouts/master.php";
?>