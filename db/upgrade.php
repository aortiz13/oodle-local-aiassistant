<?php
defined('MOODLE_INTERNAL') || die();

/**
 * Runs on installation to populate the knowledge base.
 */
function xmldb_local_aiassistant_install() {
    global $DB;

    $knowledge = [
        ['category' => 'grades', 'question_pattern' => '¿Dónde veo mis calificaciones?', 'answer' => 'Puedes ver tus calificaciones haciendo clic en "Calificaciones" en el menú de navegación del curso, usualmente a la izquierda.'],
        ['category' => 'grades', 'question_pattern' => '¿Cómo sé si mi tarea fue calificada?', 'answer' => 'Recibirás una notificación, o puedes ir a la sección "Calificaciones" para ver si hay una nota en la tarea correspondiente.'],
        ['category' => 'assignments', 'question_pattern' => '¿Cómo subo una tarea?', 'answer' => 'Ve a la tarea en el curso, haz clic en ella y busca el botón "Agregar entrega". Podrás arrastrar y soltar tus archivos allí.'],
        ['category' => 'assignments', 'question_pattern' => '¿Hasta cuándo puedo entregar?', 'answer' => 'La fecha límite de entrega se muestra en la descripción de cada tarea. Asegúrate de revisarla bien.'],
        ['category' => 'forums', 'question_pattern' => '¿Dónde está el foro?', 'answer' => 'Los foros del curso se encuentran en las secciones del tema principal. También puede haber un foro general en la sección "General".'],
        ['category' => 'forums', 'question_pattern' => '¿Cómo publico en el foro?', 'answer' => 'Entra al foro, haz clic en "Añadir un nuevo tema de debate", escribe tu mensaje y haz clic en "Enviar al foro".'],
        ['category' => 'messaging', 'question_pattern' => '¿Cómo envío un mensaje a un compañero?', 'answer' => 'Puedes usar el ícono de "Mensajes" (burbuja de chat) en la parte superior derecha, o ir a la sección "Participantes" del curso y seleccionar a quién quieres contactar.'],
        ['category' => 'messaging', 'question_pattern' => '¿Cómo envío un mensaje a mi profesor?', 'answer' => 'Ve a "Participantes", busca el rol de "Profesor" y haz clic en su nombre para enviarle un mensaje.'],
        ['category' => 'profile', 'question_pattern' => '¿Cómo cambio mi foto de perfil?', 'answer' => 'Haz clic en tu nombre en la esquina superior derecha, ve a "Perfil", luego "Editar perfil" y busca la sección "Imagen de usuario".'],
        ['category' => 'profile', 'question_pattern' => '¿Cómo actualizo mi correo?', 'answer' => 'Puedes editar tu información de contacto en "Perfil" > "Editar perfil".'],
        ['category' => 'navigation', 'question_pattern' => '¿Dónde están mis cursos?', 'answer' => 'Puedes encontrar todos tus cursos en tu "Área personal" o en el menú "Mis cursos".'],
        ['category' => 'navigation', 'question_pattern' => '¿Cómo vuelvo a la página principal?', 'answer' => 'Usualmente puedes hacer clic en el logo del sitio en la esquina superior izquierda para volver al inicio.'],
        ['category' => 'resources', 'question_pattern' => '¿Cómo descargo un archivo?', 'answer' => 'Simplemente haz clic en el nombre del archivo (PDF, Word, etc.) en la página del curso y se descargará automáticamente.'],
        ['category's' => 'resources', 'question_pattern' => 'No puedo abrir un video', 'answer' => 'Asegúrate de tener una buena conexión a internet y que tu navegador esté actualizado. Intenta recargar la página.'],
        ['category' => 'quizzes', 'question_pattern' => '¿Cómo funciona un cuestionario?', 'answer' => 'Haz clic en el cuestionario. Tendrás un tiempo límite para responder. Tus respuestas se guardan automáticamente.'],
        ['category' => 'quizzes', 'question_pattern' => '¿Puedo rehacer un cuestionario?', 'answer' => 'Depende de la configuración del profesor. Algunos cuestionarios permiten múltiples intentos, otros solo uno. Revisa la descripción del cuestionario.'],
        ['category' => 'general', 'question_pattern' => '¿Quién es mi profesor?', 'answer' => 'Puedes ver al profesor y a tus compañeros en la sección "Participantes" del curso.'],
        ['category' => 'general', 'question_pattern' => '¿Dónde está el calendario?', 'answer' => 'El calendario, que muestra las fechas de entrega, suele estar en tu "Área personal" o como un bloque en el lado derecho del curso.'],
        ['category' => 'completion', 'question_pattern' => '¿Cómo marco una actividad como completada?', 'answer' => 'Algunas actividades se marcan solas al completarlas (ej. subir una tarea). Otras tienen una casilla de verificación manual que debes tildar.'],
        ['category' => 'completion', 'question_pattern' => '¿Qué es la barra de progreso?', 'answer' => 'La barra de progreso te muestra visualmente cuántas actividades del curso has completado.'],
    ];

    foreach ($knowledge as $entry) {
        $record = new stdClass();
        $record->category = $entry['category'];
        $record->question_pattern = $entry['question_pattern'];
        $record->answer = $entry['answer'];
        $DB->insert_record('local_aiassistant_knowledge', $record, false);
    }

    return true;
}