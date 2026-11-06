<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#f8f6f2">
    <meta name="description" content="Khám phá những thiết kế thời trang hiện đại, tinh tế và dễ ứng dụng mỗi ngày tại HHDM Studio.">
    <title><?= htmlspecialchars($title ?? 'HHDM Studio | Mặc điều bạn yêu') ?></title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/main.css">
    <script src="assets/js/main.js" defer></script>
</head>
<body>
    <div class="announcement">
        MIỄN PHÍ VẬN CHUYỂN TỪ 899K · ĐỔI SIZE DỄ DÀNG TRONG 7 NGÀY
    </div>

    <header class="header">
        <div class="header-main">
            <nav class="nav-links" aria-label="Điều hướng chính">
                <a href="#hang-moi">Hàng mới</a>
                <a href="#cong-so">Đồ công sở</a>
                <a href="#streetwear">Streetwear</a>
                <a href="#danh-muc">Danh mục</a>
            </nav>

            <a class="brand" href="<?= htmlspecialchars(BASE_URL) ?>" aria-label="HHDM Studio trang chủ">
                HHDM
            </a>

            <div class="header-actions">
                <a class="icon-link" href="#san-pham" aria-label="Tìm kiếm">
                    <svg class="icon" viewBox="0 0 24 24" aria-hidden="true">
                        <circle cx="10.8" cy="10.8" r="6.8" />
                        <path d="m16 16 4.5 4.5" />
                    </svg>
                    <span>Tìm kiếm</span>
                </a>
                <a class="icon-link" href="#dang-nhap" aria-label="Tài khoản">
                    <svg class="icon" viewBox="0 0 24 24" aria-hidden="true">
                        <circle cx="12" cy="8" r="3.5" />
                        <path d="M4.5 20c.8-4 3.2-6 7.5-6s6.7 2 7.5 6" />
                    </svg>
                    <span>Tài khoản</span>
                </a>
                <a class="icon-link" href="#san-pham" aria-label="Giỏ hàng">
                    <svg class="icon" viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M4 8h16l-1.3 12H5.3L4 8Z" />
                        <path d="M9 9V6a3 3 0 0 1 6 0v3" />
                    </svg>
                    <span>Giỏ hàng (<b id="cart-count">0</b>)</span>
                </a>
            </div>
        </div>
    </header>

    <main>
        <section class="hero" aria-label="Bộ sưu tập thời trang nam mới">
            <div class="hero-slider" id="hero-slider">
                <article class="hero-slide hero-slide--daily is-active">
                    <div class="hero-copy">
                        <span class="eyebrow">HHDM STUDIO · NEW COLLECTION</span>
                        <h1>Phong cách của bạn. Quy tắc của bạn.</h1>
                        <p>Đồ nam hiện đại, dễ phối và đủ thoải mái để đồng hành cùng bạn mỗi ngày.</p>
                        <a class="button" href="#hang-moi">
                            Khám phá hàng mới <span aria-hidden="true">→</span>
                        </a>
                    </div>
                    <span class="slide-count">01 <i>/</i> 03</span>
                </article>

                <article class="hero-slide hero-slide--casual">
                    <div class="hero-copy">
                        <span class="eyebrow">SMART CASUAL · EVERYDAY STYLE</span>
                        <h1>Chỉn chu trong mọi khoảnh khắc.</h1>
                        <p>Từ ngày làm việc đến cuộc hẹn cuối tuần, chọn những món đồ hợp với nhịp sống của bạn.</p>
                        <a class="button" href="#cong-so">
                            Khám phá đồ công sở <span aria-hidden="true">→</span>
                        </a>
                    </div>
                    <span class="slide-count">02 <i>/</i> 03</span>
                </article>

                <article class="hero-slide hero-slide--streetwear">
                    <div class="hero-copy">
                        <span class="eyebrow">STREETWEAR · MAKE IT YOURS</span>
                        <h1>Mặc chất riêng, sống chất mình.</h1>
                        <p>Phom dáng thoải mái, màu sắc dễ mặc và điểm nhấn vừa đủ để bạn nổi bật.</p>
                        <a class="button" href="#streetwear">
                            Khám phá streetwear <span aria-hidden="true">→</span>
                        </a>
                    </div>
                    <span class="slide-count">03 <i>/</i> 03</span>
                </article>

                <div class="hero-controls">
                    <button class="hero-arrow hero-prev" type="button" aria-label="Poster trước">←</button>
                    <div class="hero-dots" aria-label="Chọn poster">
                        <button class="is-active" type="button" aria-label="Poster 1"></button>
                        <button type="button" aria-label="Poster 2"></button>
                        <button type="button" aria-label="Poster 3"></button>
                    </div>
                    <button class="hero-arrow hero-next" type="button" aria-label="Poster tiếp theo">→</button>
                </div>
            </div>
        </section>

        <div class="benefits">
            <div>
                <b>01</b> CHẤT LIỆU CHỌN LỌC
                <span>Thoải mái trong từng chuyển động</span>
            </div>
            <div>
                <b>02</b> GIAO HÀNG TOÀN QUỐC
                <span>Miễn phí từ 899.000₫</span>
            </div>
            <div>
                <b>03</b> ĐỔI SIZE LINH HOẠT
                <span>Hỗ trợ trong vòng 7 ngày</span>
            </div>
        </div>

        <section class="section" id="danh-muc">
            <div class="section-heading">
                <span class="eyebrow">Tìm phong cách của bạn</span>
                <h2>Mua sắm theo danh mục</h2>
                <p>Những món đồ làm nên tủ quần áo bạn yêu thích.</p>
            </div>

            <div class="category-grid">
                <a class="category-card" href="#san-pham">
                    <img src="https://images.unsplash.com/photo-1617137968427-85924c800a22?auto=format&fit=crop&w=900&q=85" alt="Thời trang nam hiện đại" loading="lazy">
                    <span class="category-label">
                        <b>Thời trang nam</b>
                        <span>Khám phá ngay →</span>
                    </span>
                </a>
                <a class="category-card" href="#san-pham">
                    <img src="https://images.unsplash.com/photo-1516826957135-700dedea698c?auto=format&fit=crop&w=900&q=85" alt="Phong cách streetwear nam" loading="lazy">
                    <span class="category-label">
                        <b>Streetwear nam</b>
                        <span>Khám phá ngay →</span>
                    </span>
                </a>
                <a class="category-card" href="#san-pham">
                    <img src="https://images.unsplash.com/photo-1490481651871-ab68de25d43d?auto=format&fit=crop&w=900&q=80" alt="Phụ kiện thời trang" loading="lazy">
                    <span class="category-label">
                        <b>Phụ kiện</b>
                        <span>Khám phá ngay →</span>
                    </span>
                </a>
            </div>
        </section>

        <section class="section section--products" id="hang-moi">
            <div class="section-heading">
                <span class="eyebrow">HHDM DAILY EDIT</span>
                <h2 id="san-pham">Sản phẩm thời trang hàng ngày</h2>
                <p>Những thiết kế dễ mặc, dễ phối cho tủ đồ nam hiện đại.</p>
            </div>

            <div class="products">
                <article class="product-card">
                    <div class="product-image">
                        <img src="https://images.unsplash.com/photo-1603252110481-7ba873bf42ab?auto=format&fit=crop&w=750&q=85" alt="Áo sơ mi nam màu trung tính" loading="lazy">
                        <span class="badge">Bán chạy</span>
                        <button class="quick-add" type="button">Thêm vào giỏ</button>
                    </div>
                    <div class="product-info">
                        <h3>Áo sơ mi nam linen An Nhiên</h3>
                        <div class="price">590.000₫</div>
                        <div class="swatches">
                            <i class="swatch swatch--1"></i>
                            <i class="swatch swatch--2"></i>
                            <i class="swatch swatch--3"></i>
                        </div>
                    </div>
                </article>

                <article class="product-card">
                    <div class="product-image">
                        <img src="https://images.unsplash.com/photo-1551028719-00167b16eac5?auto=format&fit=crop&w=750&q=85" alt="Áo khoác nam phong cách tối giản" loading="lazy">
                        <span class="badge">Hàng mới</span>
                        <button class="quick-add" type="button">Thêm vào giỏ</button>
                    </div>
                    <div class="product-info">
                        <h3>Áo khoác nam Mộc</h3>
                        <div class="price">1.290.000₫</div>
                        <div class="swatches">
                            <i class="swatch swatch--4"></i>
                            <i class="swatch swatch--5"></i>
                        </div>
                    </div>
                </article>

                <article class="product-card">
                    <div class="product-image">
                        <img src="https://images.unsplash.com/photo-1551163943-3f6a855d1153?auto=format&fit=crop&w=750&q=80" alt="Túi đeo chéo nam phong cách tối giản" loading="lazy">
                        <span class="badge">Bán chạy</span>
                        <button class="quick-add" type="button">Thêm vào giỏ</button>
                    </div>
                    <div class="product-info">
                        <h3>Túi đeo chéo nam Gạo</h3>
                        <div class="price">790.000₫</div>
                        <div class="swatches">
                            <i class="swatch swatch--6"></i>
                            <i class="swatch swatch--7"></i>
                            <i class="swatch swatch--8"></i>
                        </div>
                    </div>
                </article>

                <article class="product-card">
                    <div class="product-image">
                        <img src="https://images.unsplash.com/photo-1523398002811-999ca8dec234?auto=format&fit=crop&w=750&q=85" alt="Trang phục nam phong cách hiện đại" loading="lazy">
                        <span class="badge">Hàng mới</span>
                        <button class="quick-add" type="button">Thêm vào giỏ</button>
                    </div>
                    <div class="product-info">
                        <h3>Áo polo nam Hoa Nắng</h3>
                        <div class="price">890.000₫ <span class="old-price">1.090.000₫</span></div>
                        <div class="swatches">
                            <i class="swatch swatch--9"></i>
                            <i class="swatch swatch--10"></i>
                        </div>
                    </div>
                </article>
            </div>
        </section>

        <section class="section collection-section" aria-labelledby="collection-title">
            <div class="section-heading collection-heading">
                <span class="eyebrow">CHỌN GU CỦA BẠN</span>
                <h2 id="collection-title">Mặc đẹp theo cách riêng</h2>
                <p>Hai sắc thái, cùng một tinh thần tự tin.</p>
            </div>

            <div class="collection-grid">
                <a class="collection-card collection-card--office" id="cong-so" href="#hang-moi">
                    <img src="https://images.unsplash.com/photo-1617137968427-85924c800a22?auto=format&fit=crop&w=1100&q=85" alt="Trang phục nam smart casual" loading="lazy">
                    <span class="collection-copy">
                        <small>SMART CASUAL</small>
                        <b>Chỉn chu mỗi ngày</b>
                        <i>Khám phá đồ công sở →</i>
                    </span>
                </a>
                <a class="collection-card collection-card--street" id="streetwear" href="#hang-moi">
                    <img src="https://images.unsplash.com/photo-1523398002811-999ca8dec234?auto=format&fit=crop&w=1100&q=85" alt="Phong cách streetwear nam" loading="lazy">
                    <span class="collection-copy">
                        <small>STREETWEAR</small>
                        <b>Tự do thể hiện mình</b>
                        <i>Khám phá bộ sưu tập →</i>
                    </span>
                </a>
            </div>
        </section>

        <section class="story" id="cau-chuyen">
            <div class="story-inner">
                <div class="story-image" role="img" aria-label="Bạn bè tận hưởng khoảnh khắc cùng nhau"></div>
                <div class="story-copy">
                    <span class="eyebrow">Chọn ít hơn, mặc lâu hơn</span>
                    <h2>Phong cách bắt đầu từ sự thoải mái.</h2>
                    <p>HHDM tạo nên những thiết kế gần gũi với cuộc sống thật — dễ mặc, dễ phối và đủ tinh tế để bạn luôn cảm thấy là chính mình.</p>
                    <a class="text-link" href="#danh-muc">Tìm hiểu câu chuyện HHDM →</a>
                </div>
            </div>
        </section>

        <section class="reviews-section" aria-labelledby="reviews-title">
            <div class="reviews-heading">
                <span class="eyebrow">LỜI KHÁCH HÀNG</span>
                <h2 id="reviews-title">Được chọn và mặc mỗi ngày</h2>
            </div>
            <div class="review-grid">
                <blockquote>
                    <div class="review-stars" aria-label="5 trên 5 sao">★★★★★</div>
                    <p>“Form áo đẹp, chất vải thoáng. Mặc đi làm hay đi chơi đều ổn.”</p>
                    <footer>Minh T. <span>· Khách hàng HHDM</span></footer>
                </blockquote>
                <blockquote>
                    <div class="review-stars" aria-label="5 trên 5 sao">★★★★★</div>
                    <p>“Mình thích cách phối màu đơn giản, nhận hàng đúng mô tả và đóng gói chỉn chu.”</p>
                    <footer>Quang H. <span>· Khách hàng HHDM</span></footer>
                </blockquote>
                <blockquote>
                    <div class="review-stars" aria-label="5 trên 5 sao">★★★★★</div>
                    <p>“Được tư vấn size rất kỹ. Chiếc sơ mi mặc lên vừa vặn, chắc chắn sẽ quay lại.”</p>
                    <footer>Tuấn A. <span>· Khách hàng HHDM</span></footer>
                </blockquote>
            </div>
        </section>

        <section class="newsletter">
            <h2>Thư từ HHDM</h2>
            <p>Đăng ký nhận tin để cập nhật bộ sưu tập mới và ưu đãi dành riêng cho bạn.</p>
            <form class="newsletter-form" id="newsletter-form">
                <input type="email" placeholder="Địa chỉ email của bạn" aria-label="Địa chỉ email" required>
                <button type="submit">Đăng ký →</button>
            </form>
        </section>
    </main>

    <footer class="footer">
        <div class="footer-inner">
            <div class="footer-about">
                <div class="footer-brand">HHDM</div>
                <p>Mặc điều bạn yêu. Thiết kế dành cho những ngày bình thường trở nên thật đặc biệt.</p>
            </div>
            <div>
                <h3>Khám phá</h3>
                <a href="#hang-moi">Hàng mới</a>
                <a href="#danh-muc">Thời trang nam</a>
                <a href="#streetwear">Streetwear nam</a>
                <a href="#danh-muc">Phụ kiện</a>
            </div>
            <div>
                <h3>Hỗ trợ</h3>
                <a href="#ho-tro">Hướng dẫn mua hàng</a>
                <a href="#ho-tro">Chính sách đổi trả</a>
                <a href="#ho-tro">Vận chuyển & giao hàng</a>
            </div>
            <div>
                <h3>Kết nối</h3>
                <a href="#facebook">Facebook</a>
                <a href="#instagram">Instagram</a>
                <span>hello@hhdm.vn</span>
            </div>
        </div>
        <div class="copyright">
            <span>© 2025 HHDM Studio. All rights reserved.</span>
            <span>Được làm với sự chăm chút tại Việt Nam.</span>
        </div>
    </footer>
</body>
</html>