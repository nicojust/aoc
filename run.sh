#!/usr/bin/env bash

language=${1:-"php"}
day=${2:-"1"}
env=${4:-"test"}

if [[ "$language" == "php" ]]; then
    year=${3:-"2023"}
    application="src/Year2023/application.php"

    php "$application" "aoc:year:${year:2:2}:day:$day" "$env"
else
    year=${3:-"2024"}
    application="src/Year2024/application.py"

    poetry run python "$application" "$day" "$year" "$env"
fi
