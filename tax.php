<?php

// Does not apply to books, food and medical products
define('BASICTAX', 0.10);

// Applies to all imported goods
define('IMPORTDUTY', 0.05);

if (!file_exists($argv[1])) {
    die("File does not exist. Exiting.");
}

$inputs = file($argv[1]);
$receiptItems = array();

foreach ($inputs as $inputItem) {
    $receiptItem = new receiptItem;

    $processItem = preg_split('/\s+/', $inputItem, 2);
    $receiptItem->quantity = $processItem[0];

    $processItem = explode(' at ', $processItem[1], 2);
    $receiptItem->product = trim($processItem[0]);
    $receiptItem->price = trim($processItem[1]);

    $receiptItems[] = $receiptItem;
}
var_dump($receiptItems);

class receiptItem {

}
