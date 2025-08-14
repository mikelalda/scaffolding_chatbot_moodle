<?php

namespace block_course_chatbot;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/blocks/course_chatbot/classes/ChatbotLogic.php');

class api extends \core_external\external_api {

    public static function process_message_parameters() {
        return new \external_function_parameters([
            'blockid'   => new \external_value(PARAM_INT, 'ID de la instancia del bloque.'),
            'message'   => new \external_value(PARAM_RAW, 'Mensaje del usuario.'),
            'unitindex' => new \external_value(PARAM_INT, 'Índice de la unidad seleccionada.'),
        ]);
    }

    public static function process_message($blockid, $message, $unitindex) {
        global $DB;

        self::validate_parameters(self::process_message_parameters(), ['blockid' => $blockid, 'message' => $message, 'unitindex' => $unitindex]);

        $blockinstance = $DB->get_record('block_instances', ['id' => $blockid], '*', MUST_EXIST);
        $context = \context_block::instance($blockinstance->id);
        self::validate_context($context);
        require_capability('block/course_chatbot:view', $context);

        $config = unserialize($blockinstance->configdata);
        
        // Cargar la configuración de la unidad específica.
        $unit_config = $config->units[$unitindex] ?? null;
        if (empty($unit_config) || empty($unit_config->json)) {
             throw new \moodle_exception('noconfigset', 'block_course_chatbot');
        }

        $bot_data = json_decode($unit_config->json, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \moodle_exception('invalidconfig', 'block_course_chatbot', '', null, json_last_error_msg());
        }
        
        $chatbot_logic = new ChatbotLogic($bot_data);
        return $chatbot_logic->process_message($message);
    }

    public static function process_message_returns() {
        return new \external_single_structure([
            'message' => new \external_value(PARAM_RAW, 'Respuesta del bot.'),
            'type'    => new \external_value(PARAM_ALPHANUMEXT, 'Tipo de mensaje ("bot" o "user").'),
            'action'  => new \external_value(PARAM_ALPHANUMEXT, 'Acción sugerida.', VALUE_OPTIONAL),
            'current_step_index' => new \external_value(PARAM_INT, 'Índice del paso actual.', VALUE_OPTIONAL),
        ]);
    }
}
