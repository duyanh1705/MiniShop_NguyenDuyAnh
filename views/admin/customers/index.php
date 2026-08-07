<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/../../../dao/CustomerDAO.php";

$dao = new CustomerDAO();

// Xử lý XÓA khách hàng
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["btnDelete"])) {
    $id = (int)($_POST["id"] ?? 0);
    if ($id > 0) {
        try {
            if ($dao->delete($id)) {
                $_SESSION['success'] = "Xóa khách hàng thành công!";
                header("Location: index.php");
                exit();
            } else {
                $_SESSION['error'] = "Xóa thất bại! Vui lòng thử lại.";
            }
        } catch (Exception $e) {
            $_SESSION['error'] = "Không thể xóa khách hàng này (có thể do đang có đơn hàng trong hệ thống).";
            header("Location: index.php");
            exit();
        }
    }
}

// Xử lý TÌM KIẾM
$keyword = "";
if (isset($_GET["keyword"])) {
    $keyword = trim($_GET["keyword"]);
}

$customers = $dao->getAll($keyword);
$pageTitle = "Quản lý Khách hàng";

ob_start();
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Danh sách khách hàng</h3>
    <a href="create.php" class="btn btn-primary"><i class="fa-solid fa-plus me-1"></i>Thêm khách hàng</a>
</div>

<!-- HIỂN THỊ THÔNG BÁO TỪ SESSION -->
<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fa-solid fa-circle-check me-2"></i><?= $_SESSION['success'] ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['success']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fa-solid fa-triangle-exclamation me-2"></i><?= $_SESSION['error'] ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['error']); ?>
<?php endif; ?>

<!-- FORM TÌM KIẾM CƠ BẢN -->
<form class="row mb-3" action="index.php" method="GET">
    <div class="col-md-4">
        <input type="text" name="keyword" class="form-control" placeholder="Nhập tên hoặc SĐT khách hàng..." value="<?= $keyword ?>">
    </div>
    <div class="col-md-2">
        <button type="submit" class="btn btn-primary">
            <i class="fa-solid fa-magnifying-glass me-1"></i>Tìm kiếm
        </button>
        <?php if (!empty($keyword)): ?>
            <a href="index.php" class="btn btn-outline-secondary ms-1" title="Xóa bộ lọc">
                <i class="fa-solid fa-xmark"></i>
            </a>
        <?php endif; ?>
    </div>
</form>

<!-- BẢNG HIỂN THỊ DỮ LIỆU -->
<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle mb-0 bg-white">
                <thead class="table-dark">
                    <tr>
                        <th class="text-center" width="60">STT</th>
                        <th>Họ và tên</th>
                        <th>Số điện thoại</th>
                        <th>Email</th>
                        <th class="text-center">Trạng thái</th>
                        <th>Ngày tạo</th>
                        <th class="text-center" width="200">Chức năng</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($customers)): ?>
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                <i class="fa-solid fa-magnifying-glass me-2"></i>Không tìm thấy dữ liệu phù hợp với từ khóa "<b><?= $keyword ?></b>".
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($customers as $index => $item): ?>
                            <tr>
                                <td class="text-center"><?= $index + 1 ?></td>
                                <td><b><?= $item->fullName ?></b></td>
                                <td><?= $item->phone ?></td>
                                <td><?= $item->email ?? 'N/A' ?></td>
                                <td class="text-center">
                                    <?php if ($item->status === 1): ?>
                                        <span class="badge bg-success">Hoạt động</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Khóa / Ẩn</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= date('d/m/Y H:i', strtotime($item->createdAt)) ?></td>
                                <td class="text-center">
                                    <a href="detail.php?id=<?= $item->id ?>" class="btn btn-sm btn-info text-white me-1" title="Chi tiết">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                    <a href="edit.php?id=<?= $item->id ?>" class="btn btn-sm btn-warning me-1" title="Sửa">
                                        <i class="fa-solid fa-pen"></i>
                                    </a>
                                    <form method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc muốn xóa khách hàng này?');">
                                        <input type="hidden" name="id" value="<?= $item->id ?>">
                                        <button type="submit" name="btnDelete" class="btn btn-sm btn-danger" title="Xóa">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . "/../layouts/master.php";
?>