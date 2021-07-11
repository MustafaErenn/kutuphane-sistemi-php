<?php 

session_start(); // session başlatıyoruz.,
require "config/config.php"; // veritabanına baglanıyoruz.

        $adminUserName='';   
        $adminPassword='';
        $userEmail='';
        $userTel='';

        $ad="";
        $soyad="";
        $il="";
        $ilce="";
        $postakodu="";
        $ikamet="";
        $email="";
        $telefon="";

        $ad2="";
        $soyad2="";
        $il2="";
        $ilce2="";
        $postakodu2="";
        $ikamet2="";
        $email2="";
        $telefon2="";
        $errors = array(); // hataları depolucak array

        if (isset($_POST['login-btn'])) { // giriş yap butonuna basarsa çalışacak
            $adminUserName=$_POST['username']; // form verisini alıyor
            $adminPassword=$_POST['password'];//  form verisini alıyor

            if(empty($adminUserName)){
                $errors['adminUserName'] = "Kullanacı adı boş bırakılamaz";
            }
            if(empty($adminPassword)){
                $errors['adminPassword'] = "Şifre boş bırakılamaz";
            }


            $emailQuery = "SELECT * FROM adminler where KullaniciAdi=? and Sifre=? LIMIT 1";
            $stmt = $baglanti -> prepare($emailQuery);
            $stmt -> bind_param('ss',$adminUserName,$adminPassword);
            $stmt->execute();
            $result = $stmt -> get_result();
            $adminCount = $result->num_rows; 
            $admin = $result->fetch_assoc();
            $stmt -> close();

            if($adminCount!==0){
                echo "admin bulundu";
                if(count($errors)===0){
                    $_SESSION['id'] = $admin['ID'];
                    $_SESSION['adminUserName'] = $admin['KullaniciAdi'] ;
                    $_SESSION['adminKutuphaneId'] = $admin['KutuphaneID'] ;
                    header('location: search.php'); 
                    exit();
                }
            }
            else{
                $errors['usernotfound'] = "Böyle bir admin bulunamadı";
            }
        }

        

        if (isset($_POST['memberlogin-btn'])) {
            $userEmail=$_POST['memberemail']; // form verisini alıyor
            $userTel=$_POST['membertel'];//  form verisini alıyor

            if(empty($userEmail)){
                $errors['userEmail'] = "Eposta boş bırakılamaz";
            }
            if(empty($userTel)){
                $errors['userTel'] = "Telefon numarası boş bırakılamaz";
            }

            $userQuery = "SELECT * FROM uyeler where Eposta=? and Telefon = ? ";
            $stmt = $baglanti -> prepare($userQuery);
            $stmt -> bind_param('ss',$userEmail,$userTel);
            $stmt->execute();
            $result = $stmt -> get_result();
            $userCount = $result->num_rows; 
            $user = $result->fetch_assoc();
            $stmt -> close();


            if($userCount!==0){
                if(count($errors)===0){
                    header("Location: profile.php?userid={$user['ID']}"); 
                    exit();
                }
            }
            else{
                $errors['usernotfound'] = "Böyle bir kullanıcı bulunamadı";
            }
        }


        if (isset($_POST['register-btn'])) { 
            $ad=$_POST['ad'];
            $soyad=$_POST['soyad'];
            $il=$_POST['il'];
            $ilce=$_POST['ilce'];
            $postakodu=$_POST['postakodu'];
            $ikamet=$_POST['ikamet'];
            $email=$_POST['email'];
            $telefon=$_POST['telefon'];

            echo $ad;
            echo $soyad;
            echo $il;
            echo $ilce;
            echo $postakodu;
            echo $email;
            echo $telefon;
            echo $ikamet;

            if(empty($ad)){
                $errors['ad'] = "İsim boş bırakılamaz";
            }
            if(empty($soyad)){
                $errors['soyad'] = "Soyisim boş bırakılamaz";
            }
            if(empty($email)){
                $errors['email'] = "Eposta boş bırakılamaz";
            }
            if(empty($telefon)){
                $errors['telefon'] = "Telefon boş bırakılamaz";
            }
            if(empty($il)){
                $errors['il'] = "İl boş bırakılamaz";
            }
            if(empty($ilce)){
                $errors['ilce'] = "İlçe boş bırakılamaz";
            }
            if(empty($postakodu)){
                $errors['postakodu'] = "Posta Kodu boş bırakılamaz";
            }
            if(empty($ikamet)){
                $errors['ikamet'] = "İkamet Adresi boş bırakılamaz";
            }

            $uyeQuery = "SELECT * FROM uyeler where Eposta=?";
            $stmt = $baglanti -> prepare($uyeQuery);
            $stmt -> bind_param('s',$email);
            $stmt->execute();
            $result = $stmt -> get_result();
            $uyeCount = $result->num_rows; 
            $uye = $result->fetch_assoc();
            $stmt -> close();

            if($uyeCount==0){
                $uyeAdresiInsert = 'INSERT into adresler (Il, Ilce, PostaKodu, İkamet_Adresi)
                values
                (?,?,?,?)';
                $stmt = $baglanti -> prepare($uyeAdresiInsert); 
                $stmt -> bind_param('ssss',$il,$ilce,$postakodu,$ikamet);
                $stmt->execute();
                $stmt->close();
                $adres_id = $baglanti->insert_id;

                $uyeTablosuInsert = 'INSERT into uyeler (Ad, Soyad, Eposta, Telefon, AdresID)
                values
                (?,?,?,?,?)';
                $stmt = $baglanti -> prepare($uyeTablosuInsert); 
                $stmt -> bind_param('sssss',$ad,$soyad,$email,$telefon,$adres_id);
                $stmt->execute();
                $stmt->close();

            }else{
                $errors['emailmevcut'] = "E-mail kullanımda";
            }
        }


        if (isset($_POST['editprofile-btn'])) { 
            $ad2=$_POST['ad'];
            $soyad2=$_POST['soyad'];
            $il2=$_POST['il'];
            $ilce2=$_POST['ilce'];
            $postakodu2=$_POST['postakodu'];
            $ikamet2=$_POST['ikamet'];
            $email2=$_POST['email'];
            $telefon2=$_POST['telefon'];


            if(empty($ad2)){
                $errors['ad2'] = "İsim boş bırakılamaz";
            }
            if(empty($soyad2)){
                $errors['soyad2'] = "Soyisim boş bırakılamaz";
            }
            if(empty($email2)){
                $errors['email2'] = "Eposta boş bırakılamaz";
            }
            if(empty($telefon2)){
                $errors['telefon2'] = "Telefon boş bırakılamaz";
            }
            if(empty($il2)){
                $errors['il2'] = "İl boş bırakılamaz";
            }
            if(empty($ilce2)){
                $errors['ilce2'] = "İlçe boş bırakılamaz";
            }
            if(empty($postakodu2)){
                $errors['postakodu2'] = "Posta Kodu boş bırakılamaz";
            }
            if(empty($ikamet2)){
                $errors['ikamet2'] = "İkamet Adresi boş bırakılamaz";
            }

            $uyeQuery = "SELECT * FROM uyeler where Eposta=?";
            $stmt = $baglanti -> prepare($uyeQuery);
            $stmt -> bind_param('s',$email2);
            $stmt->execute();
            $result = $stmt -> get_result();
            $uyeCount = $result->num_rows; 
            $uye = $result->fetch_assoc();
            $stmt -> close();

            if($uyeCount==0 || ($email2==$uye['Eposta'])){

                $uyeID = $uye['ID'];
                echo "email uygun";
                $uyeInfoEditQuery = 'UPDATE uyeler SET ad=?,soyad=?,Eposta=?,
                Telefon=? where uyeler.ID=?';
                $stmt = $baglanti -> prepare($uyeInfoEditQuery); 
                $stmt -> bind_param('sssss',$ad2,$soyad2,$email2,$telefon2,$uyeID);
                $stmt->execute();
                $stmt->close();

                $uyeAdresId = $uye['AdresID'];

                $uyeAdresInfoEditQuery = 'UPDATE adresler SET Il=?,Ilce=?,İkamet_Adresi=?,
                PostaKodu=? where adresler.ID=?;';
                $stmt = $baglanti -> prepare($uyeAdresInfoEditQuery); 
                $stmt -> bind_param('sssss',$il2,$ilce2,$ikamet2,$postakodu2,$uyeAdresId);
                $stmt->execute();
                $stmt->close();

                echo 'güncellenmiş olması lazım';
                echo 'güncellenmiş olması lazım';

            }else{
                $errors['emailmevcut'] = "E-mail kullanımda";
            }
        }

        if(isset($_GET['logout'])){
            session_destroy();
            unset($_SESSION['id']);
            unset($_SESSION['adminUserName']);
            unset($_SESSION['adminKutuphaneId']);

            
            header('location: login.php');
            exit();
        }

?>