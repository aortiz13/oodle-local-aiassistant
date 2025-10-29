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
    
    // Get course ID safely
    $courseid = isset($COURSE->id) ? $COURSE->id : 1;
    
    // Initialize the JavaScript module
    $PAGE->requires->js_call_amd('local_aiassistant/chat', 'init', [
        ['courseid' => $courseid]
    ]);
    
    // Render HTML directly with inline styles as fallback
    echo <<<HTML
<div class="local-aiassistant-container">
    <div id="ai-chat-window" class="ai-chat-window" hidden>
        <div class="ai-chat-header">
            <span>AI Assistant</span>
            <button id="ai-chat-close" class="ai-chat-close" aria-label="Close chat">&times;</button>
        </div>
        <div id="ai-chat-messages" class="ai-chat-messages">
            <div class="ai-message ai-message-bot">
                Hello! I am your AI assistant. How can I help you with Moodle today?
            </div>
        </div>
        <div id="ai-chat-typing" class="ai-typing" hidden>
            <span>Typing...</span>
        </div>
        <div class="ai-chat-input-area">
            <input type="text" id="ai-chat-input" placeholder="Ask a question about Moodle...">
            <button id="ai-chat-send" class="ai-chat-send" aria-label="Send">Send</button>
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