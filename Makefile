.PHONY: run run-all

command ?= aoc:day:01

run:
	php src/application.php ${command} prod

run-all:
	./run_commands.sh
