import sys

sys.dont_write_bytecode = True

import typer
from typing_extensions import Annotated
from util import Environment, notice

from Day01.main import aoc as Day01
from Day02.main import aoc as Day02
from Day03.main import aoc as Day03

app = typer.Typer()


@app.command()
def aoc(
    day: int,
    year: Annotated[int, typer.Argument()] = 2024,
    env: Annotated[Environment, typer.Argument()] = Environment.test,
):
    notice(f"Running Advent of Code (Year: {year}, Day: {day}, Env: {env})")

    match day:
        case 1:
            Day01(year=year, day=day, env=env)
        case 2:
            Day02(year=year, day=day, env=env)
        case 3:
            Day03(year=year, day=day, env=env)
        case _:
            print("Nothing to see here")
            sys.exit()


if __name__ == "__main__":
    app()
