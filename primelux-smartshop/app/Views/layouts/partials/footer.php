<?php
$supportEnabled = class_exists('SupportController') || file_exists(APP_PATH . '/Controllers/SupportController.php');
?>
<footer class="bg-[var(--color-bg-secondary)] border-t border-[var(--color-divider)] mt-auto">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-10">

            <!-- Marca -->
            <div>
                <img src="<?= APP_URL ?>/assets/img/logos/logo_secundario.webp"
                     alt="PrimeLux SmartShop"
                     class="h-16 w-auto mb-4">

                <p class="text-[var(--color-text-muted)] text-sm leading-relaxed">
                    Tu supermercado digital de confianza.<br>
                    Venta online de productos.
                </p>
            </div>

            <!-- Atención al cliente -->
            <div>
                <h3 class="text-[var(--color-text-primary)] text-sm font-semibold mb-4 uppercase tracking-wider">
                    Atención al cliente
                </h3>
                <ul class="space-y-2">
                    <li><a href="#" class="text-[var(--color-text-secondary)] hover:text-[var(--color-text-primary)] text-sm transition-colors">Contacto</a></li>
                    <li><a href="#" class="text-[var(--color-text-secondary)] hover:text-[var(--color-text-primary)] text-sm transition-colors">Preguntas frecuentes</a></li>
                    <li><a href="#" class="text-[var(--color-text-secondary)] hover:text-[var(--color-text-primary)] text-sm transition-colors">Envíos y devoluciones</a></li>
                    <?php if ($supportEnabled): ?><li><a href="<?= APP_URL ?>/support" class="text-[var(--color-text-secondary)] hover:text-[var(--color-text-primary)] text-sm transition-colors">Soporte</a></li><?php else: ?><li><span class="text-[var(--color-text-disabled)] text-sm cursor-not-allowed">Soporte (próximamente)</span></li><?php endif; ?>
                </ul>
            </div>

            <!-- Legal -->
            <div>
                <h3 class="text-[var(--color-text-primary)] text-sm font-semibold mb-4 uppercase tracking-wider">
                    Legal
                </h3>
                <ul class="space-y-2">
                    <li><a href="#" class="text-[var(--color-text-secondary)] hover:text-[var(--color-text-primary)] text-sm transition-colors">Política de privacidad</a></li>
                    <li><a href="#" class="text-[var(--color-text-secondary)] hover:text-[var(--color-text-primary)] text-sm transition-colors">Política de cookies</a></li>
                    <li><a href="#" class="text-[var(--color-text-secondary)] hover:text-[var(--color-text-primary)] text-sm transition-colors">Términos y condiciones</a></li>
                </ul>
            </div>

        </div>

        <!-- Barra inferior -->
        <div class="border-t border-[var(--color-divider)] pt-6 flex flex-col sm:flex-row items-center justify-between gap-4">
            <p class="text-[var(--color-text-disabled)] text-sm">
                © <?= date('Y') ?> PrimeLux SmartShop
            </p>

            <!-- Redes sociales -->
            <div class="flex items-center gap-3">
                <a href="#" aria-label="X (Twitter)"
                   class="w-9 h-9 rounded-full bg-[var(--color-bg-card)] flex items-center justify-center
                          text-[var(--color-text-secondary)] hover:text-[var(--color-text-primary)] hover:bg-[var(--color-bg-hover)] transition-colors">
                    <!-- X logo -->
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-4.714-6.231-5.401
                                 6.231H2.744l7.73-8.835L1.254 2.25H8.08l4.253 5.622 5.911-5.622z"/>
                    </svg>
                </a>
                <a href="#" aria-label="Instagram"
                   class="w-9 h-9 rounded-full bg-[var(--color-bg-card)] flex items-center justify-center
                          text-[var(--color-text-secondary)] hover:text-[var(--color-text-primary)] hover:bg-[var(--color-bg-hover)] transition-colors">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919
                                 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149
                                 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204
                                 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849
                                 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057
                                 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78
                                 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072
                                 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259
                                 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948
                                 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0
                                 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0
                                 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/>
                    </svg>
                </a>
                <a href="#" aria-label="WhatsApp"
                   class="w-9 h-9 rounded-full bg-[var(--color-bg-card)] flex items-center justify-center
                          text-[var(--color-text-secondary)] hover:text-[var(--color-text-primary)] hover:bg-[var(--color-bg-hover)] transition-colors">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94
                                 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198
                                 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/>
                        <path d="M12 0C5.373 0 0 5.373 0 12c0 2.127.555 4.122 1.524 5.855L0 24l6.266-1.647A11.94 11.94 0
                                 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 21.818a9.807 9.807 0 01-5.001-1.371l-.36-.214-3.72.977.994-3.624-.235-.373A9.786 9.786
                                 0 012.182 12C2.182 6.57 6.57 2.182 12 2.182S21.818 6.57 21.818 12 17.43 21.818 12 21.818z"/>
                    </svg>
                </a>
            </div>
        </div>
    </div>
</footer>
