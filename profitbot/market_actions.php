<?php
include 'multidim_utils.php';

$config = json_decode(file_get_contents('config.json'), true);

$lower_edge = $config['lower_edge'];
$upper_edge = $config['upper_edge'];
$popularity_7d = $config['popularity_7d'];

function getAndFilterMarketItems () {
    global $stop_list,
           $lower_edge,
           $upper_edge,
           $popularity_7d;
    $url = 'https://market.dota2.net/api/v2/prices/class_instance/RUB.json';
    $filtered_items = [];
    $json = json_decode(file_get_contents($url),true);
    $items = $json['items'];
    foreach ($items as $key => $item) {
        if ($item['price'] >= $lower_edge &&
            $item['price'] <= $upper_edge &&
            $item['popularity_7d'] >= $popularity_7d &&
            !in_array($item['market_hash_name'] , $stop_list)) {
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
    // исключаем неуникальные записи, оставляя таким образом самые дешевые из неуникальных предложений
    $filtered_items = unique_multidim_array($filtered_items, 'market_hash_name');
    return $filtered_items;
}

// эти предметы не ищутся в стиме либо их нельзя продать
$stop_list = [
    'Genuine Weather Rain',
    'Genuine Weather Snow',
    'Genuine Weather Moonbeam',
    'Genuine Weather Harvest',
    'Genuine Weather Spring',
    'Genuine Weather Pestilence',
    'Compact of the Guardian Construct Style Unlock',
    'Lucid Torment Style Unlock',
    'Genuine Weather Aurora',
    'Inscribed Royal Dagger of the Tahlin Watch - Off-H',
    'Immortal Treasure III 2016 - Unreleased',
    'Autograph: Kevin -&quot;Purge-&quot; Godec',
    'Genuine Mentor of the High Plains Decorated Pauldr',
    'Inscribed Crystal Scavenger\'s Transducing Contrapt',
    'Inscribed Crystal Scavenger\'s Galvanic Mining Head',
    'Inscribed Mentor of the High Plains Decorated Paul',
    'Genuine Hwytty & Shyzzyrd',
    'Trove Carafe 2015 Autographed by Owen ODPixel Davi',
    'Auspicious Bite of the Slithereen Knight - Off-Han',
    'International 2015 Autograph: Andrey \'Dread\' Golub',
    'The Second Disciple\'s Chakram',
    'Spectator: NewBee',
    'Genuine Weather Sirocco',
    'Auspicious Reprieve of the Clergy Ascetic - Off-Ha',
    'Kunkka & Tidehunter Announcer Pack',
    'Trove Carafe 2015 Autographed by Toby TobiWan Daws',
    'Genuine Weather Ash',
    'Dota 2 Champion\'s League Season 3 - No Contributio',
    'Bloom Harvest',
    'Mega-Kills: Kunkka & Tidehunter',
    'Announcer: Kunkka & Tidehunter',
    'Trove Carafe 2015 Autographed by Charles Nahaz Bes',
    'Immortal Treasure II 2016 - Unreleased',
    'Mo\'rokai Emoticon',
    'Autograph: 董灿',
    'Eaglesong 2015 Emoticon',
    'Spectator: Virtus.Pro',
    'Dota 2 Asia Championship 2015 - 500 Compendium Poi',
    'Inscribed Starrider of the Crescent Steel Shoulder',
    'Inscribed Shield of the Burning Nightmare',
    'Inscribed Headpiece of the Deadly Nightshade',
    'Team Pennant: AL',
    'Auspicious Form of the Atniw',
    'Frozen Stein of the Bar Brawler',
    'Charge of the Wrathrunner Loading Screen',
    'Auspicious Armor of Eternal Eclipse',
    'Dark Artistry Paldrons'
];
