(function () {
    const isLoggedIn = document.body.dataset.loggedIn === "true";
    const CART_STORAGE_KEY = isLoggedIn ? "hhdm_cart_user_" + document.body.dataset.userId : null;
    const cart = (() => {
        if (!CART_STORAGE_KEY) return [];
        try {
            const saved = JSON.parse(localStorage.getItem(CART_STORAGE_KEY) || "[]");
            return Array.isArray(saved) ? saved.map((item) => ({ ...item, selected: item.selected !== false })) : [];
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
                ...(Array.isArray(saved) ? saved.map((item) => ({ ...item, selected: item.selected !== false })) : []),
            );
        } catch {}
        renderCart();
    }
    const drawer = document.getElementById("cart-drawer");
    const backdrop = document.getElementById("drawer-backdrop");
    const money = (n) => new Intl.NumberFormat("vi-VN").format(n) + "₫";
    let selectedSize = null;
    let selectedStock = 0;
    const sizeWrap = document.getElementById("detail-sizes");
    const hint = document.getElementById("size-hint");
    const stockInfo = document.getElementById("detail-stock-info");
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
    function requireSize() {
        if (!selectedSize) {
            if (hint) hint.classList.add("is-visible");
            toast("Vui lòng chọn size trước khi tiếp tục.");
            return false;
        }
        if (hint) hint.classList.remove("is-visible");
        return true;
    }
    function addToCart(openAfter) {
        if (!isLoggedIn) {
            toast("Vui lòng đăng nhập để thêm sản phẩm vào giỏ hàng.");
            setTimeout(() => {
                window.location.href =
                    "index.php?page=login&message=" +
                    encodeURIComponent("Vui lòng đăng nhập để thêm sản phẩm vào giỏ hàng.");
            }, 1000);
            return;
        }
        if (!requireSize()) return;
        const button = openAfter ? document.getElementById("buy-now") : document.getElementById("add-to-cart");
        const key = button.dataset.code + "-" + selectedSize;
        const existing = cart.find((item) => item.key === key);
        if (existing && existing.quantity >= selectedStock) {
            toast("Size " + selectedSize + " chỉ còn " + selectedStock + " sản phẩm.");
            return;
        }
        if (existing) {
            existing.quantity++;
        } else {
            cart.push({
                key,
                name: button.dataset.name,
                code: button.dataset.code,
                price: Number(button.dataset.price),
                size: selectedSize,
                image: button.dataset.image,
                quantity: 1,
                selected: true,
            });
        }
        if (existing) existing.selected = true;
        saveCart();
        renderCart();
        if (openAfter) {
            showCart();
        } else {
            toast("Đã thêm " + button.dataset.name + " (size " + selectedSize + ") vào giỏ hàng.");
        }
    }
    if (sizeWrap) {
        sizeWrap.addEventListener("click", (event) => {
            const option = event.target.closest(".detail-size");
            if (!option || option.disabled) return;
            sizeWrap.querySelectorAll(".detail-size").forEach((el) => el.classList.remove("is-selected"));
            option.classList.add("is-selected");
            selectedSize = option.dataset.size;
            selectedStock = Number(option.dataset.stock);
            if (stockInfo) {
                stockInfo.textContent =
                    "Tồn kho size " +
                    selectedSize +
                    ": " +
                    new Intl.NumberFormat("vi-VN").format(selectedStock) +
                    " sản phẩm";
                stockInfo.hidden = false;
            }
            if (hint) hint.classList.remove("is-visible");
            const extra = Number(option.dataset.additionalPrice);
            const price = Number(document.getElementById("add-to-cart").dataset.basePrice) + extra;
            const baseOld = document.getElementById("add-to-cart").dataset.baseOld;
            const old = baseOld === "" ? null : Number(baseOld);
            const displayedOld = old !== null && price < old ? old : null;
            document
                .querySelectorAll("#add-to-cart,#buy-now")
                .forEach((button) => (button.dataset.price = String(price)));
            document.getElementById("detail-current-price").textContent = money(price);
            const oldLabel = document.getElementById("detail-old-price");
            const discountLabel = document.getElementById("detail-discount");
            if (oldLabel) {
                oldLabel.textContent = displayedOld === null ? "" : money(displayedOld);
                oldLabel.hidden = displayedOld === null;
            }
            if (discountLabel) {
                discountLabel.hidden = displayedOld === null;
                discountLabel.textContent =
                    displayedOld === null ? "" : "-" + Math.round((1 - price / displayedOld) * 100) + "%";
            }
        });
    }
    document.getElementById("add-to-cart").addEventListener("click", () => addToCart(false));
    document.getElementById("buy-now").addEventListener("click", () => addToCart(true));
    document.addEventListener("click", (event) => {
        const remove = event.target.closest("[data-remove]");
        if (remove) {
            const index = cart.findIndex((item) => item.key === remove.dataset.remove);
            if (index > -1) cart.splice(index, 1);
            saveCart();
            renderCart();
        }
    });
    document.querySelector(".checkout-button").addEventListener("click", () => {
        const selectedCart = cart.filter((item) => item.selected !== false);
        if (!selectedCart.length) return;
        const amount = selectedCart.reduce((sum, item) => sum + item.price * item.quantity, 0);
        hideCart();
        window.HHDMPayment.open({ items: selectedCart, amount });
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
    window.addEventListener("pageshow", syncCart);
    renderCart();
})();
