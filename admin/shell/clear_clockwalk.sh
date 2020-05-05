#!/usr/bin/env bash

find /tmp/clockwork/ -type f -exec rm -f {} \;

find /tmp -mtime +1 -name '*.log' -delete

find /tmp -mtime +1 -name '*.sh' -delete
