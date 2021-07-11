<?php 
        //include 'base.php';

        require_once "controllers/authController.php";

	if(isset($_SESSION['id'])){
        echo "buradayım";
        echo $_SESSION['id'];
        header("location:search.php"); 
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
            <li><?php echo $error; ?></li>
        <?php endforeach; ?>
        </div>
    <?php endif; ?>
    
    <form action="login.php" method="POST">
        <div class="mb-3">
            <label for="exampleFormControlInput1" class="form-label">Admin Kullanıcı Adı</label>
            <input type="text" class="form-control" value="<?php echo $adminUserName ?>" name="username"
                placeholder="admin">
        </div>

        <div class="mb-3">
            <label for="exampleFormControlInput1" class="form-label">Admin Şifre</label>
            <input type="password" class="form-control" value="<?php echo $adminPassword ?>" name="password"
                placeholder="password">
        </div>
        <div>
            <button class="btn btn-primary" name="login-btn" type="submit"><a style="color: white;">Admin Girişi
                </a></button>

        </div>

    </form>
</div>