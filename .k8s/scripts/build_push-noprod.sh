#!/bin/bash
#
# Copyright (c) 2024. Medialogic S.p.A.
#
case $1 in
  app)
    echo "Building & pushing app image for NOPROD environment"
    docker build --force-rm -t europe-west3-docker.pkg.dev/w3-core-gke-noprod-prj-807c/vas-n-docker-repo/mdm-dbterminali/app:latest -f .k8s/php/Dockerfile . && docker push --all-tags europe-west3-docker.pkg.dev/w3-core-gke-noprod-prj-807c/vas-n-docker-repo/mdm-dbterminali/app
    ;;

  web)
    echo "Building & pushing web image for NOPROD environment"
    docker build --force-rm -t europe-west3-docker.pkg.dev/w3-core-gke-noprod-prj-807c/vas-n-docker-repo/mdm-dbterminali/web:latest -f .k8s/nginx/Dockerfile . && docker push --all-tags europe-west3-docker.pkg.dev/w3-core-gke-noprod-prj-807c/vas-n-docker-repo/mdm-dbterminali/web
    ;;

  *)
    echo "Building & pushing app and web images for NOPROD environment"
    docker build --force-rm -t europe-west3-docker.pkg.dev/w3-core-gke-noprod-prj-807c/vas-n-docker-repo/mdm-dbterminali/app:latest -f .k8s/php/Dockerfile . && docker push --all-tags europe-west3-docker.pkg.dev/w3-core-gke-noprod-prj-807c/vas-n-docker-repo/mdm-dbterminali/app
    docker build --force-rm -t europe-west3-docker.pkg.dev/w3-core-gke-noprod-prj-807c/vas-n-docker-repo/mdm-dbterminali/web:latest -f .k8s/nginx/Dockerfile . && docker push --all-tags europe-west3-docker.pkg.dev/w3-core-gke-noprod-prj-807c/vas-n-docker-repo/mdm-dbterminali/web
    ;;
esac