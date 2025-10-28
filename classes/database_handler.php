<?php
namespace local_aiassistant;

defined('MOODLE_INTERNAL') || die();

class database_handler {

    protected $db;

    public function __construct() {
        global $DB;
        $this->db = $DB;
    }

    /**
     * Find a matching answer in the local knowledge base.
     * (Esta es una búsqueda simple, se puede mejorar con IA o full-text)
     */
    public function find_in_knowledge_base(string $question) {
        $sql = "SELECT answer FROM {local_aiassistant_knowledge} WHERE ? LIKE CONCAT('%', question_pattern, '%')";
        $record = $this->db->get_record_sql($sql, [clean_text($question)], IGNORE_MULTIPLE);
        
        return $record ? $record->answer : null;
    }

    /**
     * Get user and course context.
     */
    public function get_user_context(int $userid, int $courseid) {
        $user = $this->db->get_record('user', ['id' => $userid], 'id, firstname, lastname');
        $course = $this->db->get_record('course', ['id' => $courseid], 'id, fullname');

        $context = new \stdClass();
        $context->username = fullname($user);
        $context->coursename = $course->fullname;
        
        return $context;
    }

    /**
     * Log a chat interaction.
     */
    public function log_chat(int $userid, int $courseid, string $question, string $answer) {
        $log = new \stdClass();
        $log->userid = $userid;
        $log->courseid = $courseid;
        $log->question = $question;
        $log->answer = $answer;
        $log->timestamp = time();
        
        return $this->db->insert_record('local_aiassistant_chat_logs', $log);
    }

    /**
     * Update chat log with user feedback.
     */
    public function log_feedback(int $logid, int $helpful, int $userid) {
        // Check if user owns this log entry
        if (!$this->db->record_exists('local_aiassistant_chat_logs', ['id' => $logid, 'userid' => $userid])) {
            throw new \moodle_exception('invalidlogid', 'local_aiassistant');
        }

        $record = new \stdClass();
        $record->id = $logid;
        $record->helpful = $helpful; // 1 or 0
        
        $this->db->update_record('local_aiassistant_chat_logs', $record);
    }
}