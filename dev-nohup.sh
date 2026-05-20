#!/bin/bash

set -x

### nohup cmd args >/dev/null 2>&1 &

__DIR__="$(dirname $(readlink -f $0))"

ON_FOREGROUND=0

if [[ "$1" = "-f" ]]; then
    ON_FOREGROUND=1
fi

if [[ -f "${__DIR__}/.env" ]]; then
    source "${__DIR__}/.env"
else
    echo "File not found:'${__DIR__}/.env'"
fi

export EDITOR_OPEN_CMD="${EDITOR_OPEN_CMD:-code -g}"

export FRONTEND_PROJECT_ROOT="${FRONTEND_PROJECT_ROOT:-$PWD}"

# export EDITOR_OPEN_CMD="antigravity -g"

nohup docker compose up >/dev/null 2>&1 &

kill -9 $(ps aux | grep -v grep | grep 'open-in-editor-server' |awk '{print $2}') >/dev/null 2>&1

if [[ $ON_FOREGROUND = "1" ]]; then
    npm run open-in-editor-server
else
    nohup npm run open-in-editor-server >/dev/null 2>&1 &
fi
