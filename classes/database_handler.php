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
        // Limpiar la pregunta
        $clean_question = strtolower(trim($question));
        
        // Buscar coincidencias
        $sql = "SELECT answer FROM {local_aiassistant_knowledge} 
                WHERE LOWER(?) LIKE CONCAT('%', LOWER(question_pattern), '%')";
        $record = $this->db->get_record_sql($sql, [$clean_question], IGNORE_MULTIPLE);
        
        return $record ? $record->answer : null;
    }

    /**
     * Get user and course context with spatial awareness.
     */
    public function get_user_context(int $userid, int $courseid, string $currenturl = '', string $pagetype = '') {
        global $CFG;

        $user = $this->db->get_record('user', ['id' => $userid], 'id, firstname, lastname');
        $course = $this->db->get_record('course', ['id' => $courseid], 'id, fullname');

        $context = new \stdClass();
        $context->username = fullname($user);
        $context->coursename = $course->fullname;
        $context->currenturl = $currenturl;
        $context->pagetype = $pagetype;
        $context->wwwroot = $CFG->wwwroot;

        // Build common Moodle URLs map
        $context->urls = [
            'perfil' => $CFG->wwwroot . '/user/profile.php',
            'editar_perfil' => $CFG->wwwroot . '/user/edit.php',
            'cambiar_foto' => $CFG->wwwroot . '/user/edit.php#id_moodle_picture',
            'cambiar_contraseña' => $CFG->wwwroot . '/login/change_password.php',
            'mis_cursos' => $CFG->wwwroot . '/my/courses.php',
            'dashboard' => $CFG->wwwroot . '/my/',
            'calificaciones' => $CFG->wwwroot . '/grade/report/user/index.php?id=' . $courseid,
            'participantes' => $CFG->wwwroot . '/user/index.php?id=' . $courseid,
            'curso_actual' => $CFG->wwwroot . '/course/view.php?id=' . $courseid,
        ];

        // Determine current location context
        if (strpos($pagetype, 'user-profile') !== false) {
            $context->location = 'página de perfil de usuario';
        } else if (strpos($pagetype, 'course-view') !== false) {
            $context->location = 'página principal del curso';
        } else if (strpos($pagetype, 'my-index') !== false) {
            $context->location = 'dashboard principal';
        } else if (strpos($pagetype, 'grade') !== false) {
            $context->location = 'página de calificaciones';
        } else {
            $context->location = 'Moodle';
        }

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