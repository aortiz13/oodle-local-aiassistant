<?php
defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'Asistente con IA';
$string['config_title'] = 'Configuración del Asistente con IA';

// Settings
$string['config_enable'] = 'Activar Asistente con IA';
$string['config_enable_desc'] = 'Activar globalmente el asistente de chat flotante';
$string['config_apikey'] = 'Clave API de OpenAI';
$string['config_apikey_desc'] = 'Ingresa tu clave API de OpenAI';
$string['config_system_prompt'] = 'Prompt del Sistema';
$string['config_system_prompt_desc'] = 'El contexto inicial dado a la IA. Usa {USERNAME} y {COURSENAME} como marcadores de posición.';

// Chat Interface
$string['chat_title'] = 'Asistente con IA';
$string['chat_input_placeholder'] = 'Haz una pregunta sobre Moodle...';
$string['chat_send'] = 'Enviar';
$string['chat_typing'] = 'Escribiendo...';
$string['feedback_helpful'] = 'Útil';
$string['feedback_not_helpful'] = 'No Útil';
$string['welcome_message'] = '¡Hola! Soy el asistente con IA de Grupo TU. ¿Cómo puedo ayudarte hoy?';
