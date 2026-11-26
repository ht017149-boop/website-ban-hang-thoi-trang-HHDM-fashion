(function () {
    const modal = document.getElementById("payment-modal");
    if (!modal) return;

    const dialog = modal.querySelector(".payment-dialog");
    const qr = document.getElementById("payment-qr");
    const qrWrap = document.getElementById("payment-qr-wrap");
    const amountLabel = document.getElementById("payment-amount");
    const itemsList = document.getElementById("payment-order-items");
    const referenceLabel = document.getElementById("payment-reference");
    const feedback = document.getElementById("payment-feedback");
    const checkoutForm = document.getElementById("checkout-details");
    const paymentDetails = document.getElementById("payment-details");
    const paymentNote = document.getElementById("payment-note");
    const orderCreatedNotice = document.getElementById("order-created-notice");
    const orderCreatedNumber = document.getElementById("order-created-number");
    const money = (value) => new Intl.NumberFormat("vi-VN").format(value) + "₫";
    let pendingItems = [];
    let onCreated;
    let creatingOrder = false;

    function closePayment() {
        modal.classList.remove("is-open");
        modal.setAttribute("aria-hidden", "true");
        document.body.classList.remove("payment-open");
        window.setTimeout(() => { modal.hidden = true; }, 220);
    }

    async function watchOrder(orderId) {
        if (modal.hidden || !modal.classList.contains("is-open")) return;
        try {
            const response = await fetch(`index.php?page=payment_status&order=${encodeURIComponent(orderId)}`, { cache: "no-store" });
            const result = await response.json();
            if (result.paid) {
                window.location.assign(`index.php?page=payment_success&order=${encodeURIComponent(orderId)}`);
                return;
            }
        } catch (_) {}
        window.setTimeout(() => watchOrder(orderId), 5000);
    }

    function openPayment({ items = [], onCreated: afterCreate } = {}) {
        pendingItems = items.filter((item) => item && Number(item.productId) > 0 && item.size && Number(item.quantity) > 0);
        if (!pendingItems.length) return;
        onCreated = afterCreate;
        checkoutForm.reset();
        checkoutForm.elements.recipient.value = document.body.dataset.userName || "";
        checkoutForm.hidden = false;
        checkoutForm.querySelector("button[type=submit]").disabled = false;
        qrWrap.hidden = true;
        paymentDetails.hidden = true;
        paymentNote.hidden = true;
        orderCreatedNotice.hidden = true;
        itemsList.replaceChildren(...pendingItems.map((item) => {
            const row = document.createElement("p");
            row.textContent = `${item.name} · Size ${item.size} × ${item.quantity}`;
            return row;
        }));
        amountLabel.textContent = money(pendingItems.reduce((sum, item) => sum + Number(item.price) * Number(item.quantity), 0));
        referenceLabel.textContent = "Sẽ hiển thị sau khi tạo đơn";
        feedback.textContent = "Nhập thông tin giao hàng để tạo đơn và mã QR thanh toán.";
        modal.hidden = false;
        requestAnimationFrame(() => {
            modal.classList.add("is-open");
            modal.setAttribute("aria-hidden", "false");
            document.body.classList.add("payment-open");
            checkoutForm.elements.recipient.focus();
        });
    }

    checkoutForm.addEventListener("submit", async (event) => {
        event.preventDefault();
        if (creatingOrder || !checkoutForm.reportValidity()) return;
        creatingOrder = true;
        const submitButton = checkoutForm.querySelector("button[type=submit]");
        submitButton.disabled = true;
        feedback.textContent = "Đang kiểm tra tồn kho và kết nối cổng thanh toán…";
        try {
            const form = new FormData(checkoutForm);
            const response = await fetch("index.php?page=create_order", {
                method: "POST",
                headers: { "Content-Type": "application/json", "X-CSRF-Token": document.body.dataset.csrfToken || "" },
                body: JSON.stringify({
                    items: pendingItems.map((item) => ({ product_id: item.productId, size: item.size, quantity: item.quantity })),
                    checkout: {
                        recipient: form.get("recipient"),
                        phone: form.get("phone"),
                        address: form.get("address"),
                        voucher_code: form.get("voucher_code"),
                    },
                }),
            });
            const result = await response.json();
            if (!response.ok || !result.success) throw new Error(result.error || "Không thể khởi tạo thanh toán.");
            const order = result.order;
            if (typeof onCreated === "function") onCreated();
            orderCreatedNumber.textContent = "Mã đơn hàng: #" + order.order_id + (order.discount > 0 ? " · Đã giảm " + money(order.discount) : "");
            orderCreatedNotice.hidden = false;
            amountLabel.textContent = money(order.amount);
            referenceLabel.textContent = order.reference;
            document.getElementById("payment-bank").textContent = [order.payment.bank_bin, order.payment.account_name].filter(Boolean).join(" · ") || "payOS";
            document.getElementById("payment-account").textContent = order.payment.account_number || "Xem trên trang payOS";
            itemsList.replaceChildren(...order.items.map((item) => {
                const row = document.createElement("p");
                row.textContent = `${item.name} · Size ${item.size} × ${item.quantity}`;
                return row;
            }));
            checkoutForm.hidden = true;
            qr.src = `https://api.qrserver.com/v1/create-qr-code/?size=320x320&format=svg&data=${encodeURIComponent(order.payment.qr_code)}`;
            qr.alt = `Mã QR thanh toán đơn hàng ${order.order_id}, số tiền ${money(order.amount)}`;
            qrWrap.hidden = false;
            paymentDetails.hidden = false;
            paymentNote.hidden = false;
            feedback.textContent = "Đơn hàng đã tạo. Quét mã bằng ứng dụng ngân hàng; trang sẽ tự chuyển khi payOS xác nhận đã nhận đủ tiền.";
            watchOrder(order.order_id);
        } catch (error) {
            feedback.textContent = error.message || "Không thể khởi tạo thanh toán.";
            submitButton.disabled = false;
        } finally {
            creatingOrder = false;
        }
    });

    modal.addEventListener("click", (event) => {
        if (event.target.closest("[data-payment-close]")) closePayment();
        const copyButton = event.target.closest("[data-copy]");
        if (!copyButton) return;
        const value = copyButton.dataset.copy === "account"
            ? document.getElementById("payment-account").textContent
            : referenceLabel.textContent;
        if (navigator.clipboard?.writeText) {
            navigator.clipboard.writeText(value).then(() => { feedback.textContent = "Đã sao chép."; }).catch(() => { feedback.textContent = value; });
        } else feedback.textContent = value;
    });
    qr.addEventListener("error", () => { feedback.textContent = "Không tải được hình QR. Vui lòng thử lại sau ít phút."; });
    document.addEventListener("keydown", (event) => { if (event.key === "Escape" && !modal.hidden) closePayment(); });
    window.HHDMPayment = { open: openPayment, close: closePayment };
})();
