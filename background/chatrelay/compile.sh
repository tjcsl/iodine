#!/bin/bash

# lrt is for librt, the thing that allows message queues
gcc relay.c -lrt -o Relay