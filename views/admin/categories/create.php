<?php
require_once __DIR__ . "/../../../dao/CategoryDAO.php";

$errors = [];
$cateName = "";
$slug = "";
$description = "";
$status = 1;

// Xử lý khi người dùng nhấn nút Lưu (Gửi Form phương thức POST)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Đọc dữ liệu từ Form và loại bỏ khoảng trắng thừa
    $cateName    = trim($_POST["cateName"] ?? "");
    $slug        = trim($_POST["slug"] ?? "");
    $description = trim($_POST["description"] ?? "");
    $status      = isset($_POST["status"]) ? (int)$_POST["status"] : 1;

    // Kiểm tra dữ liệu đầu vào (Validation)
    if (empty($cateName)) {
        $errors[] = "Tên danh mục không được để trống.";
    }

    if (empty($slug)) {
        $errors[] = "Slug không được để trống.";
    }

    // Nếu không có lỗi validation
    if (empty($errors)) {
        $dao = new CategoryDAO();
        
        // Khởi tạo đối tượng Category từ thông tin nhập vào
        $category = new Category($cateName, $slug, null, $description, $status);

        // Gọi phương thức insert() để lưu dữ liệu
        if ($dao->insert($category)) {
            // Thêm thành công -> Chuyển hướng về trang danh sách
            header("Location: index.php");
            exit();
        } else {
            $errors[] = "Có lỗi xảy ra khi thêm danh mục vào cơ sở dữ liệu.";
        }
    }
}

$pageTitle = "Thêm mới danh mục";

// Bắt đầu lưu bộ nhớ đệm (Output Buffering)
ob_start();
?>

<div class="card shadow-sm border-0">
    <div class="card-header bg-primary text-white">
        <h5 class="m-0"><i class="fa-solid fa-plus me-2"></i>Thêm mới danh mục</h5>
    </div>
    <div class="card-body">
        
        <!-- Hiển thị danh sách thông báo lỗi nếu Validation thất bại -->
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul class="mb-0 ps-3">
                    <?php foreach ($errors as $err): ?>
                        <li><?= htmlspecialchars($err) ?></li>
                    <?php endforeach; ?>
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Form thêm mới danh mục -->
        <form action="create.php" method="POST">
            
            <!-- Tên danh mục -->
            <div class="mb-3">
                <label class="form-label font-weight-bold">Tên danh mục <span class="text-danger">*</span></label>
                <input type="text" name="cateName" class="form-control" placeholder="Nhập tên danh mục..." value="<?= htmlspecialchars($cateName) ?>">
            </div>

            <!-- Slug -->
            <div class="mb-3">
                <label class="form-label font-weight-bold">Slug <span class="text-danger">*</span></label>
                <input type="text" name="slug" class="form-control" placeholder="nhap-ten-danh-muc" value="<?= htmlspecialchars($slug) ?>">
            </div>

            <!-- Mô tả -->
            <div class="mb-3">
                <label class="form-label font-weight-bold">Mô tả</label>
                <textarea name="description" class="form-control" rows="4" placeholder="Nhập mô tả ngắn cho danh mục..."><?= htmlspecialchars($description) ?></textarea>
            </div>

            <!-- Trạng thái -->
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

            <!-- Các nút bấm chức năng -->
            <button type="submit" class="btn btn-primary me-2"><i class="fa-solid fa-floppy-disk me-1"></i>Lưu</button>
            <button type="reset" class="btn btn-warning me-2"><i class="fa-solid fa-rotate-left me-1"></i>Làm mới</button>
            <a href="index.php" class="btn btn-secondary"><i class="fa-solid fa-arrow-left me-1"></i>Quay lại</a>

        </form>
    </div>
</div>

<?php
// Thu gom toàn bộ HTML gán vào $content
$content = ob_get_clean();

// Bơm vào khung Layout chung master.php
include __DIR__ . "/../layouts/master.php";
?>