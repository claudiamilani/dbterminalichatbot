#!/bin/bash
#
# Copyright (c) 2024. Medialogic S.p.A.
#
case $1 in
  app)
    echo "Building & pushing app image for DEV environment"
    docker build --force-rm --build-arg ENV=dev -t europe-west3-docker.pkg.dev/w3-core-gke-dev-prj-6799/vas-d-docker-repo/mdm-dbterminali/app:latest -f .k8s/php/Dockerfile . && docker push --all-tags europe-west3-docker.pkg.dev/w3-core-gke-dev-prj-6799/vas-d-docker-repo/mdm-dbterminali/app
    ;;

  web)
    echo "Building & pushing web image for DEV environment"
    docker build --force-rm -t europe-west3-docker.pkg.dev/w3-core-gke-dev-prj-6799/vas-d-docker-repo/mdm-dbterminali/web:latest -f .k8s/nginx/Dockerfile . && docker push --all-tags europe-west3-docker.pkg.dev/w3-core-gke-dev-prj-6799/vas-d-docker-repo/mdm-dbterminali/web
    ;;

  *)
    echo "Building & pushing app and web images for DEV environment"
    docker build --force-rm --build-arg ENV=dev -t europe-west3-docker.pkg.dev/w3-core-gke-dev-prj-6799/vas-d-docker-repo/mdm-dbterminali/app:latest -f .k8s/php/Dockerfile . && docker push --all-tags europe-west3-docker.pkg.dev/w3-core-gke-dev-prj-6799/vas-d-docker-repo/mdm-dbterminali/app
    docker build --force-rm -t europe-west3-docker.pkg.dev/w3-core-gke-dev-prj-6799/vas-d-docker-repo/mdm-dbterminali/web:latest -f .k8s/nginx/Dockerfile . && docker push --all-tags europe-west3-docker.pkg.dev/w3-core-gke-dev-prj-6799/vas-d-docker-repo/mdm-dbterminali/web
    ;;
esac