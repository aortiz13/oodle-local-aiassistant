<?php
namespace local_aiassistant;

defined('MOODLE_INTERNAL') || die();

class nlp_processor {

    private $api_key;
    private $model;
    private $system_prompt;

    public function __construct() {
        $this->api_key = get_config('local_aiassistant', 'openai_api_key');
        $this->model = 'gpt-4o-mini';
        $this->system_prompt = get_config('local_aiassistant', 'system_prompt');
    }

    /**
     * Get a response from OpenAI.
     */
    public function get_response(string $question, object $context, array $chat_history = []) {
        if (empty($this->api_key)) {
            return 'Error: No se ha configurado la API key de OpenAI.';
        }

        // Replace placeholders in system prompt
        $systemprompt = str_replace('{USERNAME}', $context->username, $this->system_prompt);
        $systemprompt = str_replace('{COURSENAME}', $context->coursename, $systemprompt);

        // Add spatial context
        $spatial_context = "\n\nCONTEXTO ESPACIAL:\n";
        $spatial_context .= "- El usuario {$context->username} YA ESTÁ DENTRO de Moodle (no necesita iniciar sesión).\n";
        $spatial_context .= "- Está actualmente en: {$context->location}\n";
        $spatial_context .= "- URL base de Moodle: {$context->wwwroot}\n\n";

        $spatial_context .= "URLS IMPORTANTES (úsalas para dar enlaces directos clickeables):\n";
        foreach ($context->urls as $name => $url) {
            $spatial_context .= "- {$name}: {$url}\n";
        }

        $spatial_context .= "\nINSTRUCCIONES IMPORTANTES:\n";
        $spatial_context .= "1. Cuando des instrucciones, sé DIRECTO y CONTEXTUAL.\n";
        $spatial_context .= "2. NO digas 'Inicia sesión' - el usuario YA ESTÁ dentro de Moodle.\n";
        $spatial_context .= "3. SIEMPRE que menciones una página o acción, incluye el enlace como HTML clickeable.\n";
        $spatial_context .= "4. FORMATO DE ENLACES: Usa <a href=\"URL\">texto descriptivo</a> - NUNCA muestres la URL completa como texto.\n";
        $spatial_context .= "5. Usa descripciones visuales: 'En la esquina superior derecha', 'el icono de tu foto', etc.\n";
        $spatial_context .= "6. Ejemplo BUENO: 'Para cambiar tu foto, haz clic en tu nombre arriba a la derecha y ve a <a href=\"{$context->urls['editar_perfil']}\">Editar perfil</a>'\n";
        $spatial_context .= "7. Ejemplo MALO: 'Inicia sesión, ve a tu perfil: https://moodle.com/user/edit.php'\n";
        $spatial_context .= "8. NUNCA escribas URLs como texto plano. SIEMPRE usa el formato <a href=\"...\">texto bonito</a>\n";

        $full_prompt = $systemprompt . $spatial_context;

        $messages = [];
        $messages[] = ['role' => 'system', 'content' => $full_prompt];

        // TODO: Add chat history to messages array

        $messages[] = ['role' => 'user', 'content' => $question];

        $api_url = 'https://api.openai.com/v1/chat/completions';

        $data = [
            'model' => $this->model,
            'messages' => $messages,
        ];

        try {
            $ch = curl_init($api_url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $this->api_key,
            ]);

            $response = curl_exec($ch);
            curl_close($ch);

            $result = json_decode($response, true);

            if (isset($result['choices'][0]['message']['content'])) {
                return $result['choices'][0]['message']['content'];
            } else if (isset($result['error'])) {
                return "Error from API: " . $result['error']['message'];
            } else {
                return "Error: Could not parse AI response.";
            }

        } catch (\Exception $e) {
            return "Error connecting to AI: " . $e->getMessage();
        }
    }
}