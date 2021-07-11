<?php 

    require_once "controllers/authController.php";

	if(!isset($_SESSION['id'])){
        header("location:login.php");
        exit();
    }

    $errors = array();

    if(isset($_POST['teslimal-btn'])){
        $kitapisbn=$_POST['kitapisbn'];
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
            $teslimAlQuery = 'UPDATE
	        kitap_emanet 
            SET kitap_emanet.Durum = 1,kitap_emanet.TeslimTarih = NOW()
            where 
            kitap_emanet.UyeID= ?
            and kitap_emanet.Durum=0 
            and kitap_emanet.ISBN=?
            and kitap_emanet.KutuphaneID = ?;';
            $stmt = $baglanti -> prepare($teslimAlQuery); 
            $stmt -> bind_param('isi',$uyeID,$kitapisbn,$_SESSION['adminKutuphaneId']);
            $stmt->execute();

            $affectedRowCount = $baglanti->affected_rows;

            if($affectedRowCount==0){
                $errors['teslimedilmemis'] = 'Böyle bir kitap emanet edilmemiş.';
            }else{
                $MiktarArtirmaQuery = 'UPDATE kitap_kutuphane
                SET kitap_kutuphane.Miktar = kitap_kutuphane.Miktar+1 
                where kitap_kutuphane.KutuphaneID = ?';
                $stmt = $baglanti -> prepare($MiktarArtirmaQuery); 
                $stmt -> bind_param('i',$_SESSION['adminKutuphaneId']);
                $stmt->execute();
            }
        }else{
            $errors['uyeyok'] = 'İşlem başarısız bilgileri kontrol edin ';
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

        <form action="teslimAl.php" method="POST">
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

            <div class="form-group">
                <label for="exampleInputPassword1">Kitap ISBN</label>
                <input type="tel" required class="form-control border-0" id="kitapisbn" name="kitapisbn"
                    placeholder="Kitap ISBN">
            </div>



            <div class="text-center">
                <button name='teslimal-btn' type="submit" class="btn btn-primary">Teslim Al</button>
            </div>
        </form>
    </div>
</div>