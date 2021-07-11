<?php 
    require_once "controllers/authController.php";
    if(!isset($_SESSION['id'])){
        header("location:login.php");
        exit();
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
    <form action="register.php" method="POST">
        <div class="registerdivs my-auto">
            <div class="d-flex justify-content-between">
                <div class="mb-2">
                    <label for="ad" class="form-label">İsim</label>
                    <input type="text" required class="form-control border-0" id="ad" value="<?php echo $ad ?>"
                        name="ad" placeholder="İsim" />
                </div>
                <div class="mb-2">
                    <label for="soyad" class="form-label">Soyisim</label>
                    <input type="text" required class="form-control border-0" id="soyad"
                        value="<?php echo $soyad ?>" name="soyad" placeholder="Soyisim" />
                </div>
            </div>
            <div class="mb-2">
                <label for="telefon" class="form-label">Telefon Numarası</label>
                <input type="text" required class="form-control border-0" id="telefon"
                    value="<?php echo $telefon ?>" name="telefon" placeholder="(0 5XX XXX XX XX)" />
            </div>
            <div class="mb-2">
                <label for="email" class="form-label">E-mail</label>
                <input required type="email" class="form-control border-0" id="email" value="<?php echo $email ?>"
                    name="email" placeholder="mail@gmail.com" />
            </div>
            <div class="d-flex justify-content-between">
                <div class="mb-2">
                    <label for="Iller" class="form-label">İl</label>
                    <select required class="form-control border-0" id="Iller" value="<?php echo $il ?>" name="il">
                        <option hidden value="0">Lütfen Bir İl Seçiniz</option>
                    </select>
                </div>
                <div class="mb-2">
                    <label for="Ilceler" class="form-label">İlçe</label>
                    <select required class="form-control border-0" id="Ilceler" value="<?php echo $il ?>" disabled="disabled" name="ilce">
                        <option hidden value="0">Lütfen Önce bir İl seçiniz</option>
                    </select>
                </div>
            </div>
            <div class="mb-2">
                <label for="postakodu" class="form-label">Posta Kodu</label>
                <input type="text" required class="form-control border-0" id="postakodu"
                    value="<?php echo $postakodu ?>" name="postakodu" placeholder="Posta Kodu" />
            </div>
            <div class="mb-2">
                <label for="ikamet" class="form-label">İkamet Adresi</label>
                <input type="text" required class="form-control border-0" id="ikamet" value="<?php echo $ikamet ?>"
                    name="ikamet" placeholder="İkamet Adresi" />
            </div>
            <br>
            <button name='register-btn' class="btn btn-primary" type="submit">
                <a style="color: white">Kayıt Et</a>
            </button>
        </div>


    </form>
</div>