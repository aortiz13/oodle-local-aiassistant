<?php
defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/local/aiassistant/classes/agent_manager.php');

class local_aiassistant_external extends \external_api {

    /**
     * Define parameters for the query_agent function.
     */
    public static function query_agent_parameters() {
        return new \external_function_parameters([
            'courseid' => new \external_value(PARAM_INT, 'The current course ID'),
            'question' => new \external_value(PARAM_TEXT, 'The user question'),
        ]);
    }

    /**
     * The actual function to process the query.
     */
    public static function query_agent($courseid, $question) {
        global $USER;
        self::validate_context(context_course::instance($courseid));

        $manager = new \local_aiassistant\agent_manager($USER->id, $courseid);
        $response = $manager->handle_question($question);

        return $response;
    }

    /**
     * Define return value for query_agent function.
     */
    public static function query_agent_returns() {
        return new \external_single_structure([
            'answer' => new \external_value(PARAM_RAW, 'The answer from the AI'),
            'log_id' => new \external_value(PARAM_INT, 'The chat log ID'),
            'source' => new \external_value(PARAM_ALPHA, 'The source of the answer (kb or openai)'),
        ]);
    }

    // --- Feedback Function ---

    public static function log_feedback_parameters() {
        return new \external_function_parameters([
            'logid' => new \external_value(PARAM_INT, 'The chat log ID'),
            'helpful' => new \external_value(PARAM_BOOL, 'Was the response helpful?'),
        ]);
    }

    public static function log_feedback($logid, $helpful) {
        global $USER, $DB;
        
        // Basic validation
        if (!$log = $DB->get_record('local_aiassistant_chat_logs', ['id' => $logid, 'userid' => $USER->id])) {
            throw new \moodle_exception('invalidlogid', 'local_aiassistant');
        }

        $manager = new \local_aiassistant\agent_manager($USER->id, $log->courseid);
        return $manager->handle_feedback($logid, $helpful);
    }

    public static function log_feedback_returns() {
        return new \external_single_structure([
            'status' => new \external_value(PARAM_ALPHA, 'Status of the operation')
        ]);
    }
}