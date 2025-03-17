FROM nginx:latest AS base
# [BASE STAGE]
LABEL maintainer="Francesco Tesone <tesone@medialogic.it>"
LABEL description="Latest version of Nginx with DBTerminali"
COPY --chown=www-data:0 --chmod=775 . .
# [END BASE STAGE]

FROM node:12-alpine AS assets
# [ASSETS STAGE]
WORKDIR /var/www/html
COPY . .
RUN apk add --no-progress --quiet --no-cache git \
    && npm install \
    && npm run prod
# [END ASSETS STAGE]


FROM base AS web
# [WEB STAGE]
COPY --from=assets --chown=www-data:0 --chmod=775 /var/www/html/public /var/www/html/public
COPY .k8s/nginx/default.conf /etc/nginx/conf.d/default.conf
# [END WEB STAGE]

FROM web
# DEFAULT STAGE