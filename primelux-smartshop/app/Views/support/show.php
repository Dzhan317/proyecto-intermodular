<?php
/*
 * Soporte — vista de chat de una conversación.
 * Mensajes del usuario a la izquierda, del admin a la derecha.
 * Si la conversación está cerrada, no se muestra el campo de mensaje.
 */
ob_start();

$error = $_SESSION['support_error'] ?? null;
unset($_SESSION['support_error']);

$isClosed = ($conversation['status'] === 'closed');
$userId   = (int) $_SESSION['user_id'];
?>

<div class="flex flex-col md:flex-row gap-6">

    <?php require APP_PATH . '/Views/layouts/partials/profile-sidebar.php'; ?>

    <div class="flex-1 min-w-0 space-y-4">

        <!-- Cabecera -->
        <div class="flex items-center justify-between gap-4">
            <h2 class="text-lg font-semibold text-[var(--color-text-primary)] truncate">
                <?= htmlspecialchars($conversation['subject']) ?>
            </h2>
            <a href="<?= APP_URL ?>/support"
               class="text-sm text-[var(--color-link)] hover:text-[var(--color-link-hover)]
                      transition-colors flex-shrink-0">
                ← Volver
            </a>
        </div>

        <!-- Estado -->
        <div class="flex items-center gap-2">
            <span class="w-2.5 h-2.5 rounded-full flex-shrink-0
                         <?= $isClosed ? 'bg-[var(--color-error)]' : 'bg-[var(--color-success)]' ?>"></span>
            <span class="text-sm text-[var(--color-text-secondary)]">
                Estado: <?= $isClosed ? 'Cerrada' : 'Abierta' ?>
            </span>
        </div>

        <?php if ($error): ?>
            <div class="bg-[var(--color-error-bg)] border border-[var(--color-error-border)]
                        text-[var(--color-error)] text-sm rounded-xl px-4 py-3">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <!-- Mensajes -->
        <div class="bg-[var(--color-bg-card)] rounded-2xl border border-[var(--color-border)] overflow-hidden">

            <div class="p-5 space-y-4 min-h-[200px]" id="messageList">
                <?php if (empty($conversation['messages'])): ?>
                    <p class="text-xs text-center text-[var(--color-text-muted)] py-8">
                        Aún no hay mensajes. Escribe el primero.
                    </p>
                <?php else: ?>
                    <?php foreach ($conversation['messages'] as $msg):
                        $isOwn = ((int) $msg['user_id'] === $userId);
                    ?>
                        <div class="flex <?= $isOwn ? 'justify-start' : 'justify-end' ?>" data-msg-id="<?= (int) $msg['id'] ?>">
                            <div class="max-w-[75%] space-y-1">
                                <div class="px-4 py-2.5 rounded-2xl text-sm
                                            <?= $isOwn
                                                ? 'bg-[var(--color-brand)] text-white rounded-tl-sm'
                                                : 'bg-[var(--color-bg-secondary)] text-[var(--color-text-primary)] rounded-tr-sm border border-[var(--color-border)]' ?>">
                                    <?= nl2br(htmlspecialchars($msg['message'])) ?>
                                </div>
                                <p class="text-xs text-[var(--color-text-disabled)]
                                          <?= $isOwn ? 'text-left' : 'text-right' ?>">
                                    <?= date('H:i', strtotime($msg['created_at'])) ?>
                                </p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- Campo de mensaje -->
            <?php if (!$isClosed): ?>
                <div class="border-t border-[var(--color-border)] p-4">
                    <form id="chatForm" method="POST"
                          action="<?= APP_URL ?>/support/<?= (int) $conversation['id'] ?>/message"
                          class="flex gap-3 items-end">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                        <textarea id="chatInput" name="message" rows="2" maxlength="2000" required
                                  placeholder="Escribe un mensaje..."
                                  class="flex-1 bg-[var(--color-bg-secondary)] border border-[var(--color-border)]
                                         rounded-xl px-4 py-2.5 text-sm text-[var(--color-text-primary)]
                                         placeholder-[var(--color-text-disabled)]
                                         focus:outline-none focus:border-[var(--color-brand)]
                                         transition-colors resize-none"></textarea>
                        <button type="submit"
                                class="bg-[var(--color-brand)] hover:bg-[var(--color-brand-hover)]
                                       text-white font-semibold px-5 py-2.5 rounded-xl text-sm
                                       transition-colors flex-shrink-0">
                            Enviar
                        </button>
                    </form>
                </div>
            <?php else: ?>
                <div class="border-t border-[var(--color-border)] px-5 py-4">
                    <p class="text-xs text-center text-[var(--color-text-muted)]">
                        Esta conversación está cerrada. Si necesitas más ayuda, abre una nueva.
                    </p>
                </div>
            <?php endif; ?>

        </div>
    </div>
</div>

<?php
$lastMsgId = !empty($conversation['messages'])
    ? (int) end($conversation['messages'])['id']
    : 0;
?>
<script src="<?= APP_URL ?>/assets/js/support-chat.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    initSupportChat({
        messagesUrl: '<?= APP_URL ?>/support/<?= (int) $conversation['id'] ?>/messages',
        sendUrl:     '<?= APP_URL ?>/support/<?= (int) $conversation['id'] ?>/message',
        csrfToken:   '<?= htmlspecialchars($csrfToken, ENT_QUOTES) ?>',
        lastId:      <?= $lastMsgId ?>,
        isAdmin:     false,
        isClosed:    <?= $isClosed ? 'true' : 'false' ?>,
    });
});
</script>

<?php
$content = ob_get_clean();
require_once APP_PATH . '/Views/layouts/main.php';
