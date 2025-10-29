// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.

define(['jquery', 'core/ajax', 'core/notification'], function($, ajax, notification) {

    var courseId, wwwroot, currentUrl, pageType;
    var chatWindow, chatButton, messagesArea, input, sendButton, typingIndicator, closeButton;

    // --- Funciones de la UI ---

    var showTyping = function() {
        typingIndicator.hidden = false;
        messagesArea.scrollTop = messagesArea.scrollHeight;
    };

    var hideTyping = function() {
        typingIndicator.hidden = true;
    };

    var addMessage = function(content, type) {
        type = type || 'bot';
        var msgDiv = document.createElement('div');
        msgDiv.classList.add('ai-message', 'ai-message-' + type);
        // Use innerHTML to support clickable links
        msgDiv.innerHTML = content;
        messagesArea.appendChild(msgDiv);
        messagesArea.scrollTop = messagesArea.scrollHeight;
        return msgDiv;
    };

    var addFeedbackButtons = function(logId, messageElement) {
        var feedbackDiv = document.createElement('div');
        feedbackDiv.classList.add('ai-feedback');
        
        var helpfulBtn = document.createElement('button');
        helpfulBtn.classList.add('ai-feedback-btn');
        helpfulBtn.innerText = '👍';
        helpfulBtn.dataset.logid = logId;
        helpfulBtn.dataset.helpful = '1';
        
        var notHelpfulBtn = document.createElement('button');
        notHelpfulBtn.classList.add('ai-feedback-btn');
        notHelpfulBtn.innerText = '👎';
        notHelpfulBtn.dataset.logid = logId;
        notHelpfulBtn.dataset.helpful = '0';

        feedbackDiv.appendChild(helpfulBtn);
        feedbackDiv.appendChild(notHelpfulBtn);
        messageElement.appendChild(feedbackDiv);
    };

    var toggleChat = function() {
        var isHidden = chatWindow.hidden;
        chatWindow.hidden = !isHidden;
        chatButton.hidden = isHidden;
        if (isHidden) {
            input.focus();
        }
    };

    // --- Lógica de Eventos ---

    var onSendClick = function() {
        var question = input.value.trim();
        if (question === '') {
            return;
        }

        console.log('[AI Assistant] Sending question:', question);
        console.log('[AI Assistant] Input disabled:', input.disabled);
        addMessage(question, 'user');
        input.value = '';
        input.disabled = true;
        sendButton.disabled = true;
        showTyping();

        // Llamada AJAX al servicio web de Moodle
        ajax.call([{
            methodname: 'local_aiassistant_query',
            args: {
                courseid: courseId,
                question: question,
                currenturl: currentUrl || '',
                pagetype: pageType || ''
            },
            done: function(response) {
                console.log('[AI Assistant] Response received:', response);
                var messageEl = addMessage(response.answer, 'bot');
                addFeedbackButtons(response.log_id, messageEl);

                // Re-enable input after success
                console.log('[AI Assistant] Re-enabling input after success');
                hideTyping();
                input.disabled = false;
                sendButton.disabled = false;
                console.log('[AI Assistant] Input re-enabled:', !input.disabled);
                input.focus();
            },
            fail: function(ex) {
                console.error('[AI Assistant] AJAX failed:', ex);
                notification.exception(ex);
                addMessage('Sorry, I am having trouble connecting.', 'bot');

                // Re-enable input after failure
                console.log('[AI Assistant] Re-enabling input after failure');
                hideTyping();
                input.disabled = false;
                sendButton.disabled = false;
                input.focus();
            }
        }]);
    };

    var onFeedbackClick = function(e) {
        var target = e.target.closest('.ai-feedback-btn');
        if (!target) {
            return;
        }

        // Desactivar botones de este grupo
        var parent = target.parentElement;
        parent.querySelectorAll('.ai-feedback-btn').forEach(function(btn) {
            btn.disabled = true;
            btn.classList.remove('active');
        });
        target.classList.add('active');

        // Enviar feedback
        ajax.call([{
            methodname: 'local_aiassistant_feedback',
            args: {
                logid: parseInt(target.dataset.logid),
                helpful: target.dataset.helpful === '1'
            },
            done: function() {
                // Opcional: mostrar un "Gracias"
            },
            fail: notification.exception
        }]);
    };

    // --- Inicialización ---

    return {
        init: function(params) {
            courseId = params.courseid;
            wwwroot = params.wwwroot || '';
            currentUrl = params.currenturl || '';
            pageType = params.pagetype || '';

            // Encontrar elementos del DOM
            chatWindow = document.getElementById('ai-chat-window');
            chatButton = document.getElementById('ai-chat-button');
            messagesArea = document.getElementById('ai-chat-messages');
            input = document.getElementById('ai-chat-input');
            sendButton = document.getElementById('ai-chat-send');
            typingIndicator = document.getElementById('ai-chat-typing');
            closeButton = document.getElementById('ai-chat-close');

            if (!chatWindow) {
                return;
            }

            // Asignar eventos
            chatButton.addEventListener('click', toggleChat);
            closeButton.addEventListener('click', toggleChat);
            sendButton.addEventListener('click', onSendClick);
            input.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    onSendClick();
                }
            });
            messagesArea.addEventListener('click', onFeedbackClick);
        }
    };
});