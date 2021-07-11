<?php 
    require_once "controllers/authController.php";
    if(!isset($_SESSION['id'])){
        header("location:login.php");
        exit();
    }

    if(isset($_GET['userid'])){ 
        $userid=$_GET['userid'];
        $allMemberInfoQuery = 'SELECT uyeler.id,ad, soyad, eposta, telefon, il, ilce, İkamet_Adresi,adresler.postakodu  FROM uyeler 
        left JOIN adresler on adresler.ID = uyeler.AdresID
        where uyeler.id = ?';
        $stmt = $baglanti -> prepare($allMemberInfoQuery); 
        $stmt -> bind_param('i',$userid);
        $stmt->execute();
        $result = $stmt -> get_result();
        $uye = $result->fetch_assoc();
        $stmt -> close();

        $ad2=$uye['ad'];
        $soyad2=$uye['soyad'];
        $il2=$uye['il'];
        $ilce2=$uye['ilce'];
        $postakodu2=$uye['postakodu'];
        $ikamet2=$uye['İkamet_Adresi'];
        $email2=$uye['eposta'];
        $telefon2=$uye['telefon'];
        
    }
?>
<?php 
include 'base.php';
?>
<div class="container register my-auto">
    <?php if(count($errors)>0): ?>
    <div class="alert alert-danger">
        <?php foreach($errors as $error): ?>
        <li>
            <?php echo $error; ?>
        </li>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
    <form action="uyeDuzenle.php" method="POST">
        <div class="registerdivs my-auto">
            <div class="d-flex justify-content-between">
                <div class="mb-2">
                    <label for="ad" class="form-label">İsim</label>
                    <input type="text" required class="form-control border-0" id="ad" value="<?php echo $ad2 ?>"
                        name="ad" placeholder="İsim" />
                </div>
                <div class="mb-2">
                    <label for="soyad" class="form-label">Soyisim</label>
                    <input type="text" required class="form-control border-0" id="soyad" value="<?php echo $soyad2 ?>"
                        name="soyad" placeholder="Soyisim" />
                </div>
            </div>
            <div class="mb-2">
                <label for="telefon" class="form-label">Telefon Numarası</label>
                <input type="text" required class="form-control border-0" id="telefon" value="<?php echo $telefon2 ?>"
                    name="telefon" placeholder="(0 5XX XXX XX XX)" />
            </div>
            <div class="mb-2">
                <label for="email" class="form-label">E-mail</label>
                <input required type="email" class="form-control border-0" id="email" value="<?php echo $email2 ?>"
                    name="email" placeholder="mail@gmail.com" />
            </div>
            <div class="d-flex justify-content-between">
                <div class="mb-2">
                    <label for="Iller" class="form-label">İl</label>
                    <select required class="form-control border-0" id="Iller" value="<?php echo $il2 ?>" name="il">
                        <option hidden value="0">Lütfen Bir İl Seçiniz</option>
                    </select>
                </div>
                <div class="mb-2">
                    <label for="Ilceler" class="form-label">İlçe</label>
                    <select required class="form-control border-0" id="Ilceler" value="<?php echo $ilce ?>"
                        disabled="disabled" name="ilce">
                        <option hidden value="0">Lütfen Önce bir İl seçiniz</option>
                    </select>
                </div>
            </div>
            <div class="mb-2">
                <label for="postakodu" class="form-label">Posta Kodu</label>
                <input type="text" required class="form-control border-0" id="postakodu"
                    value="<?php echo $postakodu2 ?>" name="postakodu" placeholder="Posta Kodu" />
            </div>
            <div class="mb-2">
                <label for="ikamet" class="form-label">İkamet Adresi</label>
                <input type="text" required class="form-control border-0" id="ikamet" value="<?php echo $ikamet2 ?>"
                    name="ikamet" placeholder="İkamet Adresi" />
            </div>
            <br>
            <button name='editprofile-btn' class="btn btn-primary" type="submit">
                <a style="color: white">Güncelle !</a>
            </button>
        </div>


    </form>
</div>