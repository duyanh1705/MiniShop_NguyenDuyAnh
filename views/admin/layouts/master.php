<?php include __DIR__ . "/header.php"; ?>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar Menu -->
        <?php include __DIR__ . "/sidebar.php"; ?>

        <!-- Nội dung hiển thị chính -->
        <main class="col-md-9 col-lg-10 p-4">
            <?= $content ?>
        </main>
    </div>
</div>

<?php include __DIR__ . "/footer.php"; ?>