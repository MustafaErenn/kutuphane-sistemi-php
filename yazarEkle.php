<?php 
    $errors=array();
    require_once "controllers/authController.php";
    if(!isset($_SESSION['id'])){
        header("location:login.php");
        exit();
    }
    if(isset($_POST['yazarkaydet-btn'])){
        $YazarName=$_POST['YazarName']; 
        $YazarSurname=$_POST['YazarSurname'];

        if(empty($YazarName)){
            $errors['YazarName'] = "Yazar Adı boş bırakılamaz";
        }
        if(empty($YazarSurname)){
            $errors['YazarSurname'] = "Yazar Soyadı bırakılamaz";
        }

        if(count($errors)==0){
            $yazarEkleQuery = 'INSERT INTO yazarlar (ad, soyad) VALUES (?,?);';
            $stmt = $baglanti -> prepare($yazarEkleQuery); 
            $stmt -> bind_param('ss',$YazarName,$YazarSurname);
            $stmt->execute();
        }
    }

?>

<?php 
    include 'base.php';
?>


<div id='kitapDiv' class="container">

    <div id='kitapDiv' class="container mt-4" style='display: flex;justify-content: center;align-items: center;'>
        <div id='yazarEkleDiv' class="yazarEkle">

            <div class="container mt-3" style="width: 25rem; text-align: center;">
                <?php if(count($errors)>0): ?>
                    <div class="alert alert-danger">
                    <?php foreach($errors as $error): ?>
                        <li><?php echo $error; ?></li>
                    <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <form id='yazarEkleForm' action='yazarEkle.php' method="POST">
                <div class="mb-3" style='width:25rem'>
                    <label style='user-select:none' class="form-label">Yazar Adı</label>
                    <input type="text" required class="form-control border-0" id="YazarName 1" name="YazarName"
                        placeholder="Yazar adı giriniz">
                </div>
                <div class="mb-3" style='width:25rem'>
                    <label style='user-select:none' class="form-label">Yazar Soyadı</label>
                    <input type="text" class="form-control border-0" id="YazarSurname 1" name="YazarSurname"
                        placeholder="Yazar soyadı giriniz">
                </div>
                <button id='YazalariKaydet' name='yazarkaydet-btn' type="submit" class="btn btn-primary">Yazarı
                    Kaydet</button>
            </form>
            <a id='topluKaydet' href="add.php" class="btn btn-primary mt-3">Geri dön</a>

        </div>

        <br><br>

    </div>

</div>
