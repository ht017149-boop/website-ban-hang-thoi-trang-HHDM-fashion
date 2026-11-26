<?php
$user = $_SESSION['user'] ?? null;
$escape = static fn($value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
$newProducts = $homepageData['new_products'];
$categories = $homepageData['categories'];

$heroSlides = [
    [
        'image_url' => 'https://images.unsplash.com/photo-1539109136881-3be0616acf4b?auto=format&fit=crop&w=2200&q=88',
        'image_alt' => 'Bộ sưu tập thời trang mới HHDM',
        'eyebrow' => 'HHDM · COLLECTION 2026',
        'title' => 'PHONG CÁCH, KHÔNG GIỚI HẠN',
        'description' => 'Khám phá thời trang nữ, nam và phụ kiện cho mọi nhịp sống.',
        'button_label' => 'KHÁM PHÁ BỘ SƯU TẬP',
        'target_url' => '#new-arrivals',
    ],
    [
        'image_url' => 'https://images.unsplash.com/photo-1483985988355-763728e1935b?auto=format&fit=crop&w=2200&q=88',
        'image_alt' => 'Ưu đãi thời trang HHDM cho nữ và nam',
        'eyebrow' => 'HHDM MEMBER DAYS',
        'title' => 'ƯU ĐÃI MÙA NÀY, ĐẾN 50%',
        'description' => 'Thêm yêu thích, bớt một chút giá cho những thiết kế được yêu thích nhất.',
        'button_label' => 'XEM ƯU ĐÃI',
        'target_url' => '#sale',
    ],
    [
        'image_url' => 'https://images.unsplash.com/photo-1617137968427-85924c800a22?auto=format&fit=crop&w=2200&q=88',
        'image_alt' => 'Bộ sưu tập thời trang nam HHDM',
        'eyebrow' => 'HHDM · MENSWEAR',
        'title' => 'PHONG ĐỘ MỖI NGÀY',
        'description' => 'Thiết kế nam hiện đại, thoải mái và linh hoạt cho mọi hoạt động.',
        'button_label' => 'XEM THỜI TRANG NAM',
        'target_url' => '#menswear',
    ],
];
$heroSlideCount = count($heroSlides);
?>
<!doctype html>
<html lang="vi">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="theme-color" content="#fff">
  <meta name="description" content="Khám phá thời trang nữ, nam và phụ kiện HHDM: thiết kế dễ mặc, bộ sưu tập mới và ưu đãi theo mùa.">
  <title>HHDM Fashion — Thời trang nữ, nam & phụ kiện</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700;800&family=Playfair+Display:wght@500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/home.css?v=account-drawer-delivery-greeting">
</head>
<body data-logged-in="<?= !empty($user) ? 'true' : 'false' ?>" data-user-id="<?= (int) ($user['user_id'] ?? 0) ?>" data-user-name="<?= $escape($user['full_name'] ?? '') ?>" data-csrf-token="<?= $escape($_SESSION['csrf_token'] ?? '') ?>">
  <div class="announcement"><span>GIAO HÀNG MIỄN PHÍ</span> cho đơn hàng từ 799.000₫ <b>✦</b> Ưu đãi thành viên mới đến 15%</div>
  <header class="site-header">
    <div class="header-main">
      <button class="mobile-menu" type="button" aria-label="Mở danh mục" aria-expanded="false">☰</button>
      <a class="brand" href="#home" aria-label="HHDM Fashion trang chủ">HHDM<span>FASHION</span></a>
      <form class="search-form" id="search-form" role="search"><label class="sr-only" for="search-input">Tìm kiếm sản phẩm</label><input id="search-input" type="search" placeholder="Bạn đang tìm sản phẩm nào?"><button type="submit" aria-label="Tìm kiếm">⌕</button></form>
      <div class="account-actions">
        <?php if ($user): ?>
          <span class="welcome">Xin chào, <?= $escape($user['full_name'] ?? 'bạn') ?></span>
          <?php if (($user['role'] ?? '') === 'admin'): ?>
            <a href="index.php?page=admin">Quản trị</a>
            <button class="cart-trigger" id="cart-trigger" type="button" aria-label="Mở giỏ hàng"><span class="action-icon">♧</span><span>Giỏ hàng</span><b id="cart-count">0</b></button>
            <form method="post" action="index.php?page=logout" class="logout-form">
              <input type="hidden" name="csrf_token" value="<?= $escape($_SESSION['csrf_token'] ?? '') ?>">
              <button type="submit">Đăng xuất</button>
            </form>
          <?php else: ?>
            <button class="cart-trigger" id="cart-trigger" type="button" aria-label="Mở giỏ hàng"><span class="action-icon">♧</span><span>Giỏ hàng</span><b id="cart-count">0</b></button>
            <button class="account-trigger" id="account-trigger" type="button" aria-label="Mở menu tài khoản" aria-expanded="false" aria-controls="account-drawer"><span class="action-icon">♙</span><span class="account-trigger-label">Tài khoản của tôi</span></button>
          <?php endif; ?>
        <?php else: ?>
          <button class="cart-trigger" id="cart-trigger" type="button" aria-label="Mở giỏ hàng"><span class="action-icon">♧</span><span>Giỏ hàng</span><b id="cart-count">0</b></button>
          <a href="index.php?page=login"><span class="action-icon">♙</span><span>Tài khoản</span></a>
        <?php endif; ?>
      </div>
    </div>
    <nav class="main-nav" id="main-nav" aria-label="Danh mục chính"><a class="sale-link" href="#new-arrivals">HÀNG MỚI</a><?php foreach ($categories as $category): ?><a href="#<?= $escape($category['slug']) ?>"><?= $escape(mb_strtoupper($category['category_name'])) ?></a><?php endforeach; ?><a href="#collection">BỘ SƯU TẬP</a><a class="sale-link" href="#sale">ƯU ĐÃI</a><a href="#stories">GÓC PHỐI ĐỒ</a><a href="#footer">LIÊN HỆ</a></nav>
  </header>
  <main id="home">
    <section class="hero" id="hero-slideshow" aria-label="Banner nổi bật HHDM" aria-roledescription="carousel">
      <?php foreach ($heroSlides as $slideIndex => $slide): ?>
        <div class="hero-slide<?= $slideIndex === 0 ? ' is-active' : '' ?>" data-slide="<?= $slideIndex ?>" role="group" aria-roledescription="slide" aria-label="Slide <?= $slideIndex + 1 ?>/<?= $heroSlideCount ?>"<?= $slideIndex === 0 ? '' : ' aria-hidden="true"' ?>>
          <img src="<?= $escape($slide['image_url']) ?>" alt="<?= $escape($slide['image_alt']) ?>"<?= $slideIndex === 0 ? ' fetchpriority="high"' : ' loading="lazy"' ?>>
          <div class="hero-shade"></div>
          <div class="hero-copy"><span class="hero-kicker"><?= $escape($slide['eyebrow']) ?></span><h1><?= nl2br($escape($slide['title'])) ?></h1><p><?= $escape($slide['description']) ?></p><a class="primary-button" href="<?= $escape($slide['target_url']) ?>" tabindex="<?= $slideIndex === 0 ? '0' : '-1' ?>"><?= $escape($slide['button_label']) ?> <span>→</span></a></div>
        </div>
      <?php endforeach; ?>
      <button class="hero-arrow hero-prev" id="hero-prev" type="button" aria-label="Slide trước">←</button>
      <button class="hero-arrow hero-next" id="hero-next" type="button" aria-label="Slide sau">→</button>
      <div class="hero-dots" id="hero-dots" role="tablist" aria-label="Chọn slide"><?php foreach ($heroSlides as $slideIndex => $slide): ?><button class="hero-dot<?= $slideIndex === 0 ? ' is-active' : '' ?>" type="button" role="tab" aria-label="Tới slide <?= $slideIndex + 1 ?>" data-slide="<?= $slideIndex ?>"></button><?php endforeach; ?></div>
      <div class="hero-count">HHDM <i></i> 2026</div>
    </section>
    <section class="category-strip" aria-label="Danh mục nổi bật"><?php foreach ($categories as $category): ?><a href="#<?= $escape($category['slug']) ?>"><?= $escape(mb_strtoupper($category['category_name'])) ?> <span>→</span></a><?php endforeach; ?><a href="#sale">ƯU ĐÃI ĐẶC BIỆT <span>→</span></a></section>
    <section class="catalog-section" id="new-arrivals"><div class="section-heading"><div><span class="section-kicker">VỪA CÓ MẶT TẠI HHDM</span><h2>SẢN PHẨM MỚI</h2><p>Những thiết kế mới nhất, sẵn sàng đồng hành cùng bạn.</p></div><a class="all-link" href="#<?= $escape($categories[0]['slug'] ?? 'new-arrivals') ?>">XEM TẤT CẢ <span>→</span></a></div><div class="product-grid" id="new-products"><?php foreach ($newProducts as $product): ?><?php include __DIR__ . '/partials/product-card.php'; ?><?php endforeach; ?></div></section>
    <?php foreach ($categories as $category): ?><section class="catalog-section collection-section" id="<?= $escape($category['slug']) ?>"><div class="section-heading"><div><span class="section-kicker">HHDM · GỢI Ý DÀNH CHO BẠN</span><h2><?= $escape(mb_strtoupper($category['category_name'])) ?></h2><p><?= $escape($category['description'] ?: 'Thiết kế được chọn lọc dành riêng cho phong cách của bạn.') ?></p></div><a class="all-link" href="#collection">XEM TẤT CẢ <span>→</span></a></div><div class="product-grid"><?php foreach ($category['products'] as $product): ?><?php include __DIR__ . '/partials/product-card.php'; ?><?php endforeach; ?></div></section><?php endforeach; ?>
    <section class="sale-banner" id="sale"><img loading="lazy" src="https://images.unsplash.com/photo-1483985988355-763728e1935b?auto=format&fit=crop&w=1800&q=85" alt="Ưu đãi thời trang HHDM cho nữ và nam"><div class="sale-overlay"></div><div class="sale-copy"><span>HHDM MEMBER DAYS</span><h2>THÊM YÊU THÍCH.<br>BỚT MỘT CHÚT GIÁ.</h2><p>Ưu đãi theo mùa cho những thiết kế được yêu thích.</p><a class="light-button" href="#new-arrivals">XEM ƯU ĐÃI <span>→</span></a></div><div class="sale-stamp"><span>UP TO</span><strong>50%</strong><span>OFF</span></div></section>
    <section class="featured-collections" id="collection"><div class="section-heading"><div><span class="section-kicker">CHỌN CẢM HỨNG CỦA BẠN</span><h2>BỘ SƯU TẬP NỔI BẬT</h2></div><a class="all-link" href="#new-arrivals">KHÁM PHÁ THÊM <span>→</span></a></div><div class="feature-grid"><a class="feature-card feature-large" href="#everyday"><img loading="lazy" src="https://images.unsplash.com/photo-1529139574466-a303027c1d8b?auto=format&fit=crop&w=1100&q=85" alt="Trang phục HHDM cho ngày thường"><span class="feature-number">01 / EVERYDAY</span><strong>CHUYỂN ĐỘNG CÙNG BẠN</strong><span class="feature-description">Những lựa chọn dễ mặc cho ngày thường.</span><i>KHÁM PHÁ →</i></a><a class="feature-card" href="#menswear"><img loading="lazy" src="https://images.unsplash.com/photo-1617137968427-85924c800a22?auto=format&fit=crop&w=900&q=85" alt="Bộ sưu tập thời trang nam HHDM"><span class="feature-number">02 / MENSWEAR</span><strong>PHONG ĐỘ MỖI NGÀY</strong><span class="feature-description">Thiết kế nam hiện đại, thoải mái và linh hoạt.</span><i>KHÁM PHÁ →</i></a><a class="feature-card" href="#accessories"><img loading="lazy" src="https://images.unsplash.com/photo-1548036328-c9fa89d128fa?auto=format&fit=crop&w=900&q=85" alt="Bộ sưu tập phụ kiện HHDM"><span class="feature-number">03 / ACCESSORIES</span><strong>ĐIỂM NHẤN CỦA BẠN</strong><span class="feature-description">Phụ kiện hoàn thiện phong cách theo cách riêng.</span><i>KHÁM PHÁ →</i></a></div></section>
    <section class="story-section" id="stories"><div class="story-image"><img loading="lazy" src="https://images.unsplash.com/photo-1485230895905-ec40ba36b9bc?auto=format&fit=crop&w=1100&q=85" alt="Câu chuyện phong cách và chất liệu HHDM"></div><div class="story-copy"><span class="section-kicker">MẶC ĐẸP, SỐNG THẬT</span><h2>Phong cách của bạn<br>không cần theo khuôn mẫu.</h2><p>HHDM tạo nên những thiết kế dễ ứng dụng, chú trọng phom dáng và cảm giác khi mặc. Để mỗi ngày, bạn có thể chọn điều phù hợp với mình — không cần phải chọn giữa thoải mái và chỉn chu.</p><a class="all-link" href="#footer">CÂU CHUYỆN HHDM <span>→</span></a></div></section>
    <section class="reviews"><div class="section-heading"><div><span class="section-kicker">HHDM IN REAL LIFE</span><h2>KHÁCH HÀNG NÓI GÌ?</h2><p>Cảm ơn bạn đã để HHDM có mặt trong những ngày thường thật đẹp.</p></div><span class="review-count">03 CẢM NHẬN</span></div><div class="review-grid"><article class="review-card"><div class="stars">★★★★★</div><blockquote>“Form quần rất đẹp, mặc cả ngày vẫn thoải mái. Mình phối đi làm hay đi chơi đều hợp.”</blockquote><div class="review-person"><span class="avatar">ML</span><div><strong>Minh Anh L.</strong><small>Đã mua Quần ống rộng Miro</small></div><span class="verified">✓ Đã mua hàng</span></div></article><article class="review-card"><div class="stars">★★★★★</div><blockquote>“Vải mềm, đường may chỉn chu. Đóng gói xinh và giao hàng nhanh hơn mình nghĩ.”</blockquote><div class="review-person"><span class="avatar">TH</span><div><strong>Thu Hà N.</strong><small>Đã mua Đầm midi cổ vuông Hana</small></div><span class="verified">✓ Đã mua hàng</span></div></article><article class="review-card"><div class="stars">★★★★★</div><blockquote>“Màu ngoài đời rất dễ mặc. Bạn tư vấn size nhiệt tình, mình chọn được size vừa ngay lần đầu.”</blockquote><div class="review-person"><span class="avatar">PQ</span><div><strong>Phương Q.</strong><small>Đã mua Áo linen cổ V Luma</small></div><span class="verified">✓ Đã mua hàng</span></div></article></div></section>
    <section class="benefit-bar" aria-label="Chính sách mua hàng"><article><span class="benefit-icon">↺</span><div><strong>ĐỔI SIZE DỄ DÀNG</strong><small>Hỗ trợ đổi trong 7 ngày</small></div></article><article><span class="benefit-icon">▱</span><div><strong>GIAO HÀNG TOÀN QUỐC</strong><small>Miễn phí từ 799.000₫</small></div></article><article><span class="benefit-icon">♡</span><div><strong>ƯU ĐÃI THÀNH VIÊN</strong><small>Quyền lợi riêng cho bạn</small></div></article><article><span class="benefit-icon">☏</span><div><strong>TƯ VẤN TẬN TÂM</strong><small>Hỗ trợ mỗi ngày 9:00–21:00</small></div></article></section>
    <section class="newsletter"><div><span class="section-kicker">THE HHDM NOTE</span><h2>Nhận cảm hứng mới trong hộp thư.</h2><p>Đăng ký để cập nhật bộ sưu tập, gợi ý phối đồ và ưu đãi dành riêng cho bạn.</p></div><form id="signup-form"><label class="sr-only" for="email">Email của bạn</label><input id="email" type="email" placeholder="Nhập địa chỉ email" required><button type="submit">ĐĂNG KÝ <span>→</span></button><small id="signup-status" aria-live="polite"></small></form></section>
  </main>
  <footer class="site-footer" id="footer"><div class="footer-main"><div class="footer-about"><a class="brand footer-brand" href="#home">HHDM<span>FASHION</span></a><p>Thời trang nữ, nam và phụ kiện cho nhịp sống của bạn. Dễ mặc, dễ phối, tự tin theo cách riêng.</p><strong>HỖ TRỢ KHÁCH HÀNG</strong><a href="tel:1900636828">1900 636 828</a><a href="mailto:hello@hhdmfashion.vn">hello@hhdmfashion.vn</a><small>Thứ Hai – Chủ Nhật · 09:00–21:00</small></div><div><h3>VỀ HHDM</h3><a href="#stories">Câu chuyện thương hiệu</a><a href="#collection">Bộ sưu tập</a><a href="#stories">Góc phối đồ</a><a href="#footer">Hệ thống cửa hàng</a><a href="#footer">Cơ hội nghề nghiệp</a></div><div><h3>HỖ TRỢ</h3><a href="#footer">Hướng dẫn chọn size</a><a href="#footer">Hướng dẫn mua hàng</a><a href="#footer">Chính sách vận chuyển</a><a href="#footer">Chính sách đổi trả</a><a href="#footer">Câu hỏi thường gặp</a></div><div><h3>KẾT NỐI VỚI HHDM</h3><p>Theo dõi HHDM để cập nhật sản phẩm và ưu đãi mới.</p><div class="social-links"><a href="#footer" aria-label="Instagram">IG</a><a href="#footer" aria-label="Facebook">FB</a><a href="#footer" aria-label="TikTok">TT</a></div><h3 class="payment-title">THANH TOÁN</h3><div class="payment-methods"><span>COD</span><span>VISA</span><span>ATM</span></div></div></div><div class="company-info"><strong>HHDM FASHION</strong><span>Thời trang nữ, nam và phụ kiện · © 2026 HHDM Fashion</span><span>Thông tin & chính sách bảo mật</span></div></footer>
  <?php include __DIR__ . '/partials/payment-modal.php'; ?>
  <aside class="cart-drawer" id="cart-drawer" aria-hidden="true">
    <div class="cart-head">
      <div>
        <span class="section-kicker">HHDM SHOPPING BAG</span>
        <h2>Giỏ hàng <span id="drawer-count">(0)</span></h2>
      </div>
      <button id="cart-close" type="button" aria-label="Đóng giỏ hàng">×</button>
    </div>
    <div class="cart-items" id="cart-items">
      <p class="empty-cart">Giỏ hàng của bạn đang trống.<br>Thêm món đồ bạn yêu thích nhé!</p>
    </div>
    <div class="cart-summary">
      <div class="cart-selection">
        <label for="cart-select-all"><input id="cart-select-all" type="checkbox" checked> Chọn tất cả</label>
        <small id="cart-selected-count">0 sản phẩm đã chọn</small>
      </div>
      <div><span>Tạm tính món đã chọn</span><strong id="cart-subtotal">0₫</strong></div>
      <small>Phí vận chuyển sẽ được tính ở bước tiếp theo.</small>
      <button class="checkout-button" type="button" disabled>THANH TOÁN MÓN ĐÃ CHỌN</button>
    </div>
  </aside>
  <?php if ($user && ($user['role'] ?? '') !== 'admin'): ?>
  <aside class="account-drawer" id="account-drawer" aria-hidden="true">
    <div class="cart-head account-drawer-head">
      <div>
        <span class="section-kicker">HHDM ACCOUNT</span><h2>Tài khoản của tôi</h2><p class="account-drawer-greeting">Xin chào, <?= $escape($user['full_name'] ?? 'bạn') ?></p>
      </div>
      <button id="account-close" type="button" aria-label="Đóng menu tài khoản">×</button>
    </div>
    <nav class="account-drawer-nav" aria-label="Menu tài khoản">
      <a class="account-drawer-link" href="index.php?page=profile"><span>Tài khoản của tôi</span><small>Thông tin cá nhân</small></a>
      <form method="post" action="index.php?page=logout" class="account-drawer-logout">
        <input type="hidden" name="csrf_token" value="<?= $escape($_SESSION['csrf_token'] ?? '') ?>">
        <button type="submit" class="account-drawer-link account-drawer-button">
          <span>Đăng xuất</span>
          <small>Đăng xuất tài khoản</small>
        </button>
      </form>
    </nav>
  </aside>
  <?php endif; ?>
  <div class="drawer-backdrop" id="drawer-backdrop"></div>
  <div class="toast" id="toast" role="status" aria-live="polite"></div>
  <script src="assets/js/home.js?v=hero-speed-3500" defer></script>
</body>
</html>
