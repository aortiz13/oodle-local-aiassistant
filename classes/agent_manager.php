<?php
namespace local_aiassistant;

defined('MOODLE_INTERNAL') || die();

class agent_manager {

    protected $db_handler;
    protected $nlp_processor;
    protected $userid;
    protected $courseid;

    public function __construct(int $userid, int $courseid) {
        $this->db_handler = new database_handler();
        $this->nlp_processor = new nlp_processor();
        $this->userid = $userid;
        $this->courseid = $courseid;
    }

    /**
     * Handle an incoming question from the user.
     */
    public function handle_question(string $question) {
        // 1. Try to find a simple answer in the local knowledge base
        $answer = $this->db_handler->find_in_knowledge_base($question);
        $source = 'knowledge_base';

        // 2. If not found, go to OpenAI
        if (!$answer) {
            $context = $this->db_handler->get_user_context($this->userid, $this->courseid);
            $answer = $this->nlp_processor->get_response($question, $context);
            $source = 'openai';
        }

        // 3. Log the interaction
        $log_id = $this->db_handler->log_chat($this->userid, $this->courseid, $question, $answer);

        // 4. Return the response
        return [
            'answer' => $answer,
            'log_id' => $log_id,
            'source' => $source,
        ];
    }
    
    /**
     * Handle feedback from the user.
     */
    public function handle_feedback(int $log_id, bool $was_helpful) {
        $helpful_int = $was_helpful ? 1 : 0;
        $this->db_handler->log_feedback($log_id, $helpful_int, $this->userid);
        
        return ['status' => 'success'];
    }
}