# Usa Nginx come base per servire il file HTML
FROM nginx:alpine

# Copia i file statici (webchat) nella directory di Nginx
COPY ./rasa-webchat/webchat /usr/share/nginx/html

# Esponi la porta 80
EXPOSE 80

