<?php

require_once "connection.php";

if(isset($_GET["id"])){
    $id = $_GET["id"];
    $query = "DELETE FROM barche WHERE id = $id";
    if($conn->query($query)){
        header("Location: lista.php");
    }
    else{
        echo "Errore nell'eleiminazione" . $conn->error;
    }
}
else{
    echo "ID non valido";
}

?>