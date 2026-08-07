<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/../../../dao/ProductDAO.php";

$dao = new ProductDAO();

// 1. Nhận productId từ URL
$id = (int)($_GET["id"] ?? 0);
$product = $dao->findById($id);

// Nếu không tìm thấy sản phẩm, quay về trang danh sách
if (!$product) {
    $_SESSION['error'] = "Không tìm thấy sản phẩm.";
    header("Location: index.php");
    exit();
}

// 2. Lấy danh sách ảnh phụ Gallery của sản phẩm (Mục E.6 trong Lab 8)
$galleryImages = $dao->getImagesByProductId($product->id);

$pageTitle = "Chi tiết sản phẩm - " . $product->proName;
ob_start();
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Chi tiết sản phẩm</h3>
    <div>
        <a href="edit.php?id=<?= $product->id ?>" class="btn btn-warning me-2">
            <i class="fa-solid fa-pen me-1"></i>Chỉnh sửa
        </a>
        <a href="index.php" class="btn btn-secondary">
            <i class="fa-solid fa-arrow-left me-1"></i>Quay lại danh sách
        </a>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-header bg-info text-white">
        <h5 class="m-0"><i class="fa-solid fa-circle-info me-2"></i>Thông tin chi tiết: <?= $product->proName ?></h5>
    </div>
    <div class="card-body">
        <div class="row mb-4">
            
            <!-- 1. HIỂN THỊ HÌNH ẢNH ĐẠI DIỆN CỦA SẢN PHẨM (Mục D trong Lab 8) -->
            <div class="col-md-4 text-center border-end">
                <h6 class="text-muted fw-bold mb-3">Hình ảnh đại diện chính</h6>
                
                <?php if (!empty($product->image) && file_exists(__DIR__ . "/../../../uploads/products/" . $product->image)): ?>
                    <img src="../../../uploads/products/<?= $product->image ?>" 
                         alt="<?= $product->proName ?>" 
                         class="img-fluid rounded shadow-sm border p-2 bg-white" 
                         style="max-height: 280px; object-fit: contain;">
                <?php else: ?>
                    <!-- Hiển thị dòng chữ No Image nếu sản phẩm chưa có hình ảnh (Mục D trong Lab 8) -->
                    <div class="p-5 bg-light text-muted border rounded">
                        <i class="fa-solid fa-image fa-3x mb-2 d-block"></i>
                        <span class="fw-bold">No Image</span>
                    </div>
                <?php endif; ?>
            </div>

            <!-- 2. HIỂN THỊ ĐẦY ĐỦ CÁC THÔNG TIN CỦA SẢN PHẨM (Mục D trong Lab 8) -->
            <div class="col-md-8">
                <table class="table table-striped table-bordered align-middle mb-0">
                    <tbody>
                        <tr>
                            <th width="180" class="bg-light">Mã sản phẩm (ID):</th>
                            <td><b>#<?= $product->id ?></b></td>
                        </tr>
                        <tr>
                            <th class="bg-light">Tên sản phẩm:</th>
                            <td><b class="text-primary fs-5"><?= $product->proName ?></b></td>
                        </tr>
                        <tr>
                            <th class="bg-light">Slug (SEO Friendly):</th>
                            <td><code><?= $product->slug ?></code></td>
                        </tr>
                        <tr>
                            <th class="bg-light">Danh mục:</th>
                            <td><span class="badge bg-info text-dark"><?= $product->cateName ?? 'Chưa phân loại' ?></span></td>
                        </tr>
                        <tr>
                            <th class="bg-light">Thương hiệu:</th>
                            <td><span class="badge bg-secondary"><?= $product->brandName ?? 'Chưa có thương hiệu' ?></span></td>
                        </tr>
                        <tr>
                            <th class="bg-light">Giá bán niêm yết:</th>
                            <td class="text-danger fw-bold fs-6"><?= number_format($product->price, 0, ',', '.') ?> VNĐ</td>
                        </tr>
                        <tr>
                            <th class="bg-light">Giá khuyến mãi:</th>
                            <td class="text-success fw-bold">
                                <?= $product->discountPrice > 0 ? number_format($product->discountPrice, 0, ',', '.') . ' VNĐ' : '<span class="text-muted fw-normal">Không áp dụng</span>' ?>
                            </td>
                        </tr>
                        <tr>
                            <th class="bg-light">Số lượng tồn kho:</th>
                            <td><b><?= $product->quantity ?></b> sản phẩm</td>
                        </tr>
                        <tr>
                            <th class="bg-light">Trạng thái kinh doanh:</th>
                            <td>
                                <?php if ($product->status === 1): ?>
                                    <span class="badge bg-success">Đang bán</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Ngừng bán</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <th class="bg-light">Ngày khởi tạo:</th>
                            <td><?= date('d/m/Y H:i:s', strtotime($product->createdAt)) ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 3. HIỂN THỊ BỘ SƯU TẬP ÁNH PHỤ GALLERY (Mục E.6 trong Lab 8) -->
        <?php if (!empty($galleryImages)): ?>
            <div class="mb-4">
                <h6 class="fw-bold border-bottom pb-2 text-dark">
                    <i class="fa-solid fa-images me-2 text-primary"></i>Bộ sưu tập ảnh phụ (Gallery):
                </h6>
                <div class="d-flex flex-wrap gap-2 mt-3">
                    <?php foreach ($galleryImages as $gImg): ?>
                        <?php if (file_exists(__DIR__ . "/../../../uploads/products/" . $gImg["image"])): ?>
                            <div class="border rounded p-1 bg-white shadow-sm">
                                <img src="../../../uploads/products/<?= $gImg["image"] ?>" 
                                     class="img-thumbnail" 
                                     width="120" 
                                     style="height: 100px; object-fit: cover;">
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- MÔ TẢ SẢN PHẨM -->
        <div class="mb-3">
            <h6 class="fw-bold border-bottom pb-2 text-dark">
                <i class="fa-solid fa-align-left me-2 text-primary"></i>Mô tả chi tiết sản phẩm:
            </h6>
            <div class="p-3 bg-light border rounded">
                <?= !empty($product->description) ? nl2br($product->description) : '<span class="text-muted">Sản phẩm này chưa có bài viết mô tả chi tiết.</span>' ?>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . "/../layouts/master.php";
?>