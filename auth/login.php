<?php
session_start();
require '../config.php';
$tipe = "Masuk";

if (isset($_COOKIE['cookie_token'])) {
    $data = $conn->query("SELECT * FROM users WHERE cookie_token='" . $_COOKIE['cookie_token'] . "'");
    if (mysqli_num_rows($data) > 0) {
        $hasil = mysqli_fetch_assoc($data);
        $_SESSION['user'] = $hasil;
    }
}

if (isset($_SESSION['user'])) {
    header("Location: " . $config['web']['url']);
} else {

    if (isset($_POST['masuk'])) {
        $username = $conn->real_escape_string(filter($_POST['username']));
        $password = $conn->real_escape_string(filter($_POST['password']));

        $cek_pengguna = $conn->query("SELECT * FROM users WHERE username = '$username'");
        $cek_pengguna_ulang = mysqli_num_rows($cek_pengguna);
        $data_pengguna = mysqli_fetch_assoc($cek_pengguna);

        $verif_password = password_verify($password, $data_pengguna['password']);

        $error = array();
        if (empty($username)) {
            $error['username'] = '*Tidak Boleh Kosong';
        } else if ($cek_pengguna_ulang == 0) {
            $error['username'] = '*Pengguna Tidak Terdaftar';
        }
        if (empty($password)) {
            $error['password'] = '*Tidak Boleh Kosong';
        } else if ($verif_password <> $data_pengguna['password']) {
            $error['password'] = '*Kata Sandi Anda Salah';
        } else {

            if ($data_pengguna['status'] == "Tidak Aktif") {
                $_SESSION['hasil'] = array('alert' => 'danger', 'pesan' => 'Ups, Akun Sudah Tidak Aktif.<script>swal("Gagal!", "Akun Sudah Tidak Aktif.", "error");</script>');
            } else if ($data_pengguna['status_akun'] == "Belum Verifikasi") {
                $_SESSION['hasil'] = array('alert' => 'danger', 'pesan' => 'Ups, Akun Kamu Belum Di Verifikasi.<script>swal("Gagal!", "Akun Kamu Belum Di Verifikasi.", "error");</script>');
            } else {

                if ($cek_pengguna_ulang == 1) {
                    if ($verif_password == true) {
                        $remember = isset($_POST['remember']) ? TRUE : false;
                        if ($remember == TRUE) {
                            $cookie_token = md5($username);
                            $conn->query("UPDATE users SET cookie_token='" . $cookie_token . "' WHERE username='" . $username . "'");
                            setcookie('cookie_token', $cookie_token, time() + 60 * 60 * 24 * 365, '/');
                        }
                        $conn->query("INSERT INTO aktifitas VALUES ('','$username', 'Masuk', '" . get_client_ip() . "','$date','$time')");
                        $_SESSION['user'] = $data_pengguna;
                        exit(header("Location: " . $config['web']['url']));
                    } else {
                        $_SESSION['hasil'] = array('alert' => 'danger', 'pesan' => 'Ups, Gagal! Sistem Kami Sedang Mengalami Gangguan.<script>swal("Ups Gagal!", "Sistem Kami Sedang Mengalami Gangguan.", "error");</script>');
                    }
                }
            }
        }
    }
}

require '../lib/header_home.php';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- Mobile Specific Metas -->
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <title>Login</title>
    <!-- Favicon and Touch Icons  -->
    <link rel="shortcut icon" href="<?php echo $config['web']['url'] ?>mobile/images/logo.png" />
    <link rel="apple-touch-icon-precomposed" href="<?php echo $config['web']['url'] ?>mobile/images/logo.png" />
    <!-- Font -->
    <link rel="stylesheet" href="<?php echo $config['web']['url'] ?>mobile/fonts/fonts.css" />
    <!-- Icons -->
    <link rel="stylesheet" href="<?php echo $config['web']['url'] ?>mobile/fonts/icons-alipay.css">
    <link rel="stylesheet" href="../mobile/styles/bootstrap.css">

    <link rel="stylesheet" type="text/css" href="<?php echo $config['web']['url'] ?>mobile/styles/styles.css" />
    <link rel="manifest" href="<?php echo $config['web']['url'] ?>mobile/_manifest.json" data-pwa-version="set_in_manifest_and_pwa_js">
    <link rel="apple-touch-icon" sizes="192x192" href="<?php echo $config['web']['url'] ?>mobile/app/icons/icon-192x192.png">
        

</head>

<body>
     <!-- preloade -->
     <div class="preload preload-container">
        <div class="preload-logo">
          <div class="spinner"></div>
        </div>
      </div>
    <!-- /preload -->    
    <div class="mt-7 login-section">
        <div class="tf-container">
            <form class="tf-form" method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo $config['csrf_token'] ?>">
                    <h1>Login</h1>
                    <div class="group-input">
                        <label>Username</label>
                        <input type="text" name="username" value="<?php echo $username; ?>" required>
                        <small class="text-danger"><?php echo ($error['username']) ? $error['username'] : ''; ?></small>
                    </div>
                    <div class="group-input auth-pass-input last">
                        <label>Password</label>
                        <input type="password" class="password-input" name="password" required>
                        <a class="icon-eye password-addon" id="password-addon"></a>
                        <small class="text-danger"><?php echo ($error['password']) ? $error['password'] : ''; ?></small>
                    </div>
                    <a href="08_reset-password.html" class="auth-forgot-password mt-3">Forgot Password?</a>

                <button type="submit" name="masuk" class="tf-btn accent large">Masuk</button>

            </form>
            <div class="auth-line">Or</div>
        
            <p class="mb-9 fw-3 text-center ">Belum Punya Akun? <a href="register" class="auth-link-rg" >Sign up</a></p>
        </div>
    </div>
    
  



    <script type="text/javascript" src="<?php echo $config['web']['url'] ?>mobile/javascript/jquery.min.js"></script>
    <script type="text/javascript" src="<?php echo $config['web']['url'] ?>mobile/javascript/bootstrap.min.js"></script>
    <script type="text/javascript" src="<?php echo $config['web']['url'] ?>mobile/javascript/password-addon.js"></script>
    <script type="text/javascript" src="<?php echo $config['web']['url'] ?>mobile/javascript/main.js"></script>
    <script type="text/javascript" src="<?php echo $config['web']['url'] ?>mobile/javascript/init.js"></script>


</body>
</html>