// Мобильді мәзірді ашу/жабу
document.addEventListener('DOMContentLoaded', () => {
    const btn = document.getElementById('menuBtn');
    const nav = document.getElementById('nav');
    if (btn && nav) {
        btn.addEventListener('click', () => {
            const open = nav.classList.toggle('open');
            btn.setAttribute('aria-expanded', open ? 'true' : 'false');
        });
    }

    // Тіркелу формасы: құпиясөздер сәйкестігін браузерде де тексереміз
    // (сервердегі тексеру БАРІБІР міндетті — бұл тек ыңғайлылық үшін)
    const p1 = document.getElementById('password');
    const p2 = document.getElementById('password2');
    if (p1 && p2) {
        const check = () => p2.setCustomValidity(
            p2.value && p1.value !== p2.value ? 'Құпиясөздер сәйкес келмейді' : ''
        );
        p1.addEventListener('input', check);
        p2.addEventListener('input', check);
    }
});
