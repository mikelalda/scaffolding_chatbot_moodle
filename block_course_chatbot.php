<?php

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

        $display_name = $this->config->chatbot_display_name ?? get_string('defaultchatbotname', 'block_course_chatbot');

        // Obtener el sectionid actual (puedes ajustar esto según tu lógica)
        $sectionid = optional_param('section', 0, PARAM_INT);
        $chatbot_config_json = $this->config->chatbot_config_json_by_section[$sectionid] ?? null;

        if (empty($chatbot_config_json)) {
            $this->content->text = html_writer::tag('p', get_string('noconfigset', 'block_course_chatbot'));
            return $this->content;
        }

        json_decode($chatbot_config_json);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->content->text = html_writer::tag('p', get_string('invalidconfig', 'block_course_chatbot'));
            return $this->content;
        }

        $data = [
            'blockid' => $this->instance->id,
            'courseid' => $this->page->course->id,
            'displayname' => $display_name,
            'chatbot_initial_message' => get_string('initialmessage', 'block_course_chatbot'),
            'config_json' => $chatbot_config_json
        ];

        $renderer = $this->page->get_renderer('block_course_chatbot');
        $this->content->text = $renderer->render_from_template('block_course_chatbot/chatbot_block', $data);

        return $this->content;
    }

    /**
     * Le dice a Moodle que este bloque tiene un formulario de configuración.
     * Moodle buscará automáticamente un archivo 'edit_form.php'.
     * @return bool
     */
    public function has_config() {
        return true;
    }

    /**
     * Permite que el bloque sea añadido múltiples veces a la misma página del curso.
     * @return bool
     */
    public function instance_allow_multiple() {
        return true;
    }
    
    /**
     * Carga el JavaScript necesario para el bloque, solo si está configurado.
     */
    public function get_page_params() {
        // Carga el JavaScript solo si el bloque está configurado.
        if (!empty($this->config->chatbot_config_json)) {
            $this->page->requires->js_call_amd('block_course_chatbot/chatbot', 'init', [$this->instance->id]);
        }
    }

    public function instance_config_save($data, $dontdelete = false) {
        $json = '';
        $sectionid = $data->config_chatbot_sectionid ?? 0;

        $fs = get_file_storage();
        $context = $this->context;
        $files = $fs->get_area_files(
            $context->id,
            'block_course_chatbot',
            'config_chatbot_config_json_file',
            0,
            'itemid, filepath, filename',
            false
        );
        foreach ($files as $file) {
            if ($file->get_filename() !== '.') {
                $json = $file->get_content();
                break;
            }
        }

        if ($this->config === null) {
            $this->config = new stdClass();
        }
        if (!isset($this->config->chatbot_config_json_by_section)) {
            $this->config->chatbot_config_json_by_section = [];
        }
        $this->config->chatbot_config_json_by_section[$sectionid] = $json;
        $this->config->chatbot_display_name = $data->config_chatbot_display_name ?? get_string('defaultchatbotname', 'block_course_chatbot');
        return true;
    }
}