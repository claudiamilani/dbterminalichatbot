#!/bin/bash
#
# Copyright (c) 2024. Medialogic S.p.A.
#

# Creates or updates configmap with env vars required by the application to work.
# Specify the desired env as first parameter
# Specify a custom env file as second parameter to be used as env file or ignore to use default

case $1 in
  dev)
    echo "Generating configmap and applying to DEV k8s environment using ${2:-.env.dev} file"
    kubectl --context connectgateway_w3-core-gke-dev-prj-6799_global_core-std-gke-awsmi-d-001 -n mdm-vas create configmap mdm-dbterminali-common --from-env-file="${2:-.env.dev}" -o yaml --dry-run=client |
      kubectl --context connectgateway_w3-core-gke-dev-prj-6799_global_core-std-gke-awsmi-d-001 -n mdm-vas apply -f -
    ;;

  noprod)
    echo "Generating configmap and applying to NOPROD k8s environment using ${2:-.env.noprod} file"
    kubectl --context connectgateway_w3-core-gke-noprod-prj-807c_global_core-std-gke-awsmi-n-001 -n mdm-vas create configmap mdm-dbterminali-common --from-env-file="${2:-.env.noprod}" -o yaml --dry-run=client |
      kubectl --context connectgateway_w3-core-gke-noprod-prj-807c_global_core-std-gke-awsmi-n-001 -n mdm-vas apply -f -
    ;;

  prod)
    echo "Generating configmap and applying to PROD k8s environment  using ${2:-.env.prod} file"
    kubectl --context connectgateway_w3-core-gke-p-prj-4a34_global_core-std-gke-awsmi-p-001 -n mdm-vas create configmap mdm-dbterminali-common --from-env-file="${2:-.env.prod}" -o yaml --dry-run=client |
      kubectl --context connectgateway_w3-core-gke-p-prj-4a34_global_core-std-gke-awsmi-p-001 -n mdm-vas apply -f -
    ;;

    *)
      echo "Invalid ${1} environment. Only dev, noprod and prod envs are allowed"
    ;;
esac