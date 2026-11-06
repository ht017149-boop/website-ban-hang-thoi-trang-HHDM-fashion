const slides = [...document.querySelectorAll('.hero-slide')];
const dots = [...document.querySelectorAll('.hero-dots button')];
let activeSlide = 0;
let slideTimer;

function showSlide(index) {
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

document.querySelector('.hero-prev').addEventListener('click', () => {
    showSlide(activeSlide - 1);
    restartSlideTimer();
});

document.querySelector('.hero-next').addEventListener('click', () => {
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

let cartCount = 0;

document.querySelectorAll('.quick-add').forEach((button) => {
    button.addEventListener('click', () => {
        cartCount += 1;
        document.querySelector('#cart-count').textContent = cartCount;
        button.textContent = 'Đã thêm ✓';

        window.setTimeout(() => {
            button.textContent = 'Thêm vào giỏ';
        }, 1200);
    });
});

document.querySelector('#newsletter-form').addEventListener('submit', (event) => {
    event.preventDefault();

    const form = event.currentTarget;
    const button = form.querySelector('button');
    button.textContent = 'Cảm ơn bạn ✓';
    form.querySelector('input').value = '';
});