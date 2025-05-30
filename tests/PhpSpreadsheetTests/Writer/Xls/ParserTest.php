<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Writer\Xls;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Exception as WriterException;
use PhpOffice\PhpSpreadsheet\Writer\Xls\Parser;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class ParserTest extends TestCase
{
    private ?Spreadsheet $spreadsheet = null;

    protected function tearDown(): void
    {
        if ($this->spreadsheet !== null) {
            $this->spreadsheet->disconnectWorksheets();
            $this->spreadsheet = null;
        }
    }

    public function testNonArray(): void
    {
        $this->expectException(WriterException::class);
        $this->expectExceptionMessage('Unexpected non-array');
        $this->spreadsheet = new Spreadsheet();
        $parser = new Parser($this->spreadsheet);
        $parser->toReversePolish();
    }

    public function testMissingIndex(): void
    {
        $this->expectException(WriterException::class);
        $this->expectExceptionMessage('Unexpected non-array');
        $this->spreadsheet = new Spreadsheet();
        $parser = new Parser($this->spreadsheet);
        $parser->toReversePolish(['left' => 0]);
    }

    public function testParseError(): void
    {
        $this->expectException(WriterException::class);
        $this->expectExceptionMessage('Unknown token +');
        $this->spreadsheet = new Spreadsheet();
        $parser = new Parser($this->spreadsheet);
        $parser->toReversePolish(['left' => 1, 'right' => 2, 'value' => '+']);
    }

    public function testGoodParse(): void
    {
        $this->spreadsheet = new Spreadsheet();
        $parser = new Parser($this->spreadsheet);
        self::assertSame('1e01001e02001e0300', bin2hex($parser->toReversePolish(['left' => 1, 'right' => 2, 'value' => 3])));
    }

    #[DataProvider('cellSheetnameQuotedProvider')]
    public function testCellSheetnameQuoted(bool $expected, string $address): void
    {
        self::assertSame($expected, Parser::matchCellSheetnameQuoted($address));
    }

    public static function cellSheetnameQuotedProvider(): array
    {
        return [
            [true, '\'TS GK Mustermann Hans 2\'!$N$1'],
            [true, '\'TS GK Mustermann Hans 2\'!N15'],
            [true, '\'TS GK Mus\'\'termann Hans 2\'!N15'],
            [false, '\'TS GK Mus\'termann Hans 2\'!N15'],
            [false, '\'TS GK Mustermann Hans 2\'!N15:P16'],
            [false, '\'TS GK Mustermann Hans 2\'!$N$15:$P$16'],
            [false, 'sheet1!N15'],
            [false, 'sheet1!N15:P16'],
            [false, 'N15'],
            [false, 'N15:P16'],
        ];
    }

    #[DataProvider('rangeSheetnameQuotedProvider')]
    public function testRangeSheetnameQuoted(bool $expected, string $address): void
    {
        self::assertSame($expected, Parser::matchRangeSheetnameQuoted($address));
    }

    public static function rangeSheetnameQuotedProvider(): array
    {
        return [
            [false, '\'TS GK Mustermann Hans 2\'!$N$1'],
            [false, '\'TS GK Mustermann Hans 2\'!N15'],
            [false, '\'TS GK Mus\'\'termann Hans 2\'!N15'],
            [false, '\'TS GK Mus\'termann Hans 2\'!N15'],
            [true, '\'TS GK Mustermann Hans 2\'!N15:P16'],
            [true, '\'TS GK Mustermann Hans 2\'!$N$15:$P$16'],
            [false, 'sheet1!N15'],
            [false, 'sheet1!N15:P16'],
            [false, 'N15'],
            [false, 'N15:P16'],
        ];
    }
}
