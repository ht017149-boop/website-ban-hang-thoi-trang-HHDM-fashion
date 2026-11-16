<?php
$user = $_SESSION['user'] ?? null;
$escape = static fn($value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
$imageValue = (string) ($product['image_url'] ?? '');
$productImageUrl = preg_match('/^https?:\/\//i', $imageValue)
  ? $imageValue
  : (strpos($imageValue, 'assets/uploads/') === 0
    ? BASE_URL . $imageValue
    : ($imageValue !== '' ? 'https://images.unsplash.com/' . $imageValue . '?auto=format&fit=crop&w=900&q=85' : ''));
$hasDiscount = $product['old'] !== null;
?>
<!doctype html>
<html lang="vi">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="theme-color" content="#fff">
  <meta name="description" content="<?= $escape(mb_substr((string) ($product['description'] ?? $product['product_name']), 0, 160)) ?>">
  <title><?= $escape($product['product_name']) ?> | HHDM Fashion</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700;800&family=Playfair+Display:wght@500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/home.css">
  <style>
    .detail-main { max-width: 1180px; margin: 0 auto; padding: 38px 22px 70px; }
    .breadcrumb { color: #999b95; font-size: 10px; letter-spacing: .07em; margin-bottom: 22px; }
    .breadcrumb a { color: inherit; text-decoration: none; }
    .breadcrumb a:hover { color: var(--ink); }
    .detail-layout { display: grid; grid-template-columns: minmax(0, 460px) minmax(0, 1fr); gap: 42px; align-items: start; }
    .detail-image { width: 100%; aspect-ratio: .76; object-fit: cover; background: #f0eee9; display: block; }
    .detail-name { font: 500 30px 'Playfair Display', Georgia, serif; margin: 0 0 6px; }
    .detail-code { color: #999b95; font-size: 9px; letter-spacing: .07em; }
    .detail-price { display: flex; align-items: center; gap: 10px; margin: 16px 0 4px; }
    .detail-price strong { font-size: 20px; }
    .detail-price del { color: #999b95; font-size: 13px; }
    .detail-discount { color: var(--red); font-size: 11px; font-weight: 700; }
    .detail-section-label { display: block; margin: 26px 0 10px; font-size: 10px; font-weight: 700; letter-spacing: .09em; }
    .detail-sizes { display: flex; gap: 8px; flex-wrap: wrap; }
    .detail-size { min-width: 46px; height: 38px; padding: 0 12px; border: 1px solid #d8d9d4; background: #fff; font: 600 11px 'DM Sans', Arial, sans-serif; cursor: pointer; }
    .detail-size:hover, .detail-size.is-selected { border-color: var(--ink); color: #fff; background: var(--ink); }
    .detail-hint { margin-top: 10px; color: var(--red); font-size: 10px; visibility: hidden; }
    .detail-hint.is-visible { visibility: visible; }
    .detail-actions { display: flex; gap: 12px; margin-top: 22px; flex-wrap: wrap; }
    .cart-button, .buy-button { min-height: 48px; padding: 0 26px; border: 0; font: 700 11px 'DM Sans', Arial, sans-serif; letter-spacing: .08em; cursor: pointer; }
    .cart-button { background: #fff; border: 1px solid var(--ink); color: var(--ink); }
    .cart-button:hover { background: #f0eee9; }
    .buy-button { background: var(--ink); color: #fff; }
    .buy-button:hover { background: #333; }
    .detail-desc { margin-top: 8px; color: #55574f; font-size: 12px; line-height: 1.8; white-space: pre-line; }
    .comments-list { display: grid; gap: 14px; margin-top: 14px; }
    .comment-card { border: 1px solid #eceae4; background: #fff; padding: 15px 17px; }
    .comment-head { display: flex; align-items: center; gap: 10px; margin-bottom: 8px; }
    .comment-avatar { width: 30px; height: 30px; border-radius: 50%; background: var(--ink); color: #fff; display: inline-flex; align-items: center; justify-content: center; font-size: 10px; font-weight: 700; }
    .comment-head strong { font-size: 11px; display: block; }
    .comment-head small { color: #999b95; font-size: 9px; }
    .comment-stars { color: #d9a441; font-size: 10px; letter-spacing: 2px; }
    .comment-content { margin: 0; color: #55574f; font-size: 12px; line-height: 1.7; }
    .no-comment { color: #999b95; font-size: 11px; padding: 18px 0; }
    @media (max-width: 860px) { .detail-layout { grid-template-columns: 1fr; } }
  </style>
</head>
<body>
  <div class="announcement"><span>GIAO HÀNG MIỄN PHÍ</span> cho đơn hàng từ 799.000₫ <b>✦</b> Ưu đãi thành viên mới đến 15%</div>
  <header class="site-header">
    <div class="header-main">
      <a class="brand" href="index.php?page=home" aria-label="HHDM Fashion trang chủ">HHDM<span>FASHION</span></a>
      <div class="account-actions">
        <a href="index.php?page=home"><span class="action-icon">←</span><span>Về trang chủ</span></a>
        <button class="cart-trigger" id="cart-trigger" type="button" aria-label="Mở giỏ hàng"><span class="action-icon">♧</span><span>Giỏ hàng</span><b id="cart-count">0</b></button>
      </div>
    </div>
  </header>
  <main class="detail-main">
    <nav class="breadcrumb"><a href="index.php?page=home">TRANG CHỦ</a> / <a href="index.php?page=home#<?= $escape($product['category_slug'] ?? '') ?>"><?= $escape(mb_strtoupper($product['category_name'] ?? '')) ?></a> / <span><?= $escape($product['product_name']) ?></span></nav>
    <div class="detail-layout">
      <div>
        <?php if ($productImageUrl !== ''): ?><img class="detail-image" src="<?= $escape($productImageUrl) ?>" alt="<?= $escape($product['product_name']) ?>"><?php else: ?><div class="detail-image" role="img" aria-label="Sản phẩm chưa có ảnh"></div><?php endif; ?>
      </div>
      <div>
        <div class="detail-code">HHDM / <?= $escape($product['code']) ?> · <?= $escape(mb_strtoupper($product['category_name'] ?? '')) ?></div>
        <h1 class="detail-name"><?= $escape($product['product_name']) ?></h1>
        <div class="detail-price"><strong><?= number_format((float) $product['price'], 0, ',', '.') ?>₫</strong><?php if ($hasDiscount): ?><del><?= number_format((float) $product['old'], 0, ',', '.') ?>₫</del><span class="detail-discount">-<?= (int) round((1 - $product['price'] / $product['old']) * 100) ?>%</span><?php endif; ?></div>
        <span class="detail-section-label">KÍCH CỠ</span>
        <?php if ($product['sizes']): ?>
          <div class="detail-sizes" id="detail-sizes"><?php foreach ($product['sizes'] as $size): ?><button class="detail-size" type="button" data-size="<?= $escape($size) ?>"><?= $escape($size) ?></button><?php endforeach; ?></div>
          <p class="detail-hint" id="size-hint">Vui lòng chọn size trước khi thêm vào giỏ hoặc mua hàng.</p>
        <?php else: ?>
          <p class="no-comment">Sản phẩm hiện chưa có kích cỡ khả dụng.</p>
        <?php endif; ?>
        <div class="detail-actions">
          <button class="cart-button" type="button" id="add-to-cart" data-name="<?= $escape($product['product_name']) ?>" data-code="<?= $escape($product['code']) ?>" data-price="<?= (int) $product['price'] ?>" data-image="<?= $escape($productImageUrl) ?>">THÊM VÀO GIỎ</button>
          <button class="buy-button" type="button" id="buy-now" data-name="<?= $escape($product['product_name']) ?>" data-code="<?= $escape($product['code']) ?>" data-price="<?= (int) $product['price'] ?>" data-image="<?= $escape($productImageUrl) ?>">MUA NGAY</button>
        </div>
        <span class="detail-section-label">MÔ TẢ SẢN PHẨM</span>
        <p class="detail-desc"><?= $escape($product['description'] !== null && $product['description'] !== '' ? $product['description'] : 'Sản phẩm chính hãng HHDM Fashion, chất liệu thoáng mát, phom dáng dễ mặc.') ?></p>
      </div>
    </div>
    <section id="comments">
      <div class="section-heading" style="margin-top:54px"><div><span class="section-kicker">HHDM REVIEW</span><h2>BÌNH LUẬN (<?= count($comments) ?>)</h2></div></div>
      <?php if (!$comments): ?>
        <p class="no-comment">Chưa có bình luận nào cho sản phẩm này.</p>
      <?php else: ?>
        <div class="comments-list"><?php foreach ($comments as $comment): ?>
          <article class="comment-card">
            <div class="comment-head">
              <span class="comment-avatar"><?= $escape(mb_strtoupper(mb_substr((string) $comment['full_name'], 0, 2))) ?></span>
              <div><strong><?= $escape($comment['full_name']) ?></strong><small><?= $escape(date('d/m/Y H:i', strtotime($comment['created_at']))) ?></small></div>
              <?php if ($comment['rating'] !== null): ?><span class="comment-stars"><?= $escape(str_repeat('★', (int) $comment['rating']) . str_repeat('☆', max(0, 5 - (int) $comment['rating']))) ?></span><?php endif; ?>
            </div>
            <p class="comment-content"><?= $escape($comment['content']) ?></p>
          </article>
        <?php endforeach; ?></div>
      <?php endif; ?>
    </section>
  </main>
  <aside class="cart-drawer" id="cart-drawer" aria-hidden="true"><div class="cart-head"><div><span class="section-kicker">HHDM SHOPPING BAG</span><h2>Giỏ hàng <span id="drawer-count">(0)</span></h2></div><button id="cart-close" type="button" aria-label="Đóng giỏ hàng">×</button></div><div class="cart-items" id="cart-items"><p class="empty-cart">Giỏ hàng của bạn đang trống.<br>Thêm món đồ bạn yêu thích nhé!</p></div><div class="cart-summary"><div><span>Tạm tính</span><strong id="cart-subtotal">0₫</strong></div><small>Phí vận chuyển sẽ được tính ở bước tiếp theo.</small><button class="checkout-button" type="button" disabled>TIẾP TỤC MUA SẮM</button></div></aside><div class="drawer-backdrop" id="drawer-backdrop"></div>
  <div class="toast" id="toast" role="status" aria-live="polite"></div>
  <script>
  (function(){
    const cart=[];const drawer=document.getElementById('cart-drawer');const backdrop=document.getElementById('drawer-backdrop');const money=n=>new Intl.NumberFormat('vi-VN').format(n)+'₫';
    let selectedSize=null;const sizeWrap=document.getElementById('detail-sizes');const hint=document.getElementById('size-hint');
    function showCart(){drawer.classList.add('is-open');backdrop.classList.add('is-open');drawer.setAttribute('aria-hidden','false');document.body.classList.add('drawer-open');}
    function hideCart(){drawer.classList.remove('is-open');backdrop.classList.remove('is-open');drawer.setAttribute('aria-hidden','true');document.body.classList.remove('drawer-open');}
    function renderCart(){const count=cart.reduce((sum,item)=>sum+item.quantity,0);document.getElementById('cart-count').textContent=count;document.getElementById('drawer-count').textContent='('+count+')';document.getElementById('cart-subtotal').textContent=money(cart.reduce((sum,item)=>sum+item.price*item.quantity,0));const list=document.getElementById('cart-items');if(!cart.length){list.innerHTML='<p class="empty-cart">Giỏ hàng của bạn đang trống.<br>Thêm món đồ bạn yêu thích nhé!</p>';document.querySelector('.checkout-button').disabled=true;return;}document.querySelector('.checkout-button').disabled=false;list.innerHTML=cart.map(item=>'<article class="cart-item"><img src="'+item.image+'" alt=""><div><strong>'+item.name+'</strong><small>Size: '+item.size+'</small><small>'+money(item.price)+' × '+item.quantity+'</small></div><button type="button" data-remove="'+item.key+'" aria-label="Xóa '+item.name+'">×</button></article>').join('');}
    document.getElementById('cart-trigger').addEventListener('click',showCart);document.getElementById('cart-close').addEventListener('click',hideCart);backdrop.addEventListener('click',hideCart);document.addEventListener('keydown',e=>{if(e.key==='Escape')hideCart();});
    let toastTimer;function toast(message){const el=document.getElementById('toast');el.textContent=message;el.classList.add('is-visible');clearTimeout(toastTimer);toastTimer=setTimeout(()=>el.classList.remove('is-visible'),2400);}
    function requireSize(){if(!selectedSize){if(hint)hint.classList.add('is-visible');toast('Vui lòng chọn size trước khi tiếp tục.');return false;}if(hint)hint.classList.remove('is-visible');return true;}
    function addToCart(openAfter){if(!requireSize())return;const button=openAfter?document.getElementById('buy-now'):document.getElementById('add-to-cart');const key=button.dataset.code+'-'+selectedSize;const existing=cart.find(item=>item.key===key);if(existing){existing.quantity++;}else{cart.push({key,name:button.dataset.name,code:button.dataset.code,price:Number(button.dataset.price),size:selectedSize,image:button.dataset.image,quantity:1});}renderCart();if(openAfter){showCart();}else{toast('Đã thêm '+button.dataset.name+' (size '+selectedSize+') vào giỏ hàng.');}}
    if(sizeWrap){sizeWrap.addEventListener('click',event=>{const option=event.target.closest('.detail-size');if(!option)return;sizeWrap.querySelectorAll('.detail-size').forEach(el=>el.classList.remove('is-selected'));option.classList.add('is-selected');selectedSize=option.dataset.size;if(hint)hint.classList.remove('is-visible');});}
    document.getElementById('add-to-cart').addEventListener('click',()=>addToCart(false));
    document.getElementById('buy-now').addEventListener('click',()=>addToCart(true));
    document.addEventListener('click',event=>{const remove=event.target.closest('[data-remove]');if(remove){const index=cart.findIndex(item=>item.key===remove.dataset.remove);if(index>-1)cart.splice(index,1);renderCart();}});
    document.querySelector('.checkout-button').addEventListener('click',()=>toast('Chức năng thanh toán sẽ sớm ra mắt.'));
    renderCart();
  })();
  </script>
</body>
</html>
