<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Information;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Information\Value;
use PHPUnit\Framework\TestCase;

class IsNumberTest extends TestCase
{
    public function testIsNumberNoArgument(): void
    {
        $result = Value::isNumber();
        self::assertFalse($result);
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('providerIsNumber')]
    public function testIsNumber(bool $expectedResult, mixed $value): void
    {
        $result = Value::isNumber($value);
        self::assertEquals($expectedResult, $result);
    }

    public static function providerIsNumber(): array
    {
        return require 'tests/data/Calculation/Information/IS_NUMBER.php';
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('providerIsNumberArray')]
    public function testIsNumberArray(array $expectedResult, string $values): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=ISNUMBER({$values})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEquals($expectedResult, $result);
    }

    public static function providerIsNumberArray(): array
    {
        return [
            'vector' => [
                [[true, false, false, false, true]],
                '{-2, "PHP", "123.456", false, 2.34}',
            ],
        ];
    }
}
