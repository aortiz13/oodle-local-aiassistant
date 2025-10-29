<?php
namespace local_aiassistant\output;

defined('MOODLE_INTERNAL') || die();

use renderable;
use templatable;
use renderer_base;
use stdClass;

/**
 * Clase 'renderable' para la interfaz del chat.
 */
class chat_interface implements renderable, templatable {
    
    public $data;

    public function __construct() {
        $this->data = new stdClass();
    }
    
    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param renderer_base $output
     * @return stdClass
     */
    public function export_for_template(renderer_base $output) {
        return $this->data;
    }
}