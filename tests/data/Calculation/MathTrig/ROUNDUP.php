<?php

declare(strict_types=1);

return [
    [0, '0,2'],
    [663, '662.79, 0'],
    [662.8, '662.79,1'],
    [60, '54.1,-1'],
    [60, '55.1,-1'],
    [-23.7, '-23.67,1'],
    [4, '3.2,0'],
    [4, '3.2,0.01'],
    [77, '76.9,0'],
    [3.142, '3.14159,3'],
    [-3.2, '-3.14159,1'],
    [31500, '31415.92654,"-2"'],
    [31420, '31415.92654,-1'],
    [4.44, '4.4400,2'],
    [5.20, '2.26 + 2.94, 2'],
    [-4.44, '-4.4400,2'],
    [-5.20, '-2.26 - 2.94, 2'],
    ['#VALUE!', '"ABC",1'],
    ['#VALUE!', '1.234,"ABC"'],
    [0, ', 0'],
    [0, 'false, 0'],
    [1, 'true, 0'],
    ['#VALUE!', '"", 0'],
    [2, 'A2, 0'],
    [3, 'A3, 0'],
    [-4, 'A4, 0'],
    [-6, 'A5, 0'],
    [0, 'B1, 0'],
    ['exception', ''],
    ['exception', '35.51'],
];
