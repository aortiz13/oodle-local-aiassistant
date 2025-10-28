<?php
namespace local_aiassistant\output;

defined('MOODLE_INTERNAL') || die();

use plugin_renderer_base;

/**
 * Clase 'renderable' para la interfaz del chat.
 * Su único propósito es decirle a Moodle qué plantilla usar.
 */
class chat_interface implements \renderable {
    
    /**
     * No necesitamos pasarle datos complejos por ahora,
     * ya que la plantilla solo usa 'strings' de idioma.
     */
    public $data;

    public function __construct() {
        $this->data = new \stdClass();
    }
}