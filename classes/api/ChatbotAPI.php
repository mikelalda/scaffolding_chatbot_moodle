<?php

namespace block_course_chatbot\api;

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../ChatbotLogic.php'); // Asegura que ChatbotLogic esté disponible

use \core_webservice\external\server;
use \block_course_chatbot\ChatbotLogic;

/**
 * Endpoint de la API para las interacciones del chatbot.
 */
class ChatbotAPI extends \core_external\external_api {

    /**
     * Define los parámetros para el método de procesamiento de mensajes.
     * @return \external_function_parameters
     */
    public static function process_message_parameters() {
        return new \external_function_parameters([
            'blockid' => new \external_value(PARAM_INT, 'ID de la instancia del bloque del chatbot.'),
            'message' => new \external_value(PARAM_RAW, 'El mensaje enviado por el usuario.'),
            // Aquí podrías añadir parámetros para mantener el estado de la conversación,
            // como 'current_step_index' para los ejercicios de resolución.
            // 'current_step_index' => new \external_value(PARAM_INT, 'Índice del paso actual en la resolución de problemas.', ['default' => -1]),
        ]);
    }

    /**
     * Procesa un mensaje del usuario y devuelve la respuesta del bot.
     * @param int $blockid ID de la instancia del bloque.
     * @param string $message El mensaje del usuario.
     * @return array La respuesta del bot.
     * @throws \moodle_exception
     */
    public static function process_message($blockid, $message) {
        global $DB, $USER;

        self::validate_parameters(self::process_message_parameters(), ['blockid' => $blockid, 'message' => $message]);

        // Verificar el contexto del bloque y obtener la configuración
        $blockinstance = $DB->get_record('block', ['id' => $blockid], '*', MUST_EXIST);
        $blockcontext = \context_block::instance($blockinstance->id);
        
        // Comprobación de capacidades: Asegura que el usuario pueda interactuar con el bot (ej. moodle/block:view)
        \require_capability('moodle/block:view', $blockcontext);

        // Obtener la configuración JSON almacenada en la instancia del bloque
        $chatbot_config_json = $blockinstance->configdata['chatbot_config_json'] ?? null;
        if (empty($chatbot_config_json)) {
            throw new \moodle_exception(get_string('noconfigset', 'block_course_chatbot'), 'block_course_chatbot');
        }

        $bot_data = json_decode($chatbot_config_json, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \moodle_exception(get_string('invalidconfig', 'block_course_chatbot'), 'block_course_chatbot', '', json_last_error_msg());
        }

        // Inicializar la lógica del chatbot
        $chatbot_logic = new ChatbotLogic($bot_data);

        // Procesar el mensaje
        $response = $chatbot_logic->process_message($message);

        return $response; // Devolver la respuesta del bot
    }

    /**
     * Define el tipo de datos que devuelve el método process_message.
     * @return \external_value
     */
    public static function process_message_returns() {
        return new \external_single_structure([
            'message' => new \external_value(PARAM_RAW, 'Mensaje de respuesta del bot.'),
            'type' => new \external_value(PARAM_ALPHANUMEXT, 'Tipo de mensaje (ej. "bot", "user").'),
            // 'action' => new \external_value(PARAM_ALPHANUMEXT, 'Acción sugerida por el bot (ej. "start_resolution").', ['optional' => true]),
            // 'current_step_index' => new \external_value(PARAM_INT, 'Índice del paso actual del ejercicio.', ['optional' => true]),
        ]);
    }
}