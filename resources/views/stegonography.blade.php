@extends('layouts.layout')
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">
    google.charts.load('current', {'packages':['corechart']});
    google.charts.setOnLoadCallback(drawChart);

    function drawChart() {
        var data = {!! \GuzzleHttp\json_encode($IF) !!};
        var dataChart = [];
        //dataChart.push(['bits in encrypted file', 'IF']);
        for(var key in data)
        {
            dataChart.push([Number(key), data[key]])
        }
        var data = google.visualization.arrayToDataTable(dataChart, true);
        var options = {
            title: 'IF',
            hAxis: {title: 'Bits',  titleTextStyle: {color: '#333'}},
            vAxis: {minValue: 0.999999, maxValue: 1}
        };

        var chart = new google.visualization.AreaChart(document.getElementById('chart_div'));
        chart.draw(data, options);
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
                @foreach($crypto as $image)
                    <td>
                        <img src="{{$image}}">
                    </td>
                @endforeach
            </tr>
            <tr>
                @foreach($crypto as $image => $key)
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
                <div id="chart_div" style="width: 100%"></div>
            </div>
            <div id="SNR" class="tab-pane fade">
                <h3>SNR</h3>
                <p>Some content in menu 1.</p>
            </div>
            <div id="CQ" class="tab-pane fade">
                <h3>CQ</h3>
                <p>Some content in menu 2.</p>
            </div>
            <div id="AD" class="tab-pane fade">
                <h3>AD</h3>
                <p>Some content in menu 2.</p>
            </div>
            <div id="NAD" class="tab-pane fade">
                <h3>NAD</h3>
                <p>Some content in menu 2.</p>
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
