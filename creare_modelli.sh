docker run -v /home/milani/sviluppo/dbterminalinew/my_rasa_project:/app -w /app --user $(id -u):$(id -g) rasa/rasa:latest train
docker run -it -v /home/milani/sviluppo/dbterminalinew/my_rasa_project:/app -w /app --user $(id -u):$(id -g) rasa/rasa:latest shell
