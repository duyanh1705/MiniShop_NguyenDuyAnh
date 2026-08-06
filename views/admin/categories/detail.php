<?php
require_once __DIR__ . "/../../../dao/CategoryDAO.php";

$dao = new CategoryDAO();

// 1. Nhận id từ URL (detail.php?id=1)
$id = (int)($_GET["id"] ?? 0);

// 2. Gọi phương thức findById() (hoặc getById()) trong CategoryDAO
$category = $dao->findById($id);

// Nếu không tìm thấy id hợp lệ, chuyển về trang danh sách
if (!$category) {
    header("Location: index.php");
    exit();
}

$pageTitle = "Chi tiết danh mục";
ob_start();
?>

<!-- 3. Hiển thị đầy đủ thông tin của danh mục -->
<div class="container mt-4">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-info text-white">
            <h4 class="mb-0"><i class="fa-solid fa-circle-info me-2"></i>Chi tiết danh mục</h4>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-striped align-middle">
                <tbody>
                    <tr>
                        <th width="200">Mã danh mục (ID)</th>
                        <td><?= $category->id ?></td>
                    </tr>
                    <tr>
                        <th>Tên danh mục</th>
                        <td><b><?= htmlspecialchars($category->catename) ?></b></td>
                    </tr>
                    <tr>
                        <th>Slug</th>
                        <td><code><?= htmlspecialchars($category->slug) ?></code></td>
                    </tr>
                    <tr>
                        <th>Hình ảnh</th>
                        <td>
                            <?php if (!empty($category->image)): ?>
                                <img src="<?= htmlspecialchars($category->image) ?>" alt="Image" width="100" class="img-thumbnail">
                            <?php else: ?>
                                <span class="text-muted">Không có hình ảnh</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th>Mô tả</th>
                        <td><?= nl2br(htmlspecialchars($category->description ?? 'Chưa có mô tả')) ?></td>
                    </tr>
                    <tr>
                        <th>Trạng thái</th>
                        <td>
                            <?php if ($category->status === 1): ?>
                                <span class="badge bg-success">Hiển thị / Hoạt động</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">Ẩn / Ngừng hoạt động</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th>Ngày tạo</th>
                        <td><?= !empty($category->createdAt) ? date('d/m/Y H:i:s', strtotime($category->createdAt)) : 'N/A' ?></td>
                    </tr>
                    <tr>
                        <th>Ngày cập nhật</th>
                        <td><?= !empty($category->updatedAt) ? date('d/m/Y H:i:s', strtotime($category->updatedAt)) : 'N/A' ?></td>
                    </tr>
                </tbody>
            </table>

            <!-- Các nút điều hướng -->
            <div class="mt-3">
                <a href="edit.php?id=<?= $category->id ?>" class="btn btn-warning me-2">
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