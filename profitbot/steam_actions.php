<?php
function priceOverviewUrlFormer($market_hash_name) {
    $url = 'http://steamcommunity.com/market/priceoverview/?currency=5&appid=570&market_hash_name=';
    return $url.rawurlencode($market_hash_name); // + - пробел
}

function anonymize($url) {
    $params = array(
        'url' => $url
    );
    $ch = curl_init('https://jahproxy.pro/index.php');
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
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
