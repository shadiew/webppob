<?php
session_start();
require '../config.php';
require '../lib/session_login_admin.php'; 
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
                        <th>No</th>
                        <th>Waktu</th>
                        <th>Username</th>
                        <th>Tipe</th>
                        <th>Jumlah</th>
                        <th>Keterangan</th>
                        
                      </tr>
                    </thead>
                    <tbody>
                    	<?php
if (isset($_GET['tipe'])) {
    $cari_tipe = $conn->real_escape_string($_GET['tipe']);
    $cek_data = "SELECT * FROM riwayat_saldo_koin WHERE aksi LIKE '%$cari_tipe%' ORDER BY id DESC";
} else {
    $cek_data = "SELECT * FROM riwayat_saldo_koin ORDER BY id DESC";
}

$new_query = $conn->query($cek_data);
$no = 1;

while ($view_data = $new_query->fetch_assoc()) {
    if ($view_data['tipe'] == "Saldo") {
        $icon = "la la-credit-card";
    } else if ($view_data['tipe'] == "Koin") {
        $icon = "flaticon-coins";
    }
    ?>
                                <tr>
                                    <td><?php echo $no++; ?></td>
                                    <td><?php echo tanggal_indo($view_data['date']); ?>, <?php echo $view_data['time']; ?></td>
                                    <td><?php echo $view_data['username']; ?></td>
                                    
                                    <td><?php echo $view_data['aksi']; ?></td>
                                    <td><sup>Rp</sup>.<?php echo number_format($view_data['nominal'],0,',','.'); ?></td>
                                    <td><?php echo $view_data['pesan']; ?></td>
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