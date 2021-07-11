<?php 
    require_once "controllers/authController.php";
    if(!isset($_SESSION['id'])){
        header("location:login.php");
        exit();
    }

    $errors = array();

    if(isset($_POST['deletebook-btn'])){

        $KitapISBN=$_POST['KitapISBN'];
        $miktar=$_POST['miktar'];

        if(empty($KitapISBN)){
            $errors['KitapISBN'] = "ISBN boş bırakılamaz";
        }
        if(empty($miktar)){
            $errors['miktar'] = "Miktar bilgisi boş bırakılamaz";
        }

        $kitapInfo = 'SELECT kitap_kutuphane.ISBN, kitap_kutuphane.Miktar 
        from kitap_kutuphane 
        where kitap_kutuphane.ISBN =?
        and kitap_kutuphane.KutuphaneID =?';
        $stmt = $baglanti -> prepare($kitapInfo);
        $stmt -> bind_param('si',$KitapISBN,$_SESSION['adminKutuphaneId']);
        $stmt->execute();
        $result = $stmt -> get_result();
        $kitapInfoCount = $result->num_rows; 
        $kitapAddInfo = $result->fetch_assoc();
        $stmt -> close();

        if($kitapInfoCount>0){
            try {

                if((int)$kitapAddInfo['Miktar']<(int)$miktar){
                    $errors['not-valid'] = "Geçersiz Miktar";
                }else{
                    $integerMiktar = (int)$miktar;
                    $updateBookAmount = 'UPDATE kitap_kutuphane SET Miktar=Miktar-? WHERE ISBN = ? and kutuphaneID = ?';
                    $stmt = $baglanti -> prepare($updateBookAmount); 
                    $stmt -> bind_param('isi',$integerMiktar,$KitapISBN,$_SESSION['adminKutuphaneId']);
                    $stmt->execute();
                }
            } catch (Exception $e) {
                $errors['not-valid'] = "Geçersiz MiktarE";
            }

            
        }else{
            $errors['not-exist'] = "Kitap Mevcut Değil";
        }
    }
?>

<?php 
include 'base.php';
?>
<div class="container my-auto">
    <form action="" method="POST">
        <div id='kitapDiv' class="container">
            <div class="Kitap">
                <div class="mb-3 float-left">
                    <label for="KitapISBN" class="form-label">Kitap ISBN</label>
                    <input type="text" required class="form-control border-0" id="KitapISBN" name="KitapISBN"
                        placeholder="ISBN giriniz">
                </div>
                <div class="mb-3 float-right">
                    <label for="miktar" class="form-label">Silinecek Adet</label>
                    <input type="number" required min="0" class="form-control border-0" id="miktar" name="miktar"
                        placeholder="Adet giriniz">
                </div>

            </div>
            <div style="justify-content: center; display: flex; align-items: center; flex-direction: column;">
                <div>
                    <button name='deletebook-btn' id='topluKaydet' type="submit" class="btn btn-primary mt-3 align-center">Sil</button>
                </div>

                <div class="mt-3">
                    <?php if(count($errors)>0): ?>
                    <div class="alert alert-danger">
                        <?php foreach($errors as $error): ?>
                        <li>
                            <?php echo $error; ?>
                        </li>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>

            </div>

        </div>
    </form>
</div>