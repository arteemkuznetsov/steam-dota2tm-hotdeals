const fetch = require('node-fetch');
const delayMarket = 250; // request to market (ms)
const delayLocalhost = 120000; // request to localhost (ms)

function sleep(ms) {
	return new Promise(resolve => {
		setTimeout(resolve, ms);
	});
}

async function requestHotDeals() {
	let items = await (
		await fetch('http://localhost/profitbot/hotdeals.php')
		).json();
	if (items.length > 0) {
		return items;
	}
	else return 0;
}

async function requestAllOffersByName(apiKey, marketHashName) {
	let url = `https://market.dota2.net/api/v2/search-list-items-by-hash-name-all?key=${apiKey}&list_hash_name[]=${encodeURI(marketHashName)}`;
	let offers = await (
		await fetch(url)
		).json();
	return offers['data'][marketHashName];
}

function filterOffers(apiKey, offers, steamPrice, profit) { // другие офферы, которые входят в наш profit %
	let filteredOffers = [];
	let commission = 1.15;
	let upperPrice = steamPrice / commission / profit;
	offers.forEach(offer => {
        if ((offer['price'] / 100) <= upperPrice) { // т.к. маркет возвращает цены в копейках
        	filteredOffers.push(offer);
        	console.log('price: ' + offer['price'] / 100 + '\tupper price: ' + upperPrice);
        }
    });
	return filteredOffers;
}

async function buy(apiKey, offer) {
    let id = offer['id']; // т.к. может быть несколько одинаковых хэшнеймов с одинаковой ценой, а id уникально
    let price = offer['price'];
    let url = `https://market.dota2.net/api/v2/buy?key=${apiKey}&id=${id}&price=${price}`;
    try {
    	let status = await (
    		await fetch(url)
    	).json();
    	console.log(status);
    }
    catch (e) {
    	console.log('\x1b[41m%s\x1b[0m', "JSON parse error handled! " + e);
    } 
}

(async () => {
	let config = await (
		await fetch('http://localhost/profitbot/config.json')
		).json();
	let apiKey = config['api_key'];
	let profit = config['profit'];
	console.log('Api Key: ' + apiKey);

	while (true) {
		console.log('\x1b[36m%s\x1b[0m', 'Requesting localhost');
		let items = await requestHotDeals();
            if (items.length > 0) { // если есть предложения
            	if (typeof items !== 'undefined') {
            		items.forEach(item => {
            			console.log(item['market_hash_name'] +
            				'\tmarket price: ' + item['market_price'] +
            				'\tsteam price: ' + item['steam_price']);
            		});
            		let offersToBuy = [];
	                for (let i = 0; i < items.length; i++) { // т.к. forEach ломает асинхронность
	                	let offersByName = await requestAllOffersByName(apiKey, items[i]['market_hash_name']);
		                if (typeof offersByName !== 'undefined') {
		                	let filteredOffersByName = await filterOffers(apiKey, offersByName, items[i]['steam_price'], profit);
		                	filteredOffersByName.forEach(offer => {
		                		offersToBuy.push(offer);
		                	})
		                	await sleep(delayMarket);
		                }
		                else {
		                	console.log('\x1b[41m%s\x1b[0m', 'Undefined error handled!');
		                }
	                }
	                // если filteredOffers не пусто, то покупаем по id
	                if (offersToBuy.length > 0) {
	                	for (let i = 0; i < offersToBuy.length; i++) {
	                		await buy(apiKey, offersToBuy[i]);
	                		await sleep(delayMarket);
	                	}
	                }
	            }
	        }
	        await sleep(delayLocalhost);
	    }
	}
	)();