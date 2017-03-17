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
            title: 'LSB',
            hAxis: {title: 'Bits',  titleTextStyle: {color: '#333'}},
            vAxis: {minValue: 0.999999, maxValue: 1}
        };

        var chart = new google.visualization.AreaChart(document.getElementById('chart_div'));
        chart.draw(data, options);
    }
</script>
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
<div id="chart_div" style="width: 100%; height: 500px;"></div>