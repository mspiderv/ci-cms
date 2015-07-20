<script type='text/javascript'>
    google.load('visualization', '1', {'packages': ['geochart']});
    google.setOnLoadCallback(drawChart);
    
    function drawChart() {
        
        var headings = [['<?= __('partners_districts_heading_1') ?>', '<?= __('partners_districts_heading_2') ?>']];
        var data = <?= json_encode($data) ?>;
        var chart_data = google.visualization.arrayToDataTable(headings.concat(data));
        var options = {
            region: 'SK',
            displayMode: 'markers',
            'chartArea': {'width': '95%', 'height': '80%'}
        };

        var chart = new google.visualization.GeoChart(document.getElementById('chart_partners_districts'));
        chart.draw(chart_data, options);
    };
</script>
<div id="chart_partners_districts" style="width: 100%; height: 330px;"></div>