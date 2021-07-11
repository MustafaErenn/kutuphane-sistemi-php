<?php 


        require_once "controllers/authController.php";


        $errors = array(); // hataları depolucak array
        $kitaplarArrayi = array();
        if(!isset($_SESSION['id'])){
            header("location:login.php");
            exit();
        }


        if (isset($_POST['ownLib-btn'])) {
            $selectSearchType=$_POST['selectSearchType']; // form verisini alıyor
            $data=$_POST['data']; // form verisini alıyor

            if(empty($selectSearchType)){
                $errors['selectSearchType'] = "Arama türü seçiniz";
            }
            
            
            if($selectSearchType === 'KITAPLAR.ISBN'){

                $bookQuery = 'SELECT * from kitap_kutuphane  
                join kitaplar on kitaplar.ISBN = kitap_kutuphane.ISBN 
                where kitap_kutuphane.ISBN=? and kitap_kutuphane.KutuphaneID=?';
                $stmt = $baglanti -> prepare($bookQuery);
                $stmt -> bind_param('si',$data,$_SESSION['adminKutuphaneId']);
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
                $stmt -> bind_param('s',$data);
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
                $stmt -> bind_param('s',$data);
                $stmt->execute();
                $result = $stmt -> get_result();
                $kategoriCount = $result->num_rows; 
                $kategoriler = $result->fetch_all();
                $stmt -> close(); // kitapları görüntülemek için veriyi book diye bir değişkende tutuyoruz.

                array_push($book,$kategoriler);

                $kutuphaneQuery = 'SELECT Ad from kutuphaneler where ID =?';
                $stmt = $baglanti -> prepare($kutuphaneQuery);
                $stmt -> bind_param('i',$_SESSION['adminKutuphaneId']);
                $stmt->execute();
                $result = $stmt -> get_result();
                $kutuphaneCount = $result->num_rows; 
                $kutuphaneler = $result->fetch_all();
                $stmt -> close(); // kitapları görüntülemek için veriyi book diye bir değişkende tutuyoruz.

                array_push($book,$kutuphaneler);
                array_push($kitaplarArrayi,$book);
                }
                
            }
            else if($selectSearchType === 'Baslik'){
                
                $data = "%" . $_POST['data'] . "%";
                $basliklarQuery = 'SELECT ISBN from kitaplar where baslik LIKE ?';;
                $stmt = $baglanti -> prepare($basliklarQuery);
                $stmt -> bind_param('s',$data);
                $stmt->execute();
                $result = $stmt -> get_result();
                $baslikCount = $result->num_rows; 
                $isbnler = $result->fetch_all();
                $stmt -> close(); 
                for($i = 0;$i<$baslikCount;$i++){


                    $sql1 = 'SELECT * from kitap_kutuphane 
                    join kitaplar on kitaplar.ISBN = kitap_kutuphane.ISBN
                    where kitap_kutuphane.ISBN =? and kitap_kutuphane.KutuphaneID=?';
                    $stmt = $baglanti -> prepare($sql1);
                    $stmt -> bind_param('si',$isbnler[$i][0],$_SESSION['adminKutuphaneId']);
                    $stmt->execute();
                    $result = $stmt -> get_result();
                    $bookCount = $result->num_rows; 
                    $book = $result->fetch_all();
                    $stmt -> close();

                    if($bookCount==0){
                        continue;
                    }
                    $sqlYazarlar = 'SELECT CONCAT(yazarlar.Ad, " ", yazarlar.Soyad) AS tamAd from kitap_yazar 
                    join yazarlar on yazarlar.ID = kitap_yazar.YazarID
                    where kitap_yazar.ISBN =?';
                    $stmt = $baglanti -> prepare($sqlYazarlar);
                    $stmt -> bind_param('s',$isbnler[$i][0]);
                    $stmt->execute();
                    $result = $stmt -> get_result();
                    $yazarCount = $result->num_rows; 
                    $yazarlar = $result->fetch_all();
                    $stmt -> close();
                    array_push($book,$yazarlar);


                    $sqlKategoriler = 'SELECT Ad from kitap_kategori
                    join kategoriler on kategoriler.id = kitap_kategori.KategoriID
                    where kitap_kategori.ISBN = ?';
                    $stmt = $baglanti -> prepare($sqlKategoriler);
                    $stmt -> bind_param('s',$isbnler[$i][0]);
                    $stmt->execute();
                    $result = $stmt -> get_result();
                    $kategoriCount = $result->num_rows; 
                    $kategoriler = $result->fetch_all();
                    $stmt -> close();

                    array_push($book,$kategoriler);


                    $sqlKutuphaneler = 'SELECT Ad from kutuphaneler where ID =?';
                    $stmt = $baglanti -> prepare($sqlKutuphaneler);
                    $stmt -> bind_param('i',$_SESSION['adminKutuphaneId']);
                    $stmt->execute();
                    $result = $stmt -> get_result();
                    $kutuphaneCount = $result->num_rows; 
                    $kutuphaneler = $result->fetch_all();
                    $stmt -> close();

                    array_push($book,$kutuphaneler);
                    array_push($kitaplarArrayi,$book);
                }   
                
                
            }

        }

        if (isset($_POST['otherLibs-btn'])) {
            $selectSearchType=$_POST['selectSearchType']; // form verisini alıyor
            $data=$_POST['data']; // form verisini alıyor

            if(empty($selectSearchType)){
                $errors['selectSearchType'] = "Arama türü seçiniz";
            }

            if($selectSearchType === 'KITAPLAR.ISBN'){

                $bookQuery = 'SELECT * from kitap_kutuphane  
                join kitaplar on kitaplar.ISBN = kitap_kutuphane.ISBN 
                where kitap_kutuphane.ISBN=? and kitap_kutuphane.KutuphaneID!=?';
                $stmt = $baglanti -> prepare($bookQuery);
                $stmt -> bind_param('si',$data,$_SESSION['adminKutuphaneId']);
                $stmt->execute();
                $result = $stmt -> get_result();
                $bookCount = $result->num_rows; 
                $book = $result->fetch_all();
                $stmt -> close(); // kitapları görüntülemek için veriyi book diye bir değişkende tutuyoruz.

                $yazarQuery = 'SELECT CONCAT(yazarlar.Ad, " ", yazarlar.Soyad) AS tamAd from kitap_yazar 
                join yazarlar on yazarlar.ID = kitap_yazar.YazarID
                where kitap_yazar.ISBN =?';
                $stmt = $baglanti -> prepare($yazarQuery);
                $stmt -> bind_param('s',$data);
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
                $stmt -> bind_param('s',$data);
                $stmt->execute();
                $result = $stmt -> get_result();
                $kategoriCount = $result->num_rows; 
                $kategoriler = $result->fetch_all();
                $stmt -> close(); // kitapları görüntülemek için veriyi book diye bir değişkende tutuyoruz.

                array_push($book,$kategoriler);

                $kutuphaneQuery = 'SELECT Ad from kutuphaneler where ID !=?';
                $stmt = $baglanti -> prepare($kutuphaneQuery);
                $stmt -> bind_param('i',$_SESSION['adminKutuphaneId']);
                $stmt->execute();
                $result = $stmt -> get_result();
                $kutuphaneCount = $result->num_rows; 
                $kutuphaneler = $result->fetch_all();
                $stmt -> close(); // kitapları görüntülemek için veriyi book diye bir değişkende tutuyoruz.

                array_push($book,$kutuphaneler);
                array_push($kitaplarArrayi,$book);
                
            }
            else if($selectSearchType === 'Baslik'){
                
                $data = "%" . $_POST['data'] . "%";
                $basliklarQuery = 'SELECT ISBN from kitaplar where baslik LIKE ?';;
                $stmt = $baglanti -> prepare($basliklarQuery);
                $stmt -> bind_param('s',$data);
                $stmt->execute();
                $result = $stmt -> get_result();
                $baslikCount = $result->num_rows; 
                $isbnler = $result->fetch_all();
                $stmt -> close(); 
                for($i = 0;$i<$baslikCount;$i++){


                    $sql1 = 'SELECT * from kitap_kutuphane 
                    join kitaplar on kitaplar.ISBN = kitap_kutuphane.ISBN
                    where kitap_kutuphane.ISBN =? and kitap_kutuphane.KutuphaneID!=?';
                    $stmt = $baglanti -> prepare($sql1);
                    $stmt -> bind_param('si',$isbnler[$i][0],$_SESSION['adminKutuphaneId']);
                    $stmt->execute();
                    $result = $stmt -> get_result();
                    $bookCount = $result->num_rows; 
                    $book = $result->fetch_all();
                    $stmt -> close();
                    
                    if($bookCount==0){
                        continue;
                    }
                    $sqlYazarlar = 'SELECT CONCAT(yazarlar.Ad, " ", yazarlar.Soyad) AS tamAd from kitap_yazar 
                    join yazarlar on yazarlar.ID = kitap_yazar.YazarID
                    where kitap_yazar.ISBN =?';
                    $stmt = $baglanti -> prepare($sqlYazarlar);
                    $stmt -> bind_param('s',$isbnler[$i][0]);
                    $stmt->execute();
                    $result = $stmt -> get_result();
                    $yazarCount = $result->num_rows; 
                    $yazarlar = $result->fetch_all();
                    $stmt -> close();
                    array_push($book,$yazarlar);


                    $sqlKategoriler = 'SELECT Ad from kitap_kategori
                    join kategoriler on kategoriler.id = kitap_kategori.KategoriID
                    where kitap_kategori.ISBN = ?';
                    $stmt = $baglanti -> prepare($sqlKategoriler);
                    $stmt -> bind_param('s',$isbnler[$i][0]);
                    $stmt->execute();
                    $result = $stmt -> get_result();
                    $kategoriCount = $result->num_rows; 
                    $kategoriler = $result->fetch_all();
                    $stmt -> close();
                    array_push($book,$kategoriler);


                    $sqlKutuphaneler = 'SELECT Ad from kutuphaneler where ID !=?';
                    $stmt = $baglanti -> prepare($sqlKutuphaneler);
                    $stmt -> bind_param('i',$_SESSION['adminKutuphaneId']);
                    $stmt->execute();
                    $result = $stmt -> get_result();
                    $kutuphaneCount = $result->num_rows; 
                    $kutuphaneler = $result->fetch_all();
                    $stmt -> close();

                    array_push($book,$kutuphaneler);
                    array_push($kitaplarArrayi,$book);
                }   
                
            }
            
        }

?>
<?php 
    include 'base.php';
?>
<div class="container" style="padding: 2%; background-color: #F6F6EB;" id="Arama">
    <div class="card border-0">
        <div style="padding: 2%; background-color: #F6F6EB;" class="card-body">
            <form action="search.php" method="POST">
                <div class="input-group mt-3">
                    <select class="form-control border-0" id="select" name="selectSearchType">
                        <option hidden>Seçin</option>
                        <option value="Baslik">Eser Adı
                        </option>

                        <option 
                            value="KITAPLAR.ISBN">ISBN
                        </option>

                    </select>
                </div>
                <div class="input-group mt-3">
                    <input class="form-control border-0" type="text" placeholder="Aramanızı Girin" id="data" name="data" />
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-primary ml-3" name='ownLib-btn' >Kütüphanemde Ara</button>
                        <button type="submit" class="btn btn-primary ml-3" name='otherLibs-btn'>Diğer Kütüphanelerde Ara</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="lister-item mode-advanced solid 2px p-3" style="margin-left: 20%; margin-right: 20%;">
<?php if(count($kitaplarArrayi)!=0): ?>

        <?php for ($i = 0; $i < count($kitaplarArrayi) ; $i++) { ?>
            
           
            <div class="lister-item-content">
                <h3 class="lister-item-header">
                    <span class="text-primary"><?php $i ?></span>
                    <span style="color: #007BFF;"><?php echo $kitaplarArrayi[$i][0][5] ?></span>
                </h3>
                <p class="text-muted">
                    <span><?php echo $kitaplarArrayi[$i][0][6] ?> Sayfa</span>
                    <span>|</span>

                    <?php for ($j = 0; $j < count($kitaplarArrayi[$i][2]) ; $j++) { ?>
                    <span><?php echo $kitaplarArrayi[$i][2][$j][0] ?> /</span>
                    <?php } ?>

                </p>
                <p class="">
                    Yazar:
                    <?php for ($k = 0; $k < count($kitaplarArrayi[$i][1]) ; $k++) { ?>
                    <span style="color: #007BFF;"><?php echo $kitaplarArrayi[$i][1][$k][0] ?> / </span>
                    <?php } ?>

                    <span class="ghost">|</span>
                    Yayın Evi:
                    <span style="color: #007BFF;"><?php echo $kitaplarArrayi[$i][0][7] ?></span>
                    <span class="ghost">|</span>
                    ISBN:
                    <span style="color: #007BFF;"><?php echo $kitaplarArrayi[$i][0][4] ?></span>
                    <span class="ghost">|</span>
                    Kütüphane:

                    <?php for ($x = 0; $x < $kutuphaneCount ; $x++) { ?>
                    <span style="color: #007BFF;"><?php echo $kitaplarArrayi[$i][3][$x][0] ?></span>
                    <?php } ?>

                    

                    <span class="ghost">|</span>
                </p>
            </div>
            <div>
                <a class="btn btn-primary" href="details.php?lib=<?php echo $kitaplarArrayi[0][0][2] ?>&book=<?php echo $kitaplarArrayi[$i][0][4] ?>">Detay</a>
                BURASINI KONTROL ET
            </div>
            <hr>
        <?php } ?>
    
    <div style="text-align: right;">
        <a href="#Arama" class="btn btn-primary">Başa Dön</a>
    </div>


<?php endif; ?>

    

</div>


