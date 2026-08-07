<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/../../../dao/CustomerDAO.php";
require_once __DIR__ . "/../../../models/Customer.php";

$errors = [];
$fullName = "";
$phone = "";
$email = "";
$address = "";
$note = "";
$status = 1;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullName = trim($_POST["fullName"] ?? "");
    $phone    = trim($_POST["phone"] ?? "");
    $email    = trim($_POST["email"] ?? "");
    $address  = trim($_POST["address"] ?? "");
    $note     = trim($_POST["note"] ?? "");
    $status   = isset($_POST["status"]) ? (int)$_POST["status"] : 1;

    // Validation
    if (empty($fullName)) {
        $errors[] = "Họ và tên không được để trống.";
    }

    if (empty($phone)) {
        $errors[] = "Số điện thoại không được để trống.";
    }

    if (empty($errors)) {
        $dao = new CustomerDAO();
        // Sử dụng Constructor của class Customer
        $customer = new Customer($fullName, $phone, $email, $address, $note, $status);

        if ($dao->insert($customer)) {
            $_SESSION['success'] = "Thêm mới khách hàng thành công!";
            header("Location: index.php");
            exit();
        } else {
            $errors[] = "Có lỗi xảy ra khi thêm khách hàng vào cơ sở dữ liệu.";
        }
    }
}

$pageTitle = "Thêm mới khách hàng";
ob_start();
?>

<div class="card shadow-sm border-0">
    <div class="card-header bg-primary text-white">
        <h5 class="m-0"><i class="fa-solid fa-user-plus me-2"></i>Thêm mới khách hàng</h5>
    </div>
    <div class="card-body">
        
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul class="mb-0 ps-3">
                    <?php foreach ($errors as $err): ?>
                        <li><?= $err ?></li>
                    <?php endforeach; ?>
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <form action="create.php" method="POST">
            <div class="mb-3">
                <label class="form-label font-weight-bold">Họ và tên <span class="text-danger">*</span></label>
                <input type="text" name="fullName" class="form-control" placeholder="Nhập họ và tên..." value="<?= $fullName ?>">
            </div>

            <div class="mb-3">
                <label class="form-label font-weight-bold">Số điện thoại <span class="text-danger">*</span></label>
                <input type="text" name="phone" class="form-control" placeholder="Nhập số điện thoại..." value="<?= $phone ?>">
            </div>

            <div class="mb-3">
                <label class="form-label font-weight-bold">Email</label>
                <input type="email" name="email" class="form-control" placeholder="example@gmail.com" value="<?= $email ?>">
            </div>

            <div class="mb-3">
                <label class="form-label font-weight-bold">Địa chỉ</label>
                <textarea name="address" class="form-control" rows="2" placeholder="Nhập địa chỉ nhận hàng..."><?= $address ?></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label font-weight-bold">Ghi chú</label>
                <textarea name="note" class="form-control" rows="3" placeholder="Ghi chú thêm về khách hàng..."><?= $note ?></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label d-block font-weight-bold">Trạng thái</label>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="status" id="status1" value="1" <?= $status == 1 ? "checked" : "" ?>>
                    <label class="form-check-label" for="status1">Hoạt động</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="status" id="status0" value="0" <?= $status == 0 ? "checked" : "" ?>>
                    <label class="form-check-label" for="status0">Khóa / Ẩn</label>
                </div>
            </div>

            <button type="submit" class="btn btn-primary me-2"><i class="fa-solid fa-floppy-disk me-1"></i>Lưu</button>
            <button type="reset" class="btn btn-warning me-2"><i class="fa-solid fa-rotate-left me-1"></i>Làm mới</button>
            <a href="index.php" class="btn btn-secondary"><i class="fa-solid fa-arrow-left me-1"></i>Quay lại</a>
        </form>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . "/../layouts/master.php";
?>