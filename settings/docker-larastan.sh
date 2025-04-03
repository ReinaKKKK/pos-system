#!/bin/bash
command="docker compose exec -w $(pwd)/src phpqa ./vendor/bin/phpstan $@"

eval "$command"
