<div>
    <canvas id="chart"></canvas>
    <canvas id="pie"></canvas>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    var ctx = document.getElementById('chart').getContext('2d');
    var chart = new Chart(ctx, {
        type: 'line',
        data: @json($chartData),
    });

    var ctxPie = document.getElementById('pie').getContext('2d');
    var pie = new Chart(ctxPie, {
        type: 'pie',
        data: @json($pieData),
    });
</script>