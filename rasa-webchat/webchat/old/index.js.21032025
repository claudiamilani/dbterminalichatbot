// Genera un ID fisso una sola volta all'inizio.
// Puoi anche usare Math.random() o un timestamp unico, ma SOLO UNA VOLTA.
const senderId = "user_static_id";

// Funzione per inviare il messaggio all'API di Rasa e ricevere la risposta
async function sendMessageToRasa(message) {
    const responseContainer = document.querySelector(".messages"); // Contenitore dei messaggi

    // Prepara i dati da inviare a Rasa, usando SEMPRE lo stesso senderId
    const data = {
        sender: senderId,
        message: message
    };

    try {
        // Invia il messaggio all'endpoint di Rasa
        const response = await fetch('http://localhost:5005/webhooks/rest/webhook', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        });

        // Ottieni la risposta JSON
        const rasaResponse = await response.json();

        // Aggiungi la risposta di Rasa al contenitore della chat
        if (rasaResponse && rasaResponse.length > 0) {
            rasaResponse.forEach(rasaMessage => {
                const botMessage = document.createElement('li');
                botMessage.classList.add('bot-message');
                // Supporta i ritorni a capo sostituendo "\n" con <br>
                botMessage.innerHTML = rasaMessage.text.replace(/\n/g, "<br>");
                responseContainer.appendChild(botMessage);
                scrollToBottom();
            });
        }
    } catch (error) {
        console.error('Errore durante la comunicazione con Rasa:', error);
    }
}

// Funzione per gestire lo scroll verso il basso
function scrollToBottom() {
    const chatBox = document.getElementById("chat-box");
    chatBox.scrollTop = chatBox.scrollHeight;
}

// Gestione del pulsante "Invia"
document.getElementById('send-btn').addEventListener('click', () => {
    const userInput = document.getElementById('user-input');
    const userMessage = userInput.value.trim();

    if (userMessage) {
        // Mostra il messaggio dell'utente nella chat
        const userMessageElement = document.createElement('li');
        userMessageElement.classList.add('user-message');
        userMessageElement.textContent = userMessage;
        document.querySelector(".messages").appendChild(userMessageElement);
        scrollToBottom();

        // Invia il messaggio a Rasa
        sendMessageToRasa(userMessage);

        // Pulisce il campo di input
        userInput.value = '';
    }
});

// Aggiunge un listener per inviare il messaggio premendo "Enter"
document.getElementById('user-input').addEventListener('keypress', (event) => {
    if (event.key === 'Enter') {
        document.getElementById('send-btn').click();
    }
});

