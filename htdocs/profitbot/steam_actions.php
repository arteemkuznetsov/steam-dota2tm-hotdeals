<?php
$cameleo = 'https://cameleo.xyz/r';
$noblockme = 'http://noblockme.ru/go';

function priceOverviewUrlFormer($market_hash_name) {
    $url = 'http://steamcommunity.com/market/priceoverview/?currency=5&appid=570&market_hash_name=';
    return $url.rawurlencode($market_hash_name); // + - пробел
}

function anonymize($url, $anonymizer) {
    $ch = curl_init($anonymizer);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, 'url='.urlencode($url));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    $html = curl_exec($ch);
    curl_close($ch);
    return $html;
}

function formatPrice($value) {
    $formatted = str_replace(',', '.', $value);
    return floatval($formatted);
}
