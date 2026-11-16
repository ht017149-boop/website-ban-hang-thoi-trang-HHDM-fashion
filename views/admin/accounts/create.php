<span class="eyebrow">HHDM FASHION · QUẢN LÝ</span>
<h1 class="page-title">Thêm tài khoản</h1>
<p class="page-intro">Tạo tài khoản quản trị viên hoặc khách hàng mới.</p>

<div class="split">
  <section class="panel">
    <div class="panel-head"><h2>Tạo tài khoản mới</h2><a href="index.php?page=admin-accounts">← DANH SÁCH TÀI KHOẢN</a></div>
    <?php if ($accountErrors): ?><div class="form-errors" role="alert"><?php foreach ($accountErrors as $error): ?><div><?= $escape($error) ?></div><?php endforeach; ?></div><?php endif; ?>
    <form method="post" action="index.php?page=admin-account-create">
      <input type="hidden" name="csrf_token" value="<?= $escape($csrfToken) ?>">
      <input type="hidden" name="action" value="create_account">
      <div class="form-grid">
        <div class="field full"><label for="full_name">HỌ VÀ TÊN</label><input id="full_name" name="full_name" maxlength="150" value="<?= $escape($accountOld['full_name']) ?>" required></div>
        <div class="field"><label for="email">EMAIL</label><input id="email" name="email" type="email" maxlength="191" autocomplete="email" value="<?= $escape($accountOld['email']) ?>" required></div>
        <div class="field"><label for="phone">SỐ ĐIỆN THOẠI</label><input id="phone" name="phone" maxlength="20" autocomplete="tel" value="<?= $escape($accountOld['phone']) ?>"></div>
        <div class="field"><label for="password">MẬT KHẨU</label><input id="password" name="password" type="password" minlength="8" autocomplete="new-password" required></div>
        <div class="field"><label for="password_confirmation">XÁC NHẬN MẬT KHẨU</label><input id="password_confirmation" name="password_confirmation" type="password" minlength="8" autocomplete="new-password" required></div>
        <div class="field full"><label for="role">VAI TRÒ</label><select id="role" name="role"><option value="customer" <?= $accountOld['role'] === 'customer' ? 'selected' : '' ?>>Khách hàng</option><option value="admin" <?= $accountOld['role'] === 'admin' ? 'selected' : '' ?>>Quản trị viên</option></select></div>
        <div class="field full"><div class="actions"><button class="button" type="submit">TẠO TÀI KHOẢN</button><a class="button button-light" href="index.php?page=admin-accounts" style="text-decoration:none">HỦY</a></div></div>
      </div>
    </form>
    <p class="hint">Mật khẩu phải có ít nhất 8 ký tự. Chỉ quản trị viên đã đăng nhập mới có thể tạo tài khoản quản trị.</p>
  </section>

  <section class="panel">
    <div class="panel-head"><h2>Lưu ý quản lý</h2></div>
    <p class="page-intro">Tài khoản có đơn hàng sẽ không thể bị xóa để bảo toàn lịch sử mua hàng. Bạn cũng không thể tự hạ quyền hoặc xóa chính mình, cũng như xóa quản trị viên cuối cùng.</p>
    <p class="hint">Để khởi tạo quản trị viên đầu tiên, chạy <strong>php database/bootstrap_admin.php</strong> trong terminal tại thư mục dự án. Sau đó có thể tạo các tài khoản còn lại ở đây.</p>
  </section>
</div>
