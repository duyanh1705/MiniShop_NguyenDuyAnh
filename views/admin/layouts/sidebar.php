<?php
// Lấy đường dẫn URL hiện tại
$currentUrl = $_SERVER['REQUEST_URI'];
?>

<div class="col-md-3 col-lg-2 bg-dark text-white min-vh-100 p-3">
    <a href="/MiniShop_NguyenDuyAnh/views/admin/dashboard.php" class="text-white text-decoration-none">
        <h4 class="mb-3"><i class="fa-solid fa-cart-shopping me-2"></i>Mini Shop</h4>
    </a>
    <hr>
    <ul class="nav nav-pills flex-column mb-auto">
        <li class="nav-item mb-1">
            <a href="/MiniShop_NguyenDuyAnh/views/admin/dashboard.php" 
               class="nav-link text-white <?= (strpos($currentUrl, 'dashboard.php') !== false) ? 'bg-primary active' : '' ?>">
                <i class="fa-solid fa-gauge me-2"></i>Dashboard
            </a>
        </li>
        <li class="nav-item mb-1">
            <a href="/MiniShop_NguyenDuyAnh/views/admin/categories/index.php" 
               class="nav-link text-white <?= (strpos($currentUrl, '/categories/') !== false) ? 'bg-primary active' : '' ?>">
                <i class="fa-solid fa-list me-2"></i>Danh mục
            </a>
        </li>
        <li class="nav-item mb-1">
            <a href="/MiniShop_NguyenDuyAnh/views/admin/brands/index.php" 
               class="nav-link text-white <?= (strpos($currentUrl, '/brands/') !== false) ? 'bg-primary active' : '' ?>">
                <i class="fa-solid fa-copyright me-2"></i>Thương hiệu
            </a>
        </li>
        <li class="nav-item mb-1">
            <a href="/MiniShop_NguyenDuyAnh/views/admin/products/index.php" 
               class="nav-link text-white <?= (strpos($currentUrl, '/products/') !== false) ? 'bg-primary active' : '' ?>">
                <i class="fa-solid fa-box me-2"></i>Sản phẩm
            </a>
        </li>
        <li class="nav-item mb-1">
            <a href="/MiniShop_NguyenDuyAnh/views/admin/customers/index.php" 
               class="nav-link text-white <?= (strpos($currentUrl, '/customers/') !== false) ? 'bg-primary active' : '' ?>">
                <i class="fa-solid fa-users me-2"></i>Khách hàng
            </a>
        </li>
        <li class="nav-item mb-1">
            <a href="/MiniShop_NguyenDuyAnh/views/admin/users/index.php" 
               class="nav-link text-white <?= (strpos($currentUrl, '/users/') !== false) ? 'bg-primary active' : '' ?>">
                <i class="fa-solid fa-user-gear me-2"></i>Người dùng
            </a>
        </li>
        <li class="nav-item mb-1">
            <a href="/MiniShop_NguyenDuyAnh/views/admin/orders/index.php" 
               class="nav-link text-white <?= (strpos($currentUrl, '/orders/') !== false) ? 'bg-primary active' : '' ?>">
                <i class="fa-solid fa-file-invoice-dollar me-2"></i>Đơn hàng
            </a>
        </li>
    </ul>
    <hr>
    <div>
        <a href="#" class="btn btn-danger w-100 btn-sm">
            <i class="fa-solid fa-right-from-bracket me-2"></i>Đăng xuất
        </a>
    </div>
</div>