/*
 * support-chat.js
 * Polling de mensajes nuevos en el chat de soporte.
 * Usado tanto por la vista del usuario (/support/:id) como por el admin (/admin/support/:id).
 *
 * Funciones exportadas al scope global:
 *   initSupportChat(config) — inicia el polling del chat
 *   initSupportBadge(config) — inicia el polling del badge de mensajes no leídos
 */

/* ── Chat polling ─────────────────────────────────────────────────────────── */

/**
 * Inicia el polling de mensajes nuevos en una conversación.
 *
 * @param {Object} config
 * @param {string}  config.messagesUrl  — URL del endpoint JSON (ej: /support/5/messages)
 * @param {string}  config.sendUrl      — URL del formulario POST de envío
 * @param {string}  config.csrfToken    — token CSRF actual
 * @param {number}  config.lastId       — ID del último mensaje cargado en el HTML
 * @param {number}  config.userId       — ID del usuario autenticado (para distinguir propios/ajenos)
 * @param {boolean} config.isAdmin      — true si es el panel admin
 * @param {boolean} config.isClosed     — true si la conversación está cerrada
 * @param {number}  [config.interval]   — intervalo en ms (por defecto 4000)
 */
function initSupportChat(config) {
    var listEl    = document.getElementById('messageList');
    var formEl    = document.getElementById('chatForm');
    var inputEl   = document.getElementById('chatInput');

    if (!listEl) return;

    var lastId   = config.lastId   || 0;
    var interval = config.interval || 4000;

    // ── Renderiza un mensaje en el DOM ──────────────────────────────────────
    function renderMessage(msg) {
        var isOwn = msg.is_own;

        // Alineado: en admin propio va a la derecha, en usuario propio va a la izquierda
        var alignOwn  = config.isAdmin ? 'justify-end'   : 'justify-start';
        var alignOther = config.isAdmin ? 'justify-start' : 'justify-end';

        var bubble = document.createElement('div');
        bubble.className = 'flex ' + (isOwn ? alignOwn : alignOther);
        bubble.dataset.msgId = msg.id;

        var inner = document.createElement('div');
        inner.className = 'max-w-[75%] space-y-1';

        var text = document.createElement('div');
        text.className = 'px-4 py-2.5 rounded-2xl text-sm ';

        if (config.isAdmin) {
            // En el admin: propio = admin (derecha/error-bg), ajeno = cliente (izquierda/brand)
            text.className += isOwn
                ? 'bg-[var(--color-error-bg)] border border-[var(--color-error-border)] text-[var(--color-text-primary)] rounded-tr-sm'
                : 'bg-[var(--color-brand)] text-white rounded-tl-sm';
        } else {
            // En el usuario: propio = usuario (izquierda/brand), ajeno = admin (derecha/secondary)
            text.className += isOwn
                ? 'bg-[var(--color-brand)] text-white rounded-tl-sm'
                : 'bg-[var(--color-bg-secondary)] text-[var(--color-text-primary)] rounded-tr-sm border border-[var(--color-border)]';
        }

        // El contenido ya viene con htmlspecialchars del servidor — solo reemplazamos saltos de línea
        text.innerHTML = msg.message.replace(/\n/g, '<br>');
        inner.appendChild(text);

        var meta = document.createElement('p');
        meta.className = 'text-xs text-[var(--color-text-disabled)] ' + (isOwn ? 'text-left' : 'text-right');

        var metaText = msg.time;
        if (config.isAdmin) {
            metaText = msg.sender_name + ' · ' + msg.time;
        }
        meta.textContent = metaText;
        inner.appendChild(meta);

        bubble.appendChild(inner);
        listEl.appendChild(bubble);
        listEl.scrollTop = listEl.scrollHeight;
    }

    // ── Polling de mensajes nuevos ──────────────────────────────────────────
    function poll() {
        fetch(config.messagesUrl + '?since=' + lastId, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(function (res) { return res.json(); })
        .then(function (data) {
            if (!data.messages || data.messages.length === 0) return;
            data.messages.forEach(function (msg) {
                // Evita duplicar mensajes que ya están en el DOM
                if (!document.querySelector('[data-msg-id="' + msg.id + '"]')) {
                    renderMessage(msg);
                }
                if (msg.id > lastId) lastId = msg.id;
            });
        })
        .catch(function () { /* silencioso — no rompe la UI si falla una petición */ });
    }

    // ── Envío por AJAX — evita recarga de página ────────────────────────────
    if (formEl && inputEl && !config.isClosed) {
        formEl.addEventListener('submit', function (e) {
            e.preventDefault();

            var message = inputEl.value.trim();
            if (!message) return;

            var body = new URLSearchParams();
            body.append('csrf_token', config.csrfToken);
            body.append('message',    message);

            fetch(config.sendUrl, {
                method:  'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body:    body,
            })
            .then(function (res) { return res.json(); })
            .then(function (data) {
                if (data.message) {
                    renderMessage(data.message);
                    if (data.message.id > lastId) lastId = data.message.id;
                    inputEl.value = '';
                    inputEl.focus();
                }
            })
            .catch(function () { /* fallback: submit normal si falla el AJAX */ formEl.submit(); });
        });
    }

    // Scroll inicial y arranque del polling
    listEl.scrollTop = listEl.scrollHeight;
    setInterval(poll, interval);
}

/* ── Badge polling ────────────────────────────────────────────────────────── */

/**
 * Inicia el polling del badge de mensajes no leídos en el header.
 *
 * @param {Object} config
 * @param {string} config.unreadUrl  — URL del endpoint JSON de no leídos
 * @param {string} config.badgeId    — ID del elemento badge en el DOM
 * @param {number} [config.interval] — intervalo en ms (por defecto 10000)
 */
function initSupportBadge(config) {
    var badgeEl       = document.getElementById(config.badgeId || 'supportBadge');
    var badgeMobileEl = document.getElementById('supportBadgeMobile');
    var interval      = config.interval || 10000;

    if (!badgeEl && !badgeMobileEl) return;

    function updateBadge() {
        fetch(config.unreadUrl, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(function (res) { return res.json(); })
        .then(function (data) {
            var count = data.count || 0;
            var text  = count > 99 ? '99+' : count;

            // Badge desktop
            if (badgeEl) {
                if (count > 0) {
                    badgeEl.textContent = text;
                    badgeEl.classList.remove('hidden');
                } else {
                    badgeEl.classList.add('hidden');
                }
            }

            // Badge móvil (dentro del menú hamburguesa)
            if (badgeMobileEl) {
                if (count > 0) {
                    badgeMobileEl.textContent = text;
                    badgeMobileEl.classList.remove('hidden');
                } else {
                    badgeMobileEl.classList.add('hidden');
                }
            }
        })
        .catch(function () { /* silencioso */ });
    }

    updateBadge();
    setInterval(updateBadge, interval);
}
