@extends('layouts.layout')
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
        var members = {!! $members->toJson() !!};
        var parseMembers = [];
        members.forEach(function (item) {
            parseMembers.push([item.first_name+' '+item.last_name + ' (' + item.id_member + ')', item.weight])
        });
        // Load the Visualization API and the corechart package.
        google.charts.load('current', {'packages':['corechart']});

        // Set a callback to run when the Google Visualization API is loaded.
        google.charts.setOnLoadCallback(drawChart);

        // Callback that creates and populates a data table,
        // instantiates the pie chart, passes in the data and
        // draws it.
        function drawChart() {

            // Create the data table.
            var data = new google.visualization.DataTable();
            data.addColumn('string', 'Name');
            data.addColumn('number', 'Parts');

            data.addRows(parseMembers);

            // Set chart options
            var options = {
                'width':1300,
                'height':600};

            // Instantiate and draw our chart, passing in some options.
            var chart = new google.visualization.PieChart(document.getElementById('chart_div'));
            chart.draw(data, options);
        }
    </script>

<h2>Leaders group <a class="btn btn-info" href="https://vk.com/programmerrepublic">Programmer Republic</a></h2>
<!--Div that will hold the pie chart-->
<div id="chart_div"></div>
