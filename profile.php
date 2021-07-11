<?php 

    require_once "controllers/authController.php";

    if(!isset($_SESSION['id'])){
        header("location:login.php");
        exit();
    }

    if(isset($_GET['userid'])){
        $userid=$_GET['userid']; // uyenin idsini burda alıyoruz

        $intUyeID= (int)$userid;
        $uyeInfo = 'SELECT uyeler.ID,ad, soyad, eposta, telefon, il, ilce, İkamet_Adresi  FROM uyeler 
        left JOIN adresler on adresler.ID = uyeler.AdresID
        where uyeler.ID =?';
        $stmt = $baglanti -> prepare($uyeInfo); 
        $stmt -> bind_param('i',$intUyeID);
        $stmt->execute();
        $result3 = $stmt -> get_result();
        $uyeCount = $result3->num_rows; 
        $uye = $result3->fetch_assoc();
        $stmt -> close();

        $teslimEdilmemisKitaplar = 'SELECT Baslik,AlimTarih FROM kitap_emanet left join kitaplar on 
        kitap_emanet.ISBN=kitaplar.ISBN
        where kitap_emanet.UyeID=? and kitap_emanet.Durum=0;';
        $stmt = $baglanti -> prepare($teslimEdilmemisKitaplar); 
        $stmt -> bind_param('i',$intUyeID);
        $stmt->execute();
        $result = $stmt -> get_result();
        $durum0Count = $result->num_rows;
        // $teslimEdilmemisKitaplar = $result->fetch_assoc();// BURAYI DIREKT ALTTA GOSTERİCEKSIN 
        // while dongusu fetch assoc memoryleri gosterdıgın gibi
        // $stmt -> close();

        $teslimEdilmisKitaplar = 'SELECT Baslik,AlimTarih FROM kitap_emanet left join kitaplar on 
        kitap_emanet.ISBN=kitaplar.ISBN
        where kitap_emanet.UyeID=? and kitap_emanet.Durum=1;';
        $stmt2 = $baglanti -> prepare($teslimEdilmisKitaplar); 
        $stmt2 -> bind_param('i',$intUyeID);
        $stmt2->execute();
        $result2 = $stmt2 -> get_result();
        $durum1Count = $result2->num_rows;
    }
?>

<?php 
include 'base.php';
?>
<div class="card text-white m-2 mx-auto border-0" style="width: 55%; background-color: #f6f6eb">
    <div class="card-header border-0 " style="color: #243a51">
        <h3 class="card-title my-auto py-2">
            <i class="fas fa-id-card"></i> <?php echo $uye['ad']." ".$uye['soyad'] ?>
            
            <?php if(isset($_SESSION['id'])): ?>
            <div>
            <a href="uyeDuzenle.php?userid=<?php echo $userid; ?>" class="btn btn-primary float-right">Düzenle</a>
                <!-- <form action="{{ url_for('profile_edit')}}">
                    <button class='btn btn-primary float-right' type='submit' name='edit' value="{{uyeData['eposta']}}">
                        Düzenle
                    </button>

                    
                    
                </form> -->
            </div>
            <?php endif; ?>
        </h3>
    </div>
    <div class="card-body d-flex border-0" style="justify-content: space-between; background-color: white">
        <div style="color: #243a51; margin: auto 0">



            <?php if($uyeCount >= 1): ?>
           

            <h5 class="card-text mt-3">
                <i class="fa fa-phone-square" aria-hidden="true"></i>
                <?php echo $uye['telefon'] ?>
            </h5>
            <h5 class="card-text mt-3">
                <i class="fa fa-envelope" aria-hidden="true"></i>
                <?php echo $uye['eposta'] ?>
            </h5>
            <h5 class="card-text mt-3 mb-3">
                <i class="fas fa-map-marker-alt"></i>
                <?php echo $uye['İkamet_Adresi'].", ".$uye['ilce']."/ ".$uye['il'] ?>
            </h5>
            <?php endif; ?>


        </div>

        <div>
            <img src="http://akhisar11noluasm.net/wp-content/uploads/2018/04/default-user.png" alt="" />
        </div>
    </div>
</div>

<div class="card-body d-flex flex-wrap mx-auto"
    style="justify-content: space-between;margin: 0px 5%;width:55%;padding:0px">
    <div class="card mb-3 d-inline-flex  border-0" style="max-width: 400px; height: 100%; ">
        <div class="row g-0 ">
            <div class="col-md-4 p-4">
                <i class="fas fa-book fa-8x" aria-hidden="true"></i>
            </div>
            <div class="col-md-8">
                <div class="card-body m-4">
                    <h2 class="card-title">
                        
                        <?php if($durum0Count>=1): ?>
                            <?php echo $durum0Count ?>
                        <?php else: ?>
                            <?php echo "0" ?>
                        <?php endif; ?>

                    </h2>
                    <p class="card-text">Mevcut Kitaplar</p>
                </div>
            </div>
            <div class="col-md-8 text-center ">
                <ol class='list-group ' style="text-align: left;list-style: inside;float:right">

                    <?php if($durum0Count>=1): ?>
                    

                    <?php while($durum0Row=$result->fetch_assoc()): ?>
                    <li class='list-group-item border-0'>-> <?php echo $durum0Row['Baslik'] ?><br> - Alım Tarih:

                    <?php echo date("d/m/Y", strtotime($durum0Row['AlimTarih']));?> 
                    </li>
                    <?php endwhile; ?>

                    <?php endif; ?>


                </ol>
            </div>
        </div>
    </div>

    <div class="card mb-3 d-inline-flex  border-0" style="max-width: 400px; height: 100%;">
        <div class="row g-0">
            <div class="col-md-4 p-4">
                <i class="fas fa-book-reader fa-8x"></i>
            </div>
            <div class="col-md-8">
                <div class="card-body m-4">
                    <h2 class="card-title">
                    <?php if($durum1Count>=1): ?>
                            <?php echo $durum1Count ?>
                        <?php else: ?>
                            <?php echo "0" ?>
                        <?php endif; ?></h2>
                    <p class="card-text">Teslim Edilmiş Kitaplar</p>
                </div>
            </div>
            <div class="col-md-8 text-center ">
                <ol class='list-group ' style="text-align: left;list-style: inside;float:right">
                <?php if($durum1Count>=1): ?>
                    

                    <?php while($durum1Row=$result2->fetch_assoc()): ?>
                    <li class='list-group-item border-0'>-> <?php echo $durum1Row['Baslik'] ?><br> - Alım Tarih:

                    <?php echo $durum1Row['AlimTarih'] ?> 
                    </li>
                    <?php endwhile; ?>

                    <?php endif; ?>


                </ol>
            </div>



        </div>
    </div>

</div>
