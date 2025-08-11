<?php

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');

class block_course_chatbot_edit_form extends block_edit_form {

    protected function specific_definition($mform) {
        global $COURSE, $DB;

        $mform->addElement('header', 'configheader', get_string('settings', 'block_course_chatbot'));

        // Nombre del chatbot
        $mform->addElement('text', 'config_chatbot_display_name', get_string('displayname', 'block_course_chatbot'), 'maxlength="100" size="50"');
        $mform->setType('config_chatbot_display_name', PARAM_TEXT);
        $mform->setDefault('config_chatbot_display_name', get_string('defaultchatbotname', 'block_course_chatbot'));

        // Obtener secciones del curso
        $sections = $DB->get_records_menu('course_sections', ['course' => $COURSE->id], 'section', 'id, name');
        $sectionoptions = [];
        foreach ($sections as $id => $name) {
            $sectionoptions[$id] = $name ?: get_string('topic') . " $id";
        }
        $mform->addElement('select', 'config_chatbot_sectionid', get_string('section'), $sectionoptions);

        // Filepicker para el JSON de ese tema
        $mform->addElement('filepicker', 'config_chatbot_config_json_file', get_string('uploadjson', 'block_course_chatbot'), null,
            ['accepted_types' => ['json', 'web_json', 'text_plain']]);
        $mform->addHelpButton('config_chatbot_config_json_file', 'uploadjson_help', 'block_course_chatbot');

    }
}