/**
 * app.js — Rendimiento y experiencia de navegación global.
 *
 * 1. Barra de progreso fina al navegar entre páginas.
 * 2. Prefetch de categorías al cargar + prefetch por hover en cualquier enlace.
 */

/* ─── Barra de progreso ───────────────────────────────────────────────────── */

var NavProgress = (function () {
    var bar      = null;
    var timer    = null;
    var progress = 0;

    function createBar() {
        bar = document.createElement('div');
        bar.setAttribute('id', 'nav-progress-bar');
        bar.style.cssText = [
            'position:fixed',
            'top:0',
            'left:0',
            'height:3px',
            'width:0',
            'background:linear-gradient(90deg,#2563EB,#60A5FA)',
            'z-index:99999',
            'opacity:0',
            'pointer-events:none',
            'box-shadow:0 0 8px rgba(37,99,235,0.6)',
            'transition:width 0.08s ease,opacity 0.25s ease',
        ].join(';');
        document.body.appendChild(bar);
    }

    function start() {
        if (!bar) createBar();

        clearInterval(timer);
        progress = 0;
        bar.style.opacity  = '1';
        bar.style.width    = '0';

        timer = setInterval(function () {
            // Avanza rápido al principio y se ralentiza para simular espera real
            if      (progress < 30) progress += 6;
            else if (progress < 60) progress += 2.5;
            else if (progress < 80) progress += 0.8;
            else if (progress < 90) progress += 0.2;

            bar.style.width = progress + '%';

            if (progress >= 90) clearInterval(timer);
        }, 60);
    }

    function done() {
        clearInterval(timer);
        if (!bar) return;
        bar.style.width   = '100%';
        bar.style.opacity = '0';
        setTimeout(function () {
            if (bar) bar.style.width = '0';
        }, 300);
    }

    return { start: start, done: done };
}());

/* ─── Prefetch ────────────────────────────────────────────────────────────── */

var Prefetcher = (function () {
    var prefetched = new Set();

    function prefetch(href) {
        if (!href || prefetched.has(href)) return;
        if (!href.startsWith(window.location.origin)) return;
        if (href.includes('#')) return;

        prefetched.add(href);

        var link = document.createElement('link');
        link.rel  = 'prefetch';
        link.href = href;
        document.head.appendChild(link);
    }

    /** Precarga todas las categorías de la barra de navegación al cargar la página. */
    function prefetchNavCategories() {
        document.querySelectorAll('nav a[href*="/category/"]').forEach(function (a) {
            prefetch(a.href);
        });
    }

    /** Precarga cualquier enlace interno cuando el usuario pasa el ratón por encima. */
    function initHoverPrefetch() {
        document.addEventListener('mouseover', function (e) {
            var link = e.target.closest('a[href]');
            if (!link) return;
            prefetch(link.href);
        }, { passive: true });

        // También en dispositivos táctiles (touchstart = intención de pulsar)
        document.addEventListener('touchstart', function (e) {
            var link = e.target.closest('a[href]');
            if (!link) return;
            prefetch(link.href);
        }, { passive: true });
    }

    return {
        prefetchNavCategories: prefetchNavCategories,
        initHoverPrefetch:     initHoverPrefetch,
    };
}());

/* ─── Inicialización ──────────────────────────────────────────────────────── */

document.addEventListener('DOMContentLoaded', function () {

    // Barra de progreso en clics de enlace y envíos de formulario
    document.addEventListener('click', function (e) {
        var link = e.target.closest('a[href]');
        if (!link) return;

        var href      = link.getAttribute('href') || '';
        var isExternal = link.hostname && link.hostname !== window.location.hostname;
        var isNewTab   = link.target === '_blank';
        var isAnchor   = href.startsWith('#');
        var isDownload = link.hasAttribute('download');

        if (!isExternal && !isNewTab && !isAnchor && !isDownload) {
            NavProgress.start();
        }
    });

    document.addEventListener('submit', function () {
        NavProgress.start();
    });

    // Prefetch al cargar + prefetch por hover
    Prefetcher.prefetchNavCategories();
    Prefetcher.initHoverPrefetch();
});

// Si la página se carga desde la caché del navegador (bfcache),
// oculta la barra si quedó visible
window.addEventListener('pageshow', function (e) {
    if (e.persisted) NavProgress.done();
});
