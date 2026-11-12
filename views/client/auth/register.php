<?php
$e = static fn(string $value): string => htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
?>
<!doctype html>
<html lang="vi">
<head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Tạo tài khoản | HHDM Fashion</title>
  <link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:wght@500;600;700&display=swap" rel="stylesheet">
  <style>
    *{box-sizing:border-box}body{margin:0;min-height:100vh;background:#f7f5f0;color:#242720;font-family:'DM Sans',Arial,sans-serif;display:grid;grid-template-columns:1.05fr .95fr}.visual{position:relative;min-height:100vh;background:#a9a49b;overflow:hidden}.visual img{width:100%;height:100%;position:absolute;inset:0;object-fit:cover;filter:brightness(.77)}.visual:after{content:'';position:absolute;inset:0;background:linear-gradient(0deg,rgba(20,24,20,.58),transparent 65%)}.visual-copy{position:absolute;z-index:1;left:10%;right:10%;bottom:9%;color:white}.visual-copy span{font-size:10px;letter-spacing:.22em;text-transform:uppercase}.visual-copy h2{font:500 clamp(38px,5vw,64px)/1.1 'Playfair Display',Georgia,serif;margin:14px 0}.visual-copy p{font-size:13px;line-height:1.8;max-width:400px;color:rgba(255,255,255,.83)}.form-side{display:flex;align-items:center;justify-content:center;padding:50px 7%}.form-wrap{width:min(430px,100%)}.brand{display:inline-block;color:#252820;text-decoration:none;text-align:center;font:700 25px 'Playfair Display',Georgia,serif;letter-spacing:.06em}.brand small{display:block;font:500 8px 'DM Sans',Arial,sans-serif;letter-spacing:.35em;margin-top:2px}.back{display:block;margin:35px 0 18px;color:#66705b;text-decoration:none;font-size:11px;letter-spacing:.08em}.form-wrap h1{font:500 40px 'Playfair Display',Georgia,serif;margin:0 0 9px}.intro{margin:0 0 25px;color:#777a72;font-size:12px;line-height:1.7}.field{margin:0 0 15px}.field label{display:block;font-size:10px;font-weight:600;letter-spacing:.08em;margin-bottom:7px}.field input{width:100%;height:46px;border:1px solid #dcd9d1;background:#fff;padding:0 13px;font:12px 'DM Sans',Arial,sans-serif;outline:none}.field input:focus{border-color:#58644b;box-shadow:0 0 0 2px rgba(88,100,75,.1)}.grid{display:grid;grid-template-columns:1fr 1fr;gap:12px}.submit{width:100%;height:49px;background:#58644b;border:0;color:white;font:600 10px 'DM Sans',Arial,sans-serif;letter-spacing:.15em;text-transform:uppercase;cursor:pointer;margin-top:5px}.submit:hover{background:#414c38}.errors{background:#f8e9e6;color:#8c352d;padding:12px 15px;margin:0 0 18px;font-size:11px;line-height:1.8}.form-foot{text-align:center;color:#777a72;font-size:11px;margin-top:19px}.form-foot a{color:#58644b;font-weight:700;text-decoration:none}.fine{font-size:9px;line-height:1.7;color:#92948d;margin:14px 0 0}@media(max-width:760px){body{display:block}.visual{display:none}.form-side{min-height:100vh;padding:35px 7%}.back{margin-top:30px}}@media(max-width:420px){.form-wrap h1{font-size:35px}.grid{grid-template-columns:1fr;gap:0}}
  </style>
</head>
<body>
  <aside class="visual"><img src="https://images.unsplash.com/photo-1483985988355-763728e1935b?auto=format&fit=crop&w=1400&q=85" alt="Thời trang HHDM"><div class="visual-copy"><span>HHDM FASHION</span><h2>Phong cách bắt đầu từ bạn.</h2><p>Tạo tài khoản để cùng khám phá những thiết kế bạn sẽ yêu thích mỗi ngày.</p></div></aside>
  <main class="form-side"><div class="form-wrap">
    <a class="brand" href="index.php">HHDM<small>FASHION</small></a>
    <a class="back" href="index.php">←&nbsp; QUAY LẠI TRANG CHỦ</a>
    <h1>Tạo tài khoản</h1><p class="intro">Tham gia HHDM để cập nhật bộ sưu tập và lưu lại phong cách yêu thích.</p>
    <?php if ($errors): ?><div class="errors" role="alert"><?php foreach ($errors as $error): ?><div><?= $e($error) ?></div><?php endforeach; ?></div><?php endif; ?>
    <form method="post" action="index.php?page=register" autocomplete="on">
      <input type="hidden" name="csrf_token" value="<?= $e($csrfToken) ?>">
      <div class="field"><label for="full_name">HỌ VÀ TÊN</label><input id="full_name" name="full_name" type="text" maxlength="150" autocomplete="name" value="<?= $e($old['full_name']) ?>" required></div>
      <div class="field"><label for="email">EMAIL</label><input id="email" name="email" type="email" maxlength="191" autocomplete="email" value="<?= $e($old['email']) ?>" required></div>
      <div class="field"><label for="phone">SỐ ĐIỆN THOẠI <span style="font-weight:400;color:#999">(không bắt buộc)</span></label><input id="phone" name="phone" type="tel" maxlength="20" autocomplete="tel" value="<?= $e($old['phone']) ?>"></div>
      <div class="grid"><div class="field"><label for="password">MẬT KHẨU</label><input id="password" name="password" type="password" minlength="8" autocomplete="new-password" required></div><div class="field"><label for="password_confirmation">NHẬP LẠI MẬT KHẨU</label><input id="password_confirmation" name="password_confirmation" type="password" minlength="8" autocomplete="new-password" required></div></div>
      <button class="submit" type="submit">Tạo tài khoản&nbsp; ↗</button>
      <p class="fine">Bằng việc đăng ký, bạn đồng ý với các điều khoản sử dụng và chính sách bảo mật của HHDM Fashion.</p>
    </form>
    <p class="form-foot">Bạn đã có tài khoản? <a href="index.php?page=login">Đăng nhập</a></p>
  </div></main>
</body>
</html>
