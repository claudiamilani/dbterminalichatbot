#!/bin/bash
# This is an utility script for local development to help generate self signed ssl certificates.
# If needed by your local system you can change it our adapt to your needs
cd "$(dirname "$0")" || exit
echo "Switched to $(dirname "$0") directory"
openssl req -new -newkey rsa:4096 -days 3650 -nodes -x509 -subj "/C=IT/ST=Rome/L=Rome/O=Medialogic/CN=medialogic.local" -keyout default.key -out ca.crt
openssl req -new -key default.key -out default.csr -sha256 -subj "/C=IT/ST=Rome/L=Rome/O=Medialogic/CN=*.lft.local"
openssl x509 -req -days 365 -in default.csr -CA ca.crt -CAkey default.key -CAcreateserial -out default.crt
rm -f default.csr ca.srl