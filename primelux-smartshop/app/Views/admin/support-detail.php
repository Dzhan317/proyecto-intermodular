<?php
/*
 * Admin — Detalle de conversación de soporte.
 * Muestra los mensajes del chat y permite responder y cambiar estado.
 */
ob_start();

$isClosed = ($conversation['status'] === 'closed');
$adminId  = (int) $_SESSION['user_id'];

$flash = $admin_success ?? $admin_error ?? null;
$flashType = isset($admin_success) ? 'success' : 'error';
?>

<div class="pt-2 space-y-4">

    <!-- Cabecera -->
    <div class="flex items-center justify-between gap-4">
        <div>
            <h2 class="text-lg font-semibold text-[var(--color-text-primary)]">
                Conversación #<?= (int) $conversation['id'] ?>
            </h2>
            <p class="text-xs text-[var(--color-text-muted)] mt-0.5">
                <?= htmlspecialchars($conversation['subject']) ?>
            </p>
        </div>
        <a href="<?= APP_URL ?>/admin/support"
           class="text-sm text-[var(--color-link)] hover:text-[var(--color-link-hover)] transition-colors">
            ← Volver
        </a>
    </div>

    <?php if ($flash): ?>
        <div class="<?= $flashType === 'success'
                        ? 'bg-[var(--color-success-bg)] border-[var(--color-success-border)] text-[var(--color-success)]'
                        : 'bg-[var(--color-error-bg)] border-[var(--color-error-border)] text-[var(--color-error)]' ?>
                    border text-sm rounded-xl px-4 py-3">
            <?= htmlspecialchars($flash) ?>
        </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

        <!-- Columna principal: chat -->
        <div class="lg:col-span-2">
            <div class="bg-[var(--color-bg-card)] rounded-2xl border border-[var(--color-border)] overflow-hidden">

                <!-- Mensajes -->
                <div class="p-5 space-y-4 min-h-[300px]" id="messageList">
                    <?php if (empty($conversation['messages'])): ?>
                        <p class="text-xs text-center text-[var(--color-text-muted)] py-8">
                            Aún no hay mensajes en esta conversación.
                        </p>
                    <?php else: ?>
                        <?php foreach ($conversation['messages'] as $msg):
                            $isAdmin = ($msg['sender_role'] === 'admin');
                        ?>
                            <div class="flex <?= $isAdmin ? 'justify-end' : 'justify-start' ?>" data-msg-id="<?= (int) $msg['id'] ?>">
                                <div class="max-w-[75%] space-y-1">
                                    <div class="px-4 py-2.5 rounded-2xl text-sm
                                                <?= $isAdmin
                                                    ? 'bg-[var(--color-error-bg)] border border-[var(--color-error-border)] text-[var(--color-text-primary)] rounded-tr-sm'
                                                    : 'bg-[var(--color-brand)] text-white rounded-tl-sm' ?>">
                                        <?= nl2br(htmlspecialchars($msg['message'])) ?>
                                    </div>
                                    <p class="text-xs text-[var(--color-text-disabled)]
                                              <?= $isAdmin ? 'text-right' : 'text-left' ?>">
                                        <?= htmlspecialchars($msg['sender_name']) ?> · <?= date('H:i', strtotime($msg['created_at'])) ?>
                                    </p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <!-- Campo respuesta -->
                <?php if (!$isClosed): ?>
                    <div class="border-t border-[var(--color-border)] p-4">
                        <form id="chatForm" method="POST"
                              action="<?= APP_URL ?>/admin/support/<?= (int) $conversation['id'] ?>/message"
                              class="flex gap-3 items-end">
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                            <textarea id="chatInput" name="message" rows="2" maxlength="2000" required
                                      placeholder="Escribe una respuesta..."
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
                            Conversación cerrada.
                        </p>
                    </div>
                <?php endif; ?>

            </div>
        </div>

        <!-- Columna lateral: info + cambiar estado -->
        <div class="space-y-4">

            <!-- Datos del usuario -->
            <div class="bg-[var(--color-bg-card)] rounded-2xl border border-[var(--color-border)] p-6">
                <h3 class="text-sm font-semibold text-[var(--color-text-primary)] mb-4">Usuario</h3>
                <div class="space-y-3">
                    <div>
                        <p class="text-xs text-[var(--color-text-muted)] uppercase tracking-wider mb-1">Nombre</p>
                        <p class="text-sm text-[var(--color-text-primary)]">
                            <?= htmlspecialchars($conversation['user_name'] . ' ' . $conversation['user_last_name']) ?>
                        </p>
                    </div>
                    <div>
                        <p class="text-xs text-[var(--color-text-muted)] uppercase tracking-wider mb-1">Email</p>
                        <p class="text-sm text-[var(--color-text-primary)] break-all">
                            <?= htmlspecialchars($conversation['user_email']) ?>
                        </p>
                    </div>
                    <div>
                        <p class="text-xs text-[var(--color-text-muted)] uppercase tracking-wider mb-1">Fecha apertura</p>
                        <p class="text-sm text-[var(--color-text-primary)]">
                            <?= date('d/m/Y H:i', strtotime($conversation['created_at'])) ?>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Cambiar estado -->
            <div class="bg-[var(--color-bg-card)] rounded-2xl border border-[var(--color-border)] p-6">
                <h3 class="text-sm font-semibold text-[var(--color-text-primary)] mb-4">Estado</h3>
                <div class="flex items-center gap-2 mb-4">
                    <span class="w-2.5 h-2.5 rounded-full flex-shrink-0
                                 <?= $isClosed ? 'bg-[var(--color-error)]' : 'bg-[var(--color-success)]' ?>"></span>
                    <span class="text-sm text-[var(--color-text-secondary)]">
                        <?= $isClosed ? 'Cerrado' : 'Abierto' ?>
                    </span>
                </div>
                <form method="POST"
                      action="<?= APP_URL ?>/admin/support/<?= (int) $conversation['id'] ?>/status">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                    <input type="hidden" name="status" value="<?= $isClosed ? 'open' : 'closed' ?>">
                    <button type="submit"
                            class="w-full font-semibold py-2.5 rounded-xl text-sm transition-colors
                                   <?= $isClosed
                                       ? 'bg-[var(--color-success-bg)] border border-[var(--color-success-border)] text-[var(--color-success)] hover:bg-[var(--color-success)]/20'
                                       : 'bg-[var(--color-error-bg)] border border-[var(--color-error-border)] text-[var(--color-error)] hover:bg-[var(--color-error)]/20' ?>">
                        <?= $isClosed ? 'Reabrir conversación' : 'Cerrar conversación' ?>
                    </button>
                </form>
            </div>

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
        messagesUrl: '<?= APP_URL ?>/admin/support/<?= (int) $conversation['id'] ?>/messages',
        sendUrl:     '<?= APP_URL ?>/admin/support/<?= (int) $conversation['id'] ?>/message',
        csrfToken:   '<?= htmlspecialchars($csrfToken, ENT_QUOTES) ?>',
        lastId:      <?= $lastMsgId ?>,
        isAdmin:     true,
        isClosed:    <?= $isClosed ? 'true' : 'false' ?>,
    });
});
</script>

<?php
$content = ob_get_clean();
require_once APP_PATH . '/Views/layouts/admin.php';
