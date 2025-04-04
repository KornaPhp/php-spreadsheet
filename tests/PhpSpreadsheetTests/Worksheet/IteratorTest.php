<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Worksheet;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Iterator;
use PHPUnit\Framework\TestCase;

class IteratorTest extends TestCase
{
    public function testIteratorFullRange(): void
    {
        $spreadsheet = new Spreadsheet();
        $spreadsheet->createSheet();
        $spreadsheet->createSheet();

        $iterator = new Iterator($spreadsheet);
        $columnIndexResult = 0;
        self::assertEquals($columnIndexResult, $iterator->key());

        foreach ($iterator as $key => $column) {
            self::assertEquals($columnIndexResult++, $key);
        }
        self::assertSame(3, $columnIndexResult);
    }
}
