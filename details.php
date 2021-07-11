<?php 

        require_once "controllers/authController.php";
        $kitapArrayi = array();
        if(!isset($_SESSION['id'])){
            header("location:login.php");
            exit();
        }

        $errors = array();

        if(isset($_GET['book']) && isset($_GET['lib'])){
            $bookISBN=$_GET['book'];
            $libID=$_GET['lib'];

           

            // sorgular vs. burdan sonra devam edecek

            $bookQuery = 'SELECT * from kitap_kutuphane  
            join kitaplar on kitaplar.ISBN = kitap_kutuphane.ISBN 
            where kitap_kutuphane.ISBN=? and kitap_kutuphane.KutuphaneID=?';
            $stmt = $baglanti -> prepare($bookQuery);
            $stmt -> bind_param('si',$bookISBN,$libID);
            $stmt->execute();
            $result = $stmt -> get_result();
            $bookCount = $result->num_rows; 
            $book = $result->fetch_all();
            $stmt -> close(); // kitapları görüntülemek için veriyi book diye bir değişkende tutuyoruz.

            if($bookCount!=0){
            $yazarQuery = 'SELECT CONCAT(yazarlar.Ad, " ", yazarlar.Soyad) AS tamAd from kitap_yazar 
            join yazarlar on yazarlar.ID = kitap_yazar.YazarID
            where kitap_yazar.ISBN =?';
            $stmt = $baglanti -> prepare($yazarQuery);
            $stmt -> bind_param('s',$bookISBN);
            $stmt->execute();
            $result = $stmt -> get_result();
            $yazarCount = $result->num_rows; 
            $yazarlar = $result->fetch_all();
            $stmt -> close(); // kitapları görüntülemek için veriyi book diye bir değişkende tutuyoruz.
            
            array_push($book,$yazarlar);

            $kategoriQuery = 'SELECT Ad from kitap_kategori
            join kategoriler on kategoriler.id = kitap_kategori.KategoriID
            where kitap_kategori.ISBN =?';
            $stmt = $baglanti -> prepare($kategoriQuery);
            $stmt -> bind_param('s',$bookISBN);
            $stmt->execute();
            $result = $stmt -> get_result();
            $kategoriCount = $result->num_rows; 
            $kategoriler = $result->fetch_all();
            $stmt -> close(); // kitapları görüntülemek için veriyi book diye bir değişkende tutuyoruz.

            array_push($book,$kategoriler);

            $kutuphaneQuery = 'SELECT Ad from kutuphaneler where ID =?';
            $stmt = $baglanti -> prepare($kutuphaneQuery);
            $stmt -> bind_param('i',$libID);
            $stmt->execute();
            $result = $stmt -> get_result();
            $kutuphaneCount = $result->num_rows; 
            $kutuphaneler = $result->fetch_all();
            $stmt -> close(); // kitapları görüntülemek için veriyi book diye bir değişkende tutuyoruz.

            array_push($book,$kutuphaneler);

            $kutuphaneAdresiQuery = 'SELECT CONCAT(adresler.İkamet_adresi,", ",adresler.Ilce,"/", adresler.Il) as tamAdres FROM kutuphaneler 
            join adresler on kutuphaneler.AdresID = adresler.ID
            where kutuphaneler.ID =?;';
            $stmt = $baglanti -> prepare($kutuphaneAdresiQuery);
            $stmt -> bind_param('i',$libID);
            $stmt->execute();
            $result = $stmt -> get_result();
            $kutuphaneAdresiCount = $result->num_rows; 
            $kutuphaneAdresi = $result->fetch_all();
            $stmt -> close(); // kitapları görüntülemek için veriyi book diye bir değişkende tutuyoruz.


            array_push($book,$kutuphaneAdresi);
            array_push($kitapArrayi,$book);
            }

        }


        if (isset($_POST['teslim-btn'])) {

            $uyeeposta=$_POST['uyeeposta'];
            $uyetel=$_POST['uyetel'];
            
            if(empty($uyeeposta)){
                $errors['uyeeposta'] = "Eposta boş bırakılamaz";
            }
            if(empty($uyetel)){
                $errors['uyetel'] = "Telefon numarası boş bırakılamaz";
            }

            $sqlUyeIDQuery = 'SELECT ID from uyeler where Eposta=? and Telefon=?';
            $stmt = $baglanti->prepare($sqlUyeIDQuery);
            $stmt -> bind_param('ss',$uyeeposta,$uyetel);
            $stmt-> execute();
            $result = $stmt-> get_result();
            $uyeIdCount = $result->num_rows;
            $uyeId = $result->fetch_all();
            $stmt->close();
            if($uyeIdCount!=0){

                $sqlQuery1 = 'SELECT kitap_kutuphane.ISBN, kitap_kutuphane.Miktar 
                from kitap_kutuphane 
                where  kitap_kutuphane.ISBN =?
                and kitap_kutuphane.KutuphaneID =?';
                $stmt = $baglanti->prepare($sqlQuery1);
                $stmt -> bind_param('si',$_GET['book'],$_GET['lib']);
                $stmt-> execute();
                $result = $stmt-> get_result();
                $sql1Count = $result->num_rows;
                $sql1 = $result->fetch_all();

                $kitapISBN= $sql1[0][0];
                $miktar= $sql1[0][1];
                $memberId = $uyeId[0][0];
                if($miktar>=1){
                    $sqlEmanetDurum = 'SELECT * FROM kitap_emanet WHERE ISBN=? and Durum=0 and UyeID=? and KutuphaneID=?';
                    $stmt = $baglanti->prepare($sqlEmanetDurum);
                    $stmt -> bind_param('sii',$_GET['book'],$memberId,$_GET['lib']);
                    $stmt-> execute();
                    $result = $stmt-> get_result();
                    $sqlEmanetDurumCount = $result->num_rows;
                    $sqlEmanetDurumResult = $result->fetch_all();
                    $stmt->close();

                    if($sqlEmanetDurumCount==0){
                        $dateNow = date('Y-m-d');
                        $date30later = date('Y-m-d', strtotime("+30 days",time()));
                        echo $dateNow." ";
                        echo $date30later;
                        $sqlKitapEmanet= 'INSERT into kitap_emanet (AlimTarih,SonTeslimTarih,KutuphaneID,UyeID,ISBN) 
                        values (?,?,?,?,?);';
                        $stmt = $baglanti -> prepare($sqlKitapEmanet);
                        $stmt -> bind_param('ssiis',$dateNow,$date30later,$_SESSION['adminKutuphaneId'],$memberId,$kitapISBN);
                        $stmt->execute();
                        $stmt->close();


                        $sqlMiktarUpdate ='UPDATE kitap_kutuphane SET miktar= miktar - 1 WHERE ISBN =? and kutuphaneID =?;';
                        $stmt = $baglanti -> prepare($sqlMiktarUpdate);
                        $stmt -> bind_param('si',$kitapISBN,$_SESSION['adminKutuphaneId']);
                        $stmt->execute();
                        $stmt->close();
                        
                    }else{
                        $errors['already-give'] = "Bu kitap kullanıcıda mevcut";
                    }
                }

            }else{
                $errors['not-found'] = "Üye Bulunamadı";
            }
        }
?>


<?php 
    include 'base.php';
?>
<?php if(count($kitapArrayi)!=0): ?>

<div class="card-body d-flex flex-wrap" style="margin: 0px 15%">
    <div class="card d-inline-block mx-auto my-0 border-0" style="width: 25rem">
        <div class="card-header text-center border-0">
            <h4>Kitap Bilgileri</h4>
        </div>
        <div class="card-body">
            <ul class="list-group">
                <li class="list-group-item border-0"><b>Kitap Adı: </b><?php echo $kitapArrayi[0][0][5] ?></li>
                <li class="list-group-item border-0"><b>ISBN: </b><?php echo $kitapArrayi[0][0][4] ?></li>
                <li class="list-group-item border-0"><b>Yazar: </b>
                    <?php for ($k = 0; $k < count($kitapArrayi[0][1]) ; $k++) { ?>
                        <?php echo $kitapArrayi[0][1][$k][0] ?> ,
                    <?php } ?>
                </li>
                <li class="list-group-item border-0"><b>Sayfa: </b><?php echo $kitapArrayi[0][0][6] ?></li>
                <li class="list-group-item border-0"><b>Kategori: </b>
                    <?php for ($j = 0; $j < count($kitapArrayi[0][2]) ; $j++) { ?>
                        <?php echo $kitapArrayi[0][2][$j][0] ?> /
                    <?php } ?>
                </li>
            </ul>
        </div>

    </div>
    <div class="card d-inline-block mx-auto my-0 border-0" style="width: 25rem">
        <div class="card-header text-center border-0">
            <h4>Kütüphane Bilgileri</h4>
        </div>
        <div class="card-body">
            <ul class="list-group">
                <li class="list-group-item border-0"><b>Kütüphane Adı: </b>
                    <?php for ($x = 0; $x < $kutuphaneCount ; $x++) { ?>
                        <?php echo $kitapArrayi[0][3][0][$x] ?>
                    <?php } ?>
            </li>
                <li class="list-group-item border-0"><b>Adresi: </b>
                    <?php for ($t = 0; $t < $kutuphaneAdresiCount ; $t++) { ?>
                        <?php echo $kitapArrayi[0][4][0][$t] ?>
                     <?php } ?>
                </li>
                <li class="list-group-item border-0"><b>Kitap Adedi: </b><?php echo $kitapArrayi[0][0][1] ?></li>
            </ul>
        </div>
    </div>



</div>



<div class="container mt-2" style="width: 30rem;">

    
    <?php if(count($errors)>0): ?>
        <div class="alert alert-danger">
        <?php foreach($errors as $error): ?>
            <li><?php echo $error; ?></li>
        <?php endforeach; ?>
        </div>
    <?php endif; ?>
    <!-- BURAYI VALIDATE ETMEN LAZIM LOGİNDEN BAKARAK YAP -->

    
    <?php if($_SESSION['adminKutuphaneId'] == $libID): ?>
    
    <form action="details.php?lib=<?php echo $libID?>&book=<?php echo $bookISBN ?>" method="POST">
        <div class="form-group">
            <label for="exampleInputEmail1">E-posta</label>
            <input type="email" class="form-control border-0" id="uyeeposta" name="uyeeposta" placeholder="E-Posta">
        </div>
        <div class="form-group">
            <label for="exampleInputPassword1">Telefon</label>
            <input type="tel" class="form-control border-0" id="uyetel" name="uyetel" placeholder="Telefon Numarası">
        </div>



        <div class="text-center">
            <button name='teslim-btn' type="submit" class="btn btn-primary">Teslim Et</button>
        </div>
    </form>

    <?php endif; ?>

</div>

<?php endif; ?>