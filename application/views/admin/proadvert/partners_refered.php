<script type="text/javascript">
    google.load("visualization", "1", {packages:["corechart"]});
    google.setOnLoadCallback(drawChart);
    function drawChart() {
        
        var headings = [['', '']];
        var data = <?= json_encode($data) ?>;
        var chart_data = google.visualization.arrayToDataTable(headings.concat(data));
        var options = {
            'chartArea': {'width': '95%', 'height': '80%'},
            'colors': ['#ff9900', '#329262'],
            pieHole: 0.3
        };

        var chart = new google.visualization.PieChart(document.getElementById('chart_partners_refered'));
        chart.draw(chart_data, options);
    }
</script>
<div id="chart_partners_refered" style="width: 100%; height: 400px;"></div>