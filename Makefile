.PHONY: run run-all

command ?= aoc:day:01
env ?= prod

run:
	php src/application.php ${command} ${env}

run-all:
	./run_commands.sh
