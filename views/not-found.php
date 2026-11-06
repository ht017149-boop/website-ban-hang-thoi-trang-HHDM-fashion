<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?></title>
    <link rel="stylesheet" href="assets/css/main.css">
</head>
<body>
    <?php require PATH_VIEW . 'partials/header.php'; ?>
    <main class="not-found-page">
        <span class="eyebrow">404 · KHÔNG TÌM THẤY</span>
        <h1>Trang này chưa có ở đây.</h1>
        <p>Liên kết có thể đã thay đổi. Bạn có thể tiếp tục xem các sản phẩm đang có tại HHDM.</p>
        <a class="button" href="?action=products">Xem sản phẩm</a>
    </main>
    <?php require PATH_VIEW . 'partials/footer.php'; ?>
</body>
</html>
