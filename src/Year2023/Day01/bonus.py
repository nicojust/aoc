import numpy as np

dict = {
    'one': 1,
    'two': 2,
    'three': 3,
    'four': 4,
    'five': 5,
    'six': 6,
    'seven': 7,
    'eight': 8,
    'nine': 9,
}

def get_calibration_number(line):
    first_digit = 0
    for key, value in dict.items():
        if line[0:len(key)] == key:
            first_digit = value

    for key, value in dict.items():
        line = line.replace(key, str(value))

    digits = [int(s) for s in line if s.isdigit()]
    if first_digit == 0:
        first_digit = digits[0]
    last_digit = digits[-1]
    calibration_number = int(str(first_digit)+str(last_digit))

    print(line, first_digit, last_digit)
    print(calibration_number)

    return calibration_number

text = np.loadtxt('src/Year2023/Day01/input/test2.txt', dtype=str)

calibration_numbers = np.zeros(len(text))
for i in range(len(calibration_numbers)):
    calibration_numbers[i] = get_calibration_number(text[i])

answer = calibration_numbers.sum()
print(answer)
