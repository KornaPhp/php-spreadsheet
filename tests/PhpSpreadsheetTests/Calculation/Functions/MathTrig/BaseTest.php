<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;

class BaseTest extends AllSetupTeardown
{
    #[\PHPUnit\Framework\Attributes\DataProvider('providerBASE')]
    public function testBASE(mixed $expectedResult, mixed $arg1 = 'omitted', mixed $arg2 = 'omitted', mixed $arg3 = 'omitted'): void
    {
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        if ($arg1 !== null) {
            $sheet->getCell('A1')->setValue($arg1);
        }
        if ($arg2 !== null) {
            $sheet->getCell('A2')->setValue($arg2);
        }
        if ($arg3 !== null) {
            $sheet->getCell('A3')->setValue($arg3);
        }
        if ($arg1 === 'omitted') {
            $sheet->getCell('B1')->setValue('=BASE()');
        } elseif ($arg2 === 'omitted') {
            $sheet->getCell('B1')->setValue('=BASE(A1)');
        } elseif ($arg3 === 'omitted') {
            $sheet->getCell('B1')->setValue('=BASE(A1, A2)');
        } else {
            $sheet->getCell('B1')->setValue('=BASE(A1, A2, A3)');
        }
        $result = $sheet->getCell('B1')->getCalculatedValue();
        self::assertEquals($expectedResult, $result);
    }

    public static function providerBASE(): array
    {
        return require 'tests/data/Calculation/MathTrig/BASE.php';
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('providerBaseArray')]
    public function testBaseArray(array $expectedResult, string $argument1, string $argument2): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=BASE({$argument1}, {$argument2})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-14);
    }

    public static function providerBaseArray(): array
    {
        return [
            'matrix' => [[['1111111', '177'], ['127', '7F']], '127', '{2, 8; 10, 16}'],
        ];
    }
}
