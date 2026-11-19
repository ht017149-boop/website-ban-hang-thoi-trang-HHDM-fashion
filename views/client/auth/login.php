<?php
$e = static fn(string $value): string => htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
?>
<!doctype html>
<html lang="vi">
<head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Đăng nhập | HHDM Fashion</title>
  <link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:wght@500;600;700&display=swap" rel="stylesheet">
  <style>
    *{box-sizing:border-box}body{margin:0;min-height:100vh;background:#f7f5f0;color:#242720;font-family:'DM Sans',Arial,sans-serif;display:grid;grid-template-columns:1.05fr .95fr}.visual{position:relative;min-height:100vh;background:#a9a49b;overflow:hidden}.visual img{width:100%;height:100%;position:absolute;inset:0;object-fit:cover;filter:brightness(.77)}.visual:after{content:'';position:absolute;inset:0;background:linear-gradient(0deg,rgba(20,24,20,.58),transparent 65%)}.visual-copy{position:absolute;z-index:1;left:10%;right:10%;bottom:9%;color:white}.visual-copy span{font-size:10px;letter-spacing:.22em;text-transform:uppercase}.visual-copy h2{font:500 clamp(38px,5vw,64px)/1.1 'Playfair Display',Georgia,serif;margin:14px 0}.visual-copy p{font-size:13px;line-height:1.8;max-width:400px;color:rgba(255,255,255,.83)}.form-side{display:flex;align-items:center;justify-content:center;padding:50px 7%}.form-wrap{width:min(430px,100%)}.brand{display:inline-block;color:#252820;text-decoration:none;text-align:center;font:700 25px 'Playfair Display',Georgia,serif;letter-spacing:.06em}.brand small{display:block;font:500 8px 'DM Sans',Arial,sans-serif;letter-spacing:.35em;margin-top:2px}.back{display:block;margin:38px 0 20px;color:#66705b;text-decoration:none;font-size:11px;letter-spacing:.08em}.form-wrap h1{font:500 42px 'Playfair Display',Georgia,serif;margin:0 0 9px}.intro{margin:0 0 28px;color:#777a72;font-size:12px;line-height:1.7}.field{margin:0 0 17px}.field label{display:block;font-size:10px;font-weight:600;letter-spacing:.08em;margin-bottom:7px}.field input{width:100%;height:48px;border:1px solid #dcd9d1;background:#fff;padding:0 13px;font:12px 'DM Sans',Arial,sans-serif;outline:none}.field input:focus{border-color:#58644b;box-shadow:0 0 0 2px rgba(88,100,75,.1)}.submit{width:100%;height:50px;background:#58644b;border:0;color:white;font:600 10px 'DM Sans',Arial,sans-serif;letter-spacing:.15em;text-transform:uppercase;cursor:pointer;margin-top:7px}.submit:hover{background:#414c38}.errors{background:#f8e9e6;color:#8c352d;padding:12px 15px;margin:0 0 18px;font-size:11px;line-height:1.8}.form-foot{text-align:center;color:#777a72;font-size:11px;margin-top:22px}.form-foot a{color:#58644b;font-weight:700;text-decoration:none}.fine{font-size:10px;color:#92948d;margin-top:20px;text-align:center;line-height:1.7}@media(max-width:760px){body{display:block}.visual{display:none}.form-side{min-height:100vh;padding:35px 7%}.back{margin-top:35px}}
  </style>
</head>
<body>
  <aside class="visual"><img src="https://images.unsplash.com/photo-1485230895905-ec40ba36b9bc?auto=format&fit=crop&w=1400&q=85" alt="Thời trang HHDM"><div class="visual-copy"><span>HHDM FASHION</span><h2>Chào mừng bạn trở lại.</h2><p>Phong cách của bạn luôn có một chỗ ở đây.</p></div></aside>
  <main class="form-side"><div class="form-wrap">
    <a class="brand" href="index.php">HHDM<small>FASHION</small></a>
    <a class="back" href="index.php">←&nbsp; QUAY LẠI TRANG CHỦ</a>
    <h1>Đăng nhập</h1><p class="intro">Đăng nhập để tiếp tục khám phá HHDM Fashion.</p>
    <?php if ($errors): ?><div class="errors" role="alert"><?php foreach ($errors as $error): ?><div><?= $e($error) ?></div><?php endforeach; ?></div><?php endif; ?>
    <form method="post" action="index.php?page=login" autocomplete="on">
      <input type="hidden" name="csrf_token" value="<?= $e($csrfToken) ?>">
      <div class="field"><label for="email">EMAIL</label><input id="email" name="email" type="email" maxlength="191" autocomplete="email" value="<?= $e($email) ?>" required autofocus></div>
      <div class="field"><label for="password">MẬT KHẨU</label><input id="password" name="password" type="password" autocomplete="current-password" required></div>
      <button class="submit" type="submit">Đăng nhập&nbsp; ↗</button>
    </form>
    <p class="form-foot">Chưa có tài khoản? <a href="index.php?page=register">Tạo tài khoản</a></p>
    <p class="fine">Mật khẩu của bạn được bảo vệ an toàn.</p>
  </div></main>
</body>
</html>
