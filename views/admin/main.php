<?php
$escape = static fn($value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
$adminName = $escape($_SESSION['user']['full_name'] ?? 'Quản trị viên');
?>
<!doctype html>
<html lang="vi">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= $currentPage === 'accounts' ? 'Quản lý tài khoản' : 'Dashboard' ?> | HHDM Fashion</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:wght@500;600;700&display=swap" rel="stylesheet">
  <style>
    *{box-sizing:border-box}body{margin:0;background:#f5f5f1;color:#282b25;font:14px 'DM Sans',Arial,sans-serif}.shell{min-height:100vh;display:grid;grid-template-columns:250px minmax(0,1fr)}.sidebar{background:#24291f;color:#f8f7f2;padding:30px 19px;display:flex;flex-direction:column}.brand{color:#fff;text-decoration:none;font:700 22px 'Playfair Display',Georgia,serif;letter-spacing:.06em;padding:0 12px}.brand small{display:block;font:500 8px 'DM Sans',Arial,sans-serif;letter-spacing:.35em}.side-label{margin:42px 12px 12px;color:#a8ad9f;font-size:9px;font-weight:700;letter-spacing:.16em}.side-link{display:block;color:#d9dbd4;text-decoration:none;padding:12px;border-radius:4px;font-size:12px}.side-link:hover,.side-link.active{background:#3a4233;color:#fff}.side-bottom{margin-top:auto;border-top:1px solid #44493f;padding-top:18px}.side-bottom a{display:block;color:#c6c9bf;text-decoration:none;font-size:11px;padding:10px 12px}.main{min-width:0}.topbar{height:78px;background:#fff;border-bottom:1px solid #e8e8e1;display:flex;align-items:center;justify-content:space-between;padding:0 38px}.topbar span{font-size:11px;color:#75796f}.topbar strong{font-size:12px;color:#33372e}.content{max-width:1320px;padding:34px 38px 55px;margin:0 auto}.eyebrow{color:#78816c;font-size:9px;letter-spacing:.16em;font-weight:700}.page-title{font:500 34px 'Playfair Display',Georgia,serif;margin:8px 0 6px}.page-intro{margin:0;color:#7a7d74;font-size:12px;line-height:1.7}.flash{background:#edf4e8;border:1px solid #d5e4ce;color:#425538;padding:13px 15px;margin:22px 0 0;font-size:12px}.cards{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:15px;margin:28px 0}.card,.panel{background:#fff;border:1px solid #e9e9e2;border-radius:5px}.card{padding:19px}.card-label{color:#7b7f75;font-size:10px}.card-value{font:500 30px 'Playfair Display',Georgia,serif;margin-top:12px}.card-note{color:#92958d;font-size:10px;margin-top:4px}.panel{padding:22px;margin-top:20px}.panel-head{display:flex;align-items:center;justify-content:space-between;gap:12px;margin-bottom:17px}.panel h2{font:500 20px 'Playfair Display',Georgia,serif;margin:0}.panel-head a,.text-link{color:#58644b;font-size:11px;text-decoration:none;font-weight:700}.table-wrap{overflow-x:auto}table{width:100%;border-collapse:collapse;text-align:left}th{color:#858980;font-size:9px;letter-spacing:.1em;font-weight:700;padding:11px 10px;border-bottom:1px solid #ecece6}td{padding:13px 10px;border-bottom:1px solid #f0f0eb;font-size:11px;vertical-align:middle}tbody tr:last-child td{border-bottom:0}.person{font-weight:600;color:#35392f}.muted{color:#81857b}.badge{display:inline-block;border-radius:20px;padding:5px 9px;font-size:9px;font-weight:700}.badge-admin{background:#ecefe8;color:#4f5d43}.badge-customer{background:#f1f0ec;color:#76786f}.split{display:grid;grid-template-columns:minmax(0,1.35fr) minmax(300px,.8fr);gap:20px;margin-top:26px}.form-grid{display:grid;grid-template-columns:1fr 1fr;gap:14px}.field{min-width:0}.field.full{grid-column:1/-1}.field label{display:block;margin-bottom:7px;font-size:10px;font-weight:700;color:#5e6259}.field input,.field select{width:100%;height:42px;padding:0 11px;border:1px solid #dcddd5;border-radius:3px;background:#fff;color:#2d302a;font:12px 'DM Sans',Arial,sans-serif}.field input:focus,.field select:focus{outline:2px solid #dce5d5;border-color:#78866c}.button{border:0;border-radius:3px;background:#58644b;color:#fff;padding:12px 16px;font:700 10px 'DM Sans',Arial,sans-serif;letter-spacing:.06em;cursor:pointer}.button:hover{background:#414c38}.button-light{background:#eff1eb;color:#48523e}.button-danger{background:#f7ecea;color:#9a453e;padding:8px 10px}.inline-form{display:flex;gap:7px;align-items:center}.inline-form select{height:33px;border:1px solid #dedfd8;background:#fff;padding:0 7px;font:11px 'DM Sans',Arial,sans-serif}.actions{display:flex;gap:8px;align-items:center;flex-wrap:wrap}.form-errors{background:#f8e9e6;color:#8c352d;padding:11px 14px;margin:0 0 17px;font-size:11px;line-height:1.7}.empty{color:#858980;font-size:12px;padding:22px 10px;text-align:center}.hint{color:#8a8d84;font-size:10px;line-height:1.7;margin-top:12px}.revenue{color:#536448;font-weight:700}@media(max-width:1000px){.shell{grid-template-columns:210px minmax(0,1fr)}.content{padding:28px 23px}.topbar{padding:0 23px}.cards{grid-template-columns:repeat(2,minmax(0,1fr))}.split{grid-template-columns:1fr}}@media(max-width:700px){.shell{display:block}.sidebar{padding:15px;display:block}.brand{display:inline-block}.side-label{margin:18px 10px 5px}.side-link{display:inline-block;padding:9px 10px}.side-bottom{display:none}.topbar{height:58px;padding:0 17px}.content{padding:24px 15px 40px}.page-title{font-size:29px}.cards{gap:9px}.card{padding:14px}.card-value{font-size:25px}.panel{padding:16px}.form-grid{grid-template-columns:1fr}.field.full{grid-column:auto}.inline-form{align-items:flex-start;flex-direction:column}.inline-form .button{width:100%}}
  </style>
</head>
<body>
  <div class="shell">
    <aside class="sidebar">
      <a class="brand" href="index.php?page=admin">HHDM<small>FASHION · ADMIN</small></a>
      <div class="side-label">TỔNG QUAN</div>
      <a class="side-link <?= $currentPage === 'dashboard' ? 'active' : '' ?>" href="index.php?page=admin">Dashboard</a>
      <div class="side-label">QUẢN LÝ</div>
      <a class="side-link <?= $currentPage === 'accounts' ? 'active' : '' ?>" href="index.php?page=admin-accounts">Tài khoản</a>
      <div class="side-bottom"><a href="index.php">← Xem cửa hàng</a><form method="post" action="index.php?page=logout"><input type="hidden" name="csrf_token" value="<?= $escape($csrfToken) ?>"><button type="submit" style="border:0;background:none;color:#c6c9bf;font:11px 'DM Sans',Arial,sans-serif;padding:10px 12px;cursor:pointer">Đăng xuất</button></form></div>
    </aside>
    <main class="main">
      <header class="topbar"><strong>TRANG QUẢN TRỊ</strong><span>Xin chào, <?= $adminName ?></span></header>
      <div class="content">
        <?php if ($flash): ?><div class="flash" role="status"><?= $escape($flash) ?></div><?php endif; ?>
        <?php require $contentView; ?>
      </div>
    </main>
  </div>
</body>
</html>