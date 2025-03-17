#curl -X POST \
#  http://localhost:5005/webhooks/rest/webhook \
#  -H "Content-Type: application/json" \
#  -d '{
#        "sender": "user",
#        "message": "Ciao"
#      }'


#      curl -X POST -H "Content-Type: application/json" -d '{"sender": "user", "message": "Verifica se il mio dispositivo supporta la fotocamera", "entities": [{"entity": "device_name", "value": "iPhone"}, {"entity": "feature", "value": "camera"}]}' http://localhost:5005/webhooks/rest/webhook

curl -X POST -H "Content-Type: application/json" -d '{
  "sender": "user",
  "message": "Qual è il nome di Milani?"
}' "http://localhost:5005/webhooks/rest/webhook"
