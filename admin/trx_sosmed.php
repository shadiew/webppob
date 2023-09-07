<?php
session_start();
require '../config.php';
require '../lib/session_login_admin.php';

        if (isset($_POST['ubah'])) {
            $get_oid = $conn->real_escape_string($_GET['order_id']);
            $status = $conn->real_escape_string($_POST['status']);
            $s_count = $conn->real_escape_string($_POST['start_count']);
            $remains = $conn->real_escape_string($_POST['remains']);

            $cek_orders = $conn->query("SELECT * FROM pembelian_sosmed WHERE oid = '$get_oid'");
            $data_orders = $cek_orders->fetch_array(MYSQLI_ASSOC);

            $username = $data_orders['user'];
            $koin = $data_orders['koin'];
            $layanan = $data_orders['layanan'];

            if ($cek_orders->num_rows == 0) {
                $_SESSION['hasil'] = array('alert' => 'danger', 'pesan' => 'Ups, Data Pesanan Tidak Ditemukan.');
            } else {

                if ($conn->query("UPDATE pembelian_sosmed SET status = '$status', start_count = '$s_count', remains = '$remains'  WHERE oid = '$get_oid'") == true) {
                    if ($status == "Success") {
                        $update = $conn->query("INSERT INTO history_saldo VALUES ('', '$username', 'Koin', 'Penambahan Koin', '$koin', 'Mendapatkan Koin Melalui Pemesanan $layanan Dengan Kode Pesanan : WEB-$get_oid', '$date', '$time')");
                        $update = $conn->query("UPDATE users SET koin = koin+$koin WHERE username = '$username'");
                    }
                    $_SESSION['hasil'] = array('alert' => 'success', 'pesan' => 'Sip, Data Pesanan Berhasil Di Ubah.');
                } else {
                    $_SESSION['hasil'] = array('alert' => 'danger', 'pesan' => 'Ups, Gagal! Sistem Kami Sedang Mengalami Gangguan.<script>swal("Ups Gagal!", "Sistem Kami Sedang Mengalami Gangguan.", "error");</script>');
                }
            }

        } else if (isset($_POST['hapus'])) {
            $get_oid = $conn->real_escape_string($_GET['order_id']);

            $cek_orders = $conn->query("SELECT * FROM pembelian_sosmed WHERE oid = '$get_oid'");

            if ($cek_orders->num_rows == 0) {
                $_SESSION['hasil'] = array('alert' => 'danger', 'pesan' => 'Ups, Data Pesanan Tidak Ditemukan.');
            } else {

                if ($conn->query("DELETE FROM pembelian_sosmed WHERE oid = '$get_oid'") == TRUE) {
                    $_SESSION['hasil'] = array('alert' => 'success', 'pesan' => 'Sip, Data Pesanan Berhasil Di Hapus.');
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
                <div class="card-datatable table-responsive pt-0">
                  <table class="datatables-basic table table-bordered" id="datasosmed">
                    <thead>
                      <tr>
                        <th>Waktu</th>
                        <th>ID Order</th>
                        <th>ID API</th>
                        <th>Username</th>
                        <th>Service</th>
                        <th>Target</th>
                        <th>Jumlah</th>
                        <th>Start</th>
                        <th>Remain</th>
                        <th>Harga</th>
                        <th>Koin</th>
                        <th>Status</th>
                        <th>TRX</th>
                        <th>API</th>
                        <th>Refund</th>
                        <th>Aksi</th>
                      </tr>
                    </thead>
                    <tbody>
                        <?php 
// start paging config
if (isset($_GET['cari'])) {
    $cari_oid = $conn->real_escape_string(filter($_GET['cari']));
    $cari_status = $conn->real_escape_string(filter($_GET['status']));

    $cek_pesanan = "SELECT * FROM pembelian_sosmed WHERE oid LIKE '%$cari_oid%' AND status LIKE '%$cari_status%' ORDER BY id DESC"; // edit
} else {
    $cek_pesanan = "SELECT * FROM pembelian_sosmed ORDER BY id DESC"; // edit
}
if (isset($_GET['cari'])) {
$cari_urut = $conn->real_escape_string(filter($_GET['tampil']));
$records_per_page = $cari_urut; // edit
} else {
    $records_per_page = 10; // edit
}

$starting_position = 0;
if(isset($_GET["halaman"])) {
    $starting_position = ($conn->real_escape_string(filter($_GET["halaman"]))-1) * $records_per_page;
}
$new_query = $cek_pesanan." LIMIT $starting_position, $records_per_page";
$new_query = $conn->query($new_query);
// end paging config
while ($data_pesanan = $new_query->fetch_assoc()) {
?>
                        <tr>
                            <td><?php echo tanggal_indo($data_pesanan['date']); ?>, <?php echo $data_pesanan['time']; ?></td>
                            <td>#<?php echo $data_pesanan['oid']; ?></td>
                            <td>#<?php echo $data_pesanan['provider_oid']; ?></td>
                            <td><?php echo $data_pesanan['user']; ?></td>
                            <td><?php echo substr($data_pesanan['layanan'],0,15); ?>...</td>
                            <td><?php echo substr($data_pesanan['target'],0,15); ?>...</td>
                            <td><?php echo $data_pesanan['jumlah']; ?></td>
                            <td><?php echo $data_pesanan['start_count']; ?></td>
                            <td><?php echo $data_pesanan['remains']; ?></td>
                            <td><sup>Rp</sup>.<?php echo number_format($data_pesanan['harga'],0,',','.'); ?></td>
                            <td><?php echo number_format($data_pesanan['koin'],0,',','.'); ?></td>
                            <td><select class="form-control" style="width: 100px;" name="status">
                                            <option value="<?php echo $data_pesanan['status']; ?>"><?php echo $data_pesanan['status']; ?></option>
                                            <option value="Pending">Pending</option>
                                            <option value="Processing">Processing</option>
                                            <option value="Success">Success</option>
                                            <option value="Error">Error</option>
                                            <option value="Partial">Partial</option>
                                        </select></td>
                            <td><?php if($data_pesanan['place_from'] == "API") { ?>
                                <i class="mdi mdi-code-braces"></i>
                                <?php } else { ?>
                                <i class="mdi mdi-cube"></i>
                                <?php } ?>
                            </td>
                            <td><?php echo $data_pesanan['provider']; ?></td>
                            <td><?php if($data_pesanan['refund'] == "1") { ?>
                                <i class="mdi mdi-check-circle"></i>
                                <?php } else { ?>
                                <i class="mdi mdi-minus-circle"></i>
                            <?php } ?>
                            </td>
                            <td>
                                <div class="demo-inline-spacing">
                                    <button type="button" class="btn btn-primary btn-xs">Edit</button>
                                    <button type="button" class="btn btn-danger btn-xs">Hapus</button>
                                </div>
                            </td>
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