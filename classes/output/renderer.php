<?php
defined('MOODLE_INTERNAL') || die();

/**
 * Injects chat interface into the page footer.
 */
function local_aiassistant_before_footer() {
    global $PAGE, $COURSE;
    
    // Check if plugin is enabled
    if (!get_config('local_aiassistant', 'enable')) {
        return;
    }
    
    // Don't show on login page or if not logged in
    if (!isloggedin() || isguestuser()) {
        return;
    }
    
    // Initialize the JavaScript module
    $PAGE->requires->js_call_amd('local_aiassistant/chat', 'init', [
        ['courseid' => $COURSE->id]
    ]);
    
    // Render the chat interface using the output API
    $output = $PAGE->get_renderer('local_aiassistant');
    $interface = new \local_aiassistant\output\chat_interface();
    echo $output->render($interface);
}

/**
 * Minimal pluginfile implementation (needed by Moodle, can expand later).
 */
function local_aiassistant_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options = []) {
    // For now, just deny all requests to files
    return false;
}