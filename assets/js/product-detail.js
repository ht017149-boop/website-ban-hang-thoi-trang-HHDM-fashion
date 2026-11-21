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
    let selectedSize = null;
    const sizeWrap = document.getElementById("detail-sizes");
    const hint = document.getElementById("size-hint");
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
        if (!isAuthenticated) {
            window.location.href = "index.php?page=login";
            return;
        }
        if (!requireSize()) return;
        const button = openAfter ? document.getElementById("buy-now") : document.getElementById("add-to-cart");
        if (openAfter) {
            window.HHDMPayment.open({
                items: [
                    {
                        name: button.dataset.name,
                        size: selectedSize,
                        price: Number(button.dataset.price),
                        quantity: 1,
                    },
                ],
                amount: Number(button.dataset.price),
            });
            return;
        }
        const key = button.dataset.code + "-" + selectedSize;
        const existing = cart.find((item) => item.key === key);
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
            });
        }
        saveCart();
        renderCart();
        toast("Đã thêm " + button.dataset.name + " (size " + selectedSize + ") vào giỏ hàng.");
    }
    if (sizeWrap) {
        sizeWrap.addEventListener("click", (event) => {
            const option = event.target.closest(".detail-size");
            if (!option) return;
            sizeWrap.querySelectorAll(".detail-size").forEach((el) => el.classList.remove("is-selected"));
            option.classList.add("is-selected");
            selectedSize = option.dataset.size;
            if (hint) hint.classList.remove("is-visible");
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
    document
        .querySelector(".checkout-button")
        .addEventListener("click", () => toast("Chức năng thanh toán sẽ sớm ra mắt."));
    window.addEventListener("pageshow", syncCart);
    renderCart();
})();
