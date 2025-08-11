<?php

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/classes/ChatbotLogic.php');
require_once(__DIR__ . '/classes/form/ConfigForm.php');

class block_course_chatbot extends block_base {
    public function init() {
        $this->title = get_string('pluginname', 'block_course_chatbot');
    }

    public function get_content() {
        if ($this->content !== null) {
            return $this->content;
        }

        $this->content = new stdClass();
        $this->content->text = '';
        $this->content->footer = '';

        // Obtener la configuración JSON guardada para esta instancia del bloque
        // La configuración se guarda en $this->config->chatbot_config_json
        $chatbot_config_json = $this->config->chatbot_config_json ?? null;

        // Si no hay configuración JSON, mostrar un mensaje al profesor.
        if (empty($chatbot_config_json)) {
            $this->content->text = html_writer::tag('p', get_string('noconfigset', 'block_course_chatbot'));
            return $this->content;
        }

        // Crear una instancia de la lógica del chatbot con la configuración JSON
        // Esto es un placeholder; la lógica real se manejará vía AJAX en ChatbotAPI.php
        // Aquí solo necesitamos pasar la configuración al template JavaScript.
        $config_data = json_decode($chatbot_config_json, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->content->text = html_writer::tag('p', get_string('invalidconfig', 'block_course_chatbot'));
            return $this->content;
        }

        // Preparar datos para la plantilla Mustache
        $data = [
            'blockid' => $this->instance->id, // ID de la instancia del bloque
            'courseid' => $this->page->course->id, // ID del curso
            'sectionid' => $this->instance->parentcontextid, // Esto necesita ser ajustado para obtener el ID de la sección correcta
            'chatbot_initial_message' => get_string('initialmessage', 'block_course_chatbot'),
            'config_json' => $chatbot_config_json // Pasa la configuración JSON al JavaScript
        ];
        
        // Renderizar el bloque usando la plantilla Mustache
        $renderer = $this->page->get_renderer('block_course_chatbot');
        $this->content->text = $renderer->render_from_template('block_course_chatbot/chatbot_block', $data);

        return $this->content;
    }

    /**
     * Define si el bloque tiene una página de configuración o no.
     * @return bool
     */
    public function has_config() {
        return true;
    }

    /**
     * Define si el bloque puede ser añadido múltiples veces a la misma página.
     * @return bool
     */
    public function instance_allow_multiple() {
        return true;
    }

    /**
     * Especifica dónde se puede añadir el bloque (por ejemplo, a cursos de formato de temas).
     * @return array
     */
    public function applicable_formats() {
        return [
            'course-view-topics' => true,
            'course-view-weeks' => true,
            'course-view-grid' => true, // Si usas el formato de curso de cuadrícula
            // Puedes añadir otros formatos si es necesario
        ];
    }

    /**
     * Retorna el formulario de configuración para esta instancia del bloque.
     * @param int $contextid Context ID.
     * @return block_course_chatbot_config_form
     */
    public function get_form() {
        return new block_course_chatbot\form\ConfigForm();
    }

    /**
     * Se llama cuando la configuración del bloque se actualiza.
     * Guarda el JSON de configuración directamente en $this->config
     * @param array $data Configuration data.
     */
    public function instance_config_save($data, $dontdelete = false) {
        $data = file_get_contents($data['chatbot_config_json_file']['tmp_name']);
        $this->config->chatbot_config_json = $data;
        return true;
    }

    // Asegúrate de que los archivos JavaScript se carguen
    public function get_javascript_footer() {
        $this->page->requires->js_call_amd('block_course_chatbot/chatbot', 'init', [$this->instance->id]);
    }
}