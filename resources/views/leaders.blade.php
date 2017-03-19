@extends('layouts.layout')
    <script type="text/javascript">
        var members = {!! $members->toJson() !!};
        var parseMembers = [];
        members.forEach(function (item) {
            parseMembers.push([item.first_name+' '+item.last_name + ' (' + item.id_member + ')', item.weight])
        });

        google.charts.load('current', {'packages':['corechart']});
        google.charts.setOnLoadCallback(drawChart);

        function drawChart() {

            var data = new google.visualization.DataTable();
            data.addColumn('string', 'Name');
            data.addColumn('number', 'Parts');

            data.addRows(parseMembers);

            var options = {
                'width':1300,
                'height':600};

            var chart = new google.visualization.PieChart(document.getElementById('chart_div'));
            chart.draw(data, options);
        }
    </script>

<h2>Leaders group <a class="btn btn-info" href="https://vk.com/programmerrepublic">Programmer Republic</a></h2>
<div id="chart_div"></div>
