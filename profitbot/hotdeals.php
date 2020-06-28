<?php
include 'market_actions.php';
include 'steam_actions.php';
include 'db_connect.php';

global $config;
$profit = $config['profit'];

function price_cmp($market_price, $steam_price)
{
    global $profit;
    $commission = 1.15;

    $expected_price = $market_price * $profit * $commission;
    if ($steam_price >= $expected_price) return true;
    else return false;
}

function getSteamPriceFromDB($market_hash_name)
{
    global $conn;
    $steam_price = 0;

    if (!$conn->connect_error) {
        $sql = "SELECT * FROM prices WHERE market_hash_name = '" . $market_hash_name . "'";
        $result = mysqli_query($conn, $sql);
        while ($row = mysqli_fetch_array($result)) {
            $steam_price = $row['price'];
        }
    } else {
        die("Connection failed: " . $conn->connect_error);
    }
    return $steam_price;
}

function hotDeals($market_items)
{
    $json = [];
    $counter = 0;
    foreach ($market_items as $market_item) {
        // больше +-20 проверок без перерывов осилить невозможно, потому что стим даст бан по ip
        // поэтому максимум - 20 выгодных предложений
        if ($counter >= 20) break;
        if ($market_item['price'] != 0) {
            $market_id = $market_item['key'];
            $market_hash_name = $market_item['market_hash_name'];
            $market_price = $market_item['price'];
            $steam_db_price = getSteamPriceFromDB($market_hash_name);

            // если такой предмет нашелся в нашей стим-БД
            if ($steam_db_price != null) {
                $cmp_result = price_cmp($market_price, $steam_db_price);
                // если предложение выгодное
                if ($cmp_result) {
                    // проверяем еще раз делая запрос в стим
                    $answer = anonymize(priceOverviewUrlFormer($market_hash_name));
                    $answer = json_decode($answer, true);
                    $steam_actual_price = formatPrice($answer['lowest_price']);

                    $is_really_profitable = price_cmp($market_price, $steam_actual_price);
                    if ($is_really_profitable) {
                        $json[] = array(
                            'market_id' => $market_id,
                            'market_hash_name' => $market_hash_name,
                            'market_price' => $market_price,
                            'steam_price' => $steam_actual_price,
                            'profit_percent' => round(($steam_actual_price / $market_price / 1.15 - 1) * 100, 2)
                        );
                        $counter++;
                    }
                    //sleep(20);
                }
            }

        }
    }
    return $json;
}
set_time_limit(0);
error_reporting(0);

$market_items = getAndFilterMarketItems();
$json = hotDeals($market_items);
$conn->close();

echo json_encode($json, JSON_UNESCAPED_UNICODE);
