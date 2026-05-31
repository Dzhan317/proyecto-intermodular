<?php
declare(strict_types=1);

/*
 * Gestiona el soporte del usuario cliente.
 *
 * Rutas:
 *   index()       → GET  /support          — listado de conversaciones
 *   create()      → POST /support          — crear nueva conversación
 *   show()        → GET  /support/:id      — ver conversación y mensajes
 *   sendMessage() → POST /support/:id/message — enviar mensaje
 */

require_once APP_PATH . '/Models/SupportModel.php';

class SupportController extends Controller
{
    private SupportModel $supportModel;

    public function __construct()
    {
        $this->supportModel = new SupportModel();
    }

    // GET /support — listado de conversaciones del usuario
    public function index(array $params): void
    {
        $this->requireAuth();

        // El admin gestiona el soporte desde el panel admin
        if (($_SESSION['user_role'] ?? '') === 'admin') {
            $this->redirect(APP_URL . '/admin/support');
        }

        $conversations = $this->supportModel->getByUser((int) $_SESSION['user_id']);

        $this->view('support.index', [
            'pageTitle'     => 'Soporte | PrimeLux SmartShop',
            'conversations' => $conversations,
            'activeTab'     => 'support',
            'csrfToken'     => $this->csrfToken(),
        ]);
    }

    // POST /support — crear nueva conversación
    public function create(array $params): void
    {
        $this->requireAuth();
        $this->validateCsrf();

        $subject = trim($_POST['subject'] ?? '');

        if (empty($subject)) {
            $_SESSION['support_error'] = 'El asunto no puede estar vacío.';
            $this->redirect(APP_URL . '/support');
        }

        if (mb_strlen($subject) > 255) {
            $_SESSION['support_error'] = 'El asunto no puede superar los 255 caracteres.';
            $this->redirect(APP_URL . '/support');
        }

        $conversationId = $this->supportModel->create(
            (int) $_SESSION['user_id'],
            $subject
        );

        // Primer mensaje si el usuario escribió uno al crear la conversación
        $message = trim($_POST['message'] ?? '');
        if (!empty($message)) {
            $this->supportModel->addMessage(
                $conversationId,
                (int) $_SESSION['user_id'],
                $message
            );
        }

        $this->redirect(APP_URL . '/support/' . $conversationId);
    }

    // GET /support/:id — ver conversación
    public function show(array $params): void
    {
        $this->requireAuth();

        $conversationId = (int) ($params['id'] ?? 0);
        $conversation   = $this->supportModel->findById($conversationId);

        // Seguridad: debe existir y pertenecer al usuario autenticado
        if (!$conversation || (int) $conversation['user_id'] !== (int) $_SESSION['user_id']) {
            $this->redirect(APP_URL . '/support');
        }

        // Marca como leídos los mensajes del admin al abrir la conversación
        $this->supportModel->markAsRead($conversationId, (int) $_SESSION['user_id']);

        $this->view('support.show', [
            'pageTitle'    => 'Conversación #' . $conversationId . ' | PrimeLux SmartShop',
            'conversation' => $conversation,
            'activeTab'    => 'support',
            'csrfToken'    => $this->csrfToken(),
        ]);
    }

    // GET /support/:id/messages?since=:lastId — polling de mensajes nuevos (JSON)
    public function getMessages(array $params): void
    {
        $this->requireAuth();

        $conversationId = (int) ($params['id'] ?? 0);
        $conversation   = $this->supportModel->findById($conversationId);

        if (!$conversation || (int) $conversation['user_id'] !== (int) $_SESSION['user_id']) {
            http_response_code(403);
            echo json_encode(['error' => 'Acceso denegado']);
            exit;
        }

        // Marca como leídos los mensajes del admin al abrir/hacer polling
        $this->supportModel->markAsRead($conversationId, (int) $_SESSION['user_id']);

        $lastId   = (int) ($_GET['since'] ?? 0);
        $messages = $this->supportModel->getMessagesSince($conversationId, $lastId);
        $userId   = (int) $_SESSION['user_id'];

        $result = array_map(function ($msg) use ($userId) {
            return [
                'id'          => (int) $msg['id'],
                'message'     => htmlspecialchars($msg['message'], ENT_QUOTES),
                'sender_name' => htmlspecialchars($msg['sender_name'], ENT_QUOTES),
                'sender_role' => $msg['sender_role'],
                'is_own'      => ((int) $msg['user_id'] === $userId),
                'time'        => date('H:i', strtotime($msg['created_at'])),
            ];
        }, $messages);

        header('Content-Type: application/json');
        echo json_encode(['messages' => $result]);
        exit;
    }

    // GET /support/unread — cuenta mensajes no leídos (JSON para badge)
    public function unreadCount(array $params): void
    {
        if (!$this->isLoggedIn()) {
            header('Content-Type: application/json');
            echo json_encode(['count' => 0]);
            exit;
        }

        $count = $this->supportModel->getUnreadCount((int) $_SESSION['user_id']);

        header('Content-Type: application/json');
        echo json_encode(['count' => $count]);
        exit;
    }

    // POST /support/:id/message — enviar mensaje
    public function sendMessage(array $params): void
    {
        $this->requireAuth();
        $this->validateCsrf();

        $conversationId = (int) ($params['id'] ?? 0);
        $conversation   = $this->supportModel->findById($conversationId);

        // Seguridad: debe existir y pertenecer al usuario autenticado
        if (!$conversation || (int) $conversation['user_id'] !== (int) $_SESSION['user_id']) {
            $this->redirect(APP_URL . '/support');
        }

        // No se puede enviar mensaje en conversación cerrada
        if ($conversation['status'] === 'closed') {
            $_SESSION['support_error'] = 'Esta conversación está cerrada.';
            $this->redirect(APP_URL . '/support/' . $conversationId);
        }

        $message = trim($_POST['message'] ?? '');

        if (empty($message)) {
            $_SESSION['support_error'] = 'El mensaje no puede estar vacío.';
            $this->redirect(APP_URL . '/support/' . $conversationId);
        }

        $this->supportModel->addMessage(
            $conversationId,
            (int) $_SESSION['user_id'],
            $message
        );

        // Si es una petición AJAX devuelve JSON con el mensaje recién creado
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
            $userId   = (int) $_SESSION['user_id'];
            $messages = $this->supportModel->getMessagesSince($conversationId, 0);
            $last     = end($messages);
            header('Content-Type: application/json');
            echo json_encode([
                'message' => [
                    'id'          => (int) $last['id'],
                    'message'     => htmlspecialchars($last['message'], ENT_QUOTES),
                    'sender_name' => htmlspecialchars($last['sender_name'], ENT_QUOTES),
                    'sender_role' => $last['sender_role'],
                    'is_own'      => true,
                    'time'        => date('H:i', strtotime($last['created_at'])),
                ],
            ]);
            exit;
        }

        $this->redirect(APP_URL . '/support/' . $conversationId);
    }
}
