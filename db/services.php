<?php
defined('MOODLE_INTERNAL') || die();

$functions = [
    'local_aiassistant_query' => [
        'classname'   => 'local_aiassistant_external',
        'methodname'  => 'query_agent',
        'description' => 'Send a question to the AI assistant',
        'type'        => 'write',
        'ajax'        => true,
        'classpath'   => 'local/aiassistant/db/externallib.php', // <-- AÑADIR ESTO
    ],
    'local_aiassistant_feedback' => [
        'classname'   => 'local_aiassistant_external',
        'methodname'  => 'log_feedback',
        'description' => 'Log feedback for a response',
        'type'        => 'write',
        'ajax'        => true,
        'classpath'   => 'local/aiassistant/db/externallib.php', // <-- Y AÑADIR ESTO
    ]
];