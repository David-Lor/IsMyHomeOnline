#!/bin/bash

#TOKEN must be the same as the Endpoint.Token defined on config.json
TOKEN="34a1aa92998cb4b2d2ce4363c24406e4ed4bd7289d1702361e4274cd9968eb46d934e10a29d1707f12e58b4f5361e806dc88ee0fe045a42629d0a55f3e509182"
URL="full url to your hosting, pointing to endpoint.sh"
TIMEOUT=2

curl --max-time $TIMEOUT --data "token=$TOKEN" $URL
