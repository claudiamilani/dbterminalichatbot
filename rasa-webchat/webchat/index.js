// Genera un ID fisso una sola volta all'inizio.
const senderId = "user_static_id";

// Funzione principale per inviare il messaggio all'API di Rasa e ricevere la risposta
async function sendMessageToRasa(message) {
    console.log("Invio messaggio a Rasa:", message);

    const data = {
        sender: senderId,
        message: message
    };

    try {
        const response = await fetch('http://localhost:5005/webhooks/rest/webhook', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });

        const rasaResponse = await response.json();
        console.log("Risposta Rasa grezza:", rasaResponse);

        handleRasaResponse(rasaResponse);
    } catch (error) {
        console.error('Errore durante la comunicazione con Rasa:', error);
    }
}

// Funzione per gestire la risposta di Rasa
function handleRasaResponse(messages) {
    const responseContainer = document.querySelector(".messages");

    messages.forEach(message => {
        console.log("Messaggio Rasa:", message);

        // Apri popup se richiesto
        if (message.custom && message.custom.type === "popup" && message.custom.link) {
            console.log("Apro popup con link:", message.custom.link);
            window.open(message.custom.link, "_blank", "popup,width=800,height=600");
            return; // non serve mostrare il testo in chat
        }

        // Mostra messaggio di testo
        if (message.text) {
            const messageElement = document.createElement('li');
            messageElement.classList.add('bot-message');
            messageElement.innerHTML = message.text.replace(/\n/g, "<br>");
            responseContainer.appendChild(messageElement);
        }

        // Mostra pulsanti
        if (message.buttons) {
            console.log("Pulsanti ricevuti:", message.buttons);
            message.buttons.forEach(button => {
                const buttonElement = document.createElement('button');
                buttonElement.innerText = button.title;
                buttonElement.classList.add('bot-button');
                buttonElement.onclick = () => {
                    console.log("Click pulsante, invio payload:", button.payload);
                    sendMessageToRasa(button.payload);
                };
                responseContainer.appendChild(buttonElement);
            });
        }
    });

    scrollToBottom();
}

// Scroll automatico
function scrollToBottom() {
    const chatBox = document.getElementById("chat-box");
    if (chatBox) {
        chatBox.scrollTop = chatBox.scrollHeight;
    }
}

// Invia messaggio al click
document.getElementById('send-btn').addEventListener('click', () => {
    const userInput = document.getElementById('user-input');
    const userMessage = userInput.value.trim();

    if (userMessage) {
        const userMessageElement = document.createElement('li');
        userMessageElement.classList.add('user-message');
        userMessageElement.textContent = userMessage;
        document.querySelector(".messages").appendChild(userMessageElement);
        scrollToBottom();

        sendMessageToRasa(userMessage);
        userInput.value = '';
    }
});

// Invia messaggio premendo Invio
document.getElementById('user-input').addEventListener('keypress', (event) => {
    if (event.key === 'Enter') {
        document.getElementById('send-btn').click();
    }
});

