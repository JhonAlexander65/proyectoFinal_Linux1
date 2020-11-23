<html>

<h1>Tablero de control</h1>
<?php
    #Consultamos los promedios de consumo de la CPU
    exec ('top -n1 -b | head -1 | cut -d":" -f5', $promediosCPU);
    
    #echo $promediosCPU[0];
    $avgs=explode(" ",$promediosCPU[0]);
    $avg5Min= str_replace(",",".",substr($avgs[1],0,3))*100;
    $avg10Min=str_replace(",",".",substr($avgs[2],0,3) )*100;
    $avg15Min=str_replace(",",".",substr($avgs[3],0,3) )*100;
    #echo "5 min=" .$avg5Min. "<br>";
    #echo "10 min=".$avg10Min."<br>";
    #echo "15 min=".$avg15Min."<br>";

    #calculamos los datos de la memoria RAM
    exec ('free -m | head -2 | tail -1', $ram);
    #echo str_replace(' ','-',$ram[0])."<br>";
    $dRam=explode(" ",$ram[0]);
    $ramTotal=$dRam[11];
    $ramUsada=$dRam[19];
    $avgRam=round($ramUsada/$ramTotal*100,2);

    #echo "RamTotal=" .$ramTotal. "<br>";
    #echo "RamUsada=".$ramUsada."<br>";
    #echo "Prom Uso=".$avgRam."<br>";

    #Disco
    exec ( "df | tail -n +2 | awk {'print \"['\''\" $6 \"'\'','\''/'\'', \" $3\", \" $4 \"],\"'}", $disco );
    #echo "<pre>"; foreach ( $disco as $linea ){echo $linea . "\n";} echo "</pre>";

    #Tabla Procesos
    
    exec ( "top -b -n 1| tail -n +8 |sort -k 9 |tail -3 | awk {'print \"[ \" $1 \",'\''\" $12\"'\'', '\''\" $9 \"'\''],\"'}", $tProcesos );
    #echo "<pre>"; foreach ( $tProcesos as $linea ){echo $linea . "\n";} echo "</pre>";

    #Tabla Ram

    exec ( "top -b -n 1| tail -n +8 |sort -k 10 |tail -3 | awk {'print \"[ \" $1 \",'\''\" $12\"'\'', '\''\" $10 \"'\''],\"'}", $tRam );
    #echo "<pre>"; foreach ( $tRam as $linea ){echo $linea . "\n";} echo "</pre>";
?>

<head>
 <meta http-equiv="refresh" content="30">
   <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
   <script type="text/javascript">
      google.charts.load('current', {'packages':['gauge','corechart','treemap','table']});
      google.charts.setOnLoadCallback(drawCPU);
      google.charts.setOnLoadCallback(drawMemoria);
      google.charts.setOnLoadCallback(drawDisco);
      google.charts.setOnLoadCallback(drawTableProcesos);
      google.charts.setOnLoadCallback(drawTableRam);
        
      
      function drawCPU() {

        var data = google.visualization.arrayToDataTable([
          ['Label', 'Value'],
          ['5 Min', <?php echo $avg5Min ?>],
          ['10 Min', <?php echo $avg10Min ?>],
          ['15 Min', <?php echo $avg15Min ?>]
        ]);

        var options = {
          width: 400, height: 120,
          redFrom: 90, redTo: 100,
          yellowFrom:75, yellowTo: 90,
          minorTicks: 5
        };

        var chart = new google.visualization.Gauge(document.getElementById('chart_div'));

        chart.draw(data, options);        
      }

      function drawMemoria() {
        var data = google.visualization.arrayToDataTable([
          ['Tipo', 'Valor'],
          ['Memoria Total',  <?php echo $ramTotal ?>],
          ['Memoria Usada ',  <?php echo $ramUsada ?>]
        ]);

        var options = {
          title: 'Estado de la memoria',
          pieHole: 0.4,
        };

        var chart = new google.visualization.PieChart(document.getElementById('donutchart'));
        chart.draw(data, options);
      }

      function drawDisco() {
        var data = google.visualization.arrayToDataTable([
          ['Location', 'Parent', 'Market trade volume (size)', 'Market increase/decrease (color)'],
          ['/', null, 0, 0],
          <?php foreach ( $disco as $linea ){echo $linea . "\n";} ?>
        ]);

        tree = new google.visualization.TreeMap(document.getElementById('discoChart_div'));

        tree.draw(data, {
          minColor: '#f00',
          midColor: '#ddd',
          maxColor: '#0d0',
          headerHeight: 15,
          fontColor: 'black',
          showScale: true
        });

      }


      function drawTableProcesos() {
        var data = new google.visualization.DataTable();
        data.addColumn('number', 'PID');
        data.addColumn('string', 'Proceso');
        data.addColumn('string', '%CPU');
        data.addRows([
          <?php foreach ( $tRam as $linea ){echo $linea . "\n";}; ?>
        ]);

        var table = new google.visualization.Table(document.getElementById('tableProcesos_div'));

        table.draw(data, {showRowNumber: true, width: '100%', height: '100%'});
      }

      function drawTableRam() {
        var data = new google.visualization.DataTable();
        data.addColumn('number', 'PID');
        data.addColumn('string', 'Proceso');
        data.addColumn('string', '%RAM');
        data.addRows([
          <?php foreach ( $tProcesos as $linea ){echo $linea . "\n";}; ?>
        ]);

        var table = new google.visualization.Table(document.getElementById('tableRam_div'));

        table.draw(data, {showRowNumber: true, width: '100%', height: '100%'});
      }
    </script>
  </head>
  <body>
    <h3>Indicadores de consuno de CPU</3>
    <div class="col-lg-4" id="chart_div" style="width: 400px; height: 120px;"></div>
    <h3>Ocupacion de Memoria RAM</3>
    <div class="col-lg-4" id="donutchart" style="width: 900px; height: 300px;"></div>

    <h3>Indicadores de consuno del Disco</3>
    <div id="discoChart_div" style="width: 900px; height: 500px;"></div>

    <h3>Procesos con mayor consumo de CPU</3>
    <div style="position: relative; z-index: 0; max-width: 30%; max-height: 50%; width: 100%; height: 100%;" 
      id="tableProcesos_div"></div>

    <h3>Procesos con mayor consumo de Memoria</3>
    <div style="position: relative; z-index: 0; max-width: 30%; max-height: 50%; width: 100%; height: 100%;" 
      id="tableRam_div"></div>
  </body>

</html>