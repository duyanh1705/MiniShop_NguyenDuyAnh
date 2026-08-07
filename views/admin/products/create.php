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

// Lấy danh sách Categories và Brands để đổ vào 2 thẻ <select> (Mục D.2)
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

// Đọc dữ liệu gửi từ Form bằng phương thức POST
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

    // Kiểm tra dữ liệu đầu vào (Validation theo yêu cầu Mục D.2)
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

    // Nếu dữ liệu hợp lệ -> Khởi tạo đối tượng Product và lưu vào CSDL
    if (empty($errors)) {
        $dao = new ProductDAO();

        $product = new Product(
            $categoryId,
            $brandId,
            $proName,
            $slug,
            $price,
            $discountPrice,
            $quantity,
            null, // image
            $description,
            $status
        );

        if ($dao->insert($product)) {
            $_SESSION['success'] = "Thêm mới sản phẩm thành công!";
            header("Location: index.php");
            exit();
        } else {
            $errors[] = "Có lỗi xảy ra khi thêm sản phẩm vào cơ sở dữ liệu.";
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

        <!-- Hiển thị danh sách thông báo lỗi Validation nếu có -->
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

        <!-- Form thêm mới sản phẩm -->
        <form action="create.php" method="POST">

            <!-- Tên sản phẩm -->
            <div class="mb-3">
                <label class="form-label font-weight-bold">Tên sản phẩm <span class="text-danger">*</span></label>
                <input type="text" name="proName" class="form-control" placeholder="Nhập tên sản phẩm..." value="<?= $proName ?>">
            </div>

            <!-- Slug -->
            <div class="mb-3">
                <label class="form-label font-weight-bold">Slug <span class="text-danger">*</span></label>
                <input type="text" name="slug" class="form-control" placeholder="nhap-ten-san-pham" value="<?= $slug ?>">
            </div>

            <!-- Thẻ <select> chọn Danh mục và Thương hiệu (Mục D.2) -->
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

            <!-- Giá bán, Giá khuyến mãi và Số lượng -->
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

            <!-- Mô tả sản phẩm -->
            <div class="mb-3">
                <label class="form-label font-weight-bold">Mô tả sản phẩm</label>
                <textarea name="description" class="form-control" rows="4" placeholder="Nhập mô tả sản phẩm..."><?= $description ?></textarea>
            </div>

            <!-- Trạng thái -->
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

            <!-- Nút thao tác -->
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