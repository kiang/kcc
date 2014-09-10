<?php

$path = __DIR__;
$cacheFolder = $path . '/cache';
$itemFolder = $cacheFolder . '/MeetingRecord';
$targetFolder = $path . '/MeetingRecord';
if (!file_exists($itemFolder)) {
    mkdir($itemFolder, 0777, true);
}
if (!file_exists($targetFolder)) {
    mkdir($targetFolder, 0777, true);
}

foreach (glob($itemFolder . '/*.pdf') AS $pdfFile) {
    $pdfFile = str_replace(array('(', ')', ' '), array('\\(', '\\)', '\\ '), $pdfFile);
    $md5 = md5($pdfFile);
    exec("java -cp /usr/share/java/commons-logging.jar:/usr/share/java/fontbox.jar:/usr/share/java/pdfbox.jar org.apache.pdfbox.PDFBox ExtractText {$pdfFile} tmp.txt");
    if (file_exists('tmp.txt')) {
        $fh = fopen('tmp.txt', 'r');
        while ($line = fgets($fh, 1024)) {
            if (false !== strpos($line, '會議紀錄') && false !== strpos($line, '高雄市議會')) {
                $line = str_replace(array(' '), array(''), $line);
                copy('tmp.txt', "{$targetFolder}/{$line}.txt");
                unlink('tmp.txt');
                break;
            }
        }
        fclose($fh);
    }
}