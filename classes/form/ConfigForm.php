<?php

namespace block_course_chatbot\form;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');

class ConfigForm extends \moodleform {
    protected function definition() {
        $mform = $this->_form;

        $mform->addElement('header', 'settingsheader', get_string('settings', 'block_course_chatbot'));

        // Campo para subir el archivo JSON
        $mform->addElement('filepicker', 'chatbot_config_json_file', get_string('uploadjson', 'block_course_chatbot'), null,
            ['accepted_types' => ['web_json', 'text_plain']]); // Permite JSON y texto plano
        $mform->addHelpButton('chatbot_config_json_file', 'uploadjson_help', 'block_course_chatbot');
        $mform->setType('chatbot_config_json_file', PARAM_FILE);

        // Campo para pegar el JSON directamente (alternativa al upload)
        $mform->addElement('textarea', 'chatbot_config_json_text', get_string('pastejson', 'block_course_chatbot'), 'wrap="virtual" rows="10" cols="80"');
        $mform->addHelpButton('chatbot_config_json_text', 'pastejson_help', 'block_course_chatbot');
        $mform->setType('chatbot_config_json_text', PARAM_RAW); // RAW para permitir JSON no validado todavía

        // Información útil para el profesor
        $mform->addElement('html', html_writer::tag('p', get_string('jsoninfo', 'block_course_chatbot')));


        // Opcional: Nombre del chatbot (para mostrar en el bloque)
        $mform->addElement('text', 'chatbot_display_name', get_string('displayname', 'block_course_chatbot'), ['size' => '50']);
        $mform->setType('chatbot_display_name', PARAM_TEXT);
        $mform->setDefault('chatbot_display_name', get_string('defaultchatbotname', 'block_course_chatbot'));


        $this->add_action_buttons();
    }

    /**
     * Validación del formulario.
     * @param array $data Los datos del formulario.
     * @param array $files Los archivos subidos.
     * @return array Errores.
     */
    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        // Validar que se ha subido un archivo O pegado texto JSON
        if (empty($files['chatbot_config_json_file']) && empty($data['chatbot_config_json_text'])) {
            $errors['chatbot_config_json_file'] = get_string('jsonrequired', 'block_course_chatbot');
            $errors['chatbot_config_json_text'] = get_string('jsonrequired', 'block_course_chatbot');
        }

        // Si se subió un archivo, validar que es JSON
        if (!empty($files['chatbot_config_json_file'])) {
            $filepath = $files['chatbot_config_json_file']['tmp_name'];
            $content = file_get_contents($filepath);
            json_decode($content);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $errors['chatbot_config_json_file'] = get_string('invalidjsonfile', 'block_course_chatbot', json_last_error_msg());
            }
        } elseif (!empty($data['chatbot_config_json_text'])) {
            // Si se pegó texto, validar que es JSON
            json_decode($data['chatbot_config_json_text']);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $errors['chatbot_config_json_text'] = get_string('invalidjsontext', 'block_course_chatbot', json_last_error_msg());
            }
        }

        return $errors;
    }

    /**
     * Procesa los datos del formulario después de una validación exitosa.
     * @param array $data Los datos del formulario.
     * @return mixed El JSON procesado o false si hay un error.
     */
    public function process_data($data) {
        // Si se subió un archivo, leerlo
        if (!empty($data['chatbot_config_json_file'])) {
            return file_get_contents($data['chatbot_config_json_file']);
        } elseif (!empty($data['chatbot_config_json_text'])) {
            // Si se pegó texto, usarlo
            return $data['chatbot_config_json_text'];
        }
        return false; // Esto no debería pasar si la validación funciona
    }

    /**
     * Retorna los datos por defecto para el formulario.
     * @param stdClass $blockinstance The block instance.
     * @return array
     */
    public function get_instance_config_data($blockinstance) {
        $data = [];
        if (isset($blockinstance->config->chatbot_config_json)) {
            $data['chatbot_config_json_text'] = $blockinstance->config->chatbot_config_json;
        }
        if (isset($blockinstance->config->chatbot_display_name)) {
            $data['chatbot_display_name'] = $blockinstance->config->chatbot_display_name;
        }
        return $data;
    }
}