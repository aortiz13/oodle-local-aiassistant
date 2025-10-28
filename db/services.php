<?php
defined('MOODLE_INTERNAL') || die();

$functions = [
    'local_aiassistant_query' => [
        'classname' => 'local_aiassistant_external',
        'methodname' => 'query_agent',
        'description' => 'Send a question to the AI assistant',
        'type' => 'write',
        'ajax' => true,
    ],
    'local_aiassistant_feedback' => [
        'classname' => 'local_aiassistant_external',
        'methodname' => 'log_feedback',
        'description' => 'Log feedback for a response',
        'type' => 'write',
        'ajax' => true,
    ]
];