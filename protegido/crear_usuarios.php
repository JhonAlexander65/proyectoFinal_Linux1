<html>
<h1>Creacion de Usuarios</h1>

<?php
    exec ("cat /etc/passwd",$dataUsers);
    $usuarios=array();
    
    foreach ( $dataUsers as $linea ){
        $user=explode(":",$linea);
        array_push($usuarios,$user[0],$user[2]);
    }
    echo "<pre>";
    foreach ( $usuarios as $user ){
        echo $user."\n";
    }
    echo "</pre>";
?>

</html>