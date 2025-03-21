docker run -v /home/milani/sviluppo/dbterminalichatbot/dbtermrasa:/app -w /app --user $(id -u):$(id -g) rasa/rasa:latest train
docker run -it -v /home/milani/sviluppo/dbterminalichatbot/dbtermrasa:/app -w /app --user $(id -u):$(id -g) rasa/rasa:latest shell
