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
                        <th>Aksi</th>
                        <th>Ip</th>
                        
                      </tr>
                    </thead>
                    <tbody>
                      <?php 
$no = 1;

// Query untuk mengambil semua data dari tabel "aktifitas"
$cek_riwayat = "SELECT * FROM aktifitas ORDER BY id DESC"; // edit

$new_query = $conn->query($cek_riwayat);

// Loop untuk menampilkan data
while ($data_riwayat = $new_query->fetch_assoc()) {
?>
                    	<tr>
                    		<td><?php echo $no++; ?></td>
                    		<td><?php echo tanggal_indo($data_riwayat['date']); ?>, <?php echo $data_riwayat['time']; ?></td>
                            <td><?php echo $data_riwayat['username']; ?></td>
                            <td><?php echo $data_riwayat['aksi']; ?></td>
                            <td><?php echo $data_riwayat['ip']; ?></td>
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