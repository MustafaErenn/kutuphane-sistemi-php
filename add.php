<?php 

    require_once "controllers/authController.php";
    if(!isset($_SESSION['id'])){
        header("location:login.php");
        exit();
    }

    $errors = array();
    $yazarQuery = 'SELECT ad, soyad FROM yazarlar';
    $stmt = $baglanti -> prepare($yazarQuery);
    $stmt->execute();
    $result = $stmt -> get_result();
    $yazarCount = $result->num_rows; 
    $yazarlar = $result->fetch_all();
    $stmt -> close();

    $kategoriQuery = 'SELECT ad FROM kategoriler';
    $stmt = $baglanti -> prepare($kategoriQuery);
    $stmt->execute();
    $result = $stmt -> get_result();
    $kategoriCount = $result->num_rows; 
    $kategoriler = $result->fetch_all();
    $stmt -> close();
    
    $KendiISBNLERQuery = 'SELECT kitaplar.ISBN FROM kitaplar 
    JOIN kitap_kutuphane on 
    kitaplar.ISBN = kitap_kutuphane.ISBN where kitap_kutuphane.KutuphaneID=? ';
    $stmt = $baglanti -> prepare($KendiISBNLERQuery);
    $stmt -> bind_param('i',$_SESSION['adminKutuphaneId']);
    $stmt->execute();
    $result = $stmt -> get_result();
    $KendiISBNLERCount = $result->num_rows; 
    $KendiISBNLER = $result->fetch_all();
    $stmt -> close();

    $HepsiISBNLERQuery = 'SELECT ISBN FROM kitaplar';
    $stmt = $baglanti -> prepare($HepsiISBNLERQuery); 
    $stmt->execute();
    $result = $stmt -> get_result();
    $HepsiISBNLERCOUNT = $result->num_rows; 
    $HepsiISBNLER = $result->fetch_all();
    $stmt -> close();

    if(isset($_POST['newbook-btn'])){
        $KitapAdi=$_POST['KitapAdi']; 
        $KitapSayfaSayisi=$_POST['KitapSayfaSayisi'];
        $KitapYayin=$_POST['KitapYayin'];   
        $KitapISBN=$_POST['KitapISBN'];
        $miktar=$_POST['miktar'];
        $yazarlarinput=$_POST['yazarlar'];
        $kategorilerinput=$_POST['kategoriler'];

        $yazarlarListesi=preg_split ("/\,/", $yazarlarinput);
        $kategorilerListesi=preg_split ("/\,/", $kategorilerinput);
        
        //print_r($kategorilerListesi[0]);
        $butunisbnlerList=array();
        for ($i=0; $i < $HepsiISBNLERCOUNT; $i++) { 
            array_push($butunisbnlerList,$HepsiISBNLER[$i][0]);
        }

        $kendiisbnlerList=array();
        for ($i=0; $i < $KendiISBNLERCount; $i++) { 
            array_push($kendiisbnlerList,$KendiISBNLER[$i][0]);
        }

        if(in_array($KitapISBN, $butunisbnlerList)){
            if(in_array($KitapISBN, $kendiisbnlerList)){
                $errors['aldready-added'] = "Kitap Zaten Mevcut";
            }else{
                
                $kategoriIDList=array();
                $yazarIDList=array();
                for ($i=0; $i < count($kategorilerListesi); $i++) { 
                    
                    $kategoriIDQuery = 'SELECT ID from kategoriler where ad =?;';
                    $stmt = $baglanti -> prepare($kategoriIDQuery); 
                    $stmt -> bind_param('s',$kategorilerListesi[$i]);
                    $stmt->execute();
                    $result = $stmt -> get_result();
                    $kategoriID = $result->fetch_assoc();
                    $stmt -> close();

                    array_push($kategoriIDList,$kategoriID['ID']);
                }
                

                for ($i=0; $i < count($kategoriIDList); $i++) { 
                    // BUNU KONTROL ETMEDIM
                    $kategoriInsertQuery = 'INSERT INTO kitap_kategori (KategoriID, ISBN) VALUES (?,?);';
                    $stmt = $baglanti -> prepare($kategoriInsertQuery); 
                    $stmt -> bind_param('ss',$kategoriIDList[$i],$KitapISBN);
                    $stmt->execute();

                }

                $kitapKutupInsertQuery = 'INSERT INTO kitap_kutuphane (Miktar,KutuphaneID, ISBN) 
                VALUES (?,?,?);';
                $stmt = $baglanti -> prepare($kitapKutupInsertQuery); 
                $stmt -> bind_param('sss',$miktar,$_SESSION['adminKutuphaneId'],$KitapISBN);
                $stmt->execute();


                for ($i=0; $i < count($yazarlarListesi); $i++) { 
                    
                    $yazarIDQuery = 'SELECT ID FROM yazarlar where CONCAT(ad, " ", soyad) = ?;';
                    $stmt = $baglanti -> prepare($yazarIDQuery); 
                    $stmt -> bind_param('s',$yazarlarListesi[$i]);
                    $stmt->execute();
                    $result = $stmt -> get_result();
                    $yazarID = $result->fetch_assoc();
                    $stmt -> close();

                    array_push($yazarIDList,$yazarID['ID']);
                }

                for ($i=0; $i < count($yazarIDList); $i++) { 
                    $yazarInsertQuery = 'INSERT INTO kitap_yazar (YazarID, ISBN) VALUES (?,?);';
                    $stmt = $baglanti -> prepare($yazarInsertQuery); 
                    $stmt -> bind_param('ss',$yazarIDList[$i],$KitapISBN);
                    $stmt->execute();

                }
            }
        }else{
            $addToKitaplarQuery = 'INSERT INTO kitaplar (ISBN, Baslik, Sayfa, Yayin) VALUES (?,?,?,?);';
            $stmt = $baglanti -> prepare($addToKitaplarQuery); 
            $stmt -> bind_param('ssss',$KitapISBN,$KitapAdi,$KitapSayfaSayisi,$KitapYayin);
            $stmt->execute();

            $kategoriIDList=array();
                $yazarIDList=array();
                for ($i=0; $i < count($kategorilerListesi); $i++) { 
                    
                    $kategoriIDQuery = 'SELECT ID from kategoriler where ad =?;';
                    $stmt = $baglanti -> prepare($kategoriIDQuery); 
                    $stmt -> bind_param('s',$kategorilerListesi[$i]);
                    $stmt->execute();
                    $result = $stmt -> get_result();
                    $kategoriID = $result->fetch_assoc();
                    $stmt -> close();

                    array_push($kategoriIDList,$kategoriID['ID']);
                }
                

                for ($i=0; $i < count($kategoriIDList); $i++) { 
                    // BUNU KONTROL ETMEDIM
                    $kategoriInsertQuery = 'INSERT INTO kitap_kategori (KategoriID, ISBN) VALUES (?,?);';
                    $stmt = $baglanti -> prepare($kategoriInsertQuery); 
                    $stmt -> bind_param('ss',$kategoriIDList[$i],$KitapISBN);
                    $stmt->execute();

                }

                $kitapKutupInsertQuery = 'INSERT INTO kitap_kutuphane (Miktar,KutuphaneID, ISBN) 
                VALUES (?,?,?);';
                $stmt = $baglanti -> prepare($kitapKutupInsertQuery); 
                $stmt -> bind_param('sss',$miktar,$_SESSION['adminKutuphaneId'],$KitapISBN);
                $stmt->execute();


                for ($i=0; $i < count($yazarlarListesi); $i++) { 
                    
                    $yazarIDQuery = 'SELECT ID FROM yazarlar where CONCAT(ad, " ", soyad) = ?;';
                    $stmt = $baglanti -> prepare($yazarIDQuery); 
                    $stmt -> bind_param('s',$yazarlarListesi[$i]);
                    $stmt->execute();
                    $result = $stmt -> get_result();
                    $yazarID = $result->fetch_assoc();
                    $stmt -> close();

                    array_push($yazarIDList,$yazarID['ID']);
                }

                for ($i=0; $i < count($yazarIDList); $i++) { 
                    $yazarInsertQuery = 'INSERT INTO kitap_yazar (YazarID, ISBN) VALUES (?,?);';
                    $stmt = $baglanti -> prepare($yazarInsertQuery); 
                    $stmt -> bind_param('ss',$yazarIDList[$i],$KitapISBN);
                    $stmt->execute();

                }
        }
        

    }
?>

<?php 
    include 'base.php';
?>
<div class="container my-auto">
    <div class="container mt-3" style="width: 25rem; text-align: center;">
        <?php if(count($errors)>0): ?>
            <div class="alert alert-danger">
            <?php foreach($errors as $error): ?>
                <li><?php echo $error; ?></li>
            <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
    <form novalidate action="add.php" method="POST">
        <div id='kitapDiv' class="container">
            <div id='yazarlarDiv' style='visibility: visible;'>
                <label class="form-label">Yazarlar</label>
                <input type="email" required list="yazarlist" multiple name="yazarlar" class="form-control border-0"
                    placeholder="Yazar1,Yazar2">
                <datalist class="form-select" name="yazarlar" id="yazarlist">
                    <option disabled>Open this select menu</option>
                    <?php for ($y = 0; $y < $yazarCount ; $y++) { ?>
                        <option value="<?php echo $yazarlar[$y][0]." ".$yazarlar[$y][1] ?>"></option>
                    <?php } ?>
                </datalist>
                <a href="yazarEkle.php" rel="noopener noreferrer"
                    class="btn btn-primary mt-3 mb-3 float-right"
                    title="İstediğiniz yazar listede yoksa ekleyin !">EKLE</a>
            </div>
            <div class="Kitap">
                <div class="mb-3 float-left">
                    <label for="KitapAdi" class="form-label">Kitap Adı</label>
                    <input type="text" required class="form-control border-0" id="KitapAdi" name="KitapAdi"
                        placeholder="Kitap Adı giriniz">
                </div>
                <div class="mb-3 float-right">
                    <label for="KitapSayfaSayisi" class="form-label">Kitap Sayfa Sayısı</label>
                    <input type="number" min="0" required class="form-control border-0" id="KitapSayfaSayisi"
                        name="KitapSayfaSayisi" placeholder="Sayfa Sayısı giriniz">
                </div>
                <div class="mb-3 float-left">
                    <label for="KitapYayin" class="form-label">Kitap Yayınevi</label>
                    <input type="text" required class="form-control border-0" id="KitapYayin" name="KitapYayin"
                        placeholder="Yayınevi giriniz">
                </div>
                <div class="mb-3 float-right">
                    <label for="KitapISBN" class="form-label">Kitap ISBN</label>
                    <input type="text" required class="form-control border-0" id="KitapISBN" name="KitapISBN"
                        placeholder="ISBN giriniz">
                </div>
                <div class="mb-3 float-left">
                    <label for="miktar" class="form-label">Miktar</label>
                    <input type="number" required min="0" class="form-control border-0" id="miktar" name="miktar"
                        placeholder="Miktar giriniz">
                </div>

                <div class="mb-3 float-right">
                    <label class="form-label">Kategoriler</label> <br>
                    <input type="email" required list="kategorilist" multiple name="kategoriler"
                        class="form-control border-0" placeholder="Kategori Seçiniz">
                    <datalist class="form-select" name="kategorilerdatalist" id="kategorilist">
                        <option disabled>Open this select menu</option>
                        <?php for ($k = 0; $k < $kategoriCount ; $k++) { ?>
                            <option value="<?php echo $kategoriler[$k][0]?>"></option>
                        <?php } ?>
                    </datalist>
                </div>
            </div>
            <div>
                <button id='topluKaydet' name='newbook-btn' type="submit" class="btn btn-primary mt-3 align-center">Kaydet</button>
            </div>
        </div>
    </form>
</div>