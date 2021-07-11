<?php 
    $baglanti= mysqli_connect("","","","");// veritabanı bilgileri girilmelidir.

    if(!$baglanti){
        echo "MySQL sunucu ile baglanti kurulamadi! </br>";
        echo "HATA: " . mysqli_connect_error();
        exit;
    }
?>