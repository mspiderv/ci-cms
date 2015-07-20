<script type="text/javascript">
    google.load("visualization", "1", {packages:["corechart"]});
    google.setOnLoadCallback(drawChart);
    function drawChart() {
        
        var headings = [['<?= __('partners_age_heading_1') ?>', '<?= __('partners_age_heading_2') ?>']];
        var data = <?= json_encode($data) ?>;
        var chart_data = google.visualization.arrayToDataTable(headings.concat(data));
        var options = {
            legend: {
                position: 'none'
            },
            'chartArea': {'width': '95%', 'height': '80%'}
        };

        var chart = new google.visualization.AreaChart(document.getElementById('chart_partners_age'));
        chart.draw(chart_data, options);
    }
</script>
<div id="chart_partners_age" style="width: 100%; height: 400px;"></div>