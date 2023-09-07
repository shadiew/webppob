<?php
session_start();
require '../config.php';
require '../lib/session_user.php';

        if (isset($_POST['pesan'])) {
            require '../lib/session_login.php';
            $post_tipe = $conn->real_escape_string(filter($_POST['tipe']));
            $post_operator = $conn->real_escape_string(filter($_POST['operator']));
            $post_layanan = $conn->real_escape_string(filter($_POST['layanan']));
            $post_target = $conn->real_escape_string(filter($_POST['target']));
            $post_pin = $conn->real_escape_string(filter($_POST['pin']));

            $cek_layanan = $conn->query("SELECT * FROM layanan_pulsa WHERE service_id = '$post_layanan' AND status = 'Normal'");
            $data_layanan = mysqli_fetch_assoc($cek_layanan);

            $cek_pesanan = $conn->query("SELECT * FROM pembelian_pulsa WHERE target = '$post_target' AND status = 'Pending'");
            $data_pesanan = mysqli_fetch_assoc($cek_pesanan);

            $cek_rate_koin = $conn->query("SELECT * FROM setting_koin_didapat WHERE status = 'Aktif'");
            $data_rate_koin = mysqli_fetch_assoc($cek_rate_koin);

            $order_id = acak_nomor(3).acak_nomor(4);
            $provider = $data_layanan['provider'];
            $koin = $data_layanan['harga'] * $data_rate_koin['rate'];

            $cek_provider = $conn->query("SELECT * FROM provider_pulsa WHERE code = '$provider'");
            $data_provider = mysqli_fetch_assoc($cek_provider);

            $cek_rate = $conn->query("SELECT * FROM setting_rate WHERE tipe = 'Top Up'");
            $data_rate = mysqli_fetch_assoc($cek_rate);

            $error = array();
            if (empty($post_tipe)) {
                $error ['tipe'] = '*Wajib Pilih Salah Satu.';
            }
            if (empty($post_operator)) {
                $error ['operator'] = '*Wajib Pilih Salah Satu.';
            }
            if (empty($post_layanan)) {
                $error ['layanan'] = '*Wajib Pilih Salah Satu.';
            }
            if (empty($post_target)) {
                $error ['target'] = '*Tidak Boleh Kosong.';
            }
            if (empty($post_pin)) {
                $error ['pin'] = '*Tidak Boleh Kosong.';
            } else if ($post_pin <> $data_user['pin']) {
                $error ['pin'] = '*PIN Yang Kamu Masukkan Salah.';
            } else {

            if (mysqli_num_rows($cek_layanan) == 0) {
                $_SESSION['hasil'] = array('alert' => 'danger', 'pesan' => 'Ups, Layanan Tidak Tersedia.<script>swal("Ups Gagal!", "Layanan Tidak Tersedia.", "error");</script>');

            } else if (mysqli_num_rows($cek_provider) == 0) {
                $_SESSION['hasil'] = array('alert' => 'danger', 'pesan' => 'Ups, Server Kami Sedang Maintance.<script>swal("Ups Gagal!", "Server Kami Sedang Maintance.", "error");</script>');

            } else if ($data_user['saldo_top_up'] < $data_layanan['harga']) {
                $_SESSION['hasil'] = array('alert' => 'danger', 'pesan' => 'Yahh, Saldo Top Up Kamu Tidak Mencukupi Untuk Melakukan Pemesanan Ini.<script>swal("Yahh Gagal!", "Saldo Top Up Kamu Tidak Mencukupi Untuk Melakukan Pemesanan Ini.", "error");</script>');

            } else if (mysqli_num_rows($cek_pesanan) == 1) {
                $_SESSION['hasil'] = array('alert' => 'danger', 'pesan' => 'Ups, Masih Terdapat Pesanan Dengan Nomor HP Yang Sama & Berstatus Pending.<script>swal("Ups Gagal!", "Masih Terdapat Pesanan Dengan Nomor HP Yang Sama & Berstatus Pending.", "error");</script>');

            } else {

            $api_link = $data_provider['link'];
            $api_key = $data_provider['api_key'];
            $api_id = $data_provider['api_id'];

            if ($provider == "MANUAL") {
                $api_postdata = "";
            } else if ($provider == "DG-PULSA") {
            $sign = md5($api_id.$api_key.$order_id);
            $api_postdata = array( 
                'username' => $api_id,
                'buyer_sku_code' => $data_layanan['provider_id'],
                'customer_no' => "$post_target",
                'ref_id' => $order_id,
                'sign' => $sign
            );
            $header = array(
                'Content-Type: application/json',
            );
            } else {
                die("System Error!");
            }

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $api_link);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($api_postdata));
                $chresult = curl_exec($ch);
                curl_close($ch);
                $json_result = json_decode($chresult, true);
                $result = json_decode($chresult);
                // print_r($result);

                if ($provider == "DG-PULSA" && $json_result['data']['status'] == "Gagal") {
                    $_SESSION['hasil'] = array('alert' => 'danger', 'pesan' => 'Ups, '.$json_result['data']['message']);
                } else {

                    if ($provider == "DG-PULSA") {
                        $provider_oid = $order_id;
                    }

                        $check_top = $conn->query("SELECT * FROM top_users WHERE username = '$sess_username'");
                        $data_top = mysqli_fetch_assoc($check_top);
                        if ($conn->query("INSERT INTO pembelian_pulsa VALUES ('','$order_id', '$provider_oid', '$sess_username', '".$data_layanan['layanan']."', '".$data_layanan['harga']."', '".$data_rate['rate']."', '$koin', '$post_target', '', 'Pending', '$date', '$time', 'Website', '$provider', '0')") == true) {
                            $conn->query("UPDATE users SET saldo_top_up = saldo_top_up-".$data_layanan['harga'].", pemakaian_saldo = pemakaian_saldo+".$data_layanan['harga']." WHERE username = '$sess_username'");
                            $conn->query("INSERT INTO riwayat_saldo_koin VALUES ('', '$sess_username', 'Saldo', 'Pengurangan Saldo', '".$data_layanan['harga']."', 'Mengurangi Saldo Top Up Melalui Pemesanan Voucher Dengan Kode Pesanan : WEB-$order_id', '$date', '$time')");
                            $conn->query("INSERT INTO semua_pembelian VALUES ('','WEB-$order_id','$order_id', '$sess_username', '".$data_layanan['operator']."', '".$data_layanan['layanan']."', '".$data_layanan['harga']."', '$post_target', 'Pending', '$date', '$time', 'WEB', '0')");
                            if (mysqli_num_rows($check_top) == 0) {
                                $insert_topup = $conn->query("INSERT INTO top_users VALUES ('', 'Order', '$sess_username', '".$data_layanan['harga']."', '1')");
                            } else {
                                $insert_topup = $conn->query("UPDATE top_users SET jumlah = ".$data_top['jumlah']."+".$data_layanan['harga'].", total = ".$data_top['total']."+1 WHERE username = '$sess_username' AND method = 'Order'");
                            }
                            $_SESSION['hasil'] = array('alert' => 'success', 'pesan' => 'Sip, Pesanan Kamu Telah Kami Terima.');
                        } else {
                            $_SESSION['hasil'] = array('alert' => 'danger', 'pesan' => 'Ups, Gagal! Sistem Kami Sedang Mengalami Gangguan.<script>swal("Ups Gagal!", "Sistem Kami Sedang Mengalami Gangguan.", "error");</script>');
                        }
                    }
                }
            }
        }

        require("../lib/header.php");

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- Mobile Specific Metas -->
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <title>card info</title>
     <!-- Favicon and Touch Icons  -->
     <link rel="shortcut icon" href="../mobile/images/logo.png" />
     <link rel="apple-touch-icon-precomposed" href="../mobile/images/logo.png" />
    <!-- Font -->
    <link rel="stylesheet" href="../mobile/fonts/fonts.css" />
    <!-- Icons -->
    <link rel="stylesheet" href="../mobile/fonts/icons-alipay.css">
    <link rel="stylesheet" href="../mobile/styles/bootstrap.css">
    <link rel="stylesheet" href="../mobile/styles/data-picker.min.css">

    <link rel="stylesheet" type="text/css" href="../mobile/styles/styles.css" />
    <link rel="manifest" href="../mobile/_manifest.json" data-pwa-version="set_in_manifest_and_pwa_js">
    <link rel="apple-touch-icon" sizes="192x192" href="../mobile/app/icons/icon-192x192.png">
</head>

<body>
    <!-- preloade -->
    
    <!-- /preload -->
    <div class="header">
        <div class="tf-container">
            <div class="tf-statusbar d-flex justify-content-center align-items-center">
                <a href="#" class="back-btn"> <i class="icon-left"></i> </a>
                <h3>Add new card</h3>
            </div>
        </div>
    </div>
    <div class="mt-3">
        <div class="tf-container">
             
             <form class="tf-form mt-5" method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo $config['csrf_token'] ?>">
                
                <div class="group-input">
                    <label for="">Tipe</label>
                    <select class="form-control" name="tipe" id="tipe">
                                        <option value="0">Pilih Salah Satu</option>
                                        <option value="Voucher">VOUCHER</option>
                                    </select>
                </div>
                <div class="group-input">
                    <label for="">Kategori</label>
                    <select class="form-control" name="operator" id="operator">
                                        <option value="0">Pilih Salah Satu</option>
                                        
                                    </select>
                </div>
                <div class="group-double-ip">
                    <div class="group-input">
                        <label>Expiry Date</label>
                        <div class="datepicker date">
                            <input type="text" placeholder="MM/YY">
                            <span class="input-group-append"><i class="icon-calendar2"></i></span>
                        </div>
                    </div>
                    <div class="group-input">
                        <label>CVV</label>
                        <input type="text" placeholder="CVV">
                        <i class="icon-info"></i>
                    </div>
                </div>
                <div class="bottom-navigation-bar bottom-btn-fixed st2">
                    <a class="tf-btn accent large" id="btn-popup-down">Continue</a>
                </div>

             </form>

        </div>
    </div>
    
    <div class="tf-panel down">
        <div class="panel_overlay"></div>
        <div class="panel-box panel-down">
            <div class="header">
                <div class="tf-container">
                    <div class="tf-statusbar br-none d-flex justify-content-center align-items-center">
                        <a href="#" class="clear-panel"> <i class="icon-close1"></i> </a>
                        <h3>Verification OTP</h3>
                    </div>
                    
                </div>
            </div>
            
            <div class="mt-5">
                <div class="tf-container">
                    <form class="tf-form tf-form-verify" action="https://themesflat.co/html/alipay/alipay-app-pwa/37_successful.html">
                        <div class="d-flex group-input-verify">
                                <input type="tel" maxlength="1" pattern="[0-9]" class="input-verify" value="1">
                                <input type="tel" maxlength="1" pattern="[0-9]" class="input-verify" value="2">
                                <input type="tel" maxlength="1" pattern="[0-9]" class="input-verify" value="3">
                                <input type="tel" maxlength="1" pattern="[0-9]" class="input-verify">
                        </div>
                        <div class="text-send-code">
                                <p class="fw_4">A code has been sent to your phone</p>
                                <p class="primary_color fw_7">Resend in&nbsp;<span class="js-countdown" data-timer="60" data-labels=" :  ,  : , : , "></span></p>
                        </div>
                        <div class="mt-7 mb-6">
                            <button type="submit" class="tf-btn accent large">Continue</button>
                        </div>
                        
                    </form>
                </div>
        
            </div>
        
        </div>
    </div>



    <script type="text/javascript" src="../mobile/javascript/jquery.min.js"></script>
    <script type="text/javascript" src="../mobile/javascript/bootstrap.min.js"></script>
    <script type="text/javascript" src="../mobile/javascript/data-picker.min.js"></script>
    <script type="text/javascript" src="../mobile/javascript/custom-date.js"></script>
    <script type="text/javascript" src="../mobile/javascript/main.js"></script>
    <script src="https://code.jquery.com/jquery-1.11.0.min.js"></script>
        <script type="text/javascript">
        $(document).ready(function() {
            $("#tipe").change(function() {
                var tipe = $("#tipe").val();
                $.ajax({
                    url: '<?php echo $config['web']['url']; ?>ajax/type-top-up.php',
                    data: 'tipe=' + tipe,
                    type: 'POST',
                    dataType: 'html',
                    success: function(msg) {
                        $("#operator").html(msg);
                    }
                });
            });
            $("#operator").change(function() {
                var tipe = $("#tipe").val();
                var operator = $("#operator").val();
                $.ajax({
                    url: '<?php echo $config['web']['url']; ?>ajax/service-top-up.php',
                    data  : 'tipe=' +tipe + '&operator=' + operator,
                    type: 'POST',
                    dataType: 'html',
                    success: function(msg) {
                        $("#layanan").html(msg);
                    }
                });
            });
            $("#layanan").change(function() {
                var layanan = $("#layanan").val();
                $.ajax({
                    url: '<?php echo $config['web']['url']; ?>ajax/note-top-up.php',
                    data: 'layanan=' + layanan,
                    type: 'POST',
                    dataType: 'html',
                    success: function(msg) {
                        $("#catatan").html(msg);
                    }
                });
            });
            $("#layanan").change(function() {
                var layanan = $("#layanan").val();
                $.ajax({
                    url: '<?php echo $config['web']['url']; ?>ajax/rate-order-coins-top-up.php',
                    data: 'layanan=' + layanan,
                    type: 'POST',
                    dataType: 'html',
                    success: function(msg) {
                        $("#koin").html(msg);
                    }
                });
            });
            $("#layanan").change(function() {
                var layanan = $("#layanan").val();
                $.ajax({
                    url: '<?php echo $config['web']['url']; ?>ajax/price-top-up.php',
                    data: 'layanan=' + layanan,
                    type: 'POST',
                    dataType: 'html',
                    success: function(msg) {
                        $("#harga").val(msg);
                    }
                });
            });
        });
        </script>

</body>
</html>