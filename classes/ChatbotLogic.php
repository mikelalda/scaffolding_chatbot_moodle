<?php

namespace block_course_chatbot;

defined('MOODLE_INTERNAL') || die();

/**
 * Clase para la lógica del chatbot.
 * Esto es una traducción y adaptación del backend de Python a PHP.
 */
class ChatbotLogic {

    protected $botdata;

    public function __construct(array $botdata) {
        $this->botdata = $botdata;
    }

    /**
     * Procesa el mensaje del usuario y devuelve la respuesta del bot.
     * @param string $user_message El mensaje del usuario.
     * @return array La respuesta del bot (ej. ['message' => '...', 'type' => 'bot']).
     */
    public function process_message(string $user_message): array {
        $user_message_lower = mb_strtolower(trim($user_message));

        // 1. Intentos de "practicar"
        if (preg_match('/^\s*practicar\s*$/iu', $user_message_lower)) {
            if (!empty($this->botdata['resolucion'])) {
                // Iniciar un ejercicio de resolución de problemas
                // Aquí deberías tener una lógica de estado para los pasos de resolución.
                // Por simplicidad, aquí solo se devuelve el primer paso.
                return [
                    'message' => '¡Excelente! Comencemos un ejercicio. ' . $this->botdata['resolucion'][0]['instruccion'],
                    'type' => 'bot',
                    'action' => 'start_resolution',
                    'current_step_index' => 0
                ];
            } else {
                return ['message' => 'Lo siento, no tengo ejercicios de práctica configurados en este tema.', 'type' => 'bot'];
            }
        }

        // 2. Comprobar FAQs
        foreach ($this->botdata['faq'] as $faq) {
            // Normalizar el patrón regex de la FAQ (adaptar normalize_text_for_pattern de Python)
            $pattern = $this->normalize_text_for_pattern($faq['pregunta']);
            if (preg_match('/' . $pattern . '/iu', $user_message_lower)) {
                return ['message' => $faq['respuesta'], 'type' => 'bot'];
            }
        }

        // 3. Si no es una FAQ y no es "practicar", una respuesta genérica
        return ['message' => 'Lo siento, no entendí tu pregunta. ¿Podrías reformularla o probar con "practicar"?', 'type' => 'bot'];
    }

    /**
     * Adapta la lógica de normalización de texto para patrones regex de Python a PHP.
     * @param string $text El texto a normalizar.
     * @return string El patrón regex normalizado.
     */
    protected function normalize_text_for_pattern(string $text): string {
        $text_lower = mb_strtolower(trim($text));

        // Eliminar signos de interrogación al inicio y al final
        if (mb_substr($text_lower, 0, 1) === '¿') {
            $text_lower = mb_substr($text_lower, 1);
        }
        if (mb_substr($text_lower, -1) === '?') {
            $text_lower = mb_substr($text_lower, 0, -1);
        }

        // Reemplazar vocales con sus variantes acentuadas para regex
        $replacements = [
            'a' => '[aá]',
            'e' => '[eé]',
            'i' => '[ií]',
            'o' => '[oó]',
            'u' => '[uú]'
        ];
        foreach ($replacements as $original => $replacement) {
            $text_lower = str_replace($original, $replacement, $text_lower);
        }
        
        // Eliminar caracteres no alfanuméricos y espacios, excepto corchetes para regex
        $cleaned_text = preg_replace('/[^a-z0-9\s\[\]]/iu', '', $text_lower);
        $cleaned_text = trim($cleaned_text); // Limpiar espacios extra

        // Envolver el patrón para que coincida en cualquier parte de la cadena
        return '.*' . preg_quote($cleaned_text, '/') . '.*'; // Usar preg_quote para escapar caracteres especiales de regex
    }
}