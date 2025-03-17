pip install redis
pip install 
exit
rasa shell
rasa
rasa shell
exit
rasa shell
curl -XPOST http://localhost:5006/webhooks/rest/webhook -d '{"sender": "user", "message": "Ciao"}'
curl -XPOST http://localhost:5005/webhooks/rest/webhook -d '{"sender": "user", "message": "Ciao"}'
curl -XPOST http://localhost:5005/webhooks/rest/webhook -d '{"sender": "user", "message": "Helo"}'
curl -XPOST http://localhost:5005/webhooks/rest/webhook -d '{"sender": "user", "message": "Hello"}'
exit
curl -XPOST http://localhost:5006/webhooks/rest/webhook -d '{"sender": "user", "message": "Ciao"}'
curl -XPOST http://localhost:5005/webhooks/rest/webhook -d '{"sender": "user", "message": "Ciao"}'
rasa shell
exit
lsof -i :5005
exit
netstat -ano | findstr :500
rasa shell
exit
rasa shell
exit
