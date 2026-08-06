<?php
require_once __DIR__ . "/../../../dao/CategoryDAO.php";

$dao = new CategoryDAO();

// Nhận id từ trang index.php
$id = (int)($_GET["id"] ?? 0);

// Gọi phương thức findById() (hoặc getById()) trong CategoryDAO để lấy thông tin danh mục
$category = $dao->findById($id);

if (!$category) {
    header("Location: index.php");
    exit();
}

$errors = [];

// Đọc dữ liệu từ các điều khiển trên Form bằng phương thức POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $cateName    = trim($_POST["cateName"] ?? "");
    $slug        = trim($_POST["slug"] ?? "");
    $description = trim($_POST["description"] ?? "");
    $status      = (int)($_POST["status"] ?? 1);

    // Kiểm tra dữ liệu đầu vào (Validation)
    if ($cateName == "") {
        $errors[] = "Tên danh mục không được để trống.";
    }
    if ($slug == "") {
        $errors[] = "Slug không được để trống.";
    }

    if (empty($errors)) {
        $category->catename    = $cateName;
        $category->slug        = $slug;
        $category->description = $description;
        $category->status      = $status;

        // Gọi phương thức update() trong CategoryDAO để cập nhật dữ liệu
        if ($dao->update($category)) {
            // Sau khi cập nhật thành công: Chuyển về trang index.php
            header("Location: index.php");
            exit();
        } else {
            $errors[] = "Cập nhật thất bại.";
        }
    }
}

$pageTitle = "Cập nhật danh mục";
ob_start();
?>

<div class="container mt-4">
    <div class="card">
        <div class="card-header">
            <h4>Cập nhật danh mục</h4>
        </div>
        <div class="card-body">
            
            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        <?php foreach ($errors as $err): ?>
                            <li><?= htmlspecialchars($err) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form method="POST">
                <!-- ID -->
                <input type="hidden" name="categoryId" value="<?= $category->id ?>">
                
                <!-- Tên danh mục -->
                <div class="mb-3">
                    <label class="form-label">Tên danh mục </label>
                    <input type="text" name="cateName" class="form-control" value="<?= htmlspecialchars($category->catename) ?>">
                </div>

                <!-- Slug -->
                <div class="mb-3">
                    <label class="form-label">Slug</label>
                    <input type="text" name="slug" class="form-control" value="<?= htmlspecialchars($category->slug) ?>">
                </div>

                <!-- Mô tả -->
                <div class="mb-3">
                    <label class="form-label">Mô tả</label>
                    <textarea name="description" rows="5" class="form-control"><?= htmlspecialchars($category->description ?? '') ?></textarea>
                </div>

                <!-- Trạng thái -->
                <div class="mb-3">
                    <label class="form-label d-block">Trạng thái</label>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="status" value="1" <?= $category->status == 1 ? "checked" : "" ?>>
                        <label class="form-check-label">Hiển thị </label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="status" value="0" <?= $category->status == 0 ? "checked" : "" ?>>
                        <label class="form-check-label"> Ẩn </label>
                    </div>
                </div>

                <!-- Button -->
                <button type="submit" class="btn btn-primary"> Cập nhật</button>
                <button type="reset" class="btn btn-warning"> Làm mới</button>
                <a href="index.php" class="btn btn-secondary"> Quay lại </a>
            </form>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . "/../layouts/master.php";
?>