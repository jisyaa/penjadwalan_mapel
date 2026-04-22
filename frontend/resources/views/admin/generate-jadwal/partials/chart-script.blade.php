<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    var fitnessData = @json($fitness_history);
    if (fitnessData.length > 200) {
        var sampledData = [];
        var sampledLabels = [];
        for (var i = 0; i < fitnessData.length; i += 10) {
            sampledData.push(fitnessData[i]);
            sampledLabels.push("Gen " + (i + 1));
        }
        fitnessData = sampledData;
        var labels = sampledLabels;
    } else {
        var labels = fitnessData.map((v, i) => "Gen " + (i + 1));
    }
    var ctx = document.getElementById('fitnessChart');
    if (ctx) {
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Fitness',
                    data: fitnessData,
                    borderColor: '#007bff',
                    backgroundColor: 'rgba(0,123,255,0.1)',
                    tension: 0.3,
                    fill: true,
                    pointRadius: 2
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    title: { display: true, text: 'Grafik Konvergensi Genetic Algorithm' },
                    legend: { display: true }
                },
                scales: {
                    x: { title: { display: true, text: 'Generasi' } },
                    y: { title: { display: true, text: 'Fitness (semakin kecil semakin baik)' }, beginAtZero: true }
                }
            }
        });
    }
</script>
