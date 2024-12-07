import re

from util import Environment, read_file, answer


def aoc(
    day: int,
    year: int,
    env: Environment,
):
    file = read_file(year, day, env)

    mul = 0
    for line in file:
        instructions = re.findall(r"(mul\((\d{1,3}),(\d{1,3})\))", line)

        for instruction in instructions:
            print(f"{instruction}")
            mul += int(instruction[1]) * int(instruction[2])

    answer(1, mul)

    mul = 0
    mul_enabled = True
    for line in file:
        blocks = re.sub("(don't\(\))", r"\n\1\n", line)
        blocks = re.sub("(do\(\))", r"\n\1\n", blocks)

        for block in blocks.splitlines():
            if block == "don't()":
                mul_enabled = False
            elif block == "do()":
                mul_enabled = True

            instructions = re.findall(r"(mul\((\d{1,3}),(\d{1,3})\))", block)
            for instruction in instructions:
                if mul_enabled:
                    print(f"{instruction}")
                    mul += int(instruction[1]) * int(instruction[2])

    answer(2, mul)
