<?php
session_start();
require '../config.php';
require '../lib/session_login_admin.php';

        if (isset($_POST['tambah_sosmed'])) {
            $PostCode = $conn->real_escape_string($_POST['code']);
            $PostLink = $conn->real_escape_string($_POST['link']);
            $GetKey = $conn->real_escape_string($_POST['api_key']);
            $GetApiID = $conn->real_escape_string($_POST['api_id']);

            if (!$PostCode || !$PostLink || !$GetKey) {
                $_SESSION['hasil'] = array('alert' => 'danger', 'pesan' => 'Ups, Mohon Mengisi Semua Input.');
            } else {

                if ($conn->query("INSERT INTO provider VALUES ('', '$PostCode', '$PostLink', '$GetKey', '$GetApiID')") == true) {
                    $_SESSION['hasil'] = array('alert' => 'success', 'pesan' => 'Sip, Berhasil Menambahkan Provider Layanan Sosial Media Baru.');
                } else {
                    $_SESSION['hasil'] = array('alert' => 'danger', 'pesan' => 'Ups, Gagal! Sistem Kami Sedang Mengalami Gangguan.<script>swal("Ups Gagal!", "Sistem Kami Sedang Mengalami Gangguan.", "error");</script>');
                }
            }

        } else if (isset($_POST['ubah_sosmed'])) {
            $GetID = $conn->real_escape_string($_GET['this_id']);
            $PostCode = $conn->real_escape_string($_POST['code']);
            $PostLink = $conn->real_escape_string($_POST['link']);
            $GetKey = $conn->real_escape_string($_POST['api_key']);
            $GetApiID = $conn->real_escape_string($_POST['api_id']);

            $CheckData = $conn->query("SELECT * FROM provider WHERE id = '$GetID'");

            if ($CheckData->num_rows == 0) {
                $_SESSION['hasil'] = array('alert' => 'danger', 'pesan' => 'Ups, Data Tidak Di Temukan.');
            } else {

                if ($conn->query("UPDATE provider SET code = '$PostCode', link = '$PostLink', api_key = '$GetKey', api_id = '$GetApiID' WHERE id = '$GetID'") == true) {
                    $_SESSION['hasil'] = array('alert' => 'success', 'pesan' => 'Sip, Provider Layanan Sosial Media Berhasil Di Ubah.');
                } else {
                    $_SESSION['hasil'] = array('alert' => 'danger', 'pesan' => 'Ups, Gagal! Sistem Kami Sedang Mengalami Gangguan.<script>swal("Ups Gagal!", "Sistem Kami Sedang Mengalami Gangguan.", "error");</script>');
                }
            }

        } else if (isset($_POST['hapus_sosmed'])) {
            $GetID = $conn->real_escape_string($_GET['this_id']);

            $CheckData = $conn->query("SELECT * FROM provider WHERE id = '$GetID'");

            if ($CheckData->num_rows == 0) {
                $_SESSION['hasil'] = array('alert' => 'danger', 'pesan' => 'Ups, Data Tidak Di Temukan.');
            } else {

                if ($conn->query("DELETE FROM provider WHERE id = '$GetID'") == true) {
                    $_SESSION['hasil'] = array('alert' => 'success', 'pesan' => 'Sip, Provider Layanan Sosial Media Berhasil Di Hapus.');
                } else {
                    $_SESSION['hasil'] = array('alert' => 'danger', 'pesan' => 'Ups, Gagal! Sistem Kami Sedang Mengalami Gangguan.<script>swal("Ups Gagal!", "Sistem Kami Sedang Mengalami Gangguan.", "error");</script>');
                }
            }
        }

        if (isset($_POST['tambah_top_up'])) {
            $PostCode = $conn->real_escape_string($_POST['code']);
            $PostLink = $conn->real_escape_string($_POST['link']);
            $GetKey = $conn->real_escape_string($_POST['api_key']);
            $GetApiID = $conn->real_escape_string($_POST['api_id']);

            if (!$PostCode || !$PostLink || !$GetKey) {
                $_SESSION['hasil'] = array('alert' => 'danger', 'pesan' => 'Ups, Mohon Mengisi Semua Input.');
            } else {

                if ($conn->query("INSERT INTO provider_pulsa VALUES ('', '$PostCode', '$PostLink', '$GetKey', '$GetApiID')") == true) {
                    $_SESSION['hasil'] = array('alert' => 'success', 'pesan' => 'Sip, Berhasil Menambahkan Provider Layanan Top Up Baru.');
                } else {
                    $_SESSION['hasil'] = array('alert' => 'danger', 'pesan' => 'Ups, Gagal! Sistem Kami Sedang Mengalami Gangguan.<script>swal("Ups Gagal!", "Sistem Kami Sedang Mengalami Gangguan.", "error");</script>');
                }
            }

        } else if (isset($_POST['ubah_top_up'])) {
            $id = $conn->real_escape_string($_GET['id']);
            $code = $conn->real_escape_string($_POST['code']);
            $link = $conn->real_escape_string($_POST['link']);
            $key = $conn->real_escape_string($_POST['key']);
            $apiid = $conn->real_escape_string($_POST['api_id']);

            $cek_data = $conn->query("SELECT * FROM provider_pulsa WHERE id = '$id'");

            if ($cek_data->num_rows == 0) {
                $_SESSION['hasil'] = array('alert' => 'danger', 'pesan' => 'Ups, Data Tidak Di Temukan.');
            } else {

                if ($conn->query("UPDATE provider_pulsa SET code = '$code', link = '$link', api_key = '$key' , api_id = '$apiid' WHERE id = '$id'") == true) {
                    $_SESSION['hasil'] = array('alert' => 'success', 'pesan' => 'Sip, Provider Layanan Top Up Berhasil Di Ubah.');
                } else {
                    $_SESSION['hasil'] = array('alert' => 'danger', 'pesan' => 'Ups, Gagal! Sistem Kami Sedang Mengalami Gangguan.<script>swal("Ups Gagal!", "Sistem Kami Sedang Mengalami Gangguan.", "error");</script>');
                }
            }

        } else if (isset($_POST['hapus_top_up'])) {
            $id = $conn->real_escape_string($_GET['id']);

            $cek_data = $conn->query("SELECT * FROM provider_pulsa WHERE id = '$id'");

            if ($cek_data->num_rows == 0) {
                $_SESSION['hasil'] = array('alert' => 'danger', 'pesan' => 'Ups, Data Tidak Di Temukan.');
            } else {

                if ($conn->query("DELETE FROM provider_pulsa WHERE id = '$id'") == true) {
                    $_SESSION['hasil'] = array('alert' => 'success', 'pesan' => 'Sip, Provider Layanan Top Up Berhasil Di Hapus.');
                } else {
                    $_SESSION['hasil'] = array('alert' => 'danger', 'pesan' => 'Ups, Gagal! Sistem Kami Sedang Mengalami Gangguan.<script>swal("Ups Gagal!", "Sistem Kami Sedang Mengalami Gangguan.", "error");</script>');
                }
            }
        }

        require '../lib/header_admin.php';

?>
<div class="content-wrapper">
            <!-- Content -->

            <div class="container-xxl flex-grow-1 container-p-y">
              <!-- DataTable with Buttons -->
              <div class="card">
                <div class="card-header">
                  <!-- Tambahkan tombol pertama di sini -->
                  <button class="btn btn-primary">Tombol 1</button>
                  <!-- Tambahkan tombol kedua di sini -->
                  <button class="btn btn-danger">Tombol 2</button>
                </div>
                <div class="card-datatable table-responsive pt-0">
                  <table class="datatables-basic table table-bordered" id="datasosmed">
                    <thead>
                      <tr>
                        <th>No</th>
                        <th>Kode</th>
                        <th>Api Key</th>
                        <th>Api ID</th>
                        <th>Aksi</th>
                      </tr>
                    </thead>
                    <tbody>
                        <?php
$no = 1;
    $CallDB_Provider = $conn->query("SELECT * FROM provider ORDER BY id DESC"); // edit
    while ($ShowData = $CallDB_Provider->fetch_assoc()) {
?>
   
                      <tr>
                        <td><?php echo $no++; ?></td>
                        <td><?php echo $ShowData['code']; ?></td>
                        <td><?php echo $ShowData['api_key']; ?></td>
                        <td><?php echo $ShowData['api_id']; ?></td>
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