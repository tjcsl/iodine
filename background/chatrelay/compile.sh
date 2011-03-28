#!/bin/bash

# lrt is for librt, the thing that allows message queues
# lpthread is for libpthread, which allows pthread usage
gcc relay.c -lrt -lpthread -o Relay
