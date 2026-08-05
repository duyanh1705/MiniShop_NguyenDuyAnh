<?php
require_once __DIR__ . "/../../dao/CategoryDAO.php";
require_once __DIR__ . "/../../dao/BrandDAO.php";
require_once __DIR__ . "/../../dao/ProductDAO.php";
require_once __DIR__ . "/../../dao/CustomerDAO.php";
require_once __DIR__ . "/../../dao/OrderDAO.php";

// Khởi tạo các DAO
$categoryDAO = new CategoryDAO();
$brandDAO    = new BrandDAO();
$productDAO  = new ProductDAO();
$customerDAO = new CustomerDAO();
$orderDAO    = new OrderDAO();

// 1. Thống kê số lượng cho các Card
$totalCategories = count($categoryDAO->getAll());
$totalBrands     = count($brandDAO->getAll());
$totalProducts   = count($productDAO->getAll());
$totalCustomers  = count($customerDAO->getAll());
$totalOrders     = count($orderDAO->getAll());

// 2. Lấy 5 đơn hàng mới nhất
$latestOrders = $orderDAO->getLatestOrders(5);

$pageTitle = "Dashboard - Quản trị hệ thống";

ob_start();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="m-0">Dashboard</h2>
    <span class="text-muted">Xin chào, <b>Admin</b></span>
</div>

<!-- THẺ THỐNG KÊ (Yêu cầu Câu G) -->
<div class="row g-3 mb-4">
    <div class="col-md-2-4 col-sm-6">
        <div class="card bg-primary text-white shadow-sm border-0 h-100">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-uppercase mb-1" style="font-size: 0.85rem;">Danh mục</h6>
                    <h3 class="m-0 fw-bold"><?= $totalCategories ?></h3>
                </div>
                <i class="fa-solid fa-list fa-2x opacity-50"></i>
            </div>
        </div>
    </div>
    <div class="col-md-2-4 col-sm-6">
        <div class="card bg-secondary text-white shadow-sm border-0 h-100">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-uppercase mb-1" style="font-size: 0.85rem;">Thương hiệu</h6>
                    <h3 class="m-0 fw-bold"><?= $totalBrands ?></h3>
                </div>
                <i class="fa-solid fa-copyright fa-2x opacity-50"></i>
            </div>
        </div>
    </div>
    <div class="col-md-2-4 col-sm-6">
        <div class="card bg-success text-white shadow-sm border-0 h-100">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-uppercase mb-1" style="font-size: 0.85rem;">Sản phẩm</h6>
                    <h3 class="m-0 fw-bold"><?= $totalProducts ?></h3>
                </div>
                <i class="fa-solid fa-box fa-2x opacity-50"></i>
            </div>
        </div>
    </div>
    <div class="col-md-2-4 col-sm-6">
        <div class="card bg-info text-white shadow-sm border-0 h-100">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-uppercase mb-1" style="font-size: 0.85rem;">Khách hàng</h6>
                    <h3 class="m-0 fw-bold"><?= $totalCustomers ?></h3>
                </div>
                <i class="fa-solid fa-users fa-2x opacity-50"></i>
            </div>
        </div>
    </div>
    <div class="col-md-2-4 col-sm-6">
        <div class="card bg-warning text-dark shadow-sm border-0 h-100">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-uppercase mb-1" style="font-size: 0.85rem;">Đơn hàng</h6>
                    <h3 class="m-0 fw-bold"><?= $totalOrders ?></h3>
                </div>
                <i class="fa-solid fa-file-invoice-dollar fa-2x opacity-50"></i>
            </div>
        </div>
    </div>
</div>

<!-- BẢNG 5 ĐƠN HÀNG MỚI NHẤT (Yêu cầu Câu G) -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center py-3">
        <h5 class="m-0" style="font-size: 1.1rem;"><i class="fa-solid fa-clock-history me-2"></i>ĐƠN HÀNG MỚI NHẤT</h5>
        <a href="../orders/index.php" class="btn btn-sm btn-outline-light">Xem tất cả</a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Mã đơn</th>
                        <th>Khách hàng</th>
                        <th>Ngày đặt</th>
                        <th>Tổng tiền</th>
                        <th>Trạng thái</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($latestOrders)): ?>
                        <tr><td colspan="6" class="text-center py-3 text-muted">Chưa có đơn hàng nào.</td></tr>
                    <?php else: ?>
                        <?php foreach ($latestOrders as $index => $ord): ?>
                            <tr>
                                <td><?= $index + 1 ?></td>
                                <td><b class="text-primary"><?= htmlspecialchars($ord->orderCode) ?></b></td>
                                <td><?= htmlspecialchars($ord->customerName ?? 'Khách lẻ') ?></td>
                                <td><?= date('d/m/Y', strtotime($ord->createdAt)) ?></td>
                                <td class="fw-bold text-danger"><?= number_format($ord->totalAmount) ?> ₫</td>
                                <td>
                                    <?php if ($ord->status === 0): ?>
                                        <span class="badge bg-warning text-dark">Chờ xử lý</span>
                                    <?php elseif ($ord->status === 1): ?>
                                        <span class="badge bg-success">Hoàn thành</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Đã hủy</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
/* Cấu hình chia 5 cột đều trên màn hình lớn */
@media (min-width: 768px) {
    .col-md-2-4 {
        flex: 0 0 auto;
        width: 20%;
    }
}
</style>

<?php
$content = ob_get_clean();
include __DIR__ . "/layouts/master.php";
?>