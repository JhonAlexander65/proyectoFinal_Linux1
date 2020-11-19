<html>
<body>
<h1>Hola</h1>
<?php
    exec ('top -n1 -b | head -1 | cut -d":" -f5', $promediosCPU);
    $promedios=$promediosCPU[0];
    echo $promedios;
    $avgs=explode(" ",$promedios);
    echo "5 min" . substr($avgs[1],0,3) . "\n";
    echo "10 min".substr($avgs[2],0,3)."\n";
    echo "15 min".substr($avgs[3],0,3)."\n";
    

?>
</body>
</html>