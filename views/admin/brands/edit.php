<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/../../../dao/BrandDAO.php";

$dao = new BrandDAO();
$id = (int)($_GET["id"] ?? 0);

$brand = $dao->findById($id);

if (!$brand) {
    $_SESSION['error'] = "Không tìm thấy thương hiệu cần cập nhật.";
    header("Location: index.php");
    exit();
}

$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $brandName   = trim($_POST["brandName"] ?? "");
    $slug        = trim($_POST["slug"] ?? "");
    $description = trim($_POST["description"] ?? "");
    $status      = (int)($_POST["status"] ?? 1);

    if (empty($brandName)) {
        $errors[] = "Tên thương hiệu không được để trống.";
    }

    if (empty($slug)) {
        $errors[] = "Slug không được để trống.";
    }

    if (empty($errors)) {
        // Cập nhật giá trị vào thuộc tính của đối tượng Model Brand
        $brand->brandName   = $brandName;
        $brand->slug        = $slug;
        $brand->description = $description;
        $brand->status      = $status;

        if ($dao->update($brand)) {
            $_SESSION['success'] = "Cập nhật thương hiệu thành công!";
            header("Location: index.php");
            exit();
        } else {
            $errors[] = "Cập nhật thất bại. Vui lòng thử lại.";
        }
    }
}

$pageTitle = "Cập nhật thương hiệu";
ob_start();
?>

<div class="card shadow-sm border-0">
    <div class="card-header bg-warning text-dark">
        <h5 class="m-0"><i class="fa-solid fa-pen-to-square me-2"></i>Cập nhật thương hiệu</h5>
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

        <form action="edit.php?id=<?= $brand->id ?>" method="POST">
            <input type="hidden" name="id" value="<?= $brand->id ?>">

            <div class="mb-3">
                <label class="form-label font-weight-bold">Tên thương hiệu <span class="text-danger">*</span></label>
                <input type="text" name="brandName" class="form-control" value="<?= $brand->brandName ?>">
            </div>

            <div class="mb-3">
                <label class="form-label font-weight-bold">Slug <span class="text-danger">*</span></label>
                <input type="text" name="slug" class="form-control" value="<?= $brand->slug ?>">
            </div>

            <div class="mb-3">
                <label class="form-label font-weight-bold">Mô tả</label>
                <textarea name="description" class="form-control" rows="4"><?= $brand->description ?? '' ?></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label d-block font-weight-bold">Trạng thái</label>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="status" id="status1" value="1" <?= $brand->status == 1 ? "checked" : "" ?>>
                    <label class="form-check-label" for="status1">Hiển thị / Hoạt động</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="status" id="status0" value="0" <?= $brand->status == 0 ? "checked" : "" ?>>
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