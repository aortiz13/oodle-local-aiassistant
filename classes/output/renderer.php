<?php
namespace local_aiassistant\output;

defined('MOODLE_INTERNAL') || die();

use plugin_renderer_base;

/**
 * El renderizador principal del plugin.
 */
class renderer extends \core_plugin_renderer {

    /**
     * Renderiza la interfaz de chat.
     *
     * @param chat_interface $interface La clase 'renderable'
     * @return string HTML renderizado
     */
    public function render_chat_interface(\local_aiassistant\output\chat_interface $interface) {
        // Le dice a Moodle que use 'templates/chat_interface.mustache'
        // y le pase los datos (aunque estén vacíos).
        return $this->render_from_template('local_aiassistant/chat_interface', $interface->data);
    }
}