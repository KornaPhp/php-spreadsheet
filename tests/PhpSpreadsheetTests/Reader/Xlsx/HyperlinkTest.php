<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xlsx;

use PhpOffice\PhpSpreadsheet\NamedRange;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;

class HyperlinkTest extends AbstractFunctional
{
    public function testReadAndWriteHyperlinks(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet1 = $spreadsheet->getActiveSheet();
        $sheet1->setTitle('Sheet One');
        $sheet1->getCell('A1')->setValue(100);
        $sheet1->getCell('B1')->setValue('this is b1');
        $spreadsheet->addNamedRange(new NamedRange('namedb1', $sheet1, '$B$1'));
        $sheet1->setCellValue('A2', 'link to same sheet');
        $sheet1->getCell('A2')->getHyperlink()
            ->setUrl("sheet://'Sheet One'!A1");
        $sheet1->getStyle('A2')->getFont()->setHyperlinkTheme();
        $sheet1->setCellValue('A3', 'link to defined name');
        $sheet1->getCell('A3')->getHyperlink()
            ->setUrl('sheet://namedb1');
        $sheet1->getStyle('A3')->getFont()->setHyperlinkTheme();

        $sheet2 = $spreadsheet->createSheet();
        $sheet2->setTitle('Sheet Two');
        $sheet2->setCellValue('A2', 'link to other sheet');
        $sheet2->getCell('A2')->getHyperlink()
            ->setUrl("sheet://'Sheet One'!A1");
        $sheet2->getStyle('A2')->getFont()->setHyperlinkTheme();
        $sheet2->setCellValue('A3', 'external link');
        $sheet2->getCell('A3')->getHyperlink()
            ->setUrl('https://www.example.com');
        $sheet2->getStyle('A3')->getFont()->setHyperlinkTheme();
        $sheet2->setCellValue('A4', 'external link with anchor');
        $sheet2->getCell('A4')->getHyperlink()
            ->setUrl('https://www.example.com#anchor');
        $sheet2->getCell('A4')->getHyperlink()->setTooltip('go to anchor tag on example.com');
        $sheet2->getStyle('A4')->getFont()->setHyperlinkTheme();

        $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, 'Xlsx');
        $spreadsheet->disconnectWorksheets();
        $rsheet1 = $reloadedSpreadsheet->getSheet(0);
        self::assertSame('link to same sheet', $rsheet1->getCell('A2')->getValue());
        self::assertSame("sheet://'Sheet One'!A1", $rsheet1->getCell('A2')->getHyperlink()->getUrl());

        self::assertSame('link to defined name', $rsheet1->getCell('A3')->getValue());
        self::assertSame('sheet://namedb1', $rsheet1->getCell('A3')->getHyperlink()->getUrl());

        $rsheet2 = $reloadedSpreadsheet->getSheet(1);
        self::assertSame('link to other sheet', $rsheet2->getCell('A2')->getValue());
        self::assertSame("sheet://'Sheet One'!A1", $rsheet2->getCell('A2')->getHyperlink()->getUrl());
         self::assertSame(
             'FF0000FF',
             $rsheet2
                 ->getStyle('A2')
                 ->getFont()->getColor()->getARGB(),
             'argb is set in addition to theme'
         );

        self::assertSame('external link', $rsheet2->getCell('A3')->getValue());
        self::assertSame('https://www.example.com', $rsheet2->getCell('A3')->getHyperlink()->getUrl());

        self::assertSame('https://www.example.com#anchor', $rsheet2->getCell('A4')->getHyperlink()->getUrl());
        self::assertSame('external link with anchor', $rsheet2->getCell('A4')->getValue());
        self::assertSame('go to anchor tag on example.com', $rsheet2->getCell('A4')->getHyperlink()->getToolTip());

        $testCells = [
            [0, 'A2'],
            [0, 'A3'],
            [0, 'A2'],
            [1, 'A3'],
            [1, 'A4'],
        ];
        foreach ($testCells as $sheetAndCell) {
            [$sheetIndex, $cell] = $sheetAndCell;
            $rsheet = $reloadedSpreadsheet->getSheet($sheetIndex);
            self::assertSame(
                10,
                $rsheet->getStyle($cell)
                    ->getFont()->getColor()->getTheme(),
                "theme sheet $sheetIndex cell $cell"
            );
            self::assertSame(
                'single',
                $rsheet->getStyle('A2')->getFont()->getUnderline(),
                "underline sheet $sheetIndex cell $cell"
            );
        }

        $reloadedSpreadsheet->disconnectWorksheets();
    }
}
