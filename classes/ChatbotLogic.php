<?php

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');

class block_course_chatbot_edit_form extends block_edit_form {

    protected function specific_definition($mform) {
        global $COURSE, $DB;

        $mform->addElement('header', 'configheader', get_string('settings', 'block_course_chatbot'));

        // Nombre a mostrar del chatbot.
        $mform->addElement('text', 'config_chatbot_display_name', get_string('displayname', 'block_course_chatbot'), 'maxlength="100" size="50"');
        $mform->setType('config_chatbot_display_name', PARAM_TEXT);
        $mform->setDefault('config_chatbot_display_name', get_string('defaultchatbotname', 'block_course_chatbot'));

        // Selector de sección del curso.
        $sectionoptions = [0 => get_string('section0', 'block_course_chatbot')];
        if (!empty($COURSE->id)) {
            $course_sections = $DB->get_records('course_sections', ['course' => $COURSE->id], 'section', 'id, name, section');
            foreach ($course_sections as $section) {
                if ($section->section == 0) continue;
                $sectionoptions[$section->section] = $section->name ?: get_string('topic') . " " . $section->section;
            }
        }
        $mform->addElement('select', 'config_chatbot_sectionid', get_string('section'), $sectionoptions);
        $mform->addHelpButton('config_chatbot_sectionid', 'section_help', 'block_course_chatbot');

        // Selector de archivos para la configuración JSON.
        $mform->addElement('filepicker', 'config_jsonfile', get_string('uploadjson', 'block_course_chatbot'), null,
            ['maxbytes' => 102400, 'accepted_types' => ['.json']]
        );
        $mform->addHelpButton('config_jsonfile', 'uploadjson_help', 'block_course_chatbot');
    }

    // Validación de datos del formulario.
    function validation($data, $files) {
        $errors = parent::validation($data, $files);
        
        $draftitemid = $data['config_jsonfile'];

        if ($draftitemid) {
            $fs = get_file_storage();
            $usercontext = \context_user::instance($GLOBALS['USER']->id);
            $stored_files = $fs->get_area_files($usercontext->id, 'user', 'draft', $draftitemid, 'sortorder', false);

            if ($stored_files) {
                $file = reset($stored_files);
                $content = $file->get_content();
                if (json_decode($content) === null) {
                    $errors['config_jsonfile'] = get_string('invalidjson', 'block_course_chatbot');
                }
            }
        }
        
        return $errors;
    }
}
