const cartCountElement = document.querySelector('#cart-count');
const cartStorageKey = 'hhdm-cart-count';
let cartCount = Number(window.localStorage.getItem(cartStorageKey) || 0);

function updateCartCount(amount = 1) {
    cartCount += amount;
    window.localStorage.setItem(cartStorageKey, String(cartCount));

    if (cartCountElement) {
        cartCountElement.textContent = cartCount;
    }
}

if (cartCountElement) {
    cartCountElement.textContent = cartCount;
}

const slides = [...document.querySelectorAll('.hero-slide')];
const dots = [...document.querySelectorAll('.hero-dots button')];
const previousSlideButton = document.querySelector('.hero-prev');
const nextSlideButton = document.querySelector('.hero-next');
let activeSlide = 0;
let slideTimer;

function showSlide(index) {
    if (slides.length === 0) {
        return;
    }

    activeSlide = (index + slides.length) % slides.length;

    slides.forEach((slide, slideIndex) => {
        slide.classList.toggle('is-active', slideIndex === activeSlide);
    });

    dots.forEach((dot, dotIndex) => {
        dot.classList.toggle('is-active', dotIndex === activeSlide);
    });
}

function restartSlideTimer() {
    window.clearInterval(slideTimer);
    slideTimer = window.setInterval(() => showSlide(activeSlide + 1), 4500);
}

if (slides.length > 0 && previousSlideButton && nextSlideButton) {
    previousSlideButton.addEventListener('click', () => {
        showSlide(activeSlide - 1);
        restartSlideTimer();
    });

    nextSlideButton.addEventListener('click', () => {
        showSlide(activeSlide + 1);
        restartSlideTimer();
    });

    dots.forEach((dot, index) => {
        dot.addEventListener('click', () => {
            showSlide(index);
            restartSlideTimer();
        });
    });

    restartSlideTimer();
}

document.querySelectorAll('.quick-add').forEach((button) => {
    button.addEventListener('click', (event) => {
        event.preventDefault();
        updateCartCount();
        button.textContent = 'Đã thêm ✓';

        window.setTimeout(() => {
            button.innerHTML = 'Thêm nhanh <span aria-hidden="true">+</span>';
        }, 1200);
    });
});

document.querySelectorAll('.gallery-thumb').forEach((thumbnail) => {
    thumbnail.addEventListener('click', () => {
        const mainImage = document.querySelector('#product-main-image');

        if (!mainImage) {
            return;
        }

        mainImage.src = thumbnail.dataset.image;
        document.querySelectorAll('.gallery-thumb').forEach((item) => item.classList.remove('is-active'));
        thumbnail.classList.add('is-active');
    });
});

document.querySelectorAll('.detail-swatch').forEach((swatch) => {
    swatch.addEventListener('click', () => {
        document.querySelectorAll('.detail-swatch').forEach((item) => item.classList.remove('is-active'));
        swatch.classList.add('is-active');

        const selectedColorLabel = document.querySelector('#selected-color-label');
        if (selectedColorLabel) {
            selectedColorLabel.textContent = swatch.dataset.colorName;
        }
    });
});

document.querySelectorAll('.size-option').forEach((sizeButton) => {
    sizeButton.addEventListener('click', () => {
        document.querySelectorAll('.size-option').forEach((item) => item.classList.remove('is-active'));
        sizeButton.classList.add('is-active');

        const label = document.querySelector('#selected-size-label');
        if (label) {
            label.textContent = sizeButton.dataset.size;
        }

        const sizeMessage = document.querySelector('#size-message');
        if (sizeMessage) {
            sizeMessage.textContent = '';
        }
    });
});

document.querySelectorAll('[data-quantity-change]').forEach((button) => {
    button.addEventListener('click', () => {
        const quantityInput = document.querySelector('#quantity');
        if (!quantityInput) {
            return;
        }

        const nextValue = Number(quantityInput.value) + Number(button.dataset.quantityChange);
        quantityInput.value = Math.min(10, Math.max(1, nextValue));
    });
});

const sizeGuide = document.querySelector('#size-guide');
const openSizeGuideButton = document.querySelector('[data-open-size-guide]');

if (sizeGuide && openSizeGuideButton) {
    openSizeGuideButton.addEventListener('click', () => sizeGuide.showModal());
}

document.querySelectorAll('.product-tab').forEach((tab) => {
    tab.addEventListener('click', () => {
        document.querySelectorAll('.product-tab').forEach((item) => {
            item.classList.remove('is-active');
            item.setAttribute('aria-selected', 'false');
        });

        document.querySelectorAll('.product-tab-panel').forEach((panel) => {
            panel.classList.remove('is-active');
            panel.hidden = true;
        });

        const panel = document.querySelector(`#${tab.getAttribute('aria-controls')}`);
        tab.classList.add('is-active');
        tab.setAttribute('aria-selected', 'true');

        if (panel) {
            panel.classList.add('is-active');
            panel.hidden = false;
        }
    });
});

const productAddButton = document.querySelector('#product-add');

if (productAddButton) {
    productAddButton.addEventListener('click', () => {
        const selectedSize = document.querySelector('#selected-size');
        const sizeLabel = document.querySelector('#selected-size-label');
        const sizeMessage = document.querySelector('#size-message');

        if (sizeLabel && sizeLabel.textContent === 'Chưa chọn') {
            if (sizeMessage) {
                sizeMessage.textContent = 'Vui lòng chọn kích cỡ trước khi thêm vào giỏ.';
            }
            return;
        }

        const quantityInput = document.querySelector('#quantity');
        const quantity = quantityInput ? Number(quantityInput.value) : 1;
        updateCartCount(quantity);

        const cartMessage = document.querySelector('#cart-message');
        if (cartMessage) {
            const size = selectedSize ? selectedSize.value : (sizeLabel ? sizeLabel.textContent : '');
            const sizeText = size ? ` · Size ${size}` : '';
            cartMessage.textContent = `Đã thêm ${quantity} sản phẩm${sizeText} vào giỏ hàng.`;
        }
    });
}

const newsletterForm = document.querySelector('#newsletter-form');

if (newsletterForm) {
    newsletterForm.addEventListener('submit', (event) => {
        event.preventDefault();

        const button = newsletterForm.querySelector('button');
        button.textContent = 'Cảm ơn bạn ✓';
        newsletterForm.querySelector('input').value = '';
    });
}

document.querySelectorAll('[data-auto-submit]').forEach((control) => {
    control.addEventListener('change', () => control.form.submit());
});