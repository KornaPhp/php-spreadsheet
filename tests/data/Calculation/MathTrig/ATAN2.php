<?php

declare(strict_types=1);

// x_num, y_num, Result

return [
    ['#DIV/0!', '0, 0'],
    [M_PI_4, '1, 1'],
    [-3 * M_PI_4, '-1, -1'],
    [3 * M_PI_4, '-1, 1'],
    [-M_PI_4, '1, -1'],
    [1.107148717, '0.5, 1'],
    [1.815774989, '-0.5, 2'],
    [0.674740942, '1, 0.8'],
    [-0.643501109, '0.8, -0.6'],
    [-1.460139105, '1, -9'],
    [0.0, '0.2, 0'],
    [1.107148718, '0.1, 0.2'],
    [M_PI_2, '0, 0.2'],
    ['#VALUE!', '"A", 0.2'],
    ['#VALUE!', '0.2, "A"'],
    [M_PI_4, 'true, 1'],
    [-M_PI_2, 'false, -2.5'],
    ['exception', ''],
    ['exception', '1'],
    [0.876058051, 'A2, A3'],
];
