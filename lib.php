<?php
defined('MOODLE_INTERNAL') || die();

/**
 * Example: injects chat placeholder into footer.
 */
function local_aiassistant_before_footer() {
    global $PAGE;

    $renderer = $PAGE->get_renderer('local_aiassistant');
    $interface = new \local_aiassistant\output\chat_interface();
    echo $renderer->render_chat_interface($interface);
}

/**
 * Minimal pluginfile implementation (needed by Moodle, can expand later).
 */
function local_aiassistant_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options = []) {
    // For now, just deny all requests to files
    return false;
}
