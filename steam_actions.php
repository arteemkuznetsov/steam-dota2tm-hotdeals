<?php
function priceOverviewUrlFormer($market_hash_name) {
    $url = 'http://steamcommunity.com/market/priceoverview/?currency=5&appid=570&market_hash_name=';
    return $url.rawurlencode($market_hash_name); // + - пробел
}

function anonymize($url) {
    $anonymize = 'http://noblockme.ru/go';

    // отправка POST-запроса анонимайзеру для перехода по нужной ссылке
    $params = array(
        'url' => $url
    );
    return file_get_contents($anonymize, false, stream_context_create(array(
        'http' => array(
            'method'  => 'POST',
            'header'  => 'Content-type: application/x-www-form-urlencoded',
            'content' => http_build_query($params)
        )
    )));
}

function formatPrice($value) {
    $formatted = str_replace(',', '.', $value);
    return floatval($formatted);
}
