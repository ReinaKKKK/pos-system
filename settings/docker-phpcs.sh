#!/bin/bash

# 引数内のパラメータの置き換えを行う
args=()
for arg in "$@"; do
  if [[ $arg == "--stdin-path="* ]]; then
    new_arg="${arg/--stdin-path=/}"
    args+=("$new_arg")
  elif  [[ $arg == "-" ]]; then
    # - だけの場合は追加しない
    :
  else
    args+=("$arg")
  fi
done

command="docker compose exec -w /tmp phpqa phpcs ${args[*]}"
eval "$command"
