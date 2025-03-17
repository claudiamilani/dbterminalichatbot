#!/bin/bash
#
# Copyright (c) 2024. Medialogic S.p.A.
#
case $1 in
  dev)
    echo "Deploying to DEV environment"
    kubectl --context connectgateway_w3-core-gke-dev-prj-6799_global_core-std-gke-awsmi-d-001 apply -f .k8s/deployments/mdm-dbterminali-dev.yml
    ;;

  noprod)
    echo "Deploying to NOPROD environment"
    kubectl --context connectgateway_w3-core-gke-noprod-prj-807c_global_core-std-gke-awsmi-n-001 apply -f .k8s/deployments/mdm-dbterminali-noprod.yml
    ;;

  prod)
    echo "Deploying to PROD environment"
    kubectl --context connectgateway_w3-core-gke-p-prj-4a34_global_core-std-gke-awsmi-p-001 apply -f .k8s/deployments/mdm-dbterminali-prod.yml
    ;;

  *)
    echo "Invalid ${1} environment. Only dev, noprod and prod envs are allowed"
    ;;
esac