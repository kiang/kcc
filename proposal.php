<?php

$path = __DIR__;
$cacheFolder = $path . '/cache';
$listFolder = $cacheFolder . '/proposals_list';
$itemFolder = $cacheFolder . '/proposals_item';
$publicFolder = $path . '/proposals';
if (!file_exists($listFolder)) {
    mkdir($listFolder, 0777, true);
}
if (!file_exists($itemFolder)) {
    mkdir($itemFolder, 0777, true);
}

$url = 'http://cissearch.kcc.gov.tw/System/Proposal/Default.aspx';
$listContent = file_get_contents($url);
file_put_contents($listFolder . '/test1', $listContent);
$data = getDataSkel($listContent);
$data['ctl00$ContentPlaceHolder1$rblState'] = '';
$data['ctl00$ContentPlaceHolder1$ddlProposalKind'] = '0';
$data['ctl00$ContentPlaceHolder1$uscPeriodSessionMeeting$ddlPeriod'] = '07';
$data['ctl00$ContentPlaceHolder1$uscPeriodSessionMeeting$ddlSession'] = '0701';
$data['ctl00$ContentPlaceHolder1$uscPeriodSessionMeeting$ddlMeeting'] = '07010003';
file_put_contents($listFolder . '/test2', postPage($url, $data));

function postPage($url, $data) {
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $server_output = curl_exec($ch);

    curl_close($ch);

    return $server_output;
}

function getDataSkel($text) {
    $data = array();
    $pos = strpos($text, 'name="');
    while (false !== $pos) {
        $pos += 6;
        $posEnd = strpos($text, '"', $pos);
        $fieldName = substr($text, $pos, $posEnd - $pos);
        $value = '';
        $valueTmp = explode('value="', substr($text, $pos, strpos($text, '>', $posEnd) - $pos));
        if (count($valueTmp) === 2) {
            $value = substr($valueTmp[1], 0, strpos($valueTmp[1], '"'));
        }
        $data[$fieldName] = $value;
        $pos = strpos($text, 'name="', $pos);
    }
    return $data;
}
