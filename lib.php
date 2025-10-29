<?php
defined('MOODLE_INTERNAL') || die();

/**
 * Injects chat interface into the page footer.
 */
/**
 * Hook to add CSS in the head section (before head is printed)
 */
function local_aiassistant_before_standard_html_head() {
    global $PAGE;
    
    if (!get_config('local_aiassistant', 'enable')) {
        return '';
    }
    
    // Add CSS directly in head
    $cssurl = new moodle_url('/local/aiassistant/styles.css');
    return '<link rel="stylesheet" href="' . $cssurl . '">';
}

/**
 * Injects chat interface into the page footer.
 */
function local_aiassistant_before_footer() {
    global $PAGE, $COURSE;

    // Check if plugin is enabled
    if (!get_config('local_aiassistant', 'enable')) {
        return;
    }

    // Get course ID - prioritize actual course pages, fallback to site
    $courseid = 1; // Default to site
    if (isset($COURSE->id) && $COURSE->id > 1) {
        // We're in a real course (not site home)
        $courseid = $COURSE->id;
    } else if ($PAGE->course && $PAGE->course->id > 1) {
        // Try getting from PAGE object
        $courseid = $PAGE->course->id;
    }

    // Get language strings
    $chat_title = get_string('chat_title', 'local_aiassistant');
    $welcome_message = get_string('welcome_message', 'local_aiassistant');
    $chat_typing = get_string('chat_typing', 'local_aiassistant');
    $chat_input_placeholder = get_string('chat_input_placeholder', 'local_aiassistant');
    $chat_send = get_string('chat_send', 'local_aiassistant');

    // Get current page context
    global $CFG;
    $current_url = $PAGE->url ? $PAGE->url->out(false) : '';
    $page_type = $PAGE->pagetype;

    // Initialize the JavaScript module with enhanced context
    $PAGE->requires->js_call_amd('local_aiassistant/chat', 'init', [
        [
            'courseid' => $courseid,
            'wwwroot' => $CFG->wwwroot,
            'currenturl' => $current_url,
            'pagetype' => $page_type
        ]
    ]);

    // Render HTML with language strings
    echo <<<HTML
<div class="local-aiassistant-container">
    <div id="ai-chat-window" class="ai-chat-window" hidden>
        <div class="ai-chat-header">
            <span>{$chat_title}</span>
            <button id="ai-chat-close" class="ai-chat-close" aria-label="Close chat">&times;</button>
        </div>
        <div id="ai-chat-messages" class="ai-chat-messages">
            <div class="ai-message ai-message-bot">
                {$welcome_message}
            </div>
        </div>
        <div id="ai-chat-typing" class="ai-typing" hidden>
            <span>{$chat_typing}</span>
        </div>
        <div id="ai-suggestions" class="ai-suggestions">
            <button class="ai-suggestion-chip" data-question="¿Dónde veo mis calificaciones?">📊 Calificaciones</button>
            <button class="ai-suggestion-chip" data-question="¿Cómo cambio mi foto de perfil?">📷 Cambiar foto de perfil</button>
            <button class="ai-suggestion-chip" data-question="¿Cómo encuentro los módulos del curso?">📚 Módulos del curso</button>
            <button class="ai-suggestion-chip" data-question="¿Cómo subo una tarea?">📝 Subir tarea</button>
        </div>
        <div class="ai-chat-input-area">
            <input type="text" id="ai-chat-input" placeholder="{$chat_input_placeholder}">
            <button id="ai-chat-send" class="ai-chat-send" aria-label="Send">{$chat_send}</button>
        </div>
    </div>
    <button id="ai-chat-button" class="ai-chat-button" aria-label="Open AI Assistant">
        💬
    </button>
</div>
HTML;
}

/**
 * Minimal pluginfile implementation (needed by Moodle, can expand later).
 */
function local_aiassistant_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options = []) {
    return false;
}