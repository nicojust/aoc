.PHONY: activate deactivate run run-all

application ?= src/Year2023/application.php
command ?= aoc:year:23:day:01
env ?= prod

activate:
	poetry shell

deactivate:
	deactivate

run:
	php ${application} ${command} ${env}

run-all:
	./run_commands.sh
