<?php
require_once __DIR__ . "/../../../dao/UserDAO.php";

$dao = new UserDAO();
$id = (int)($_GET["id"] ?? 0);

$user = $dao->findById($id);

if (!$user) {
    header("Location: index.php");
    exit();
}

$pageTitle = "Chi tiết người dùng";
ob_start();
?>

<div class="container mt-4">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-info text-white">
            <h4 class="mb-0"><i class="fa-solid fa-circle-info me-2"></i>Chi tiết người dùng</h4>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-striped align-middle">
                <tbody>
                    <tr>
                        <th width="200">Mã ID</th>
                        <td><?= $user->id ?></td>
                    </tr>
                    <tr>
                        <th>Tên đăng nhập (Username)</th>
                        <td><code><?= $user->username ?></code></td>
                    </tr>
                    <tr>
                        <th>Họ và tên</th>
                        <td><b><?= $user->fullName ?></b></td>
                    </tr>
                    <tr>
                        <th>Email</th>
                        <td><?= $user->email ?></td>
                    </tr>
                    <tr>
                        <th>Số điện thoại</th>
                        <td><?= $user->phone ?? 'Chưa cập nhật' ?></td>
                    </tr>
                    <tr>
                        <th>Địa chỉ</th>
                        <td><?= $user->address ?? 'Chưa cập nhật' ?></td>
                    </tr>
                    <tr>
                        <th>Quyền hạn</th>
                        <td>
                            <?php if ($user->role === 1): ?>
                                <span class="badge bg-danger">Quản trị viên (Admin)</span>
                            <?php else: ?>
                                <span class="badge bg-info text-dark">Nhân viên</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th>Trạng thái</th>
                        <td>
                            <?php if ($user->status === 1): ?>
                                <span class="badge bg-success">Hoạt động</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">Khóa</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th>Ngày tạo</th>
                        <td><?= !empty($user->createdAt) ? date('d/m/Y H:i:s', strtotime($user->createdAt)) : 'N/A' ?></td>
                    </tr>
                    <tr>
                        <th>Ngày cập nhật</th>
                        <td><?= !empty($user->updatedAt) ? date('d/m/Y H:i:s', strtotime($user->updatedAt)) : 'N/A' ?></td>
                    </tr>
                </tbody>
            </table>

            <div class="mt-3">
                <a href="edit.php?id=<?= $user->id ?>" class="btn btn-warning me-2">
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