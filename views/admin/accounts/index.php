<span class="eyebrow">HHDM FASHION · QUẢN LÝ</span>
<h1 class="page-title">Tài khoản</h1>
<p class="page-intro">Tạo tài khoản quản trị viên hoặc khách hàng, phân quyền và quản lý người dùng.</p>

<section class="panel">
  <div class="panel-head"><h2>Danh sách tài khoản <span class="muted">(<?= count(
      $accounts,
  ) ?>)</span></h2></div>
  <div class="actions" style="margin-bottom:15px"><a class="button" href="index.php?page=admin-account-create" style="text-decoration:none">＋ THÊM TÀI KHOẢN</a></div>
  <?php if (!$accounts): ?>
    <div class="empty">Chưa có tài khoản nào.</div>
  <?php else: ?>
    <div class="table-wrap"><table>
      <thead><tr><th>TÀI KHOẢN</th><th>EMAIL / ĐIỆN THOẠI</th><th>VAI TRÒ</th><th>NGÀY TẠO</th><th>THAO TÁC</th></tr></thead>
      <tbody><?php foreach ($accounts as $account): ?>
        <tr>
          <td class="person">
            <?= $escape($account["full_name"]) ?>
            <?php if ((int) $account["user_id"] === (int) $_SESSION["user"]["user_id"]): ?>
              <span class="muted">(bạn)</span>
            <?php endif; ?>
          </td>
          <td><div><?= $escape(
              $account["email"],
          ) ?></div><div class="muted"><?= $escape(
    $account["phone"] ?: "—",
) ?></div></td>
          <td><span class="badge <?= $account["role"] === "admin"
              ? "badge-admin"
              : "badge-customer" ?>"><?= $account["role"] === "admin"
    ? "Quản trị viên"
    : "Khách hàng" ?></span></td>
          <td class="muted"><?= $escape(
              date("d/m/Y", strtotime($account["created_at"])),
          ) ?></td>
          <td>
            <?php if (
                (int) $account["user_id"] !== (int) $_SESSION["user"]["user_id"]
            ): ?>
              <div class="actions">
                <form class="inline-form" method="post" action="index.php?page=admin-accounts">
                  <input type="hidden" name="csrf_token" value="<?= $escape(
                      $csrfToken,
                  ) ?>"><input type="hidden" name="action" value="update_role"><input type="hidden" name="user_id" value="<?= (int) $account[
    "user_id"
] ?>">
                  <select name="role" aria-label="Vai trò của <?= $escape(
                      $account["full_name"],
                  ) ?>"><option value="customer" <?= $account["role"] ===
"customer"
    ? "selected"
    : "" ?>>Khách hàng</option><option value="admin" <?= $account["role"] ===
"admin"
    ? "selected"
    : "" ?>>Quản trị viên</option></select>
                  <button class="button button-light" type="submit">Lưu</button>
                </form>
                <form method="post" action="index.php?page=admin-accounts" data-confirm="Bạn có chắc muốn xóa tài khoản này?">
                  <input type="hidden" name="csrf_token" value="<?= $escape(
                      $csrfToken,
                  ) ?>"><input type="hidden" name="action" value="delete_account"><input type="hidden" name="user_id" value="<?= (int) $account[
    "user_id"
] ?>">
                  <button class="button button-danger" type="submit">Xóa</button>
                </form>
              </div>
            <?php else: ?><span class="muted">Tài khoản hiện tại</span><?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?></tbody>
    </table></div>
  <?php endif; ?>
</section>
