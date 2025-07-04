let lastScrollY = window.scrollY;
let ticking = false;

function handleBackToTopBtn() {
    const btn = document.getElementById('backToTopBtn');
    const footer = document.querySelector('footer');
    let footerTop = 0;
    let btnHeight = btn.offsetHeight;
    if (footer) {
        const rect = footer.getBoundingClientRect();
        footerTop = rect.top + window.scrollY;
    }
    const windowBottom = window.scrollY + window.innerHeight;

    // Só mostra em mobile se estiver a fazer scroll para cima
    const isMobile = window.innerWidth <= 600;
    const scrollingUp = window.scrollY < lastScrollY;

    if (window.scrollY > 200 && (!isMobile || (isMobile && scrollingUp))) {
        btn.style.display = 'block';
        btn.classList.remove('hide-mobile');
        // Ajusta a posição se o botão estiver sobrepondo o footer
        if (footer && (windowBottom > footerTop)) {
            btn.style.bottom = (windowBottom - footerTop + 20) + 'px';
        } else {
            btn.style.bottom = '20px';
        }
    } else {
        if (isMobile) {
            btn.classList.add('hide-mobile');
        } else {
            btn.style.display = 'none';
        }
    }
    lastScrollY = window.scrollY;
    ticking = false;
}

window.addEventListener('scroll', function() {
    if (!ticking) {
        window.requestAnimationFrame(handleBackToTopBtn);
        ticking = true;
    }
});

document.getElementById('backToTopBtn').addEventListener('click', function() {
    window.scrollTo({top: 0, behavior: 'smooth'});
});