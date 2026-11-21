(function () {
    const CART_STORAGE_KEY = "hhdm_cart";
    const cart = (() => {
        try {
            const saved = JSON.parse(localStorage.getItem(CART_STORAGE_KEY) || "[]");
            return Array.isArray(saved) ? saved : [];
        } catch {
            return [];
        }
    })();
    const isAuthenticated = document.body.dataset.authenticated === "true";
    const drawer = document.getElementById("cart-drawer");
    const backdrop = document.getElementById("drawer-backdrop");
    const money = (n) => new Intl.NumberFormat("vi-VN").format(n) + "₫";
    function saveCart() {
        localStorage.setItem(CART_STORAGE_KEY, JSON.stringify(cart));
    }
    function syncCart() {
        try {
            const saved = JSON.parse(localStorage.getItem(CART_STORAGE_KEY) || "[]");
            cart.splice(0, cart.length, ...(Array.isArray(saved) ? saved : []));
        } catch {}
        renderCart();
    }
    function showCart() {
        drawer.classList.add("is-open");
        backdrop.classList.add("is-open");
        drawer.setAttribute("aria-hidden", "false");
        document.body.classList.add("drawer-open");
    }
    function hideCart() {
        drawer.classList.remove("is-open");
        backdrop.classList.remove("is-open");
        drawer.setAttribute("aria-hidden", "true");
        document.body.classList.remove("drawer-open");
    }
    function renderCart() {
        const count = cart.reduce((sum, item) => sum + item.quantity, 0);
        document.getElementById("cart-count").textContent = count;
        document.getElementById("drawer-count").textContent = "(" + count + ")";
        document.getElementById("cart-subtotal").textContent = money(
            cart.reduce((sum, item) => sum + item.price * item.quantity, 0),
        );
        const list = document.getElementById("cart-items");
        if (!cart.length) {
            list.innerHTML = '<p class="empty-cart">Giỏ hàng của bạn đang trống.<br>Thêm món đồ bạn yêu thích nhé!</p>';
            document.querySelector(".checkout-button").disabled = true;
            return;
        }
        document.querySelector(".checkout-button").disabled = false;
        list.innerHTML = cart
            .map(
                (item) =>
                    '<article class="cart-item"><img src="' +
                    item.image +
                    '" alt=""><div><strong>' +
                    item.name +
                    "</strong><small>Size: " +
                    item.size +
                    "</small><small>" +
                    money(item.price) +
                    " × " +
                    item.quantity +
                    '</small></div><button type="button" data-remove="' +
                    item.key +
                    '" aria-label="Xóa ' +
                    item.name +
                    '">×</button></article>',
            )
            .join("");
    }
    document.getElementById("cart-trigger").addEventListener("click", showCart);
    document.getElementById("cart-close").addEventListener("click", hideCart);
    backdrop.addEventListener("click", hideCart);
    document.addEventListener("keydown", (e) => {
        if (e.key === "Escape") hideCart();
    });
    let toastTimer;
    function toast(message) {
        const el = document.getElementById("toast");
        el.textContent = message;
        el.classList.add("is-visible");
        clearTimeout(toastTimer);
        toastTimer = setTimeout(() => el.classList.remove("is-visible"), 2400);
    }
    document.addEventListener("click", (event) => {
        const favorite = event.target.closest(".favorite-button");
        if (favorite) {
            const active = favorite.classList.toggle("is-favorite");
            favorite.textContent = active ? "♥" : "♡";
            favorite.setAttribute("aria-pressed", String(active));
        }
        const button = event.target.closest("[data-add]");
        if (button) {
            if (!isAuthenticated) {
                window.location.href = "index.php?page=login";
                return;
            }
            const card = button.closest(".product-card");
            const selected = card.querySelector(".size-option.is-selected");
            if (!selected) {
                toast("Vui lòng chọn size trước khi thêm vào giỏ.");
                return;
            }
            const key = button.dataset.code + "-" + selected.dataset.size;
            const existing = cart.find((item) => item.key === key);
            if (existing) {
                existing.quantity++;
            } else {
                cart.push({
                    key,
                    name: button.dataset.name,
                    code: button.dataset.code,
                    price: Number(button.dataset.price),
                    size: selected.dataset.size,
                    image: button.dataset.image,
                    quantity: 1,
                });
            }
            saveCart();
            renderCart();
            toast("Đã thêm " + button.dataset.name + " vào giỏ hàng.");
        }
        const remove = event.target.closest("[data-remove]");
        if (remove) {
            const index = cart.findIndex((item) => item.key === remove.dataset.remove);
            if (index > -1) cart.splice(index, 1);
            saveCart();
            renderCart();
        }
    });
    document.addEventListener("click", (event) => {
        const option = event.target.closest(".size-option");
        if (!option) return;
        option
            .closest(".size-options")
            .querySelectorAll(".size-option")
            .forEach((item) => item.classList.remove("is-selected"));
        option.classList.add("is-selected");
    });
    const searchInput = document.getElementById("search-input");
    const searchResults = document.getElementById("search-results");
    const searchMatches = document.getElementById("search-matches");
    const searchEmpty = document.getElementById("search-empty");
    const searchSuggestionsWrap = document.getElementById("search-suggestions-wrap");
    const searchSuggestions = document.getElementById("search-suggestions");
    const allSearchProducts = [];
    const productIds = new Set();
    document.querySelectorAll(".collection-section .product-card").forEach((card) => {
        const href = card.querySelector(".detail-button")?.getAttribute("href") || card.dataset.search;
        if (!productIds.has(href)) {
            productIds.add(href);
            allSearchProducts.push(card);
        }
    });
    const normalizeSearch = (value) =>
        value
            .toLocaleLowerCase("vi")
            .normalize("NFD")
            .replace(/[\u0300-\u036f]/g, "")
            .replace(/đ/g, "d")
            .trim();
    function fillSearchGrid(grid, products) {
        grid.replaceChildren(...products.map((card) => card.cloneNode(true)));
    }
    const complementaryCategories = {
        menswear: ["accessories"],
        accessories: ["menswear"],
        everyday: ["weekend", "office"],
        office: ["everyday", "weekend"],
        weekend: ["everyday", "accessories"],
    };
    function runProductSearch() {
        const rawQuery = searchInput.value.trim();
        const query = normalizeSearch(rawQuery);
        if (!query) {
            document.body.classList.remove("search-mode");
            searchResults.hidden = true;
            searchMatches.replaceChildren();
            searchSuggestions.replaceChildren();
            return;
        }
        document.body.classList.add("search-mode");
        searchResults.hidden = false;
        const terms = query.split(/\s+/).filter(Boolean);
        const ranked = allSearchProducts.map((card) => {
            const text = normalizeSearch(card.dataset.search || "");
            const category = card.dataset.category || "";
            return {
                card,
                text,
                category,
                score: terms.reduce((total, term) => total + (text.includes(term) ? 1 : 0), 0),
            };
        });
        const matches = ranked.filter((item) => item.score === terms.length);
        const matchCategories = new Set(matches.map((item) => item.category).filter(Boolean));
        const relatedCategories = new Set([
            ...matchCategories,
            ...[...matchCategories].flatMap((category) => complementaryCategories[category] || []),
        ]);
        const related = ranked
            .filter((item) => !matches.includes(item))
            .map((item) => ({
                ...item,
                relatedScore:
                    item.score +
                    (matchCategories.has(item.category) ? 2 : relatedCategories.has(item.category) ? 1 : 0),
            }))
            .filter((item) => item.relatedScore > 0)
            .sort((a, b) => b.relatedScore - a.relatedScore)
            .slice(0, 4);
        fillSearchGrid(
            searchMatches,
            matches.map((item) => item.card),
        );
        fillSearchGrid(
            searchSuggestions,
            related.map((item) => item.card),
        );
        searchEmpty.hidden = matches.length > 0;
        searchMatches.hidden = matches.length === 0;
        searchSuggestionsWrap.hidden = related.length === 0;
        document.getElementById("search-results-title").textContent = matches.length
            ? "KẾT QUẢ TÌM KIẾM"
            : "CHƯA TÌM THẤY SẢN PHẨM";
        document.getElementById("search-results-summary").textContent = matches.length
            ? `${matches.length} sản phẩm phù hợp với “${rawQuery}”.`
            : `Không có kết quả cho “${rawQuery}”.`;
    }
    document.getElementById("search-form").addEventListener("submit", (event) => {
        event.preventDefault();
        runProductSearch();
    });
    searchInput.addEventListener("input", runProductSearch);
    document.querySelector(".checkout-button").addEventListener("click", () => {
        if (!isAuthenticated) {
            window.location.href = "index.php?page=login";
            return;
        }
        if (!cart.length) return;
        const amount = cart.reduce((sum, item) => sum + item.price * item.quantity, 0);
        hideCart();
        window.HHDMPayment.open({ items: cart, amount });
    });
    window.addEventListener("pageshow", syncCart);
    renderCart();
    document.getElementById("signup-form").addEventListener("submit", (event) => {
        event.preventDefault();
        document.getElementById("signup-status").textContent = "Cảm ơn bạn đã đăng ký nhận tin từ HHDM.";
        event.currentTarget.reset();
    });
    document.querySelector(".mobile-menu").addEventListener("click", (event) => {
        const nav = document.getElementById("main-nav");
        const open = nav.classList.toggle("is-open");
        event.currentTarget.setAttribute("aria-expanded", String(open));
    });
    const heroSlides = [...document.querySelectorAll(".hero-slide")];
    const heroDots = [...document.querySelectorAll(".hero-dot")];
    const heroSection = document.getElementById("hero-slideshow");
    const HERO_DELAY = 4500;
    let heroIndex = 0;
    let heroTimer = null;
    function showHeroSlide(next) {
        if (!heroSlides.length) return;
        heroIndex = (next + heroSlides.length) % heroSlides.length;
        heroSlides.forEach((slide, i) => {
            const active = i === heroIndex;
            slide.classList.toggle("is-active", active);
            slide.setAttribute("aria-hidden", String(!active));
            const link = slide.querySelector(".primary-button");
            if (link) link.tabIndex = active ? 0 : -1;
        });
        heroDots.forEach((dot, i) => dot.classList.toggle("is-active", i === heroIndex));
    }
    function restartHeroTimer() {
        clearInterval(heroTimer);
        heroTimer = setInterval(() => showHeroSlide(heroIndex + 1), HERO_DELAY);
    }
    heroDots.forEach((dot) =>
        dot.addEventListener("click", () => {
            showHeroSlide(Number(dot.dataset.slide));
            restartHeroTimer();
        }),
    );
    document.getElementById("hero-prev").addEventListener("click", () => {
        showHeroSlide(heroIndex - 1);
        restartHeroTimer();
    });
    document.getElementById("hero-next").addEventListener("click", () => {
        showHeroSlide(heroIndex + 1);
        restartHeroTimer();
    });
    heroSection.addEventListener("mouseenter", () => clearInterval(heroTimer));
    heroSection.addEventListener("mouseleave", restartHeroTimer);
    document.addEventListener("visibilitychange", () => {
        if (document.hidden) clearInterval(heroTimer);
        else restartHeroTimer();
    });
    showHeroSlide(0);
    restartHeroTimer();
    renderCart();
})();
