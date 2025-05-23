<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Engineering\ConvertOctal;
use PhpOffice\PhpSpreadsheet\Calculation\Exception as CalculationException;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\Information\ExcelError;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheetTests\Calculation\Functions\FormulaArguments;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class Oct2HexTest extends TestCase
{
    private string $compatibilityMode;

    protected function setUp(): void
    {
        $this->compatibilityMode = Functions::getCompatibilityMode();
    }

    protected function tearDown(): void
    {
        Functions::setCompatibilityMode($this->compatibilityMode);
    }

    #[DataProvider('providerOCT2HEX')]
    public function testDirectCallToOCT2HEX(mixed $expectedResult, bool|float|int|string $value, ?int $digits = null): void
    {
        $result = ($digits === null) ? ConvertOctal::toHex($value) : ConvertOctal::toHex($value, $digits);
        self::assertSame($expectedResult, $result);
    }

    private function trimIfQuoted(string $value): string
    {
        return trim($value, '"');
    }

    #[DataProvider('providerOCT2HEX')]
    public function testOCT2HEXAsFormula(mixed $expectedResult, mixed ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $calculation = Calculation::getInstance();
        $formula = "=OCT2HEX({$arguments})";

        /** @var float|int|string */
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertSame($expectedResult, $this->trimIfQuoted((string) $result));
    }

    #[DataProvider('providerOCT2HEX')]
    public function testOCT2HEXInWorksheet(mixed $expectedResult, mixed ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        $argumentCells = $arguments->populateWorksheet($worksheet);
        $formula = "=OCT2HEX({$argumentCells})";

        $result = $worksheet->setCellValue('A1', $formula)
            ->getCell('A1')
            ->getCalculatedValue();
        self::assertSame($expectedResult, $result);

        $spreadsheet->disconnectWorksheets();
    }

    public static function providerOCT2HEX(): array
    {
        return require 'tests/data/Calculation/Engineering/OCT2HEX.php';
    }

    #[DataProvider('providerUnhappyOCT2HEX')]
    public function testOCT2HEXUnhappyPath(string $expectedException, mixed ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        $argumentCells = $arguments->populateWorksheet($worksheet);
        $formula = "=OCT2HEX({$argumentCells})";

        $this->expectException(CalculationException::class);
        $this->expectExceptionMessage($expectedException);
        $worksheet->setCellValue('A1', $formula)
            ->getCell('A1')
            ->getCalculatedValue();

        $spreadsheet->disconnectWorksheets();
    }

    public static function providerUnhappyOCT2HEX(): array
    {
        return [
            ['Formula Error: Wrong number of arguments for OCT2HEX() function'],
        ];
    }

    #[DataProvider('providerOCT2HEXOds')]
    public function testOCT2HEXOds(mixed $expectedResult, bool|float|int|string $value, ?int $digits = null): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_OPENOFFICE);

        $result = ($digits === null) ? ConvertOctal::toHex($value) : ConvertOctal::toHex($value, $digits);
        self::assertSame($expectedResult, $result);
    }

    public static function providerOCT2HEXOds(): array
    {
        return require 'tests/data/Calculation/Engineering/OCT2HEXOpenOffice.php';
    }

    public function testOCT2HEXFrac(): void
    {
        $calculation = Calculation::getInstance();
        $formula = '=OCT2HEX(20.1)';

        Functions::setCompatibilityMode(Functions::COMPATIBILITY_GNUMERIC);
        /** @var float|int|string */
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertSame('10', $this->trimIfQuoted((string) $result), 'Gnumeric');

        Functions::setCompatibilityMode(Functions::COMPATIBILITY_OPENOFFICE);
        /** @var float|int|string */
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertSame(ExcelError::NAN(), $this->trimIfQuoted((string) $result), 'OpenOffice');

        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
        /** @var float|int|string */
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertSame(ExcelError::NAN(), $this->trimIfQuoted((string) $result), 'Excel');
    }

    /** @param mixed[] $expectedResult */
    #[DataProvider('providerOct2HexArray')]
    public function testOct2HexArray(array $expectedResult, string $value): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=OCT2HEX({$value})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEquals($expectedResult, $result);
    }

    public static function providerOct2HexArray(): array
    {
        return [
            'row/column vector' => [
                [['4', '7', '3F', '99', 'CC', '155']],
                '{"4", "7", "77", "231", "314", "525"}',
            ],
        ];
    }
}
