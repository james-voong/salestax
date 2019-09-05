<?php

define('BASIC_TAX', 1.10);
define('IMPORT_DUTY', 1.05);
define('IMPORT_AND_BASIC_TAX', 1.15);



if (count($argv) < 2) {
    die("No input file given. Exiting.");
}

if (!file_exists($argv[1])) {
    die("File does not exist. Exiting.");
}

$inputs = file($argv[1]);

foreach ($inputs as $inputItem) {

    $processItem = preg_split('/\s+/', $inputItem, 2);
    $receiptItem['quantity'] = $processItem[0];

    $processItem = explode(' at ', $processItem[1], 2);
    $receiptItem['product'] = trim($processItem[0]);
    $receiptItem['price'] = trim($processItem[1]);

    $receiptItems[] = $receiptItem;
}

$receipt = printReceipt($receiptItems);

function printReceipt(array $receiptItems) {
    // This could be replaced by a library of words
    $exemptions = ['book', 'headache', 'chocolates', 'chocolate'];

    $totalBeforeTax = 0;
    $totalAfterTax = 0;

    foreach ($receiptItems as $key => $receiptItem) {
        $totalBeforeTax += $receiptItem['price'];

        $productKeywords = preg_split('/\s+/', $receiptItem['product']);

        if (!array_intersect($exemptions, $productKeywords)) {
            if (in_array('imported', $productKeywords)) {
                $afterTax = $receiptItem['price'] * IMPORT_AND_BASIC_TAX;
            } else {
                $afterTax = $receiptItem['price'] * BASIC_TAX;
            }
        } else if (in_array('imported', $productKeywords)) {
            $afterTax = $receiptItem['price'] * IMPORT_DUTY;
        } else {
            $afterTax = $receiptItem['price'];
        }

        $afterTax = round($afterTax, 2, PHP_ROUND_HALF_UP);
        $totalAfterTax += $afterTax;
        $salesTax = $totalAfterTax - $totalBeforeTax;

        // TODO fix the modulo divide by error. Maybe fmod?
        if ($salesTax != 0 || $salesTax % 0.05) {
            // TODO round upwards decimals ending with .06 and .07
            $difference = round($salesTax * 20) / 20 - $salesTax;
            $salesTax += round($difference, 2);
            $afterTax += round($difference, 2);
            $totalAfterTax += round($difference, 2);
        }
        print($receiptItem['quantity']." ".$receiptItem['product'].": ".number_format($afterTax, 2)."\n");
    }
    print("Sales Taxes: ".number_format($salesTax, 2)."\n");
    print("Total: ".number_format($totalAfterTax,2)."\n");
}
