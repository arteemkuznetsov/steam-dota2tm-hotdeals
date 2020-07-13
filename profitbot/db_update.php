<?php
include 'market_actions.php';
include 'steam_actions.php';
include 'db_connect.php';

$items = getAndFilterMarketItems();
$items = array_reverse($items, true); // идем от самых дорогих к самым дешевым вещам

if (!$conn->connect_error) {
    // снимаем ограничение на время выполнения скрипта (120 с), по истечении которого он прекращается по Fatal Error
    set_time_limit(0);
    // очищаем таблицу перед перезаписью, сбрасывая serial-последовательность
    mysqli_query($conn, 'TRUNCATE prices;');

    global $cameleo, $noblockme;
    foreach ($items as $item) {
        $answer = anonymize(priceOverviewUrlFormer($item['market_hash_name']), $cameleo);
        $answer = json_decode($answer, true);
        $steam_price = formatPrice($answer['lowest_price']);
        // если не успело получить ответ или начало возвращаться 429 то перескакиваем на другой анонимайзер
        if ($steam_price == 0) {
            $answer = anonymize(priceOverviewUrlFormer($item['market_hash_name']), $noblockme);
            $answer = json_decode($answer, true);
            $steam_price = formatPrice($answer['lowest_price']);
        }
        mysqli_query($conn,
            "INSERT INTO prices (market_hash_name, price) VALUES
                ('" . $item['market_hash_name'] . "', " . $steam_price . " );"
        );
        // во избежание 429 Too Many Requests.
        // кроме того, за это время обычно успевает обработаться ответ, и в таблицу БД записывается НЕ 0
        sleep(1);
    }
} else {
    die("Connection failed: " . $conn->connect_error);
}
$conn->close();
