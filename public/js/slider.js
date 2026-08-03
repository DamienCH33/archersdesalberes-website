document.addEventListener("DOMContentLoaded", function () {
    const sliderTrack = document.querySelector(".slider-track");
    if (!sliderTrack) return;

    const slides = document.querySelectorAll(".slide");
    const totalSlides = slides.length;
    const dotsContainer = document.querySelector(".slider-dots");
    const progressBar = document.querySelector(".slider-progress-bar");
    const progressZone = document.querySelector(".slider-progress");
    const counterCurrent = document.querySelector(".counter-current");
    const sliderWrapper = document.querySelector(".slider-wrapper");
    const prevBtn = document.querySelector(".slider-btn.prev");
    const nextBtn = document.querySelector(".slider-btn.next");
    const pauseBtn = document.querySelector(".slider-pause");
    const iconPause = pauseBtn?.querySelector(".icon-pause");
    const iconPlay = pauseBtn?.querySelector(".icon-play");

    if (totalSlides === 0) return;

    let currentSlide = 0;
    let autoSlideInterval = null;
    let isPaused = false;
    const AUTO_SLIDE_DURATION = 5000;

    for (let i = 0; i < totalSlides; i++) {
        const dot = document.createElement("button");
        dot.className = "dot";
        dot.setAttribute("aria-label", `Aller à la photo ${i + 1}`);
        dot.addEventListener("click", () => {
            goToSlide(i);
            restartAutoSlide();
        });
        dotsContainer.appendChild(dot);
    }

    const dots = document.querySelectorAll(".dot");

    function showSlide(n) {
        currentSlide = (n + totalSlides) % totalSlides;
        const activeImg = slides[currentSlide].querySelector("img");
        const activeSlide = slides[currentSlide];
        if (activeImg && sliderWrapper) {
            const applyFormat = () => {
                const isPortrait =
                    activeImg.naturalHeight > activeImg.naturalWidth;
                const isMobile = window.innerWidth <= 768;
                if (isPortrait) {
                    sliderWrapper.style.height = isMobile ? "480px" : "650px";
                    activeImg.style.objectFit = "contain";
                    activeSlide.classList.add("is-portrait");
                } else {
                    sliderWrapper.style.height = isMobile ? "240px" : "520px";
                    activeImg.style.objectFit = "cover";
                    activeSlide.classList.remove("is-portrait");
                }
            };
            if (activeImg.complete) applyFormat();
            else activeImg.addEventListener("load", applyFormat, { once: true });
        }
        sliderTrack.style.transform = `translateX(${-currentSlide * 100}%)`;

        dots.forEach((dot, index) => {
            dot.classList.toggle("active", index === currentSlide);
        });

        if (counterCurrent) counterCurrent.textContent = currentSlide + 1;

        slides.forEach((slide, index) => {
            slide.classList.toggle("active", index === currentSlide);
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
        progressBar.style.transition = "none";
        progressBar.style.width = "0%";
        void progressBar.offsetWidth;
        progressBar.style.transition = `width ${AUTO_SLIDE_DURATION}ms linear`;
        progressBar.style.width = "100%";
    }

    function startAutoSlide() {
        autoSlideInterval = setInterval(() => moveSlide(1), AUTO_SLIDE_DURATION);
        resetProgressBar();
    }

    function stopAutoSlide() {
        clearInterval(autoSlideInterval);
        if (progressBar) {
            progressBar.style.transition = "none";
            progressBar.style.width = progressBar.offsetWidth + "px";
        }
    }

    function restartAutoSlide() {
        stopAutoSlide();
        startAutoSlide();
    }

    if (prevBtn) {
        prevBtn.addEventListener("click", () => {
            moveSlide(-1);
            restartAutoSlide();
        });
    }

    if (nextBtn) {
        nextBtn.addEventListener("click", () => {
            moveSlide(1);
            restartAutoSlide();
        });
    }

    if (progressZone) {
        progressZone.addEventListener("mouseenter", () => {
            if (progressBar) progressBar.style.opacity = "0";
            if (pauseBtn) pauseBtn.style.opacity = "1";
        });
        progressZone.addEventListener("mouseleave", () => {
            if (isPaused) return;
            if (pauseBtn) pauseBtn.style.opacity = "0";
            if (progressBar) progressBar.style.opacity = "1";
        });
    }

    document.addEventListener("keydown", (e) => {
        if (e.key === "ArrowLeft") {
            moveSlide(-1);
            restartAutoSlide();
        }
        if (e.key === "ArrowRight") {
            moveSlide(1);
            restartAutoSlide();
        }
    });

    let touchStartX = 0;
    let touchEndX = 0;
    const SWIPE_THRESHOLD = 50;

    sliderWrapper.addEventListener(
        "touchstart",
        (e) => {
            touchStartX = e.changedTouches[0].screenX;
        },
        { passive: true },
    );

    sliderWrapper.addEventListener(
        "touchend",
        (e) => {
            touchEndX = e.changedTouches[0].screenX;
            const diff = touchStartX - touchEndX;
            if (Math.abs(diff) > SWIPE_THRESHOLD) {
                moveSlide(diff > 0 ? 1 : -1);
                restartAutoSlide();
            }
        },
        { passive: true },
    );

    if (pauseBtn) {
        pauseBtn.addEventListener("click", () => {
            isPaused = !isPaused;
            if (isPaused) {
                stopAutoSlide();
                iconPause.style.display = "none";
                iconPlay.style.display = "block";
                pauseBtn.style.opacity = "1";
                pauseBtn.setAttribute("aria-label", "Reprendre le diaporama");
            } else {
                startAutoSlide();
                iconPause.style.display = "block";
                iconPlay.style.display = "none";
                pauseBtn.setAttribute("aria-label", "Mettre en pause");
            }
        });
    }

    showSlide(0);
    startAutoSlide();
});
