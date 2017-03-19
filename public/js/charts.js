google.charts.load('current', {'packages':['corechart']});
google.charts.setOnLoadCallback(drawChart);

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

LSB.NC = [];
for(var key in cryptoData.LSB.NC)
{
    LSB.NC.push([Number(key), cryptoData.LSB.NC[key]])
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

    var data_nc = google.visualization.arrayToDataTable(LSB.NC, true);
    var options_nc = {
        title: 'NC',
        hAxis: {title: 'Bits',  titleTextStyle: {color: '#333'}},
        vAxis: {minValue: 0.999999950, maxValue: 1.00056382}
    };

    var chart_nc = new google.visualization.AreaChart(document.getElementById('chart_div_nc'));
    $("a[href='#NC']").on('shown.bs.tab', function (e) {
        chart_nc.draw(data_nc, options_nc);
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