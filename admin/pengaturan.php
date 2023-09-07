<?php
session_start();
require '../config.php';
require '../lib/session_login_admin.php'; 

        if (isset($_POST['ubah'])) {
            $PostStitle = $conn->real_escape_string(trim($_POST['shrt_title']));
            $PostTitle = $conn->real_escape_string(trim($_POST['title']));
            $PostDescWeb = $conn->real_escape_string(trim($_POST['deskripsi']));
            $PostKontak = $conn->real_escape_string(trim($_POST['kontak']));
            $PostLokasi = $conn->real_escape_string(trim($_POST['lokasi']));
            $PostKodePos = $conn->real_escape_string(trim($_POST['kodepos']));

            if ($conn->query("UPDATE setting_web SET short_title = '$PostStitle', title = '$PostTitle', deskripsi_web = '$PostDescWeb', kontak_utama = '$PostKontak', lokasi = '$PostLokasi', kode_pos = '$PostKodePos' WHERE id = '1'") == true) {
                $_SESSION['hasil'] = array('alert' => 'success', 'pesan' => 'Sip, Pengaturan Website Telah Berhasil Di Ubah.');                    
            } else {
                $_SESSION['hasil'] = array('alert' => 'danger', 'pesan' => 'Ups, Gagal! Sistem Kami Sedang Mengalami Gangguan.<script>swal("Ups Gagal!", "Sistem Kami Sedang Mengalami Gangguan.", "error");</script>');
            }
        }
        
        require("../lib/header_admin.php");

?> 
<!-- Content wrapper -->
          <div class="content-wrapper">
            <!-- Content -->

            <div class="container-xxl flex-grow-1 container-p-y">
              <!-- Basic Layout -->
              <div class="row">
                <div class="col-xl">
                  <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                      <h5 class="mb-0">Pengaturan Website</h5>
                    </div>
                    <div class="card-body">
                        <?php 
$CekData = $conn->query("SELECT * FROM setting_web WHERE id = '1'"); // edit
while ($ShowData = $CekData->fetch_assoc()) {
?>
                      <form>
                        <div class="form-floating form-floating-outline mb-4">
                          <input type="text" class="form-control" value="<?php echo $ShowData['short_title']; ?>" readonly />
                          <label>Judul</label>
                        </div>
                        <div class="form-floating form-floating-outline mb-4">
                          <input type="text" class="form-control" value="<?php echo $ShowData['title']; ?>" readonly/>
                          <label for="basic-default-company">Judul Website</label>
                        </div>
                        
                        
                        <div class="form-floating form-floating-outline mb-4">
                          <textarea
                            
                            class="form-control"
                            placeholder="Hi, Do you have a moment to talk Joe?"
                            style="height: 80px"><?php echo $ShowData['deskripsi_web']; ?></textarea>
                          <label for="basic-default-message">Deskripsi</label>
                        </div>
                        <button type="submit" class="btn btn-primary">Ganti</button>
                      </form>
                      <?php } ?>
                    </div>
                  </div>
                </div>
                <div class="col-xl">
                  <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                      <h5 class="mb-0">Informasi Website</h5>
                      
                    </div>
                    <div class="card-body">
                      <p>Silahkan atur sesuai kebutuhan, data yang disimpan dapat diubah kembali</p>
                    </div>
                  </div>
                </div>
              </div>
          </div>
      </div>

<?php
require '../lib/footer_admin.php';
?>