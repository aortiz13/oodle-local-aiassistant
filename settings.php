<?php
defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {
    $ADMIN->add('localplugins', new admin_category('local_aiassistant_settings',
        get_string('pluginname', 'local_aiassistant')));

    $settingspage = new admin_settingpage('local_aiassistant_config',
        get_string('config_title', 'local_aiassistant'));

    $ADMIN->add('local_aiassistant_settings', $settingspage);

    // 1. Enable/Disable
    $settingspage->add(new admin_setting_configcheckbox(
        'local_aiassistant/enable',
        get_string('config_enable', 'local_aiassistant'),
        get_string('config_enable_desc', 'local_aiassistant'),
        0
    ));

    // 2. OpenAI API Key
    $settingspage->add(new admin_setting_configpasswordunmask(
        'local_aiassistant/openai_api_key',
        get_string('config_apikey', 'local_aiassistant'),
        get_string('config_apikey_desc', 'local_aiassistant'),
        ''
    ));

    // 3. System Prompt
    $settingspage->add(new admin_setting_configtextarea(
        'local_aiassistant/system_prompt',
        get_string('config_system_prompt', 'local_aiassistant'),
        get_string('config_system_prompt_desc', 'local_aiassistant'),
        'Eres un asistente virtual de Moodle. Tu nombre es "MoodleBot". Ayudas a los estudiantes y profesores a navegar y usar la plataforma. Sé breve y directo. El usuario actual se llama {USERNAME} y está en el curso {COURSENAME}.'
    ));
    
    // NOTA: "Activar/desactivar por curso" (solicitado) es mucho más complejo.
    // Requiere un plugin de tipo 'block' o 'courseformat' para añadir esa configuración
    // a nivel de curso. Para un plugin 'local', esta configuración global es la estándar.
}