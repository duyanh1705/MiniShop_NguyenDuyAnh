<?php
require_once __DIR__ . "/../../../dao/CustomerDAO.php";

$dao = new CustomerDAO();
$id = (int)($_GET["id"] ?? 0);

$customer = $dao->findById($id);

if (!$customer) {
    header("Location: index.php");
    exit();
}

$pageTitle = "Chi tiết khách hàng";
ob_start();
?>

<div class="container mt-4">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-info text-white">
            <h4 class="mb-0"><i class="fa-solid fa-circle-info me-2"></i>Chi tiết thông tin khách hàng</h4>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-striped align-middle">
                <tbody>
                    <tr>
                        <th width="200">Mã khách hàng (ID)</th>
                        <td><?= $customer->id ?></td>
                    </tr>
                    <tr>
                        <th>Họ và tên</th>
                        <td><b><?= $customer->fullName ?></b></td>
                    </tr>
                    <tr>
                        <th>Số điện thoại</th>
                        <td><?= $customer->phone ?></td>
                    </tr>
                    <tr>
                        <th>Email</th>
                        <td><?= $customer->email ?? 'Chưa cập nhật' ?></td>
                    </tr>
                    <tr>
                        <th>Địa chỉ</th>
                        <td><?= $customer->address ?? 'Chưa cập nhật' ?></td>
                    </tr>
                    <tr>
                        <th>Ghi chú</th>
                        <td><?= nl2br($customer->note ?? 'Không có ghi chú') ?></td>
                    </tr>
                    <tr>
                        <th>Trạng thái</th>
                        <td>
                            <?php if ($customer->status === 1): ?>
                                <span class="badge bg-success">Hoạt động</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">Khóa / Ẩn</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th>Ngày tạo</th>
                        <td><?= !empty($customer->createdAt) ? date('d/m/Y H:i:s', strtotime($customer->createdAt)) : 'N/A' ?></td>
                    </tr>
                    <tr>
                        <th>Ngày cập nhật</th>
                        <td><?= !empty($customer->updatedAt) ? date('d/m/Y H:i:s', strtotime($customer->updatedAt)) : 'N/A' ?></td>
                    </tr>
                </tbody>
            </table>

            <div class="mt-3">
                <a href="edit.php?id=<?= $customer->id ?>" class="btn btn-warning me-2">
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