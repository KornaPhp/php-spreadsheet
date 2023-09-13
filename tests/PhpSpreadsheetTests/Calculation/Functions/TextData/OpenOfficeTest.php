<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\TextData;

class OpenOfficeTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerOpenOffice
     */
    public function testOpenOffice(mixed $expectedResult, string $formula): void
    {
        $this->setOpenOffice();
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        $this->setCell('A1', $formula);
        $result = $sheet->getCell('A1')->getCalculatedValue();
        self::assertEquals($expectedResult, $result);
    }

    public static function providerOpenOffice(): array
    {
        return require 'tests/data/Calculation/TextData/OpenOffice.php';
    }
}
