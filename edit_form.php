<?php

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');

class block_course_chatbot_edit_form extends block_edit_form {

    protected function specific_definition($mform) {
        $mform->addElement('header', 'configheader', get_string('settings', 'block_course_chatbot'));

        $mform->addElement('text', 'config_chatbot_display_name', get_string('displayname', 'block_course_chatbot'), 'maxlength="100" size="50"');
        $mform->setType('config_chatbot_display_name', PARAM_TEXT);
        $mform->setDefault('config_chatbot_display_name', get_string('defaultchatbotname', 'block_course_chatbot'));

        $mform->addElement('header', 'unitsheader', get_string('units', 'block_course_chatbot'));

        $unitcount = 5; // Definimos un número fijo de unidades que se pueden configurar.
        for ($i = 0; $i < $unitcount; $i++) {
            $mform->addElement('static', 'spacer', '', '--- ' . get_string('unit', 'block_course_chatbot') . ' ' . ($i + 1) . ' ---');
            $mform->addElement('text', 'unitname[' . $i . ']', get_string('unitname', 'block_course_chatbot'));
            $mform->setType('unitname[' . $i . ']', PARAM_TEXT);
            
            $mform->addElement('filepicker', 'jsonfile[' . $i . ']', get_string('uploadjson', 'block_course_chatbot'), null,
                ['maxbytes' => 102400, 'accepted_types' => ['.json']]
            );
        }
    }

    // Poblar el formulario con los datos guardados.
    public function set_data($defaults) {
        if (isset($defaults->config->units) && is_array($defaults->config->units)) {
            foreach ($defaults->config->units as $key => $unit) {
                $defaults->{'unitname['.$key.']'} = $unit->name;
                
                // Mapear el contenido JSON a un archivo temporal para el filepicker.
                $draftitemid = file_get_submitted_draft_itemid('jsonfile['.$key.']');
                file_save_draft_area_files($draftitemid, $this->context->id, 'block_course_chatbot', 'unitconfig', $key, ['subdirs' => 0, 'maxfiles' => 1], $unit->json);
                $defaults->{'jsonfile['.$key.']'} = $draftitemid;
            }
        }
        parent::set_data($defaults);
    }
}
