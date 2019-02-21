<?php
/*
 * Author: soulmare@gmail.com http://shopcms-moduli.com
 * 2 Feb 2019
 */

header('Content-type: text/html; charset=utf-8');

$msg = '';

// Request rates from server
$url = 'http://resources.finance.ua/ua/public/currency-cash.json';
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_TIMEOUT, 5);
$result = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);
if (($http_code == 200) && $result) {
//    echo $result;die;
} else {
    $msg = '<p style="color:red;border: red solid 1px;padding:10px;width:300px;text-align:center">The server not responded properly. Response code: ' . $http_code . '</p>';
    $rate = 0;
    $result = null;
}

?><html>
    <head>
        <title>Курсы валют, конвертер курсов</title>
        <meta name="charset" content="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
    </head>
    <body>
        
        <h2>Курс</h2>
        
        <?php echo $msg?>
        
        <select id="sel_bank">
            <option>ПриватБанк</option>
            <option>Ощадбанк</option>
            <option>Кредобанк</option>
        </select>
        
        <br><br>

        <table border="1" id="rates">
            <thead>
                <tr>
                    <th>Валюта</th>
                    <th>Покупка</th>
                    <th>Продажа</th>
                </tr>
            </thead>
            <tbody>
                <tr data-code="USD">
                    <td>USD</td>
                    <td></td>
                    <td></td>
                </tr>
                <tr data-code="EUR">
                    <td>EUR</td>
                    <td></td>
                    <td></td>
                </tr>
                <tr data-code="RUB">
                    <td>RUB</td>
                    <td></td>
                    <td></td>
                </tr>
            </tbody>
        </table>

        <h2>Конвертер</h2>

        <table border="1" id="calc">
            <thead>
                <tr>
                    <th>Валюта</th>
                    <th>Сумма</th>
                </tr>
            </thead>
            <tbody>
                <tr data-code="UAH">
                    <td>UAH</td>
                    <td><input type="number" step="0.01" placeholder="1.00"></td>
                </tr>
                <tr data-code="USD">
                    <td>USD</td>
                    <td><input type="number" step="0.01" placeholder="1.00"></td>
                </tr>
                <tr data-code="EUR">
                    <td>EUR</td>
                    <td><input type="number" step="0.01" placeholder="1.00"></td>
                </tr>
                <tr data-code="RUB">
                    <td>RUB</td>
                    <td><input type="number" step="0.01" placeholder="1.00"></td>
                </tr>
            </tbody>
        </table>
        
        <style>
            body{
                font-size: 17px;
            }
            select{
                padding: 5px;
                min-width: 200px;
            }
            table {border: solid #e4e4e4 1px;}
            th {
                background: #ccc;
                color: #333;
            }
            th, td {
                padding: 10px;
                text-align: center;
            }
            input[type=text],
            input[type=number]{
                max-width: 100px;
                text-align: center;
                background: #fff4bc;
                font-size: 16px;
            }
            [data-code] td:first-child{
                font-weight: bold;
            }
            .footnote {
                color: #666;
                margin-top: 30px;
            }
            h1, h2 {
                margin-top: 25px;
            }
            @media (max-width: 500px) {
                body {
                    text-align: center;
                    width: 95%;
                }
                #rates,#calc {
                    width: 100%;
                }
            }
            @media (max-width: 300px) {
                th, td {
                    padding: 3px;
                }
            }
        </style>
        
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
        
        <script>
        
            var rates = <?php echo $result?>;
            
            function refresh(code) {
                for (var i in rates.organizations) {
                    var bank = rates.organizations[i];
                    if (bank.title == $('#sel_bank').val()) {
//                        console.log(bank);
                        // Show rates
                        $('#rates [data-code=USD] td:nth-child(2)').text(bank.currencies.USD ? round(bank.currencies.USD.bid) : '');
                        $('#rates [data-code=USD] td:nth-child(3)').text(bank.currencies.USD ? round(bank.currencies.USD.ask) : '');
                        $('#rates [data-code=EUR] td:nth-child(2)').text(bank.currencies.EUR ? round(bank.currencies.EUR.bid) : '');
                        $('#rates [data-code=EUR] td:nth-child(3)').text(bank.currencies.EUR ? round(bank.currencies.EUR.ask) : '');
                        $('#rates [data-code=RUB] td:nth-child(2)').text(bank.currencies.RUB ? round(bank.currencies.RUB.bid) : '');
                        $('#rates [data-code=RUB] td:nth-child(3)').text(bank.currencies.RUB ? round(bank.currencies.RUB.ask) : '');
                        
                        // Show calc
                        
                        switch (code) {
                            case 'USD':
                                var sum_basic = bank.currencies.USD ? parseFloat($('#calc [data-code=USD] input').val() * bank.currencies.USD.bid) : 0;
                                break;
                            case 'RUB':
                                var sum_basic = bank.currencies.RUB ? parseFloat($('#calc [data-code=RUB] input').val() * bank.currencies.RUB.bid) : 0;
                                break;
                            case 'EUR':
                                var sum_basic = bank.currencies.EUR ? parseFloat($('#calc [data-code=EUR] input').val() * bank.currencies.EUR.bid) : 0;
                                break;
                            default:
                                var sum_basic = parseFloat($('#calc [data-code=UAH] input').val());
                                break;
                        }
                        if (!sum_basic) {
                            sum_basic = 1;
                        }
                        
                        if (code !== 'UAH') {
                            $('#calc [data-code=UAH] input').val(round(sum_basic));
                        }
                        if (code !== 'USD') {
                            $('#calc [data-code=USD] input').val(bank.currencies.USD ? round(sum_basic / bank.currencies.USD.ask) : 0);
                        }
                        if (code !== 'EUR') {
                            $('#calc [data-code=EUR] input').val(bank.currencies.EUR ? round(sum_basic / bank.currencies.EUR.ask) : 0);
                        }
                        if (code !== 'RUB') {
                            $('#calc [data-code=RUB] input').val(bank.currencies.RUB ? round(sum_basic / bank.currencies.RUB.ask) : 0);
                        }
                    }
                }
            }

            /**
             * Корректировка округления десятичных дробей.
             * https://developer.mozilla.org/ru/docs/Web/JavaScript/Reference/Global_Objects/Math/round
             *
             * @param {String}  type  Тип корректировки.
             * @param {Number}  value Число.
             * @param {Integer} exp   Показатель степени (десятичный логарифм основания корректировки).
             * @returns {Number} Скорректированное значение.
             */
            function decimalAdjust(type, value, exp) {
              // Если степень не определена, либо равна нулю...
              if (typeof exp === 'undefined' || +exp === 0) {
                return Math[type](value);
              }
              value = +value;
              exp = +exp;
              // Если значение не является числом, либо степень не является целым числом...
              if (isNaN(value) || !(typeof exp === 'number' && exp % 1 === 0)) {
                return NaN;
              }
              // Сдвиг разрядов
              value = value.toString().split('e');
              value = Math[type](+(value[0] + 'e' + (value[1] ? (+value[1] - exp) : -exp)));
              // Обратный сдвиг
              value = value.toString().split('e');
              return +(value[0] + 'e' + (value[1] ? (+value[1] + exp) : exp));
            }
            
            function round(f) {
                return decimalAdjust('round', f, -2);
            }

            refresh();
            
            $('#calc input').bind('input', function () {
                refresh($(this).closest('[data-code]').attr('data-code'));
            }).bind('focus', function () {
                $(this).select();
            });
            
            $('#sel_bank').bind('change', function () {
               refresh(); 
            });
            
//            $('#calc [data-code=USD] input').focus();
        
        </script>
        
        <p class="footnote">Курсы взяты с <a href="https://finance.ua/ru/currency">finance.ua</a></p>
    </body>
</html>
