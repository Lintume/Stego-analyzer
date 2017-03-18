@extends('layouts.layout')
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">
    google.charts.load('current', {'packages':['corechart']});
    google.charts.setOnLoadCallback(drawChart);

    var cryptoData = {!! \GuzzleHttp\json_encode($crypto) !!};

    var LSB = [];
    LSB.IF = [];
    for(var key in cryptoData.LSB.IF)
    {
        LSB.IF.push([Number(key), cryptoData.LSB.IF[key]])
    }

    LSB.SNR = [];
    for(var key in cryptoData.LSB.SNR)
    {
        LSB.SNR.push([Number(key), cryptoData.LSB.SNR[key]])
    }

    LSB.CQ = [];
    for(var key in cryptoData.LSB.CQ)
    {
        LSB.CQ.push([Number(key), cryptoData.LSB.CQ[key]])
    }

    LSB.AD = [];
    for(var key in cryptoData.LSB.AD)
    {
        LSB.AD.push([Number(key), cryptoData.LSB.AD[key]])
    }

    LSB.NAD = [];
    for(var key in cryptoData.LSB.NAD)
    {
        LSB.NAD.push([Number(key), cryptoData.LSB.NAD[key]])
    }

    function drawChart() {
        var data = google.visualization.arrayToDataTable(LSB.IF, true);
        var options = {
            title: 'IF',
            hAxis: {title: 'Bits',  titleTextStyle: {color: '#333'}},
            vAxis: {minValue: 0.999999, maxValue: 1}
        };

        var chart = new google.visualization.AreaChart(document.getElementById('chart_divIf'));
        chart.draw(data, options);


        var data_snr = google.visualization.arrayToDataTable(LSB.SNR, true);
        var options_snr = {
            title: 'SNR',
            hAxis: {title: 'Bits',  titleTextStyle: {color: '#333'}},
            vAxis: {minValue: 600000, maxValue: 1000000}
        };

        var chart_snr = new google.visualization.AreaChart(document.getElementById('chart_div_snr'));
        $("a[href='#SNR']").on('shown.bs.tab', function (e) {
            chart_snr.draw(data_snr, options_snr);
        });

        var data_cq = google.visualization.arrayToDataTable(LSB.CQ, true);
        var options_cq = {
            title: 'CQ',
            hAxis: {title: 'Bits',  titleTextStyle: {color: '#333'}},
            vAxis: {minValue: 7119810, maxValue: 7119850}
        };

        var chart_cq = new google.visualization.AreaChart(document.getElementById('chart_div_cq'));
        $("a[href='#CQ']").on('shown.bs.tab', function (e) {
            chart_cq.draw(data_cq, options_cq);
        });

        var data_ad = google.visualization.arrayToDataTable(LSB.AD, true);
        var options_ad = {
            title: 'AD',
            hAxis: {title: 'Bits',  titleTextStyle: {color: '#333'}},
            vAxis: {minValue: 400, maxValue: 700}
        };

        var chart_ad = new google.visualization.AreaChart(document.getElementById('chart_div_ad'));
        $("a[href='#AD']").on('shown.bs.tab', function (e) {
            chart_ad.draw(data_ad, options_ad);
        });

        var data_nad = google.visualization.arrayToDataTable(LSB.NAD, true);
        var options_nad = {
            title: 'NAD',
            hAxis: {title: 'Bits',  titleTextStyle: {color: '#333'}},
            vAxis: {minValue: 0.00001, maxValue: 0.0002}
        };

        var chart_nad = new google.visualization.AreaChart(document.getElementById('chart_div_nad'));
        $("a[href='#NAD']").on('shown.bs.tab', function (e) {
            chart_nad.draw(data_nad, options_nad);
        });
    }
</script>

<ul class="nav nav-tabs">
    <li class="active"><a data-toggle="tab" href="#home">LSB</a></li>
    <li><a data-toggle="tab" href="#menu1">Блочное встраивание</a></li>
    <li><a data-toggle="tab" href="#menu2">Квантование</a></li>
    <li><a data-toggle="tab" href="#menu3">Крест</a></li>
    <li><a data-toggle="tab" href="#menu4">Коха-Жао</a></li>
</ul>

<div class="tab-content">
    <div id="home" class="tab-pane fade in active">
        <table>
            <tr>
                <td>
                    <img src="{{$originalSrc}}">
                </td>
                @foreach($crypto['LSB']['images'] as $image)
                    <td>
                        <img src="{{$image}}">
                    </td>
                @endforeach
            </tr>
            <tr>
                @foreach($crypto['LSB']['images'] as $image => $key)
                    <td>
                        {{$key}}
                    </td>
                @endforeach
                <td>
                    original
                </td>
            </tr>
        </table>
        <ul class="nav nav-tabs">
            <li class="active"><a data-toggle="tab" href="#IF">IF</a></li>
            <li><a data-toggle="tab" href="#SNR">SNR</a></li>
            <li><a data-toggle="tab" href="#CQ">CQ</a></li>
            <li><a data-toggle="tab" href="#AD">AD</a></li>
            <li><a data-toggle="tab" href="#NAD">NAD</a></li>
        </ul>
        <div class="tab-content">
            <div id="IF" class="tab-pane fade in active">
                <h3>IF</h3>
                <div id="chart_divIf" style="width: 100%"></div>
            </div>
            <div id="SNR" class="tab-pane fade">
                <h3>SNR</h3>
                <div id="chart_div_snr" style="width: 100%"></div>
            </div>
            <div id="CQ" class="tab-pane fade">
                <h3>CQ</h3>
                <div id="chart_div_cq" style="width: 100%"></div>
            </div>
            <div id="AD" class="tab-pane fade">
                <h3>AD</h3>
                <div id="chart_div_ad" style="width: 100%"></div>
            </div>
            <div id="NAD" class="tab-pane fade">
                <h3>NAD</h3>
                <div id="chart_div_nad" style="width: 100%"></div>
            </div>
        </div>
    </div>
    <div id="menu1" class="tab-pane fade">
        <h3>Блочное встраивание</h3>
        <p>Some content in menu 1.</p>
    </div>
    <div id="menu2" class="tab-pane fade">
        <h3>Квантование</h3>
        <p>Some content in menu 2.</p>
    </div>
    <div id="menu3" class="tab-pane fade">
        <h3>Крест</h3>
        <p>Some content in menu 2.</p>
    </div>
    <div id="menu4" class="tab-pane fade">
        <h3>Коха-Жао</h3>
        <p>Some content in menu 2.</p>
    </div>
</div>
