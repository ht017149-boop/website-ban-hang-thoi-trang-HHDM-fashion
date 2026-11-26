(function () {
    const isLoggedIn = document.body.dataset.loggedIn === "true";
    const CART_STORAGE_KEY = isLoggedIn ? "hhdm_cart_user_" + document.body.dataset.userId : null;
    const cart = (() => {
        if (!CART_STORAGE_KEY) return [];
        try {
            const saved = JSON.parse(localStorage.getItem(CART_STORAGE_KEY) || "[]");
            return Array.isArray(saved) ? saved.map((item) => ({ ...item, productId: Number(item.productId || (item.code || "").match(/HHDM-(\d+)/)?.[1] || 0), selected: item.selected !== false })) : [];
        } catch {
            return [];
        }
    })();
    function saveCart() {
        if (!CART_STORAGE_KEY) return;
        localStorage.setItem(CART_STORAGE_KEY, JSON.stringify(cart));
    }
    function syncCart() {
        if (!CART_STORAGE_KEY) {
            cart.splice(0, cart.length);
            renderCart();
            return;
        }
        try {
            const saved = JSON.parse(localStorage.getItem(CART_STORAGE_KEY) || "[]");
            cart.splice(
                0,
                cart.length,
                ...(Array.isArray(saved) ? saved.map((item) => ({ ...item, productId: Number(item.productId || (item.code || "").match(/HHDM-(\d+)/)?.[1] || 0), selected: item.selected !== false })) : []),
            );
        } catch {}
        renderCart();
    }
    const drawer = document.getElementById("cart-drawer");
    const accountDrawer = document.getElementById("account-drawer");
    const backdrop = document.getElementById("drawer-backdrop");
    const accountTrigger = document.getElementById("account-trigger");
    const money = (n) => new Intl.NumberFormat("vi-VN").format(n) + "₫";
    function syncDrawerState() {
        const cartOpen = drawer.classList.contains("is-open");
        const accountOpen = accountDrawer?.classList.contains("is-open");
        backdrop.classList.toggle("is-open", cartOpen || !!accountOpen);
        document.body.classList.toggle("drawer-open", cartOpen || !!accountOpen);
    }
    function showCart() {
        hideAccount();
        drawer.classList.add("is-open");
        drawer.setAttribute("aria-hidden", "false");
        syncDrawerState();
    }
    function hideCart() {
        drawer.classList.remove("is-open");
        drawer.setAttribute("aria-hidden", "true");
        syncDrawerState();
    }
    function showAccount() {
        if (!accountDrawer) return;
        hideCart();
        accountDrawer.classList.add("is-open");
        accountDrawer.setAttribute("aria-hidden", "false");
        accountTrigger?.setAttribute("aria-expanded", "true");
        syncDrawerState();
    }
    function hideAccount() {
        if (!accountDrawer) return;
        accountDrawer.classList.remove("is-open");
        accountDrawer.setAttribute("aria-hidden", "true");
        accountTrigger?.setAttribute("aria-expanded", "false");
        syncDrawerState();
    }
    function renderCart() {
        const count = isLoggedIn ? cart.reduce((sum, item) => sum + item.quantity, 0) : 0;
        const selectedCart = cart.filter((item) => item.selected !== false);
        const selectedQuantity = selectedCart.reduce((sum, item) => sum + item.quantity, 0);
        document.getElementById("cart-count").textContent = count;
        document.getElementById("drawer-count").textContent = "(" + count + ")";
        document.getElementById("cart-subtotal").textContent = money(
            selectedCart.reduce((sum, item) => sum + item.price * item.quantity, 0),
        );
        document.getElementById("cart-selected-count").textContent = selectedQuantity + " sản phẩm đã chọn";
        const selectAll = document.getElementById("cart-select-all");
        selectAll.checked = cart.length > 0 && selectedCart.length === cart.length;
        selectAll.indeterminate = selectedCart.length > 0 && selectedCart.length < cart.length;
        selectAll.closest(".cart-selection").hidden = !isLoggedIn || cart.length === 0;
        const list = document.getElementById("cart-items");
        if (!isLoggedIn) {
            list.innerHTML =
                '<div class="empty-cart"><p>Vui lòng đăng nhập để sử dụng giỏ hàng.</p><a href="index.php?page=login" style="display:inline-block;margin-top:10px;padding:9px 18px;background:var(--olive);color:#fff;font-size:10px;font-weight:700;letter-spacing:.08em;text-decoration:none;">ĐĂNG NHẬP NGAY</a></div>';
            document.querySelector(".checkout-button").disabled = true;
            return;
        }
        if (!cart.length) {
            list.innerHTML = '<p class="empty-cart">Giỏ hàng của bạn đang trống.<br>Thêm món đồ bạn yêu thích nhé!</p>';
            document.querySelector(".checkout-button").disabled = true;
            return;
        }
        document.querySelector(".checkout-button").disabled = selectedCart.length === 0;
        list.innerHTML = cart
            .map(
                (item) =>
                    '<article class="cart-item"><label class="cart-select"><input type="checkbox" data-select="' +
                    item.key +
                    '"' +
                    (item.selected !== false ? " checked" : "") +
                    '><span class="sr-only">Chọn ' +
                    item.name +
                    '</span></label><img src="' +
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
    accountTrigger?.addEventListener("click", () => {
        if (accountDrawer?.classList.contains("is-open")) hideAccount();
        else showAccount();
    });
    document.getElementById("account-close")?.addEventListener("click", hideAccount);
    backdrop.addEventListener("click", () => {
        hideCart();
        hideAccount();
    });
    document.addEventListener("keydown", (e) => {
        if (e.key === "Escape") {
            hideCart();
            hideAccount();
        }
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
            if (!isLoggedIn) {
                toast("Vui lòng đăng nhập để thêm sản phẩm vào giỏ hàng.");
                setTimeout(() => {
                    window.location.href =
                        "index.php?page=login&message=" +
                        encodeURIComponent("Vui lòng đăng nhập để thêm sản phẩm vào giỏ hàng.");
                }, 1000);
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
            if (Number(selected.dataset.stock) < 1 || (existing && existing.quantity >= Number(selected.dataset.stock))) {
                toast("Size " + selected.dataset.size + " chỉ còn " + selected.dataset.stock + " sản phẩm.");
                return;
            }
            if (existing) {
                existing.quantity++;
            } else {
                cart.push({
                    key,
                    productId: Number(button.dataset.productId),
                    name: button.dataset.name,
                    code: button.dataset.code,
                    price: Number(button.dataset.price),
                    size: selected.dataset.size,
                    image: button.dataset.image,
                    quantity: 1,
                    selected: true,
                });
            }
            if (existing) existing.selected = true;
            saveCart();
            renderCart();
            toast("Đã thêm " + button.dataset.name + " size " + selected.dataset.size + " vào giỏ hàng.");
        }
        const remove = event.target.closest("[data-remove]");
        if (remove) {
            const index = cart.findIndex((item) => item.key === remove.dataset.remove);
            if (index > -1) cart.splice(index, 1);
            saveCart();
            renderCart();
        }
    });
    document.getElementById("cart-items").addEventListener("change", (event) => {
        const checkbox = event.target.closest("[data-select]");
        if (!checkbox) return;
        const item = cart.find((entry) => entry.key === checkbox.dataset.select);
        if (item) item.selected = checkbox.checked;
        saveCart();
        renderCart();
    });
    document.getElementById("cart-select-all").addEventListener("change", (event) => {
        cart.forEach((item) => (item.selected = event.target.checked));
        saveCart();
        renderCart();
    });
    document.querySelectorAll(".size-option").forEach((option) =>
        option.addEventListener("click", () => {
            const card = option.closest(".product-card");
            const button = card.querySelector("[data-add]");
            option
                .closest(".size-options")
                .querySelectorAll(".size-option")
                .forEach((item) => item.classList.remove("is-selected"));
            option.classList.add("is-selected");
            const extra = Number(option.dataset.additionalPrice);
            const price = Number(button.dataset.basePrice) + extra;
            const old = button.dataset.baseOld === "" ? null : Number(button.dataset.baseOld);
            const displayedOld = old !== null && price < old ? old : null;
            button.dataset.price = String(price);
            const priceLabel = card.querySelector("[data-current-price]");
            priceLabel.textContent = money(price);
            const oldLabel = card.querySelector("[data-old-price]");
            const discountLabel = card.querySelector("[data-discount]");
            if (oldLabel) {
                oldLabel.textContent = displayedOld === null ? "" : money(displayedOld);
                oldLabel.hidden = displayedOld === null;
            }
            if (discountLabel) {
                discountLabel.hidden = displayedOld === null;
                discountLabel.textContent =
                    displayedOld === null ? "" : "-" + Math.round((1 - price / displayedOld) * 100) + "%";
            }
        }),
    );
    document.getElementById("search-form").addEventListener("submit", (event) => event.preventDefault());
    document.getElementById("search-input").addEventListener("input", (event) => {
        const query = event.target.value.trim().toLocaleLowerCase("vi");
        document.querySelectorAll(".product-card").forEach((card) => {
            card.hidden = query !== "" && !card.dataset.search.toLocaleLowerCase("vi").includes(query);
        });
        document.querySelectorAll(".collection-section,.catalog-section").forEach((section) => {
            if (section.querySelector(".product-grid"))
                section.hidden = query !== "" && !section.querySelector(".product-card:not([hidden])");
        });
    });
    document.querySelector(".checkout-button").addEventListener("click", () => {
        const selectedCart = cart.filter((item) => item.selected !== false);
        if (!selectedCart.length) return;
        const amount = selectedCart.reduce((sum, item) => sum + item.price * item.quantity, 0);
        hideCart();
        window.HHDMPayment.open({ items: selectedCart, onCreated: () => {
            for (let index = cart.length - 1; index >= 0; index--) {
                if (cart[index].selected !== false) cart.splice(index, 1);
            }
            saveCart();
            renderCart();
        } });
    });
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
    const HERO_DELAY = 3500;
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
    window.addEventListener("pageshow", syncCart);
    renderCart();
})();
