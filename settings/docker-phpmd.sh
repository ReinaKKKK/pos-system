#!/bin/bash
command="docker compose exec -w /tmp phpqa phpmd $@"
eval "$command"
