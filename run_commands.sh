#!/bin/bash

namespace="aoc:year:23:day"
application="src/Year2023/application.php"

for command in $(php "$application" list "$namespace" --raw | awk '{print $1}'); do
    php "$application" "$command"
done
