<?php
/*
 * Soporte — listado de conversaciones del usuario.
 * Estado vacío con botón "Contactar con soporte".
 * Con conversaciones: lista + botón "Nueva conversación".
 */
ob_start();

$error = $_SESSION['support_error'] ?? null;
unset($_SESSION['support_error']);
?>

<div class="flex flex-col md:flex-row gap-6">

    <?php require APP_PATH . '/Views/layouts/partials/profile-sidebar.php'; ?>

    <div class="flex-1 min-w-0 space-y-4">

        <div class="flex items-center justify-between gap-4">
            <h2 class="text-lg font-semibold text-[var(--color-text-primary)]">Soporte</h2>
            <?php if (!empty($conversations)): ?>
                <button onclick="document.getElementById('newConversationModal').classList.remove('hidden')"
                        class="bg-[var(--color-brand)] hover:bg-[var(--color-brand-hover)]
                               text-white font-semibold px-4 py-2 rounded-xl text-xs transition-colors">
                    + Nueva conversación
                </button>
            <?php endif; ?>
        </div>

        <?php if ($error): ?>
            <div class="bg-[var(--color-error-bg)] border border-[var(--color-error-border)]
                        text-[var(--color-error)] text-sm rounded-xl px-4 py-3">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <?php if (empty($conversations)): ?>

            <!-- Estado vacío -->
            <div class="bg-[var(--color-bg-card)] rounded-2xl border border-[var(--color-border)] p-12 text-center">
                <svg class="w-12 h-12 text-[var(--color-border)] mx-auto mb-4"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                          d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03
                             8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512
                             15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
                <h3 class="text-sm font-semibold text-[var(--color-text-primary)] mb-1">
                    No tienes conversaciones activas
                </h3>
                <p class="text-xs text-[var(--color-text-secondary)] mb-5">
                    Si necesitas ayuda, puedes contactar con soporte. ¡Te responderemos lo antes posible!
                </p>
                <button onclick="document.getElementById('newConversationModal').classList.remove('hidden')"
                        class="inline-block bg-[var(--color-brand)] hover:bg-[var(--color-brand-hover)]
                               text-white font-semibold px-5 py-2.5 rounded-xl text-xs transition-colors">
                    Contactar con soporte
                </button>
            </div>

        <?php else: ?>

            <div class="space-y-3">
                <?php foreach ($conversations as $conv): ?>
                    <div class="bg-[var(--color-bg-card)] rounded-2xl border border-[var(--color-border)] p-5">
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex-1 min-w-0">
                                <p class="text-xs text-[var(--color-text-muted)] mb-1">Soporte técnico</p>
                                <p class="text-sm font-semibold text-[var(--color-text-primary)] truncate mb-1">
                                    <?= htmlspecialchars($conv['subject']) ?>
                                </p>
                                <?php if (!empty($conv['last_message'])): ?>
                                    <p class="text-xs text-[var(--color-text-muted)] truncate mb-2">
                                        "<?= htmlspecialchars(mb_substr($conv['last_message'], 0, 60)) ?><?= mb_strlen($conv['last_message']) > 60 ? '…' : '' ?>"
                                    </p>
                                <?php endif; ?>
                                <?php if (!empty($conv['last_message_at'])): ?>
                                    <p class="text-xs text-[var(--color-text-disabled)]">
                                        <?= date('d/m/Y H:i', strtotime($conv['last_message_at'])) ?>
                                    </p>
                                <?php endif; ?>
                            </div>
                            <div class="flex flex-col items-end gap-3 flex-shrink-0">
                                <span class="inline-flex items-center gap-1.5 text-xs font-medium
                                             <?= $conv['status'] === 'open'
                                                 ? 'text-[var(--color-success)]'
                                                 : 'text-[var(--color-error)]' ?>">
                                    <span class="w-2 h-2 rounded-full flex-shrink-0
                                                 <?= $conv['status'] === 'open'
                                                     ? 'bg-[var(--color-success)]'
                                                     : 'bg-[var(--color-error)]' ?>"></span>
                                    <?= $conv['status'] === 'open' ? 'Abierta' : 'Cerrada' ?>
                                </span>
                                <a href="<?= APP_URL ?>/support/<?= (int) $conv['id'] ?>"
                                   class="bg-[var(--color-brand)] hover:bg-[var(--color-brand-hover)]
                                          text-white font-semibold px-4 py-2 rounded-xl text-xs transition-colors">
                                    Ver conversación
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

        <?php endif; ?>

    </div>
</div>

<!-- Modal nueva conversación -->
<div id="newConversationModal"
     class="hidden fixed inset-0 z-50 flex items-center justify-center
            bg-black/60 backdrop-blur-sm px-4">
    <div class="bg-[var(--color-bg-card)] rounded-2xl border border-[var(--color-border)]
                w-full max-w-md p-6 space-y-4">
        <div class="flex items-center justify-between">
            <h3 class="text-sm font-semibold text-[var(--color-text-primary)]">Nueva conversación</h3>
            <button onclick="document.getElementById('newConversationModal').classList.add('hidden')"
                    class="text-[var(--color-text-muted)] hover:text-[var(--color-text-primary)]
                           transition-colors text-lg leading-none">×</button>
        </div>
        <form method="POST" action="<?= APP_URL ?>/support" class="space-y-4">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
            <div>
                <label class="block text-xs text-[var(--color-text-muted)] uppercase tracking-wider mb-1.5">
                    Asunto
                </label>
                <input type="text" name="subject" maxlength="255" required
                       placeholder="Describe brevemente tu consulta"
                       class="w-full bg-[var(--color-bg-secondary)] border border-[var(--color-border)]
                              rounded-xl px-4 py-2.5 text-sm text-[var(--color-text-primary)]
                              placeholder-[var(--color-text-disabled)]
                              focus:outline-none focus:border-[var(--color-brand)] transition-colors">
            </div>
            <div>
                <label class="block text-xs text-[var(--color-text-muted)] uppercase tracking-wider mb-1.5">
                    Mensaje inicial (opcional)
                </label>
                <textarea name="message" rows="3" maxlength="2000"
                          placeholder="Escribe tu mensaje..."
                          class="w-full bg-[var(--color-bg-secondary)] border border-[var(--color-border)]
                                 rounded-xl px-4 py-2.5 text-sm text-[var(--color-text-primary)]
                                 placeholder-[var(--color-text-disabled)]
                                 focus:outline-none focus:border-[var(--color-brand)]
                                 transition-colors resize-none"></textarea>
            </div>
            <button type="submit"
                    class="w-full bg-[var(--color-brand)] hover:bg-[var(--color-brand-hover)]
                           text-white font-semibold py-2.5 rounded-xl text-sm transition-colors">
                Iniciar conversación
            </button>
        </form>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once APP_PATH . '/Views/layouts/main.php';
