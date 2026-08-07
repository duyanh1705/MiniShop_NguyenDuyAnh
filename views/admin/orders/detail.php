<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/../../../dao/OrderDAO.php";

$dao = new OrderDAO();

// 1. Nhận id đơn hàng từ URL (Mục E.3)
$id = (int)($_GET["id"] ?? 0);
$order = $dao->findById($id);

if (!$order) {
    $_SESSION['error'] = "Không tìm thấy đơn hàng.";
    header("Location: index.php");
    exit();
}

// 2. Xử lý CẬP NHẬT TRẠNG THÁI ĐƠN HÀNG (Mục E.4)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["btnUpdateStatus"])) {
    $newStatus = (int)($_POST["status"] ?? 0);
    if ($dao->updateStatus($order->id, $newStatus)) {
        $_SESSION['success'] = "Cập nhật trạng thái đơn hàng thành công!";
        header("Location: detail.php?id={$order->id}");
        exit();
    } else {
        $_SESSION['error'] = "Cập nhật trạng thái thất bại!";
    }
}

// 3. Lấy danh sách chi tiết sản phẩm thuộc đơn hàng
$orderDetails = $dao->getOrderDetails($order->id);

$pageTitle = "Chi tiết đơn hàng #" . $order->orderCode;
ob_start();
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Chi tiết đơn hàng <code><?= $order->orderCode ?></code></h3>
    <a href="index.php" class="btn btn-secondary"><i class="fa-solid fa-arrow-left me-1"></i>Quay lại danh sách</a>
</div>

<!-- THÔNG BÁO TỪ SESSION -->
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

<div class="row mb-4">
    <!-- THÔNG TIN TỔNG QUAN ĐƠN HÀNG & KHÁCH HÀNG (Mục E.3) -->
    <div class="col-md-7">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-primary text-white">
                <h5 class="m-0"><i class="fa-solid fa-user me-2"></i>Thông tin đơn hàng & Khách hàng</h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless mb-0">
                    <tr>
                        <th width="160">Mã đơn hàng:</th>
                        <td><code><b><?= $order->orderCode ?></b></code></td>
                    </tr>
                    <tr>
                        <th>Tên khách hàng:</th>
                        <td><b><?= $order->customerName ?? 'Khách lẻ' ?></b></td>
                    </tr>
                    <tr>
                        <th>Số điện thoại:</th>
                        <td><?= $order->customerPhone ?? 'N/A' ?></td>
                    </tr>
                    <tr>
                        <th>Địa chỉ giao hàng:</th>
                        <td><?= $order->customerAddress ?? 'N/A' ?></td>
                    </tr>
                    <tr>
                        <th>Ghi chú đơn hàng:</th>
                        <td><?= nl2br($order->note ?? 'Không có ghi chú') ?></td>
                    </tr>
                    <tr>
                        <th>Ngày đặt hàng:</th>
                        <td><?= date('d/m/Y H:i:s', strtotime($order->createdAt)) ?></td>
                    </tr>
                    <tr>
                        <th>Nhân viên xử lý:</th>
                        <td><?= $order->userName ?? 'Hệ thống' ?></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <!-- FORM CẬP NHẬT TRẠNG THÁI (Mục E.4) -->
    <div class="col-md-5">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-warning text-dark">
                <h5 class="m-0"><i class="fa-solid fa-list-check me-2"></i>Cập nhật trạng thái đơn hàng</h5>
            </div>
            <div class="card-body">
                <form action="detail.php?id=<?= $order->id ?>" method="POST">
                    <div class="mb-3">
                        <label class="form-label font-weight-bold">Lựa chọn trạng thái mới:</label>
                        <select name="status" class="form-select form-select-lg">
                            <option value="0" <?= $order->status == 0 ? "selected" : "" ?>>0 - Chờ xác nhận</option>
                            <option value="1" <?= $order->status == 1 ? "selected" : "" ?>>1 - Đã xác nhận</option>
                            <option value="2" <?= $order->status == 2 ? "selected" : "" ?>>2 - Đang giao</option>
                            <option value="3" <?= $order->status == 3 ? "selected" : "" ?>>3 - Hoàn thành</option>
                            <option value="4" <?= $order->status == 4 ? "selected" : "" ?>>4 - Đã hủy</option>
                        </select>
                    </div>
                    <button type="submit" name="btnUpdateStatus" class="btn btn-primary w-100">
                        <i class="fa-solid fa-floppy-disk me-1"></i>Lưu trạng thái
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- BẢNG DANH SÁCH SẢN PHẨM MUA (Master - Detail) (Mục E.3) -->
<div class="card shadow-sm border-0">
    <div class="card-header bg-dark text-white">
        <h5 class="m-0"><i class="fa-solid fa-boxes-packing me-2"></i>Danh sách sản phẩm thuộc đơn hàng</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle mb-0 bg-white">
                <thead class="table-light">
                    <tr>
                        <th class="text-center" width="60">STT</th>
                        <th>Tên sản phẩm</th>
                        <th class="text-end">Đơn giá</th>
                        <th class="text-center">Số lượng</th>
                        <th class="text-end">Thành tiền</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($orderDetails)): ?>
                        <tr>
                            <td colspan="5" class="text-center py-3 text-muted">Đơn hàng chưa có dữ liệu sản phẩm.</td>
                        </tr>
                    <?php else: ?>
                        <?php 
                        $stt = 1;
                        foreach ($orderDetails as $item): 
                            $price = (float)($item["price"] ?? 0);
                            $qty = (int)($item["quantity"] ?? 0);
                            $subtotal = $price * $qty;
                        ?>
                            <tr>
                                <td class="text-center"><?= $stt++ ?></td>
                                <td><b><?= $item["productName"] ?></b></td>
                                <td class="text-end"><?= number_format($price, 0, ',', '.') ?> đ</td>
                                <td class="text-center"><?= $qty ?></td>
                                <td class="text-end fw-bold text-primary"><?= number_format($subtotal, 0, ',', '.') ?> đ</td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
                <tfoot class="table-light">
                    <tr>
                        <th colspan="4" class="text-end fs-5">TỔNG CỘNG TIỀN ĐƠN HÀNG:</th>
                        <th class="text-end fs-5 text-danger fw-bold"><?= number_format($order->totalAmount, 0, ',', '.') ?> đ</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . "/../layouts/master.php";
?>