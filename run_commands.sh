#!/bin/bash

namespace="aoc:day"

for command in $(php src/application.php list "$namespace" --raw | awk '{print $1}'); do
    php src/application.php "$command"
done
