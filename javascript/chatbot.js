// Este es el archivo JS principal del módulo AMD de Moodle.
// La ruta es 'block_course_chatbot/chatbot'

define(['jquery', 'core/ajax', 'core/str'], function($, Ajax, Str) {
    /**
     * Inicializa la funcionalidad del chatbot.
     * @param {number} blockid El ID de la instancia del bloque Moodle.
     */
    var init = function(blockid) {
        var messagesDiv = $('#chatbot-messages-' + blockid);
        var inputField = $('#chatbot-input-' + blockid);
        var sendButton = $('#chatbot-send-' + blockid);

        // Acceder a la configuración del chatbot pasada desde PHP
        var chatbotConfig = window['chatbotConfig_' + blockid];
        
        // Función para añadir mensajes al chat
        function appendMessage(message, type) {
            var messageElement = $('<div class="chatbot-message ' + type + '-message"></div>');
            messageElement.text(message);
            messagesDiv.append(messageElement);
            messagesDiv.scrollTop(messagesDiv[0].scrollHeight); // Desplazar al final
        }

        // Manejador de eventos para el botón de enviar
        sendButton.on('click', function() {
            sendMessage();
        });

        // Manejador de eventos para la tecla Enter en el campo de entrada
        inputField.on('keypress', function(e) {
            if (e.which === 13) { // 13 es el código de la tecla Enter
                sendMessage();
            }
        });

        function sendMessage() {
            var userMessage = inputField.val().trim();
            if (userMessage === '') {
                return;
            }

            appendMessage(userMessage, 'user');
            inputField.val(''); // Limpiar el campo de entrada

            // Llamada AJAX al endpoint de Moodle
            Ajax.call([{
                methodname: 'block_course_chatbot_process_message', // Método definido en ChatbotAPI.php
                args: {
                    blockid: blockid,
                    message: userMessage
                },
                done: function(response) {
                    appendMessage(response.message, response.type);
                    // Aquí podrías añadir lógica para manejar 'action', 'current_step_index' si los implementas
                },
                fail: function(error) {
                    console.error('Error del chatbot:', error);
                    // Mostrar un mensaje de error al usuario
                    appendMessage(Str.get_string('errorprocessing', 'block_course_chatbot') + ' ' + error.message, 'bot');
                }
            }]);
        }

        // Mostrar un mensaje de bienvenida después de que la página se cargue completamente
        // Esto podría ser el "initial_bot_message" que ya manejas en Python.
        // No es necesario si ya lo pasas desde PHP directamente en el template.
        // appendMessage(chatbotConfig.initial_bot_message || Str.get_string('welcome', 'block_course_chatbot'), 'bot');
    };

    return {
        init: init
    };
});