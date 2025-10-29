<?php
defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/externallib.php');

class local_aiassistant_external extends external_api {

    /**
     * Define parameters for the query_agent function.
     */
    public static function query_agent_parameters() {
        return new external_function_parameters([
            'courseid' => new external_value(PARAM_INT, 'The current course ID'),
            'question' => new external_value(PARAM_TEXT, 'The user question'),
        ]);
    }

    /**
     * The actual function to process the query.
     */
    public static function query_agent($courseid, $question) {
        global $USER, $CFG;
        
        try {
            require_once($CFG->dirroot . '/local/aiassistant/classes/agent_manager.php');
            
            // Validate parameters
            $params = self::validate_parameters(self::query_agent_parameters(), [
                'courseid' => $courseid,
                'question' => $question
            ]);
            
            // Validate context
            $context = context_course::instance($params['courseid']);
            self::validate_context($context);
            
            // Require login
            require_login($params['courseid']);

            $manager = new \local_aiassistant\agent_manager($USER->id, $params['courseid']);
            $response = $manager->handle_question($params['question']);

            return $response;
            
        } catch (Exception $e) {
            // Return error as response instead of throwing
            return [
                'answer' => 'Error: ' . $e->getMessage(),
                'log_id' => 0,
                'source' => 'error'
            ];
        }
    }

    /**
     * Define return value for query_agent function.
     */
    public static function query_agent_returns() {
        return new external_single_structure([
            'answer' => new external_value(PARAM_RAW, 'The answer from the AI'),
            'log_id' => new external_value(PARAM_INT, 'The chat log ID'),
            'source' => new external_value(PARAM_ALPHA, 'The source of the answer (kb or openai)'),
        ]);
    }

    // --- Feedback Function ---

    public static function log_feedback_parameters() {
        return new external_function_parameters([
            'logid' => new external_value(PARAM_INT, 'The chat log ID'),
            'helpful' => new external_value(PARAM_BOOL, 'Was the response helpful?'),
        ]);
    }

    public static function log_feedback($logid, $helpful) {
        global $USER, $DB, $CFG;
        
        require_once($CFG->dirroot . '/local/aiassistant/classes/agent_manager.php');
        
        // Validate parameters
        $params = self::validate_parameters(self::log_feedback_parameters(), [
            'logid' => $logid,
            'helpful' => $helpful
        ]);
        
        // Basic validation - get the log record
        if (!$log = $DB->get_record('local_aiassistant_chat_logs', ['id' => $params['logid'], 'userid' => $USER->id])) {
            throw new moodle_exception('invalidlogid', 'local_aiassistant');
        }
        
        // Validate context
        $context = context_course::instance($log->courseid);
        self::validate_context($context);

        $manager = new \local_aiassistant\agent_manager($USER->id, $log->courseid);
        return $manager->handle_feedback($params['logid'], $params['helpful']);
    }

    public static function log_feedback_returns() {
        return new external_single_structure([
            'status' => new external_value(PARAM_ALPHA, 'Status of the operation')
        ]);
    }
}