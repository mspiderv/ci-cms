<script type="text/javascript">
    google.load("visualization", "1", {packages:["corechart"]});
    google.setOnLoadCallback(drawChart);
    function drawChart() {
        
        var headings = [['<?= __('registration_hour_heading_1') ?>', '<?= __('registration_hour_heading_2') ?>', '<?= __('registration_hour_heading_3') ?>', '<?= __('registration_hour_heading_4') ?>']];
        var data = <?= json_encode($data) ?>;
        var chart_data = google.visualization.arrayToDataTable(headings.concat(data));
        var options = {
            legend: {
                position: 'none'
            },
            'chartArea': {'width': '95%', 'height': '80%'}
        };

        var chart = new google.visualization.LineChart(document.getElementById('chart_registration_hour'));
        chart.draw(chart_data, options);
    }
</script>
<div id="chart_registration_hour" style="width: 100%; height: 400px;"></div>