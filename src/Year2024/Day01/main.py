from util import Environment, read_file, answer


def aoc(
    day: int,
    year: int,
    env: Environment,
):
    file = read_file(year, day, env)

    locations = [[], []]
    for line in file:
        result = list(map(int, line.strip().split()))
        locations[0].append(result[0])
        locations[1].append(result[-1])

    sorted_locations = [sorted(location) for location in locations]

    distances = [abs(a - b) for a, b in zip(sorted_locations[0], sorted_locations[1])]
    total_distance = sum(distances)

    answer(1, str(total_distance))

    similarity = 0
    for location in locations[0]:
        similarity += location * locations[1].count(location)

    answer(2, str(similarity))
