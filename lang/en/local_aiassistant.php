<?php
defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'AI Assistant';
$string['config_title'] = 'AI Assistant Settings';

// Settings
$string['config_enable'] = 'Enable AI Assistant';
$string['config_enable_desc'] = 'Globally enable the floating chat assistant';
$string['config_apikey'] = 'OpenAI API Key';
$string['config_apikey_desc'] = 'Enter your API key from OpenAI';
$string['config_model'] = 'OpenAI Model';
$string['config_model_desc'] = 'Select the GPT model to use';
$string['config_system_prompt'] = 'System Prompt';
$string['config_system_prompt_desc'] = 'The initial context given to the AI. Use {USERNAME} and {COURSENAME} as placeholders.';

// Chat Interface
$string['chat_title'] = 'AI Assistant';
$string['chat_input_placeholder'] = 'Ask a question about Moodle...';
$string['chat_send'] = 'Send';
$string['chat_typing'] = 'Typing...';
$string['feedback_helpful'] = 'Helpful';
$string['feedback_not_helpful'] = 'Not Helpful';
$string['welcome_message'] = 'Hello! I am your AI assistant. How can I help you with Moodle today?';