<?php 
        require_once "controllers/authController.php";

        if(!isset($_SESSION['id'])){
            header("location:login.php");
            exit();
        }
    
        $errors = array();

        

        if(isset($_POST['uyesil-btn'])){
            $uyeeposta=$_POST['uyeeposta'];
            $uyetel=$_POST['uyetel'];


            $sqlUyeID = 'SELECT ID from uyeler where Eposta=? and Telefon=?;';
            $stmt = $baglanti -> prepare($sqlUyeID); 
            $stmt -> bind_param('ss',$uyeeposta,$uyetel);
            $stmt->execute();
            $result = $stmt -> get_result();
            $uyeInfoCount = $result->num_rows; 
            $uyeIdResult = $result->fetch_assoc();
            $stmt -> close();

            $uyeID = $uyeIdResult['ID'];

            if($uyeInfoCount>=1){
                $uyeEmanetQuery = 'SELECT * FROM kitap_emanet where kitap_emanet.UyeID=? and kitap_emanet.Durum=0;';
                $stmt = $baglanti -> prepare($uyeEmanetQuery); 
                $stmt -> bind_param('i',$uyeID);
                $stmt->execute();
                $result = $stmt -> get_result();
                $uyeEmanetCount = $result->num_rows; 
                $stmt -> close();

                if($uyeEmanetCount>0){
                    $errors['emanetivar'] = 'Üye silme işlemi başarısız. Teslim Edilmemiş Kitap Mevcut.';
                }else{
                    $uyeSilmeQuery = 'DELETE adresler
                    FROM uyeler
                    LEFT JOIN adresler ON uyeler.AdresID = adresler.ID
                    LEFT JOIN kitap_emanet on uyeler.ID = kitap_emanet.UyeID
                    WHERE uyeler.ID =?;';
                    $stmt = $baglanti -> prepare($uyeSilmeQuery); 
                    $stmt -> bind_param('i',$uyeID);
                    $stmt->execute();
                }
            }
        }

?>

<?php 
include 'base.php';
?>
<div class="container my-auto">
    <div class="container mt-2" style="width: 30rem;">
        <?php if(count($errors)>0): ?>
        <div class="alert alert-danger">
            <?php foreach($errors as $error): ?>
            <li>
                <?php echo $error; ?>
            </li>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <form action="uyeSil.php" method="POST">
            <div class="form-group">
                <label for="exampleInputEmail1">E-posta:</label>
                <input type="email" required class="form-control border-0" id="uyeeposta" name="uyeeposta"
                    placeholder="E-Posta">
            </div>
            <div class="form-group">
                <label for="exampleInputPassword1">Telefon:</label>
                <input type="tel" required class="form-control border-0" id="uyetel" name="uyetel"
                    placeholder="Telefon Numarası">
            </div>

            <div class="text-center">
                <button name='uyesil-btn' type="submit" class="btn btn-primary">Uye Sil</button>
            </div>
        </form>
    </div>
</div>