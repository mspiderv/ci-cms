<script type="text/javascript">
    google.load("visualization", "1", {packages:["corechart"]});
    google.setOnLoadCallback(drawChart);
    function drawChart() {
        
        var headings = [['<?= __('partners_categories_heading_1') ?>', '<?= __('partners_categories_heading_2') ?>']];
        var data = <?= json_encode($data) ?>;
        var chart_data = google.visualization.arrayToDataTable(headings.concat(data));
        var options = {
            'chartArea': {'width': '70%', 'height': '90%'}
        };

        var chart = new google.visualization.BarChart(document.getElementById('chart_partners_categories'));
        chart.draw(chart_data, options);
    }
</script>
<div id="chart_partners_categories" style="width: 100%; height: <?= count($data) * 25 ?>px;"></div>