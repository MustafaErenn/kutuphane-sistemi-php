<?php 
    require_once "controllers/authController.php";
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.4.1/css/all.css" />
    <script type=text/javascript src="main.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="style.css">
    <link href='https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css'>
    <script src='https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.bundle.min.js'></script>
    <link href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css'>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js'></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-steps/1.1.0/jquery.steps.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet" />
    <title>Kütüphane Yönetim Sistemi</title>

</head>

<body class='bodyBG'>
    <div id=" main">
        <div>
            <nav class="navbar navbar-expand-md" style="background-color: #243A51;  padding: 10px 10px">
                <div id="mySidenav" class="sidenav" style='overflow: hidden;'>
                    <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a>
                    <?php if(isset($_SESSION['id'])): ?>
                    
                    
                    

                    <a style="font-size:20px" href="search.php"><i class="fa fa-search"></i>&nbspTeslim
                        Et</a>
                    <a style="font-size:20px" href="teslimAl.php"><i
                            class="fa fa-handshake"></i>&nbspTeslim
                        Al</a>
                    <a style="font-size:20px" href="add_exist.php"><i
                            class="bi bi-journal-plus"></i>&nbspKitap
                        Ekle</a>
                    <a style="font-size:20px" href="delete_book.php"><i
                            class="bi bi-journal-x"></i>&nbspKitap
                        Sil</a>
                    <a style="font-size:20px" href="memberlogin.php"><i
                            class="bi bi-person-bounding-box"></i>&nbspÜye
                        Görüntüle
                    </a>

                    <a style="font-size:20px" href="register.php"><i class="fa fa-user-plus"></i>&nbspÜye
                        Kayıt
                        Et</a>


                    <a style="font-size:20px" href="uyeSil.php"><i
                            class="fas fa-user-times"></i>&nbspÜye
                        Sil
                    </a>
                    <a style="font-size:20px" href="base.php?logout=1"><i
                            class="bi bi-box-arrow-in-left"></i>&nbspÇıkış Yap</a>
                    <?php else: ?>
                    <a style="font-size:20px" href="login.php"><i class="fa fa-sign-in"></i>&nbspOturum
                        Aç</a>
                    <a style="font-size:20px" href="memberlogin.php"><i
                            class="bi bi-person-bounding-box"></i>&nbspÜye
                        Görüntüle
                    </a>
                    <?php endif; ?>
                </div>
                <div>
                    <span style="font-size:18px;cursor:pointer; color: white;" onclick="openNav()">
                        <span style="font-size: 22px; font-weight: 1000; padding-right: 15px;">&#9776;</span>Kütüphane
                        Yönetim Sistemi</span>
                </div>


            </nav>
        </div>

    </div>
</body>

</html>