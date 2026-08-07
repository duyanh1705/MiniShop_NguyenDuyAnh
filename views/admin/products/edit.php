<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/../../../dao/ProductDAO.php";
require_once __DIR__ . "/../../../dao/CategoryDAO.php";
require_once __DIR__ . "/../../../dao/BrandDAO.php";

$productDAO = new ProductDAO();
$categoryDAO = new CategoryDAO();
$brandDAO = new BrandDAO();

// 1. Nhận id từ URL (Mục D.3)[cite: 1]
$id = (int)($_GET["id"] ?? 0);
$product = $productDAO->findById($id);

if (!$product) {
    $_SESSION['error'] = "Không tìm thấy sản phẩm cần cập nhật.";
    header("Location: index.php");
    exit();
}

// 2. Lấy danh sách Categories và Brands để đổ vào <select>[cite: 1]
$categories = $categoryDAO->getAll();
$brands = $brandDAO->getAll();

$errors = [];

// 3. Đọc dữ liệu từ Form khi gửi POST
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

    // Validation
    if (empty($proName)) { $errors[] = "Tên sản phẩm không được để trống."; }
    if (empty($slug)) { $errors[] = "Slug không được để trống."; }
    if ($categoryId == 0) { $errors[] = "Vui lòng chọn danh mục."; }
    if ($brandId == 0) { $errors[] = "Vui lòng chọn thương hiệu."; }
    if ($price <= 0) { $errors[] = "Giá bán phải lớn hơn 0."; }
    if ($quantity < 0) { $errors[] = "Số lượng không hợp lệ."; }

    if (empty($errors)) {
        // Cập nhật giá trị vào thuộc tính của Model Product
        $product->proName       = $proName;
        $product->slug          = $slug;
        $product->categoryId    = $categoryId;
        $product->brandId       = $brandId;
        $product->price         = $price;
        $product->discountPrice = $discountPrice;
        $product->quantity      = $quantity;
        $product->description   = $description;
        $product->status        = $status;

        // Gọi phương thức update() trong ProductDAO[cite: 1]
        if ($productDAO->update($product)) {
            $_SESSION['success'] = "Cập nhật sản phẩm thành công!";
            header("Location: index.php");
            exit();
        } else {
            $errors[] = "Cập nhật thất bại. Vui lòng thử lại.";
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

        <form action="edit.php?id=<?= $product->id ?>" method="POST">
            <input type="hidden" name="id" value="<?= $product->id ?>">

            <div class="mb-3">
                <label class="form-label font-weight-bold">Tên sản phẩm <span class="text-danger">*</span></label>
                <input type="text" name="proName" class="form-control" value="<?= $product->proName ?>">
            </div>

            <div class="mb-3">
                <label class="form-label font-weight-bold">Slug <span class="text-danger">*</span></label>
                <input type="text" name="slug" class="form-control" value="<?= $product->slug ?>">
            </div>

            <!-- Tự động chọn (selected) đúng danh mục và thương hiệu (Mục D.3 trong Lab 7)[cite: 1] -->
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label font-weight-bold">Danh mục <span class="text-danger">*</span></label>
                    <select name="categoryId" class="form-select">
                        <option value="0">-- Chọn danh mục --</option>
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
                        <option value="0">-- Chọn thương hiệu --</option>
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