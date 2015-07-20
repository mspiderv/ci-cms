<script type="text/javascript">
    google.load("visualization", "1", {packages:["corechart"]});
    google.setOnLoadCallback(drawChart);
    function drawChart() {
        
        var headings = [['<?= __('referers_heading_1') ?>', '<?= __('referers_heading_2') ?>']];
        var data = <?= json_encode($data) ?>;
        var chart_data = google.visualization.arrayToDataTable(headings.concat(data));
        var options = {
            'chartArea': {'width': '70%', 'height': '90%'},
            'colors': ['#109618']
        };

        var chart = new google.visualization.BarChart(document.getElementById('chart_referers'));
        chart.draw(chart_data, options);
    }
</script>
<div id="chart_referers" style="width: 100%; height: <?= count($data) * 25 ?>px;"></div>