<?php
include 'market_actions.php';
include 'steam_actions.php';
include 'db_connect.php';

$items = getAndFilterMarketItems();
$length = sizeof($items);
$items = array_reverse($items, true); // идем от самых дорогих к самым дешевым вещам
insert();

function insert()
{
    global $items, $conn;
    if (!$conn->connect_error) {
        // снимаем ограничение на время выполнения скрипта (120 с), по истечении которого он прекращается по Fatal Error
        set_time_limit(0);
        // очищаем таблицу перед перезаписью, сбрасывая serial-последовательность
        mysqli_query($conn, 'TRUNCATE prices;');
        foreach ($items as $item) {
            $answer = file_get_contents(priceOverviewUrlFormer($item['market_hash_name'])); // 'file_get_contents(' заменяется на 'anonymize('
            $answer = json_decode($answer, true);
            $steam_price = formatPrice($answer['lowest_price']);
            mysqli_query($conn,
                "INSERT INTO prices (market_hash_name, price) VALUES
                ('" . $item['market_hash_name'] . "', " . $steam_price . " );"
            );
            // случайная задержка - имитация реального человека
            sleep(rand(20, 30));
        }
    } else {
        die("Connection failed: " . $conn->connect_error);
    }
    $conn->close();
}

$json = ['items_length' => $length];
echo json_encode($json,JSON_UNESCAPED_UNICODE);
