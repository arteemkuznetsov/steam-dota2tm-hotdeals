<?php
include 'multidim_utils.php';

function getAndFilterMarketItems () {
    $filtered_items = [];
    $json = json_decode(
        file_get_contents('https://market.dota2.net/api/v2/prices/class_instance/RUB.json'),
        true);
    $items = $json['items'];
    foreach ($items as $key => $item) {
        if ($item['price'] >= 7 &&
            $item['price'] <= 75 &&
            $item['popularity_7d'] >= 70) {
            $row = [];
            $row['key'] = $key;
            $row['market_hash_name'] = $item['market_hash_name'];
            $row['price'] = $item['price'];
            $filtered_items[] = $row;
        }
    }
    // сортируем по возрастанию цены
    usort($filtered_items, function ($element1, $element2) {
        return $element1['price'] - $element2['price'];
    });
    $filtered_items = unique_multidim_array($filtered_items, 'market_hash_name');
    return $filtered_items;
}
