<?php

use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\AutoFilter\Column;
use PhpOffice\PhpSpreadsheet\Worksheet\AutoFilter\Column\Rule;

require __DIR__ . '/../Header.php';
/** @var PhpOffice\PhpSpreadsheet\Helper\Sample $helper */

// Create new Spreadsheet object
$helper->log('Create new Spreadsheet object');
$spreadsheet = new Spreadsheet();

// Set document properties
$helper->log('Set document properties');
$spreadsheet->getProperties()->setCreator('Maarten Balliauw')
    ->setLastModifiedBy('Maarten Balliauw')
    ->setTitle('PhpSpreadsheet Test Document')
    ->setSubject('PhpSpreadsheet Test Document')
    ->setDescription('Test document for PhpSpreadsheet, generated using PHP classes.')
    ->setKeywords('office PhpSpreadsheet php')
    ->setCategory('Test result file');

// Create the worksheet
$helper->log('Add data');
$spreadsheet->setActiveSheetIndex(0);
$spreadsheet->getActiveSheet()->setCellValue('A1', 'Financial Year')
    ->setCellValue('B1', 'Financial Period')
    ->setCellValue('C1', 'Country')
    ->setCellValue('D1', 'Date')
    ->setCellValue('E1', 'Sales Value')
    ->setCellValue('F1', 'Expenditure');
$dateTime = new DateTime();
$startYear = $endYear = $currentYear = (int) $dateTime->format('Y');
--$startYear;
++$endYear;

$years = range($startYear, $endYear);
$periods = range(1, 12);
$countries = [
    'United States',
    'UK',
    'France',
    'Germany',
    'Italy',
    'Spain',
    'Portugal',
    'Japan',
];

$row = 2;
foreach ($years as $year) {
    foreach ($periods as $period) {
        foreach ($countries as $country) {
            $dateString = sprintf('%04d-%02d-01T00:00:00', $year, $period);
            $dateTime = new DateTime($dateString);
            $endDays = (int) $dateTime->format('t');
            for ($i = 1; $i <= $endDays; ++$i) {
                $eDate = Date::formattedPHPToExcel(
                    $year,
                    $period,
                    $i
                );
                $value = mt_rand(500, 1000) * (1 + (mt_rand(-1, 1) / 4));
                $salesValue = $invoiceValue = null;
                $incomeOrExpenditure = mt_rand(-1, 1);
                if ($incomeOrExpenditure == -1) {
                    $expenditure = mt_rand(-1000, -500) * (1 + (mt_rand(-1, 1) / 4));
                    $income = null;
                } elseif ($incomeOrExpenditure == 1) {
                    $expenditure = mt_rand(-1000, -500) * (1 + (mt_rand(-1, 1) / 4));
                    $income = mt_rand(500, 1000) * (1 + (mt_rand(-1, 1) / 4));
                } else {
                    $expenditure = null;
                    $income = mt_rand(500, 1000) * (1 + (mt_rand(-1, 1) / 4));
                }
                $dataArray = [$year,
                    $period,
                    $country,
                    $eDate,
                    $income,
                    $expenditure,
                ];
                $spreadsheet->getActiveSheet()->fromArray($dataArray, null, 'A' . $row++);
            }
        }
    }
}
--$row;

// Set styling
$helper->log('Set styling');
$spreadsheet->getActiveSheet()->getStyle('A1:F1')->getFont()->setBold(true);
$spreadsheet->getActiveSheet()->getStyle('A1:F1')->getAlignment()->setWrapText(true);
$spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(12.5);
$spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(10.5);
$spreadsheet->getActiveSheet()->getStyle('D2:D' . $row)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_DATE_YYYYMMDD);
$spreadsheet->getActiveSheet()->getStyle('E2:F' . $row)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_CURRENCY_USD_INTEGER);
$spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(14);
$spreadsheet->getActiveSheet()->freezePane('A2');

$helper->displayGrid($spreadsheet->getActiveSheet()->toArray(null, false, true, true));

// Set autofilter range
$filterRange = $spreadsheet->getActiveSheet()->calculateWorksheetDimension();
$helper->log("Set autofilter for cells {$filterRange}");
// Always include the complete filter range if you can!
// Excel does support setting only the caption row, but that's not a best practise...
$spreadsheet->getActiveSheet()->setAutoFilter($filterRange);

// Set active filters
$autoFilter = $spreadsheet->getActiveSheet()->getAutoFilter();
$helper->log('Set active filters');

// Filter the Country column on a filter value of countries beginning with the letter U (or Japan)
//     We use * as a wildcard, so specify as U* and using a wildcard requires customFilter
$autoFilter->getColumn('C')
    ->setFilterType(Column::AUTOFILTER_FILTERTYPE_CUSTOMFILTER)
    ->createRule()
    ->setRule(Rule::AUTOFILTER_COLUMN_RULE_EQUAL, 'u*')
    ->setRuleType(Rule::AUTOFILTER_RULETYPE_CUSTOMFILTER);

$helper->log('Set country code filter (Column C) to countries beginning with "U" ("United States" and "UK")');

$autoFilter->getColumn('C')
    ->createRule()
    ->setRule(Rule::AUTOFILTER_COLUMN_RULE_EQUAL, 'japan')
    ->setRuleType(Rule::AUTOFILTER_RULETYPE_CUSTOMFILTER);

$helper->log('Add "Japan" to the country code filter (Column C)');

// Filter the Date column on a filter value of the last day of every period of the current year
// We use a dateGroup ruletype for this, although it is still a standard filter
foreach ($periods as $period) {
    $dateString = sprintf('%04d-%02d-01T00:00:00', $currentYear, $period);
    $dateTime = new DateTime($dateString);
    $endDate = (int) $dateTime->format('t');

    $autoFilter->getColumn('D')
        ->setFilterType(Column::AUTOFILTER_FILTERTYPE_FILTER)
        ->createRule()
        ->setRule(
            Rule::AUTOFILTER_COLUMN_RULE_EQUAL,
            [
                'year' => $currentYear,
                'month' => $period,
                'day' => $endDate,
            ]
        )
        ->setRuleType(Rule::AUTOFILTER_RULETYPE_DATEGROUP);
}

$helper->log('Add filter on the Date (Column D) to display only rows for the last day of each month');

// Display only sales values that are blank
//     Standard filter, operator equals, and value of NULL or empty space
$autoFilter->getColumn('E')
    ->setFilterType(Column::AUTOFILTER_FILTERTYPE_FILTER)
    ->createRule()
    ->setRule(Rule::AUTOFILTER_COLUMN_RULE_EQUAL, '');

$helper->log('Add filter on Sales Values (Column E) to display only blank values');

$helper->log('NOTE: We don\'t apply the filter rules in this example, so we can\'t see the result here; although Excel will apply the rules when the file is loaded');
$helper->log('See 10_Autofilter_selection_display.php for an example that actually executes the filter rules');

// Save
$helper->write($spreadsheet, __FILE__);
