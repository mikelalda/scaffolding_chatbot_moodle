define(['jquery', 'core/ajax'], function($, Ajax) {
    const init = (params) => {
        const { blockid, error_string, choose_unit_title } = params;

        const unitSelectionView = $(`#unit-selection-view-${blockid}`);
        const chatbotView = $(`#chatbot-view-${blockid}`);
        const chatbotTitle = $(`#chatbot-title-${blockid}`);
        const messagesDiv = $(`#chatbot-messages-${blockid}`);
        const inputField = $(`#chatbot-input-${blockid}`);
        const sendButton = $(`#chatbot-send-${blockid}`);
        const backButton = $(`#back-to-units-${blockid}`);
        
        let currentUnitIndex = -1;

        const switchToChatView = (unitIndex, unitName) => {
            currentUnitIndex = unitIndex;
            unitSelectionView.hide();
            chatbotView.show();
            chatbotTitle.text(unitName);
            // Limpiar mensajes anteriores, excepto el inicial
            messagesDiv.find('.user-message, .bot-message:not(:first-child)').remove();
            inputField.focus();
        };

        const switchToUnitView = () => {
            currentUnitIndex = -1;
            chatbotView.hide();
            unitSelectionView.show();
        };

        // Evento para seleccionar una unidad
        unitSelectionView.on('click', '.unit-selector', function() {
            const unitIndex = $(this).data('unitindex');
            const unitName = $(this).data('unitname');
            switchToChatView(unitIndex, unitName);
        });

        // Evento para volver a la selección
        backButton.on('click', switchToUnitView);

        const appendMessage = (message, type) => {
            const messageElement = $('<div class="chatbot-message"></div>')
                .addClass(`${type}-message`)
                .text(message);
            messagesDiv.append(messageElement);
            messagesDiv.scrollTop(messagesDiv[0].scrollHeight);
        };

        const sendMessage = () => {
            const userMessage = inputField.val().trim();
            if (userMessage === '' || currentUnitIndex < 0) {
                return;
            }

            appendMessage(userMessage, 'user');
            inputField.val('');
            inputField.focus();

            Ajax.call([{
                methodname: 'block_course_chatbot_process_message',
                args: {
                    blockid: blockid,
                    message: userMessage,
                    unitindex: currentUnitIndex // Enviar el índice de la unidad
                },
                done: (response) => {
                    appendMessage(response.message, response.type);
                },
                fail: (error) => {
                    console.error('Error del chatbot:', error);
                    appendMessage(`${error_string}: ${error.error}`, 'bot');
                }
            }]);
        };
        
        sendButton.on('click', sendMessage);
        inputField.on('keypress', (e) => {
            if (e.which === 13) {
                sendMessage();
            }
        });
    };

    return {
        init: init
    };
});
