<?php
require 'vendor/autoload.php';

use PhpOffice\PhpWord\IOFactory;

$file = 'uploads/3d7b95d9aef535dfe65f9259c809ce3d_Network-media-new.docx';
$phpWord = IOFactory::load($file);
$writer = IOFactory::createWriter($phpWord, 'HTML');

ob_start();
$writer->save('php://output');
$html = ob_get_clean();

echo $html;
