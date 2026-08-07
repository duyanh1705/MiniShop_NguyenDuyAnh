<?php
require_once __DIR__ . "/../../../dao/ProductDAO.php";

$dao = new ProductDAO();
$id = (int)($_GET["id"] ?? 0);
$product = $dao->findById($id);

if (!$product) {
    header("Location: index.php");
    exit();
}

$pageTitle = "Chi tiết sản phẩm";
ob_start();
?>

<div class="container mt-4">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-info text-white">
            <h4 class="mb-0"><i class="fa-solid fa-circle-info me-2"></i>Chi tiết sản phẩm</h4>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-striped align-middle">
                <tbody>
                    <tr>
                        <th width="200">Mã sản phẩm (ID)</th>
                        <td><?= $product->id ?></td>
                    </tr>
                    <tr>
                        <th>Tên sản phẩm</th>
                        <td><b><?= $product->proName ?></b></td>
                    </tr>
                    <tr>
                        <th>Slug</th>
                        <td><code><?= $product->slug ?></code></td>
                    </tr>
                    <tr>
                        <th>Danh mục</th>
                        <td><span class="badge bg-info text-dark"><?= $product->cateName ?></span></td>
                    </tr>
                    <tr>
                        <th>Thương hiệu</th>
                        <td><span class="badge bg-secondary"><?= $product->brandName ?></span></td>
                    </tr>
                    <tr>
                        <th>Giá bán gốc</th>
                        <td class="text-primary fw-bold"><?= number_format($product->price, 0, ',', '.') ?> đ</td>
                    </tr>
                    <tr>
                        <th>Giá khuyến mãi</th>
                        <td class="text-danger fw-bold"><?= number_format($product->discountPrice, 0, ',', '.') ?> đ</td>
                    </tr>
                    <tr>
                        <th>Số lượng tồn kho</th>
                        <td><?= $product->quantity ?></td>
                    </tr>
                    <tr>
                        <th>Mô tả</th>
                        <td><?= nl2br($product->description ?? 'Chưa có mô tả') ?></td>
                    </tr>
                    <tr>
                        <th>Trạng thái</th>
                        <td>
                            <?php if ($product->status === 1): ?>
                                <span class="badge bg-success">Đang bán</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">Ngừng bán</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th>Ngày tạo</th>
                        <td><?= !empty($product->createdAt) ? date('d/m/Y H:i:s', strtotime($product->createdAt)) : 'N/A' ?></td>
                    </tr>
                    <tr>
                        <th>Ngày cập nhật</th>
                        <td><?= !empty($product->updatedAt) ? date('d/m/Y H:i:s', strtotime($product->updatedAt)) : 'N/A' ?></td>
                    </tr>
                </tbody>
            </table>

            <div class="mt-3">
                <a href="edit.php?id=<?= $product->id ?>" class="btn btn-warning me-2">
                    <i class="fa-solid fa-pen me-1"></i>Chỉnh sửa
                </a>
                <a href="index.php" class="btn btn-secondary">
                    <i class="fa-solid fa-arrow-left me-1"></i>Quay lại danh sách
                </a>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . "/../layouts/master.php";
?>