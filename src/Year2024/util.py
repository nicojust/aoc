import sys
from enum import Enum
from pathlib import Path
from colorama import Fore


class Environment(str, Enum):
    test = "test"
    prod = "prod"


def read_file(year: int, day: int, env: Environment) -> str:
    file_path = f"{Path.cwd()}/src/Year{year}/Day{str(day).zfill(2)}/input/{env}.txt"

    try:
        with open(file_path, "r", encoding="utf-8") as file:
            return file.readlines()
    except FileNotFoundError:
        print(f"Error: The file '{file_path}' does not exist.")
        sys.exit(1)
    except Exception as e:
        print(f"An unexpected error occurred: {e}")
        sys.exit(1)


def notice(message: str):
    print(f"{Fore.YELLOW}{message}")


def answer(solution: int, message: str):
    print(f"{Fore.GREEN}Solution {solution}: {Fore.WHITE}{message}")
