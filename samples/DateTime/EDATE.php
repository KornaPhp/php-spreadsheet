<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;

require __DIR__ . '/../Header.php';
/** @var PhpOffice\PhpSpreadsheet\Helper\Sample $helper */
$category = 'Date/Time';
$functionName = 'EDATE';
$description = 'Returns the serial number that represents the date that is the indicated number of months before or after a specified date';

$helper->titles($category, $functionName, $description);

// Create new PhpSpreadsheet object
$spreadsheet = new Spreadsheet();
$worksheet = $spreadsheet->getActiveSheet();

$months = range(-12, 12);
$testDateCount = count($months);

for ($row = 1; $row <= $testDateCount; ++$row) {
    $worksheet->setCellValue('A' . $row, '=DATE(2020,12,31)');
    $worksheet->setCellValue('B' . $row, '=A' . $row);
    $worksheet->setCellValue('C' . $row, $months[$row - 1]);
    $worksheet->setCellValue('D' . $row, '=EDATE(B' . $row . ', C' . $row . ')');
}
$worksheet->getStyle('B1:B' . $testDateCount)
    ->getNumberFormat()
    ->setFormatCode('yyyy-mm-dd');

$worksheet->getStyle('D1:D' . $testDateCount)
    ->getNumberFormat()
    ->setFormatCode('yyyy-mm-dd');

// Test the formulae
for ($row = 1; $row <= $testDateCount; ++$row) {
    /** @var null|bool|float|int|string */
    $calc = $worksheet->getCell('D' . $row)->getCalculatedValue();
    $helper->log(
        $worksheet->getCell('B' . $row)->getFormattedValue()
        . ' and '
        . $worksheet->getCell('C' . $row)->getFormattedValue()
        . ' months is '
        . $worksheet->getCell('D' . $row)->getCalculatedValueString()
        . ' ('
        . $worksheet->getCell('D' . $row)->getFormattedValue()
        . ')'
    );
}
