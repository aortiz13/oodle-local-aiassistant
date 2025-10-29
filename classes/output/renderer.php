<?php
namespace local_aiassistant\output;

defined('MOODLE_INTERNAL') || die();

/**
 * El renderizador principal del plugin.
 */
class renderer extends \plugin_renderer_base {

    /**
     * Renderiza la interfaz de chat.
     *
     * @param chat_interface $interface La clase 'renderable'
     * @return string HTML renderizado
     */
    public function render_chat_interface(chat_interface $interface) {
        return $this->render_from_template('local_aiassistant/chat_interface', $interface->data);
    }
}