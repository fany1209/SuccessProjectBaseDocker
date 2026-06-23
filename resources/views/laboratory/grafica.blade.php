<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Gráfica de registros</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
  <style>
    body { font-family: system-ui, -apple-system, Segoe UI, Roboto, Ubuntu, Cantarell, "Helvetica Neue", Arial; }
    #donutchart { width: 100%; max-width: 980px; height: 520px; margin: 24px auto; }
  </style>
 <script>
  google.charts.load("current", {packages:["corechart"]});
  google.charts.setOnLoadCallback(initChart);

  async function initChart() {
    try {
      const res = await fetch("{{ route('lab.charts.counts') }}", {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
      });
      const json = await res.json();

      const data = google.visualization.arrayToDataTable([
        ['Tabla', 'Registros'],
        ['SSS-FOR-LID-01', Number(json.reception_of_samples)],
        ['SSS-FOR-LID-03',           Number(json.pdf_clicks_d)],
        ['SSS-FOR-LID-05',   Number(json.laboratory_samples)],
        ['SSS-FOR-LID-11',       Number(json.weekly_results)],
      ]);

      const options = {
        title: 'Registros por tabla',
        pieHole: 0.45,
        legend: { position: 'right' },
        chartArea: { left: 40, top: 60, width: '80%', height: '75%' },
        sliceVisibilityThreshold: 0
      };

      const chart = new google.visualization.PieChart(document.getElementById('donutchart'));
      function draw() { chart.draw(data, options); }
      draw();
      window.addEventListener('resize', draw);
    } catch (e) {
      console.error('Error cargando datos del gráfico', e);
      document.getElementById('donutchart').innerHTML =
        '<p style="text-align:center;color:#b91c1c;">No se pudieron cargar los datos.</p>';
    }
  }
</script>
</head>
<body>
  <div id="donutchart"></div>
</body>
</html>
