from util import Environment, read_file, answer


def aoc(
    day: int,
    year: int,
    env: Environment,
):
    file = read_file(year, day, env)

    reports = []
    for line in file:
        result = list(map(int, line.strip().split()))
        reports.append(result)

    safe_reports = []
    for report in reports:
        i = 0
        while True:
            if report[i] > report[i + 1]:
                safe = decreasing(report, 0, True, False, 0)
                break
            elif report[i] < report[i + 1]:
                safe = increasing(report, 0, True, False, 0)
                break
            else:
                i += 1

        if safe:
            safe_reports.append(report)

    answer(1, len(safe_reports))

    dampened_safe_reports = []
    for report in reports:
        i = 0
        while True:
            if report[i] > report[i + 1]:
                safe = decreasing(report, 0, True, True, 0)
                break
            elif report[i] < report[i + 1]:
                safe = increasing(report, 0, True, True, 0)
                break
            else:
                i += 1

        if safe:
            dampened_safe_reports.append(report)

    answer(2, len(dampened_safe_reports))


def decreasing(report: list, i: int, safe: bool, dampener: bool, errors: 0):
    if i == len(report) - 1:
        return safe

    if safe:
        diff = report[i] - report[i + 1]
        if diff in [1, 2, 3]:
            if errors > 1:
                return False

            return decreasing(report, i + 1, safe, dampener, errors)

        if dampener and errors == 0:
            del report[i]
            return decreasing(report, i - 1, safe, dampener, errors + 1)

    return False


def increasing(report: list, i: int, safe: bool, dampener: bool, errors: 0):
    if i == len(report) - 1:
        return safe

    if safe:
        diff = report[i + 1] - report[i]
        if diff in [1, 2, 3]:
            if errors > 1:
                return False

            return increasing(report, i + 1, safe, dampener, errors)

        if dampener and errors == 0:
            del report[i]
            return increasing(report, i - 1, safe, dampener, errors + 1)

    return False
