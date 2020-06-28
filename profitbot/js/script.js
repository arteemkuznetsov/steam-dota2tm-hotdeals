function updateDB() {
    fetch('db_update.php')
        .then(function (response) {
            if (response.status === 200) {
                response.json().then(function (data) {
                        console.log(data);
                        setTimeout(function () {
                            alert('База данных обновлена.');
                        }, data['items_length'] * 30000);
                    }
                );
            }
            else {
                console.log('Looks like there was a problem. ' +
                    'Status Code: ' + response.status);
            }
        })
        .catch(function (err) {
            console.log(err);
        });
}

function requestHotDeals() {
    document.getElementById('refresh-img').style.animation = 'spin 2s ease infinite';
    fetch('hotdeals.php')
        .then(response => {
                if (response.status === 200) {
                    let blocks = document.getElementsByClassName('block');
                    while (blocks[0]) {
                        blocks[0].remove();
                    }
                    response.json().then(function (data) {
                        console.log(data);
                        document.getElementById('ready').style.display = 'none';
                        if (data.length > 0) {
                            document.getElementById('no-hotdeals').style.display = 'none';

                            for (let i = 0; i < data.length; i++) {
                                addHtml(
                                    data[i]['market_hash_name'],
                                    marketUrlFormer(data[i]['market_id'], data[i]['market_hash_name']),
                                    steamUrlFormer(data[i]['market_hash_name']),
                                    data[i]['market_price'],
                                    data[i]['steam_price'],
                                    data[i]['profit_percent']
                                );
                            }
                        } else {
                            document.getElementById('no-hotdeals').style.display = 'flex';
                        }
                    });
                } else {
                    console.log('Looks like there was a problem. ' +
                        'Status Code: ' + response.status);
                }
            })
        .then(
            function () {
                document.getElementById('refresh-img').style.animation = 'none';
            }
        )
        .catch(function (err) {
            console.log(err);
        });
}

function addHtml(name, marketLink, steamLink, marketPrice, steamPrice, profit) {
    let html = '<div class="block">' +
        '            <table>' +
        '                <tr>' +
        '                    <td style="width: 400px; padding-left: 30px; padding-right: 30px">' +
        '                        <span id="name">' + name + '</span>' +
        '                    </td>' +
        '                    <td>' +
        '                        <a href="' + marketLink + '" target="_blank">' +
        '                            <input type="image" src="pic/market.png" id="market-btn" class="image-btn" alt="Market">' +
        '                        </a>' +
        '                    </td>' +
        '                    <td>' +
        '                        <a href="' + steamLink + '" target="_blank">' +
        '                            <input type="image" src="pic/steam.png" id="steam-btn" class="image-btn" alt="Steam">' +
        '                        </a>' +
        '                    </td>' +
        '                    <td>' +
        '                        <span id="market-price">' + marketPrice + '</span>' +
        '                    </td>' +
        '                    <td>' +
        '                        <span id="steam-price">' + steamPrice + '</span>' +
        '                    </td>' +
        '                    <td>' +
        '                        <span id="profit">' + profit + '%</span>' +
        '                    </td>' +
        '                </tr>' +
        '            </table>' +
        '        </div>';
    let content = document.getElementById('content');
    content.insertAdjacentHTML('beforeend', html);
}

function marketUrlFormer(market_id, market_hash_name) {
    market_id = market_id.replace('_', '-');
    return 'https://market.dota2.net/item/' + market_id + '-' + encodeURI(market_hash_name); // +
}

function steamUrlFormer(market_hash_name) {
    return 'https://steamcommunity.com/market/listings/570/' + encodeURI(market_hash_name); // %20
}