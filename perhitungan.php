<?php
require_once('includes/init.php');

$user_role = get_role();
if($user_role == 'admin') {

$page = "Perhitungan";
require_once('template/header.php');

mysqli_query($koneksi,"TRUNCATE TABLE hasil;");

$kriteria = array();
$q1 = mysqli_query($koneksi,"SELECT * FROM kriteria ORDER BY kode_kriteria ASC");			
while($krit = mysqli_fetch_array($q1)){
	$kriteria[$krit['id_kriteria']]['id_kriteria'] = $krit['id_kriteria'];
	$kriteria[$krit['id_kriteria']]['kode_kriteria'] = $krit['kode_kriteria'];
	$kriteria[$krit['id_kriteria']]['nama'] = $krit['nama'];
	$kriteria[$krit['id_kriteria']]['type'] = $krit['type'];
	$kriteria[$krit['id_kriteria']]['bobot'] = $krit['bobot'];
	$kriteria[$krit['id_kriteria']]['ada_pilihan'] = $krit['ada_pilihan'];
}

$alternatif = array();
$q2 = mysqli_query($koneksi,"SELECT * FROM alternatif");			
while($alt = mysqli_fetch_array($q2)){
	$alternatif[$alt['id_alternatif']]['id_alternatif'] = $alt['id_alternatif'];
	$alternatif[$alt['id_alternatif']]['nama'] = $alt['nama'];
} 
?>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-fw fa-calculator"></i> Data Perhitungan</h1>
</div>

<div class="card shadow mb-4">
    <!-- /.card-header -->
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-danger"><i class="fa fa-table"></i> Matrix Keputusan (X)</h6>
    </div>

    <div class="card-body">
		<div class="table-responsive">
			<table class="table table-bordered" width="100%" cellspacing="0">
				<thead class="bg-danger text-white">
					<tr align="center">
						<th width="5%" rowspan="2">No</th>
						<th>Nama Alternatif</th>
						<?php foreach ($kriteria as $key): ?>
							<th><?= $key['kode_kriteria'] ?></th>
						<?php endforeach ?>
					</tr>
				</thead>
				<tbody>
					<?php 
						$no=1;
						foreach ($alternatif as $keys): ?>
					<tr align="center">
						<td><?= $no; ?></td>
						<td align="left"><?= $keys['nama'] ?></td>
						<?php foreach ($kriteria as $key): ?>
						<td>
						<?php 
							if($key['ada_pilihan']==1){
								$q4 = mysqli_query($koneksi,"SELECT sub_kriteria.nilai FROM penilaian JOIN sub_kriteria WHERE penilaian.nilai=sub_kriteria.id_sub_kriteria AND penilaian.id_alternatif='$keys[id_alternatif]' AND penilaian.id_kriteria='$key[id_kriteria]'");
								$data = mysqli_fetch_array($q4);
								echo $data['nilai'];
							}else{
								$q4 = mysqli_query($koneksi,"SELECT nilai FROM penilaian WHERE id_alternatif='$keys[id_alternatif]' AND id_kriteria='$key[id_kriteria]'");
								$data = mysqli_fetch_array($q4);
								echo $data['nilai'];
							}
						?>
						</td>
						<?php endforeach ?>
					</tr>
					<?php
						$no++;
						endforeach
					?>
				</tbody>
			</table>
		</div>
	</div>
</div>

<div class="card shadow mb-4">
    <!-- /.card-header -->
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-danger"><i class="fa fa-table"></i> Matriks Ternormalisasi (R)</h6>
    </div>

    <div class="card-body">
		<div class="table-responsive">
			<table class="table table-bordered" width="100%" cellspacing="0">
				<thead class="bg-danger text-white">
					<tr align="center">
						<th width="5%" rowspan="2">No</th>
						<th>Nama Alternatif</th>
						<?php foreach ($kriteria as $key): ?>
							<th><?= $key['kode_kriteria'] ?></th>
						<?php endforeach ?>
					</tr>
				</thead>
				<tbody>
					<?php 
						$no=1;
						foreach ($alternatif as $keys): ?>
					<tr align="center">
						<td><?= $no; ?></td>
						<td align="left"><?= $keys['nama'] ?></td>
						<?php foreach ($kriteria as $key): ?>
						<td>
						<?php 
							if($key['ada_pilihan']==1){
								$q4 = mysqli_query($koneksi,"SELECT sub_kriteria.nilai FROM penilaian JOIN sub_kriteria WHERE penilaian.nilai=sub_kriteria.id_sub_kriteria AND penilaian.id_alternatif='$keys[id_alternatif]' AND penilaian.id_kriteria='$key[id_kriteria]'");
								$dt1 = mysqli_fetch_array($q4);
								
								$q5 = mysqli_query($koneksi,"SELECT MAX(sub_kriteria.nilai) as max, MIN(sub_kriteria.nilai) as min, kriteria.type FROM penilaian JOIN sub_kriteria ON penilaian.nilai=sub_kriteria.id_sub_kriteria JOIN kriteria ON penilaian.id_kriteria=kriteria.id_kriteria WHERE penilaian.id_kriteria='$key[id_kriteria]'");
								$dt2 = mysqli_fetch_array($q5);
								if($dt2['type']=="Benefit"){
									echo $dt1['nilai']/$dt2['max'];
								}else{
									echo $dt2['min']/$dt1['nilai'];
								}
							}else{
								$q4 = mysqli_query($koneksi,"SELECT nilai FROM penilaian WHERE id_alternatif='$keys[id_alternatif]' AND id_kriteria='$key[id_kriteria]'");
								$dt1 = mysqli_fetch_array($q4);
								
								$q5 = mysqli_query($koneksi,"SELECT MAX(penilaian.nilai) as max, MIN(penilaian.nilai) as min, kriteria.type FROM penilaian JOIN kriteria ON penilaian.id_kriteria=kriteria.id_kriteria WHERE penilaian.id_kriteria='$key[id_kriteria]'");
								$dt2 = mysqli_fetch_array($q5);
								if($dt2['type']=="Benefit"){
									echo $dt1['nilai']/$dt2['max'];
								}else{
									echo $dt2['min']/$dt1['nilai'];
								}
							}
						?>
						</td>
						<?php endforeach ?>
					</tr>
					<?php
						$no++;
						endforeach
					?>
				</tbody>
			</table>
		</div>
	</div>
</div>

<div class="card shadow mb-4">
    <!-- /.card-header -->
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-danger"><i class="fa fa-table"></i> Bobot Preferensi (W)</h6>
    </div>

    <div class="card-body">
		<div class="table-responsive">
			<table class="table table-bordered" width="100%" cellspacing="0">
				<thead class="bg-danger text-white">
					<tr align="center">
						<?php foreach ($kriteria as $key): ?>
						<th><?= $key['kode_kriteria'] ?> (<?= $key['type'] ?>)</th>
						<?php endforeach ?>
					</tr>
				</thead>
				<tbody>
					<tr align="center">
						<?php foreach ($kriteria as $key): ?>
						<td>
						<?php 
						echo $key['bobot'];
						?>
						</td>
						<?php endforeach ?>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
</div>

<div class="card shadow mb-4">
    <!-- /.card-header -->
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-danger"><i class="fa fa-table"></i> Perhitungan (V))</h6>
    </div>

    <div class="card-body">
		<div class="table-responsive">
			<table class="table table-bordered" width="100%" cellspacing="0">
				<thead class="bg-danger text-white">
					<tr align="center">
						<th width="5%" rowspan="2">No</th>
						<th>Nama Alternatif</th>
						<th>Perhitungan</th>
						<th>Nilai</th>
					</tr>
				</thead>
				<tbody>
					<?php 
						$no=1;
						foreach ($alternatif as $keys): ?>
					<tr align="center">
						<td><?= $no; ?></td>
						<td align="left"><?= $keys['nama'] ?></td>
						<td>
						SUM
						<?php 
						$nilai_v = 0;
						foreach ($kriteria as $key):
							$bobot = $key['bobot'];
							if($key['ada_pilihan']==1){
								$q4 = mysqli_query($koneksi,"SELECT sub_kriteria.nilai FROM penilaian JOIN sub_kriteria WHERE penilaian.nilai=sub_kriteria.id_sub_kriteria AND penilaian.id_alternatif='$keys[id_alternatif]' AND penilaian.id_kriteria='$key[id_kriteria]'");
								$dt1 = mysqli_fetch_array($q4);
								
								$q5 = mysqli_query($koneksi,"SELECT MAX(sub_kriteria.nilai) as max, MIN(sub_kriteria.nilai) as min, kriteria.type FROM penilaian JOIN sub_kriteria ON penilaian.nilai=sub_kriteria.id_sub_kriteria JOIN kriteria ON penilaian.id_kriteria=kriteria.id_kriteria WHERE penilaian.id_kriteria='$key[id_kriteria]'");
								$dt2 = mysqli_fetch_array($q5);
								if($dt2['type']=="Benefit"){
									$nilai_r = $dt1['nilai']/$dt2['max'];
								}else{
									$nilai_r = $dt2['min']/$dt1['nilai'];
								}
							}else{
								$q4 = mysqli_query($koneksi,"SELECT nilai FROM penilaian WHERE id_alternatif='$keys[id_alternatif]' AND id_kriteria='$key[id_kriteria]'");
								$dt1 = mysqli_fetch_array($q4);
								
								$q5 = mysqli_query($koneksi,"SELECT MAX(penilaian.nilai) as max, MIN(penilaian.nilai) as min, kriteria.type FROM penilaian JOIN kriteria ON penilaian.id_kriteria=kriteria.id_kriteria WHERE penilaian.id_kriteria='$key[id_kriteria]'");
								$dt2 = mysqli_fetch_array($q5);
								if($dt2['type']=="Benefit"){
									$nilai_r = $dt1['nilai']/$dt2['max'];
								}else{
									$nilai_r = $dt2['min']/$dt1['nilai'];
								}
							}
							$nilai_penjumlahan = $bobot*$nilai_r;
							$nilai_v += $nilai_penjumlahan;
							echo "(".$bobot."x".$nilai_r.") ";
						endforeach ?>
						</td>
						<td>
							<?php
								echo $nilai_v;
								mysqli_query($koneksi,"INSERT INTO hasil (id_alternatif, nilai) VALUES ('$keys[id_alternatif]', '$nilai_v')");
							?>
						</td>
					</tr>
					<?php
						$no++;
						endforeach
					?>
				</tbody>
			</table>
		</div>
	</div>
</div>

<?php
require_once('template/footer.php');
}
else {
	header('Location: login.php');
}
?>