<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/../../../dao/UserDAO.php";

$dao = new UserDAO();
$id = (int)($_GET["id"] ?? 0);

$user = $dao->findById($id);

if (!$user) {
    $_SESSION['error'] = "Không tìm thấy người dùng cần cập nhật.";
    header("Location: index.php");
    exit();
}

$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullName = trim($_POST["fullName"] ?? "");
    $email    = trim($_POST["email"] ?? "");
    $phone    = trim($_POST["phone"] ?? "");
    $address  = trim($_POST["address"] ?? "");
    $role     = (int)($_POST["role"] ?? 0);
    $status   = (int)($_POST["status"] ?? 1);

    if (empty($fullName)) { $errors[] = "Họ và tên không được để trống."; }
    if (empty($email)) { $errors[] = "Email không được để trống."; }

    if (empty($errors)) {
        $user->fullName = $fullName;
        $user->email    = $email;
        $user->phone    = $phone;
        $user->address  = $address;
        $user->role     = $role;
        $user->status   = $status;

        // Cập nhật mật khẩu mới nếu người dùng nhập
        if (!empty($_POST["password"])) {
            $user->password = trim($_POST["password"]);
        }

        if ($dao->update($user)) {
            $_SESSION['success'] = "Cập nhật người dùng thành công!";
            header("Location: index.php");
            exit();
        } else {
            $errors[] = "Cập nhật thất bại. Vui lòng thử lại.";
        }
    }
}

$pageTitle = "Cập nhật người dùng";
ob_start();
?>

<div class="card shadow-sm border-0">
    <div class="card-header bg-warning text-dark">
        <h5 class="m-0"><i class="fa-solid fa-pen-to-square me-2"></i>Cập nhật người dùng</h5>
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

        <form action="edit.php?id=<?= $user->id ?>" method="POST">
            <input type="hidden" name="id" value="<?= $user->id ?>">

            <div class="mb-3">
                <label class="form-label font-weight-bold">Tên đăng nhập (Username)</label>
                <input type="text" class="form-control" value="<?= $user->username ?>" disabled>
            </div>

            <div class="mb-3">
                <label class="form-label font-weight-bold">Mật khẩu mới (bỏ trống nếu không đổi)</label>
                <input type="password" name="password" class="form-control" placeholder="Nhập mật khẩu mới nếu cần đổi...">
            </div>

            <div class="mb-3">
                <label class="form-label font-weight-bold">Họ và tên <span class="text-danger">*</span></label>
                <input type="text" name="fullName" class="form-control" value="<?= $user->fullName ?>">
            </div>

            <div class="mb-3">
                <label class="form-label font-weight-bold">Email <span class="text-danger">*</span></label>
                <input type="email" name="email" class="form-control" value="<?= $user->email ?>">
            </div>

            <div class="mb-3">
                <label class="form-label font-weight-bold">Số điện thoại</label>
                <input type="text" name="phone" class="form-control" value="<?= $user->phone ?? '' ?>">
            </div>

            <div class="mb-3">
                <label class="form-label font-weight-bold">Địa chỉ</label>
                <textarea name="address" class="form-control" rows="2"><?= $user->address ?? '' ?></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label font-weight-bold">Quyền hạn</label>
                <select name="role" class="form-select">
                    <option value="0" <?= $user->role == 0 ? "selected" : "" ?>>Nhân viên</option>
                    <option value="1" <?= $user->role == 1 ? "selected" : "" ?>>Quản trị viên (Admin)</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label d-block font-weight-bold">Trạng thái</label>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="status" id="status1" value="1" <?= $user->status == 1 ? "checked" : "" ?>>
                    <label class="form-check-label" for="status1">Hoạt động</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="status" id="status0" value="0" <?= $user->status == 0 ? "checked" : "" ?>>
                    <label class="form-check-label" for="status0">Khóa</label>
                </div>
            </div>

            <button type="submit" class="btn btn-primary me-2"><i class="fa-solid fa-pen me-1"></i>Cập nhật</button>
            <button type="reset" class="btn btn-warning me-2"><i class="fa-solid fa-rotate-left me-1"></i>Làm mới</button>
            <a href="index.php" class="btn btn-secondary"><i class="fa-solid fa-arrow-left me-1"></i>Quay lại</a>
        </form>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . "/../layouts/master.php";
?>