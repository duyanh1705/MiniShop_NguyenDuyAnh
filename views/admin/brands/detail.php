<?php
require_once __DIR__ . "/../../../dao/BrandDAO.php";

$dao = new BrandDAO();
$id = (int)($_GET["id"] ?? 0);

$brand = $dao->findById($id);

if (!$brand) {
    header("Location: index.php");
    exit();
}

$pageTitle = "Chi tiết thương hiệu";
ob_start();
?>

<div class="container mt-4">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-info text-white">
            <h4 class="mb-0"><i class="fa-solid fa-circle-info me-2"></i>Chi tiết thương hiệu</h4>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-striped align-middle">
                <tbody>
                    <tr>
                        <th width="200">Mã thương hiệu (ID)</th>
                        <td><?= $brand->id ?></td>
                    </tr>
                    <tr>
                        <th>Tên thương hiệu</th>
                        <td><b><?= $brand->brandName ?></b></td>
                    </tr>
                    <tr>
                        <th>Slug</th>
                        <td><code><?= $brand->slug ?></code></td>
                    </tr>
                    <tr>
                        <th>Hình ảnh</th>
                        <td>
                            <?php if (!empty($brand->image)): ?>
                                <img src="<?= $brand->image ?>" alt="Image" width="100" class="img-thumbnail">
                            <?php else: ?>
                                <span class="text-muted">Không có hình ảnh</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th>Mô tả</th>
                        <td><?= nl2br($brand->description ?? 'Chưa có mô tả') ?></td>
                    </tr>
                    <tr>
                        <th>Trạng thái</th>
                        <td>
                            <?php if ($brand->status === 1): ?>
                                <span class="badge bg-success">Hiển thị / Hoạt động</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">Ẩn / Ngừng hoạt động</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th>Ngày tạo</th>
                        <td><?= !empty($brand->createdAt) ? date('d/m/Y H:i:s', strtotime($brand->createdAt)) : 'N/A' ?></td>
                    </tr>
                    <tr>
                        <th>Ngày cập nhật</th>
                        <td><?= !empty($brand->updatedAt) ? date('d/m/Y H:i:s', strtotime($brand->updatedAt)) : 'N/A' ?></td>
                    </tr>
                </tbody>
            </table>

            <div class="mt-3">
                <a href="edit.php?id=<?= $brand->id ?>" class="btn btn-warning me-2">
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