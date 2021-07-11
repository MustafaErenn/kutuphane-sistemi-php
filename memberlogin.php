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
<div class="login ">
    <?php if(count($errors)>0): ?>
    <div class="alert alert-danger">
        <?php foreach($errors as $error): ?>
        <li>
            <?php echo $error; ?>
        </li>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
    <form action="memberlogin.php" method="POST">
        <div class="mb-3">
            <label for="exampleFormControlInput1" class="form-label">Üye Eposta</label>
            <input type="email" class="form-control" value="<?php echo $userEmail ?>" name="memberemail"
                placeholder="Eposta">
        </div>

        <div class="mb-3">
            <label for="exampleFormControlInput1" class="form-label">Üye Telefon</label>
            <input type="tel" class="form-control" value="<?php echo $userTel ?>" name="membertel"
                placeholder="Telefon">
        </div>
        <div>
            <button name='memberlogin-btn' class="btn btn-primary" type="submit"><a style="color: white;">Görüntüle
                </a></button>
        </div>

    </form>
</div>