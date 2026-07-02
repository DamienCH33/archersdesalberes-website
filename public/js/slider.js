document.addEventListener('DOMContentLoaded', function () {
    const sliderTrack = document.querySelector('.slider-track');
    if (!sliderTrack) return;

    const slides = document.querySelectorAll('.slide');
    const totalSlides = slides.length;
    const dotsContainer = document.querySelector('.slider-dots');
    const progressBar = document.querySelector('.slider-progress-bar');
    const counterCurrent = document.querySelector('.counter-current');
    const sliderWrapper = document.querySelector('.slider-wrapper');
    const prevBtn = document.querySelector('.slider-btn.prev');
    const nextBtn = document.querySelector('.slider-btn.next');

    if (totalSlides === 0) return;

    let currentSlide = 0;
    let autoSlideInterval = null;
    const AUTO_SLIDE_DURATION = 5000;

    for (let i = 0; i < totalSlides; i++) {
        const dot = document.createElement('button');
        dot.className = 'dot';
        dot.setAttribute('aria-label', `Aller à la photo ${i + 1}`);
        dot.addEventListener('click', () => {
            goToSlide(i);
            restartAutoSlide();
        });
        dotsContainer.appendChild(dot);
    }

    const dots = document.querySelectorAll('.dot');

    function showSlide(n) {
        currentSlide = (n + totalSlides) % totalSlides;
        sliderTrack.style.transform = `translateX(${-currentSlide * 100}%)`;

        dots.forEach((dot, index) => {
            dot.classList.toggle('active', index === currentSlide);
        });

        if (counterCurrent) counterCurrent.textContent = currentSlide + 1;

        slides.forEach((slide, index) => {
            slide.classList.toggle('active', index === currentSlide);
        });

        resetProgressBar();
    }

    function moveSlide(direction) {
        showSlide(currentSlide + direction);
    }

    function goToSlide(n) {
        showSlide(n);
    }

    function resetProgressBar() {
        if (!progressBar) return;
        progressBar.style.transition = 'none';
        progressBar.style.width = '0%';
        void progressBar.offsetWidth;
        progressBar.style.transition = `width ${AUTO_SLIDE_DURATION}ms linear`;
        progressBar.style.width = '100%';
    }

    function startAutoSlide() {
        autoSlideInterval = setInterval(() => moveSlide(1), AUTO_SLIDE_DURATION);
        resetProgressBar();
    }

    function stopAutoSlide() {
        clearInterval(autoSlideInterval);
        if (progressBar) {
            progressBar.style.transition = 'none';
            progressBar.style.width = progressBar.offsetWidth + 'px';
        }
    }

    function restartAutoSlide() {
        stopAutoSlide();
        startAutoSlide();
    }

    if (prevBtn) {
        prevBtn.addEventListener('click', () => {
            moveSlide(-1);
            restartAutoSlide();
        });
    }

    if (nextBtn) {
        nextBtn.addEventListener('click', () => {
            moveSlide(1);
            restartAutoSlide();
        });
    }

    if (sliderWrapper) {
        sliderWrapper.addEventListener('mouseenter', stopAutoSlide);
        sliderWrapper.addEventListener('mouseleave', startAutoSlide);
    }

    document.addEventListener('keydown', (e) => {
        if (e.key === 'ArrowLeft') {
            moveSlide(-1);
            restartAutoSlide();
        }
        if (e.key === 'ArrowRight') {
            moveSlide(1);
            restartAutoSlide();
        }
    });

    let touchStartX = 0;
    let touchEndX = 0;
    const SWIPE_THRESHOLD = 50;

    sliderWrapper.addEventListener('touchstart', (e) => {
        touchStartX = e.changedTouches[0].screenX;
    }, { passive: true });

    sliderWrapper.addEventListener('touchend', (e) => {
        touchEndX = e.changedTouches[0].screenX;
        const diff = touchStartX - touchEndX;
        if (Math.abs(diff) > SWIPE_THRESHOLD) {
            moveSlide(diff > 0 ? 1 : -1);
            restartAutoSlide();
        }
    }, { passive: true });

    showSlide(0);
    startAutoSlide();
});
