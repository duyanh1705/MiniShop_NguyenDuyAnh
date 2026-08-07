<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/../../../dao/OrderDAO.php";

$dao = new OrderDAO();

// Xử lý TÌM KIẾM đơn hàng (Mục E.5)
$keyword = "";
if (isset($_GET["keyword"])) {
    $keyword = trim($_GET["keyword"]);
}

$orders = $dao->getAll($keyword);
$pageTitle = "Quản lý Đơn hàng";

// Hàm hiển thị Badge trạng thái (Mục E.2 & E.4)
function getStatusBadge($status) {
    switch ($status) {
        case 0: return '<span class="badge bg-warning text-dark">Chờ xác nhận</span>';
        case 1: return '<span class="badge bg-info text-dark">Đã xác nhận</span>';
        case 2: return '<span class="badge bg-primary">Đang giao</span>';
        case 3: return '<span class="badge bg-success">Hoàn thành</span>';
        case 4: return '<span class="badge bg-danger">Đã hủy</span>';
        default: return '<span class="badge bg-secondary">Không xác định</span>';
    }
}

ob_start();
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Danh sách đơn hàng</h3>
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

<!-- FORM TÌM KIẾM CƠ BẢN (Mục E.5) -->
<form class="row mb-3" action="index.php" method="GET">
    <div class="col-md-4">
        <input type="text" name="keyword" class="form-control" placeholder="Nhập mã đơn hoặc tên khách hàng..." value="<?= $keyword ?>">
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

<!-- BẢNG HIỂN THỊ DANH SÁCH ĐƠN HÀNG (Mục E.2) -->
<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle mb-0 bg-white">
                <thead class="table-dark">
                    <tr>
                        <th class="text-center" width="120">Mã đơn</th>
                        <th>Khách hàng</th>
                        <th>Nhân viên</th>
                        <th>Ngày đặt</th>
                        <th class="text-end">Tổng tiền</th>
                        <th class="text-center">Trạng thái</th>
                        <th class="text-center" width="150">Chức năng</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($orders)): ?>
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                <i class="fa-solid fa-magnifying-glass me-2"></i>Không tìm thấy dữ liệu phù hợp với từ khóa "<b><?= $keyword ?></b>".
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($orders as $item): ?>
                            <tr>
                                <td class="text-center"><code><?= $item->orderCode ?></code></td>
                                <td><b><?= $item->customerName ?? 'Khách lẻ' ?></b></td>
                                <td><?= $item->userName ?? 'Hệ thống' ?></td>
                                <td><?= date('d/m/Y H:i', strtotime($item->createdAt)) ?></td>
                                <td class="text-end text-danger fw-bold"><?= number_format($item->totalAmount, 0, ',', '.') ?> đ</td>
                                <td class="text-center"><?= getStatusBadge($item->status) ?></td>
                                <td class="text-center">
                                    <!-- Nút Chi tiết (Mục E.3) -->
                                    <a href="detail.php?id=<?= $item->id ?>" class="btn btn-sm btn-info text-white" title="Xem chi tiết">
                                        <i class="fa-solid fa-eye me-1"></i>Chi tiết
                                    </a>
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