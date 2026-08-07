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

    if (empty($cateName)) {
        $errors[] = "Tên danh mục không được để trống.";
    }

    if (empty($slug)) {
        $errors[] = "Slug không được để trống.";
    }

    if (empty($errors)) {
        // Cập nhật giá trị trực tiếp vào các thuộc tính đối tượng Model
        if (property_exists($category, 'cateName')) {
            $category->cateName = $cateName;
        } else {
            $category->catename = $cateName;
        }
        
        $category->slug        = $slug;
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

        <form action="edit.php?id=<?= $category->id ?? $category->id ?>" method="POST">
            <input type="hidden" name="categoryId" value="<?= $category->id ?? $category->id ?>">

            <div class="mb-3">
                <label class="form-label font-weight-bold">Tên danh mục <span class="text-danger">*</span></label>
                <input type="text" name="cateName" class="form-control" value="<?= $category->cateName ?? $category->catename ?>">
            </div>

            <div class="mb-3">
                <label class="form-label font-weight-bold">Slug <span class="text-danger">*</span></label>
                <input type="text" name="slug" class="form-control" value="<?= $category->slug ?>">
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