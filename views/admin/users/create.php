<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/../../../dao/UserDAO.php";
require_once __DIR__ . "/../../../models/User.php";

$errors = [];
$fullName = "";
$username = "";
$password = "";
$email = "";
$phone = "";
$address = "";
$role = 0;
$status = 1;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullName = trim($_POST["fullName"] ?? "");
    $username = trim($_POST["username"] ?? "");
    $password = trim($_POST["password"] ?? "");
    $email    = trim($_POST["email"] ?? "");
    $phone    = trim($_POST["phone"] ?? "");
    $address  = trim($_POST["address"] ?? "");
    $role     = (int)($_POST["role"] ?? 0);
    $status   = isset($_POST["status"]) ? (int)$_POST["status"] : 1;

    // Validation
    if (empty($fullName)) { $errors[] = "Họ và tên không được để trống."; }
    if (empty($username)) { $errors[] = "Tên đăng nhập không được để trống."; }
    if (empty($password)) { $errors[] = "Mật khẩu không được để trống."; }
    if (empty($email)) { $errors[] = "Email không được để trống."; }

    if (empty($errors)) {
        $dao = new UserDAO();
        
        // Khởi tạo đối tượng Model User
        $user = new User($fullName, $username, $password, $email, $phone, $address, $role, $status);

        if ($dao->insert($user)) {
            $_SESSION['success'] = "Thêm mới người dùng thành công!";
            header("Location: index.php");
            exit();
        } else {
            $errors[] = "Có lỗi xảy ra khi thêm người dùng vào cơ sở dữ liệu.";
        }
    }
}

$pageTitle = "Thêm mới người dùng";
ob_start();
?>

<div class="card shadow-sm border-0">
    <div class="card-header bg-primary text-white">
        <h5 class="m-0"><i class="fa-solid fa-user-plus me-2"></i>Thêm mới người dùng</h5>
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
                <label class="form-label font-weight-bold">Tên đăng nhập (Username) <span class="text-danger">*</span></label>
                <input type="text" name="username" class="form-control" placeholder="Nhập username..." value="<?= $username ?>">
            </div>

            <div class="mb-3">
                <label class="form-label font-weight-bold">Mật khẩu <span class="text-danger">*</span></label>
                <input type="password" name="password" class="form-control" placeholder="Nhập mật khẩu...">
            </div>

            <div class="mb-3">
                <label class="form-label font-weight-bold">Email <span class="text-danger">*</span></label>
                <input type="email" name="email" class="form-control" placeholder="example@gmail.com" value="<?= $email ?>">
            </div>

            <div class="mb-3">
                <label class="form-label font-weight-bold">Số điện thoại</label>
                <input type="text" name="phone" class="form-control" placeholder="Nhập số điện thoại..." value="<?= $phone ?>">
            </div>

            <div class="mb-3">
                <label class="form-label font-weight-bold">Địa chỉ</label>
                <textarea name="address" class="form-control" rows="2" placeholder="Nhập địa chỉ..."><?= $address ?></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label font-weight-bold">Quyền hạn</label>
                <select name="role" class="form-select">
                    <option value="0" <?= $role == 0 ? "selected" : "" ?>>Nhân viên</option>
                    <option value="1" <?= $role == 1 ? "selected" : "" ?>>Quản trị viên (Admin)</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label d-block font-weight-bold">Trạng thái</label>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="status" id="status1" value="1" <?= $status == 1 ? "checked" : "" ?>>
                    <label class="form-check-label" for="status1">Hoạt động</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="status" id="status0" value="0" <?= $status == 0 ? "checked" : "" ?>>
                    <label class="form-check-label" for="status0">Khóa</label>
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