<?php
session_start();
require '../config.php';
require '../lib/session_login_admin.php'; 

        if (isset($_POST['tambah'])) {
            $nama_depan = $conn->real_escape_string(filter($_POST['nama_depan']));
            $nama_belakang = $conn->real_escape_string(filter($_POST['nama_belakang']));
            $email = $conn->real_escape_string(trim($_POST['email']));
            $no_hp = $conn->real_escape_string(filter($_POST['no_hp']));
            $username = $conn->real_escape_string(filter($_POST['username']));
            $password = $conn->real_escape_string(trim($_POST['password']));
            $saldo_sosmed = $conn->real_escape_string(filter($_POST['saldo_sosmed']));
            $saldo_top_up = $conn->real_escape_string(filter($_POST['saldo_top_up']));
            $level = $conn->real_escape_string($_POST['level']);
            $pin = $conn->real_escape_string(filter($_POST['pin']));

            $hash_pass = password_hash($password, PASSWORD_DEFAULT);

            $cek_username = $conn->query("SELECT * FROM users WHERE username = '$username'");
            $cek_email = $conn->query("SELECT * FROM users WHERE email = '$email'");
            $cek_no_hp = $conn->query("SELECT * FROM users WHERE no_hp = '$no_hp'");
            $api_key =  acak(20);
            $terdaftar = "$date $time";
            $kode_referral = acak(3).acak_nomor(4);

            if (!$nama_depan || !$nama_belakang || !$email || !$no_hp || !$username || !$password || !$level || !$pin) {
                $_SESSION['hasil'] = array('alert' => 'danger', 'pesan' => 'Ups, Mohon Mengisi Semua Input.');
            } else if ($level != "Member" AND $level != "Agen") {
                $_SESSION['hasil'] = array('alert' => 'danger', 'pesan' => 'Ups, Input Tidak Sesuai.');
            } else if ($cek_username->num_rows > 0) {
                $_SESSION['hasil'] = array('alert' => 'danger', 'pesan' => 'Ups, Nama Pengguna <strong>'.$username.'</strong> Sudah Terdaftar.'); 
            } else if ($cek_email->num_rows > 0) {
                $_SESSION['hasil'] = array('alert' => 'danger', 'pesan' => 'Ups, Email <strong>'.$email.'</strong> Sudah Terdaftar.');
            } else if ($cek_no_hp->num_rows > 0) {
                $_SESSION['hasil'] = array('alert' => 'danger', 'pesan' => 'Ups, Nomor HP <strong>'.$no_hp.'</strong> Sudah Terdaftar.');
            } else if (strlen($username) < 5) { 
                $_SESSION['hasil'] = array('alert' => 'danger', 'pesan' => 'Ups, Nama Pengguna Minimal 5 Karakter.'); 
            } else if (strlen($password) < 5) { 
                $_SESSION['hasil'] = array('alert' => 'danger', 'pesan' => 'Ups, Kata Sandi Minimal 5 Karakter.');                              
            } else {

                if ($conn->query("INSERT INTO users VALUES ('', '$nama_depan', '$nama_belakang', '$nama_depan $nama_belakang', '$email', '$username', '$hash_pass', '$saldo_sosmed', '$saldo_top_up', '0', '$level', 'Aktif', 'Sudah Verifikasi', '$pin', '$api_key', '$sess_username', '$sess_username', '$date', '$time', '0', '$no_hp', '', 'SM-$kode_referral', '', '', '')") == true) {
                    $_SESSION['hasil'] = array('alert' => 'success', 'pesan' => 'Sip, Pengguna Baru Telah Berhasil Ditambahkan.');
                } else {
                    $_SESSION['hasil'] = array('alert' => 'danger', 'pesan' => 'Ups, Gagal! Sistem Kami Sedang Mengalami Gangguan.<script>swal("Ups Gagal!", "Sistem Kami Sedang Mengalami Gangguan.", "error");</script>');
                }
            }

        } else if (isset($_POST['ubah'])) {
            $get_id = $conn->real_escape_string($_POST['id']);
            $email = $conn->real_escape_string(trim($_POST['email']));
            $no_hp = $conn->real_escape_string(trim($_POST['no_hp']));
            $username = $conn->real_escape_string(trim($_POST['username']));
            $password = $conn->real_escape_string(trim($_POST['password']));
            $saldo_sosmed = $conn->real_escape_string(trim($_POST['saldo_sosmed']));
            $saldo_top_up = $conn->real_escape_string(trim($_POST['saldo_top_up']));
            $koin = $conn->real_escape_string(trim($_POST['koin']));
            $level = $conn->real_escape_string($_POST['level']);
            $status_akun = $conn->real_escape_string($_POST['status_akun']);
            $pin = $conn->real_escape_string(trim($_POST['pin']));

           

            $cek_users = $conn->query("SELECT * FROM users WHERE id = '$get_id'");

            if ($cek_users->num_rows == 0) {
                $_SESSION['hasil'] = array('alert' => 'danger', 'pesan' => 'Ups, Nama Pengguna Tidak Di Temukan.');
            } else {
                if($password == true) {
                     $password_hash = password_hash($password, PASSWORD_DEFAULT);
                     $simpan = $conn->query("UPDATE users SET email = '$email', no_hp = '$no_hp', username = '$username', password = '$password_hash', saldo_sosmed = '$saldo_sosmed', saldo_top_up = '$saldo_top_up', koin = '$koin', level = '$level', status = '$status_akun', pin = '$pin' WHERE id = '$get_id'");
                } else {
                     $simpan = $conn->query("UPDATE users SET email = '$email', no_hp = '$no_hp', username = '$username', saldo_sosmed = '$saldo_sosmed', saldo_top_up = '$saldo_top_up', koin = '$koin', level = '$level', status = '$status_akun', pin = '$pin' WHERE id = '$get_id'");
                }
                    if ($simpan == true) {
                    $_SESSION['hasil'] = array('alert' => 'success', 'pesan' => 'Sip, Data Pengguna Berhasil Di Ubah.');
                } else {
                    $_SESSION['hasil'] = array('alert' => 'danger', 'pesan' => 'Ups, Gagal! Sistem Kami Sedang Mengalami Gangguan.<script>swal("Ups Gagal!", "Sistem Kami Sedang Mengalami Gangguan.", "error");</script>');
                }
            }

        } else if (isset($_POST['hapus'])) {
            $post_id = $conn->real_escape_string($_POST['id']);

            $cek_users = $conn->query("SELECT * FROM users WHERE id = '$post_id'");

            if ($cek_users->num_rows == 0) {
                $_SESSION['hasil'] = array('alert' => 'danger', 'pesan' => 'Ups, Nama Pengguna Tidak Di Temukan.');
            } else {

                if ($conn->query("DELETE FROM users WHERE id = '$post_id'") == true) {
                    $_SESSION['hasil'] = array('alert' => 'success', 'pesan' => 'Sip, Pengguna Berhasil Di Hapus.');
                } else {
                    $_SESSION['hasil'] = array('alert' => 'danger', 'pesan' => 'Ups, Gagal! Sistem Kami Sedang Mengalami Gangguan.<script>swal("Ups Gagal!", "Sistem Kami Sedang Mengalami Gangguan.", "error");</script>');
                }
            }

        } else if (isset($_POST['ganti_api_key'])) {
            $post_id = $conn->real_escape_string($_GET['id']);

            $cek_users = $conn->query("SELECT * FROM users WHERE id = '$post_id'");

            $api_key =  acak(20);

            if ($cek_users->num_rows == 0) {
                $_SESSION['hasil'] = array('alert' => 'danger', 'pesan' => 'Nama Pengguna Tidak Di Temukan');
            } else {

                if ($conn->query("UPDATE  users SET api_key = '$api_key' WHERE id = '$post_id'") == true) {
                    $_SESSION['hasil'] = array('alert' => 'success', 'pesan' => 'Sip, API Key Berhasil Di Ubah.');
                } else {
                    $_SESSION['hasil'] = array('alert' => 'danger', 'pesan' => 'Ups, Gagal! Sistem Kami Sedang Mengalami Gangguan.<script>swal("Ups Gagal!", "Sistem Kami Sedang Mengalami Gangguan.", "error");</script>');
                }
            }
        }

        $cek_pengguna_sosmed = $conn->query("SELECT SUM(saldo_sosmed) AS total FROM users WHERE level != 'Developers' AND status = 'Aktif'");
        $total_saldo_sosmed_pengguna = $cek_pengguna_sosmed->fetch_assoc();

        $cek_pengguna_top_up = $conn->query("SELECT SUM(saldo_top_up) AS total FROM users WHERE level != 'Developers' AND status = 'Aktif'");
        $total_saldo_top_up_pengguna = $cek_pengguna_top_up->fetch_assoc();

        require("../lib/header_admin.php");

?>
<div class="content-wrapper">
            <!-- Content -->

            <div class="container-xxl flex-grow-1 container-p-y">
              <!-- DataTable with Buttons -->
              <div class="card">
                <div class="card-datatable table-responsive pt-0">
                  <table class="datatables-basic table table-bordered" id="datasosmed">
                    <thead>
                      <tr>
                        <th>Id</th>
                        <th>Nama</th>
                        <th>Username</th>
                        <th>Saldo</th>
                        <th>Koin</th>
                        <th>Level</th>
                        <th>ApiKey</th>
                        <th>Status</th>
                        <th>Akun</th>
                        <th>Register</th>
                        <th>Aksi</th>
                      </tr>
                    </thead>
                    <tbody>
                    	<?php
if (isset($_GET['search'])) {
    $search = $conn->real_escape_string(filter($_GET['search']));
    $nama = $conn->real_escape_string(filter($_GET['search']));
    $uplink = $conn->real_escape_string(filter($_GET['search']));
    $email = $conn->real_escape_string(filter($_GET['search']));

    $cek_pengguna = "SELECT * FROM users WHERE username LIKE '%$search%' ORDER BY id ASC"; // edit
} else {
    $cek_pengguna = "SELECT * FROM users ORDER BY id ASC"; // edit
}

$new_query = $conn->query($cek_pengguna);

while ($data_pengguna = $new_query->fetch_assoc()) {
    if ($data_pengguna['status'] == "Aktif") {
        $label = "success";
    } else if ($data_pengguna['status'] == "Tidak Aktif") {
        $label = "danger";
    }
    if ($data_pengguna['status_akun'] == "Sudah Verifikasi") {
        $label2 = "primary";
    } else if ($data_pengguna['status_akun'] == "Belum Verifikasi") {
        $label2 = "danger";
    }
    ?>
                    	<tr>
                    		<td>#<?php echo $data_pengguna['id']; ?></td>
                    		<td><?php echo $data_pengguna['nama']; ?></td>
                    		<td><?php echo $data_pengguna['username']; ?></td>
                    		<td><sup>Rp</sup>.<?php echo number_format($data_pengguna['saldo_top_up'],0,',','.'); ?></td>
                    		<td><?php echo number_format($data_pengguna['koin'],0,',','.'); ?></td>
                    		<td><?php echo $data_pengguna['level']; ?></td>
                    		<td><small><?php echo $data_pengguna['api_key']; ?></small></td>
                    		<td><?php echo $data_pengguna['status']; ?></td>
                    		<td><?php echo $data_pengguna['status_akun']; ?></td>
                    		<td><?php echo tanggal_indo($data_pengguna['date']); ?>, <?php echo $data_pengguna['time']; ?></td>
                    		<td></td>
                    	</tr>
                    <?php } ?>  
                    </tbody>
                  </table>
                </div>
              </div>
          </div>
      </div>


<?php
require '../lib/footer_admin.php';
?>