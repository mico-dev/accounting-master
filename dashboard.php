
<?php 
include('config.php');
error_reporting(0);
session_start();
date_default_timezone_set('Asia/Manila');
$staff_un = $_SESSION['staff_un'];
$status = $_SESSION['staff_status'];
$type = $_SESSION['staff_type'];
$staff_department =$_SESSION['staff_department'];
if(!isset($staff_un)){
	echo "<script>window.location='logout.php';</script>";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<title>MIHMCI Mailer</title>
	<meta content='width=device-width, initial-scale=1.0, shrink-to-fit=no' name='viewport' />
	<link rel="icon" href="assets/images/logo.png" type="image/x-icon"/>

	<!-- Fonts and icons -->
	<script src="assets/js/plugin/webfont/webfont.min.js"></script>
	<script>
		WebFont.load({
			google: {"families":["Lato:300,400,700,900"]},
			custom: {"families":["Flaticon", "Font Awesome 5 Solid", "Font Awesome 5 Regular", "Font Awesome 5 Brands", "simple-line-icons"], urls: ['assets/css/fonts.min.css']},
			active: function() {
				sessionStorage.fonts = true;
			}
		});
	</script>

	<!-- CSS Files -->
	<link rel="stylesheet" href="assets/css/bootstrap.min.css">
	<link rel="stylesheet" href="assets/css/atlantis.css">
	<style>
	#logo-header, .prep, .app {
		display:none;
	}
	@media print {
		#form {
			display:none;
		}
		#logo-header, .prep, .app {
			display:block;
		}
		table {
			border-color:#000 !important;
			margin-bottom:5%;
		}
		table th, table td {
			border-color:#000 !important;
		}
		#tableprint, #tableprint th {
			color:#000;
		}
		.modal {
			padding:unset;
		}
		.modal-dialog {
			max-width:100% !important;
		}
		.modal-footer, .modal-header {
			display:none;
		}

	}
	</style>
</head>
<body>
<style type="text/css" >
#log-header {
	display: none; 
}
</style>
	<div class="wrapper">
	<div class="main-header">
			<div class="logo-header bg-primary-gradient" data-background-color="" style="line-height:20px;color:#fff;"><?php echo $_SESSION['staff_department']; ?></div>
			<nav class="navbar navbar-header navbar-expand-lg" data-background-color="dark">
				<div class="container-fluid">
					<ul class="navbar-nav topbar-nav ml-md-auto align-items-center">
						<li class="nav-item dropdown hidden-caret">
							<a class="dropdown-toggle profile-pic" data-toggle="dropdown" href="#" aria-expanded="false">
								<div class="avatar-sm">
									<img src="assets/images/logo.png" alt="..." class="avatar-img rounded-circle">
								</div>
							</a>
							<ul class="dropdown-menu dropdown-user animated fadeIn">
								<div class="dropdown-user-scroll scrollbar-outer">
									<li>
										<div class="user-box">
											<div class="u-text">
												<h4>
											<?php 
												$staff_un = $_SESSION['staff_un']; 
												$user = mysqli_query($con, "SELECT * FROM tb_staff WHERE staff_un='$staff_un'") or die(mysqli_error($con));
												while($row = mysqli_fetch_assoc($user)) {
													$firstname = ucwords($row['staff_fname']);
													echo '<h4>'.$firstname.'</h4>';
												}
											?>
												</h4>
												<p class="text-muted"><?php echo $_SESSION['staff_department']; ?></p>
											</div>
										</div>
									</li>
									<li>
										<div class="dropdown-divider"></div>
										<a class="dropdown-item" href="logout.php">Logout</a>
									</li>
								</div>
							</ul>
						</li>
					</ul>
				</div>
			</nav>
		</div>
		<div class="sidebar sidebar-style-2" data-background-color="dark2">
			<div class="sidebar-wrapper scrollbar scrollbar-inner">
				<div class="sidebar-content">
					<ul class="nav nav-primary">
						<li class="nav-item active">
							<a href="dashboard.php" class="collapsed" aria-expanded="false">
								<i class="fas fa-users"></i>
								<p>Professional Fee List</p>
							</a>
						</li>
						<li class="nav-item">
							<a href="readers-fee.php" class="collapsed" aria-expanded="false">
								<i class="fas fa-users"></i>
								<p>Readers Fee List</p>
							</a>
						</li>
						<li class="nav-item">
							<a href="logout.php">
								<i class="fas fa-sign-out-alt"></i>
								<p>Logout</p>
							</a>
						</li>
					</ul>
				</div>
			</div>
		</div>
		<!-- End Sidebar -->

		<div class="main-panel">
			<div class="container">
				<div class="page-inner">
					<div class="mt-2 mb-4">
						
					</div>
					<div class="row">
						<div class="col-md-12">
							<div class="card card-dark bg-primary-gradient">
								<div class="card-body table-responsive" id="source">
								<div class="col-lg-12 mb-3">
								<h2 class="text-white pb-2">Professional Fee List</h2>
							<?php 
							if($staff_department =='Hospital Information System') {?>
								<form enctype="multipart/form-data" method="post" role="form" class="alert alert-success">
									<div class="form-group">
										<div class="custom-file">
											<label class="custom-file-label" style="color:#333 !important;">Choose "professionalfee.csv" file...</label>
											<input type="file" name="file" class="custom-file-input" accept=".csv">
										</div>
									</div>
									<div class="form-group">
										<input type="submit" class="btn btn-primary mb-3" name="submit" value="Upload CSV">
										<?php
										
										$result = mysqli_query($con, "SET NAMES utf8")or die(mysqli_error($con));
										$query = "SELECT * FROM tb_pf_dr";
										$result = mysqli_query($con, $query)or die(mysqli_error($con));
											while($row = mysqli_fetch_assoc($result)) {
												$fullname = str_replace("ñ", "Ñ", $row['d_fullname']);	
											}
										if(isset($_POST["submit"])){
											$file = $_FILES['file']['tmp_name'];
											if($_FILES['file']['size'] > 0) {
												if($_FILES['file']['name'] == 'professionalfee.csv') {

													$handle = fopen($file, "r");
													fgetcsv($handle); //skip 1st row
													$result = array();
													$totalduplicate ='';

													while (($column = fgetcsv($handle, 100000, ",")) !== FALSE) {
														if (array_filter($column)) { //ignore blank fields
														$testString = trim(mysqli_real_escape_string($con,strtoupper($column[5])));
														$testString = preg_replace('/[\/]/', ':', $testString);
														$testString = preg_split('/:/', $testString);	
														$patient_name = $testString[0];
														$pf_amount =  $column[4];
														$pf_trans_date = $column[2];
														$pf_from_date =  date('m/1/Y', strtotime($pf_trans_date));
														$pf_to_date = date('m/t/Y', strtotime($pf_trans_date));
														$d_license =  $column[1];
														#$encoder = $column[6];
														//duplicate-checker
															$duplicate = mysqli_query($con, "SELECT * FROM `tb_md_pf` WHERE patient_name='$patient_name' AND pf_amount='$pf_amount' AND pf_trans_date='$pf_trans_date' AND d_license='$d_license'")or die(mysqli_error($con));
															$totalduplicate+=mysqli_num_rows($duplicate);
															if(mysqli_num_rows($duplicate)==0) {
																$filterlicense = mysqli_query($con, "SELECT * FROM tb_pf_dr WHERE d_license='$d_license'") or die(mysqli_error($con));
																	while($row=mysqli_fetch_assoc($filterlicense)) {
																		$filteredlicense = $row['d_license'];
																		
																		$sql = mysqli_query($con,"INSERT INTO `tb_md_pf`(`patient_name`, `pf_amount`, `pf_trans_date`, `pf_from_date`, `pf_to_date`, `d_license`) VALUES ('$patient_name', '$pf_amount', '$pf_trans_date', '$pf_from_date', '$pf_to_date','$filteredlicense')")or die(mysqli_error($con));

																			if($sql) {
																				$result[]='<div class="alert alert-success text-dark animated fadeIn">PF CSV uploaded successfully</div>';
																			
																			} else {
																				$result[]='<div class="alert alert-danger text-dark animated fadeIn">PF CSV upload failed</div>';
																			}
																	}
															} else {
																while($row=mysqli_fetch_assoc($duplicate)) {
																	$patient_name = $row['patient_name'];
																	$pf_amount =  $row['pf_amount'];
																	$pf_trans_date = $row['pf_trans_date'];
																	$pf_from_date =  $row['pf_from_date'];
																	$pf_to_date = $row['pf_to_date'];
																	}
																	$result[]='<div class="alert alert-danger text-dark animated fadeIn mt-2">Duplicate Records will not be uploaded</div>';
															}
															
														}
														
													}
													fclose($handle);
												echo $result[0];
												#echo "<script>window.location='dashboard.php';</script>";
												} else {
													echo '<div class="alert alert-danger text-dark animated fadeIn">Wrong CSV file detected</div>';
												}
											} else {
												echo '<div class="alert alert-danger text-dark animated fadeIn">No CSV file detected</div>';
											}
										}//end submit
										?>
									</div>
								</form>
							<?php
							} else {
								//do nothing
								
							}
							?>
						</div>
						<div class="col-lg-12 mb-3">
							<form method="POST">
								<div class="row no-gutters">
									<div class="col-lg-5 text-white">
										<div class="form-group">
											<!--	From:
											<input class="form-control" type="date" name="from"> -->
											Lastname
											<input class="form-control" type="text" name="searchdoctor" id="searchdoctor"> 
										</div> 
									</div> 
									<div class="col-lg-3 text-white"> 
										<div class="form-group">
											Period covered
											<input class="form-control" type="month" name="to" required> 
										</div> 
									</div> 
									<div class="col-lg-2">
										<div class="form-group">
											&nbsp;  
											<input type="submit" name="filter" class="btn btn-success btn-block" value="FILTER">
										</div>
									</div>
									<div class="col-lg-2">
										<div class="form-group">
											&nbsp;  
											<a class="btn btn-danger btn-block" href="" id="reset">RESET</a>
										</div>
									</div>
									<div class="col-lg-12">
									<?php 
									if(isset($_POST['filter'])) {
										#$from = $_POST['from'];
										#$from = date('m/j/Y', strtotime($from));
										$to = $_POST['to'];
										$periodCovered = date('m/j/Y', strtotime($to));
										$searchdoctor = $_POST['searchdoctor'];
										$from =  date('m/1/Y', strtotime($to));
										$to = date('m/t/Y', strtotime($to));
										echo '<p class="mb-0 mt-2">Showing results from: <b> <span class="dfrom" data-from="'.date('m/j/Y', strtotime($from)).'">'.date('F d , Y', strtotime($from)).'</span> to <span class="dto" data-to="'.date('m/j/Y', strtotime($to)).'">'.date('F d , Y', strtotime($to)).'</span></b></p>';
									?>
									</div>
									<div class="col-lg-12 mt-3">
										<table class="table table-bordered table-dark">
											<thead>
												<tr>
													<!--<td>ID</td>
													<td>Patient Name</td>
													<td>Amount</td> 
													<td>Trans Date</td> -->
													<td>Doctor</td>
													<td>Email</td>
													<td>Status</td>
													<td width='auto'>Action</td>
												</tr>
											</thead>
											<tbody>
										<?php
										if(!empty($searchdoctor)) {
											$querydoc = mysqli_query($con, "SELECT * FROM tb_pf_dr WHERE d_fullname LIKE '%$searchdoctor%'")  or die(mysqli_error($con));
											if(mysqli_num_rows($querydoc)<>0) {
												while($row=mysqli_fetch_assoc($querydoc)) {
													$d_license =  $row['d_license'];
													$filter = mysqli_query($con, "SELECT * FROM `tb_md_pf` WHERE pf_from_date='$from' AND pf_to_date='$to' AND d_license='$d_license' GROUP BY d_license ORDER BY `d_license` DESC") or die(mysqli_error($con));
														while($row=mysqli_fetch_assoc($filter)){
															$id = $row['id'];
															$patient_name = $row['patient_name'];
															$pf_amount = $row['pf_amount'];
															$pf_trans_date = $row['pf_trans_date'];
															$pf_from_date = $row['pf_from_date'];
															$pf_to_date = $row['pf_to_date'];
															$d_license = $row['d_license'];
															$status = $row['status'];
															echo '<tr>';
															$doc = mysqli_query($con, "SELECT * FROM tb_pf_dr WHERE d_license='$d_license' ORDER BY d_id") or die(mysqli_error($con));
																while ($row =mysqli_fetch_assoc($doc)) {
																	$d_id = $row['d_id'];
																	$d_license = $row['d_license'];
																	$d_email = $row['d_email'];
																	$fullname = str_replace("ñ", "Ñ", $row['d_fullname']);
																	if($staff_department =='Hospital Information System') {
																		echo '<td><button class="btn bg-transparent p-0 edit" data-id="'.$d_id.'"><i class="far fa-edit text-success"></i></button> '.$fullname.'</td>';
																	}else{
																		echo '<td>'.$fullname.'</td>';
																	}
																	
																	echo '<td>'.$d_email.'</td>';
																}
															echo '<td>'.$status.'</td>';
															if($d_email!="-") {
																if($status=='sent') {
																	echo '<td><button class="btn btn-warning  sendgroup" data-id="'.$id.'" data-from="'.$from.'" data-to="'.$to.'" data-license="'.$d_license.'" data-drname="'.$fullname.'"><i class="fas fa-paper-plane"></i> Resend</button> <button class="btn btn-primary  prev" data-id="'.$id.'" data-from="'.$from.'" data-to="'.$to.'" data-license="'.$d_license.'" data-drname="'.$fullname.'"><i class="fas fa-search"></i> Preview</button></td>';
																}else{
																	echo '<td><button class="btn btn-success  sendgroup" data-id="'.$id.'" data-from="'.$from.'" data-to="'.$to.'" data-license="'.$d_license.'" data-drname="'.$fullname.'"><i class="fas fa-paper-plane"></i> Send</button> <button class="btn btn-primary  prev" data-id="'.$id.'" data-from="'.$from.'" data-to="'.$to.'" data-license="'.$d_license.'" data-drname="'.$fullname.'"><i class="fas fa-search"></i> xPreview</button></td>';
																}
															} else {
																echo '<td><button class="btn btn-primary  prev" data-id="'.$id.'" data-from="'.$from.'" data-to="'.$to.'" data-license="'.$d_license.'" data-drname="'.$fullname.'"><i class="fas fa-search"></i> Preview</button></td>';
															}
															
															echo '</tr>';
														}
													}
												} else {
													
													echo '<tr>';
													echo '<td colspan="4">No available result</td>';
													echo '</tr>';
													die();
												}
											} else {
												$filter = mysqli_query($con, "SELECT * FROM `tb_md_pf` INNER JOIN tb_pf_dr ON tb_pf_dr.d_license =  tb_md_pf.d_license WHERE pf_from_date='$from' AND pf_to_date='$to' GROUP BY tb_md_pf.d_license ORDER BY tb_pf_dr.d_id") or die(mysqli_error($con));
												if(mysqli_num_rows($filter)<>0) {
													while($row=mysqli_fetch_assoc($filter)){
														$id = $row['id'];
														$patient_name = $row['patient_name'];
														$pf_amount = $row['pf_amount'];
														$pf_trans_date = $row['pf_trans_date'];
														$pf_from_date = $row['pf_from_date'];
														$pf_to_date = $row['pf_to_date'];
														$d_license = $row['d_license'];
														$status = $row['status'];
														echo '<tr>';
														$doc = mysqli_query($con, "SELECT * FROM tb_pf_dr WHERE d_license='$d_license' ORDER BY d_id") or die(mysqli_error($con));
															while ($row =mysqli_fetch_assoc($doc)) {
																$d_id = $row['d_id'];
																$d_license = $row['d_license'];
																$d_email = $row['d_email'];
																$fullname = str_replace("ñ", "Ñ", $row['d_fullname']);
																if($staff_department =='Hospital Information System') {
																	echo '<td><button class="btn bg-transparent p-0 edit" data-id="'.$d_id.'"><i class="far fa-edit text-success"></i></button> '.$fullname.'</td>';
																}else{
																	echo '<td>'.$fullname.'</td>';
																}
																echo '<td>'.$d_email.'</td>';
															}
														echo '<td>'.$status.'</td>';
														if($d_email!="-") {
															if($status=='sent') {
																echo '<td><button class="btn btn-warning  sendgroup" data-id="'.$id.'" data-from="'.$from.'" data-to="'.$to.'" data-license="'.$d_license.'" data-drname="'.$fullname.'"><i class="fas fa-paper-plane"></i> Resend</button> <button class="btn btn-primary  prev" data-id="'.$id.'" data-from="'.$from.'" data-to="'.$to.'" data-license="'.$d_license.'" data-drname="'.$fullname.'"><i class="fas fa-search"></i> Preview</button></td>';
															}else{
																echo '<td><button class="btn btn-success  sendgroup" data-id="'.$id.'" data-from="'.$from.'" data-to="'.$to.'" data-license="'.$d_license.'" data-drname="'.$fullname.'"><i class="fas fa-paper-plane"></i> Send</button> <button class="btn btn-primary  prev" data-id="'.$id.'" data-from="'.$from.'" data-to="'.$to.'" data-license="'.$d_license.'" data-drname="'.$fullname.'"><i class="fas fa-search"></i> Preview</button></td>';
															}
														} else {
															echo '<td><button class="btn btn-primary  prev" data-id="'.$id.'" data-from="'.$from.'" data-to="'.$to.'" data-license="'.$d_license.'" data-drname="'.$fullname.'"><i class="fas fa-search"></i> Preview</button></td>';
														}
														
														echo '</tr>';
													}
												} else {
													
													echo '<tr>';
													echo '<td colspan="4">No available result</td>';
													echo '</tr>';
													die();
												}
											}
										
											?>
											</tbody>
										</table>
									</div>
								<?php
								} else { 
									//do nothing else filter
								/*$testString = trim('SUMAGPAO, CHAD ANDREI PANES / IPD / 5947 - Posted Professional Fee Payable');
								$testString = preg_replace('/[\/]/', ':', $testString);
								$testString = preg_replace('/[\-]/', ':', $testString);
								$testString = preg_split('/:/', $testString);	
								$patientname = $testString[0];*/

								}
								?>
									</div> <!-- end col-lg-12 tables -->
								</div><!-- end div row-->
							</form>
						</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<footer class="footer bg-primary-gradient">
				<div class="container-fluid">
					<div class="copyright ml-auto nav-link text-white">
					© 2020 | HIS Department
					</div>				
				</div>
			</footer>
		</div>
		<!-- End Custom template -->
	</div>
								
	<script src="assets/js/core/jquery.3.2.1.min.js"></script>
	<script src="assets/js/plugin/jquery-ui-1.12.1.custom/jquery-ui.min.js"></script>
	<script src="assets/js/plugin/jquery-ui-touch-punch/jquery.ui.touch-punch.min.js"></script>
	<script src="assets/js/core/bootstrap.min.js"></script>
	<script src="assets/js/core/popper.min.js"></script>
	<script src="assets/js/plugin/print/print.js"></script>
	<!-- <script src="assets/js/plugin/datatables/datatables.min.js"></script> -->
	<script>
	$(document).ready(function() {
		
		$(document).on('click', '.edit', function(e) {
			e.preventDefault();
			var id = e.currentTarget.dataset.id;
			$('#modal').modal('show');
			$('#modal').on('shown.bs.modal', function () {
				console.log(id);
				var type = 'edit';
				var modal  = $(this);
				//$(document).on('click', '.yes', function(e) { 
					//modal.find('.modal-body').addClass('is-loading');
					$.ajax ({
					url: 'pf-process.php',
					method: 'post',
					data: {
						type:type,
						id:id
					},
						success:function(result) {
							modal.find('.modal-body').html(result);
						}
					}).done(function(result) {
						//console.log(result)
						//done do something here
					});
				$(document).on('click', '.yes', function(e) {
					$('#modal').modal('hide');
				});
			});
			$('#modal').on('hide.bs.modal', function () {	
				location.reload();
			})
		});
		$(document).on('click', '.save', function(e) {
			e.preventDefault();
			var id = e.currentTarget.dataset.id;
			var name = $('[name="doctorname"]').val();
			var email = $('[name="email"]').val();
			var license =$('[name="license"]').val();
			var tax_type = $('[name="type"]').val();
			var percent = $('[name="percent"]').val();
			var type ='update';
			
			$.ajax ({
				url: 'pf-process.php',
				method: 'post',
				data: {
					type:type,
					id:id,
					name: name,
					email: email,
					license: license,
					tax: tax_type,
					percent: percent
				},
				success:function(result) {
					$('.modal-body').addClass('is-loading');
					setInterval(() => {
						$('.modal-body').removeClass('is-loading');
					}, 2000);
					$('.modal-body').html('<p>Done</p>');
				}
			}).done(function(result) {
				//console.log(result)
				//done do something here
			});
		});
		$(document).on('click', '.prev', function(e) {
			e.preventDefault();
			//var id = e.currentTarget.dataset.id;
			var from = e.currentTarget.dataset.from;
			var to = e.currentTarget.dataset.to;
			var license = e.currentTarget.dataset.license;
			var drname = e.currentTarget.dataset.drname;

			$('#modal').modal('show');
			$('#modal').on('shown.bs.modal', function () {
				var type = 'prev';
				var modal  = $(this);
				//$(document).on('click', '.yes', function(e) { 
					//modal.find('.modal-body').addClass('is-loading');
					$.ajax ({
					url: 'pf-process.php',
					method: 'post',
					data: {
						type:type,
						//id:id,
						from:from,
						to:to,
						license:license
					},
						success:function(result) {
							
							modal.find('.modal-body').html(result);
							var addPf = '<form method="POST" id="form" class="mt-3 border border-dark pt-3 pb-3"><div class="row no-gutters"><div class="col"><div class="form-group"><p class="text-dark mb-0">Transaction Date</p><input class="form-control" type="date" name="transdate" required=""></div></div><div class="col"><div class="form-group"><p class="text-dark mb-0">Patient Name</p><input class="form-control" type="text" name="patientname" required=""></div></div><div class="col"><div class="form-group"><p class="text-dark mb-0">Amount</p><input class="form-control" type="text" name="amount" required=""></div></div><div class="col-lg-2"><div class="form-group"><p class="text-white mb-0">Add PF</p><button class="btn btn-primary btn-block add">Add PF</button></div></div></div></form>';
							modal.find('.modal-body').append(addPf);
							modal.find('.modal-footer').append("");
							//modal.find('.modal-footer').append('<button class="btn btn-primary sendgroup" data-from="'+from+'" data-to="'+to+'" data-license="'+license+'" data-drname="'+drname+'"><i class="fas fa-paper-plane"></i> Send</button>');

							//
							//
						}
					}).done(function(result) {
						//console.log(result)
						//done do something here
					});
				$(document).on('click', '.yes', function(e) {
					$('#modal').modal('hide');
				});
			});
			$('#modal').on('hide.bs.modal', function () {	
				location.reload();
			});
			
		});
		
		$(document).on('click', '.delete', function(e) {
			e.preventDefault();
			let patientName = e.currentTarget.dataset.name;
			let confirmDelete = confirm("Are you sure you want to delete "+patientName+"?");
			if (confirmDelete == true) {
				var id = e.currentTarget.dataset.id;
				var pcfrom = e.currentTarget.dataset.pcfrom;
				var pcto = e.currentTarget.dataset.pcto;
				var license = e.currentTarget.dataset.license;
				var type = 'delete';
				var modal  = $(this);
				$.ajax ({
					url: 'pf-process.php',
					method: 'post',
					data: {
						type:type,
						id:id
					},
					success:function(result) {
						$.ajax ({
							url: 'pf-process.php',
							method: 'post',
							data: {
								type:'prev',
								from:pcfrom,
								to:pcto,
								license:license
							},
								success:function(result) {
									
									$('.modal-body').html(result);
									var addPf = '<form method="POST" id="form" class="mt-3 border border-dark pt-3 pb-3"><div class="row no-gutters"><div class="col"><div class="form-group"><p class="text-dark mb-0">Transaction Date</p><input class="form-control" type="date" name="transdate" required=""></div></div><div class="col"><div class="form-group"><p class="text-dark mb-0">Patient Name</p><input class="form-control" type="text" name="patientname" required=""></div></div><div class="col"><div class="form-group"><p class="text-dark mb-0">Amount</p><input class="form-control" type="text" name="amount" required=""></div></div><div class="col-lg-2"><div class="form-group"><p class="text-white mb-0">Add PF</p><button class="btn btn-primary btn-block add">Add PF</button></div></div></div></form>';
									$('.modal-body').append(addPf);
								}
							}).done(function(result) {
								//console.log(result)
								//done do something here
							});
					}
				}).done(function(result) {
					//console.log(result)
					//done do something here
				});
			} else {
				
			}	
		});
		$(document).on('click', '.add', function(e) {
			e.preventDefault();
			var patientname = $('[name="patientname"]').val();
			var transdate = $('[name="transdate"]').val();
			var amount = $('[name="amount"]').val();
			var license = $('#lic').attr("data-lic");
			var pcfrom = $('#pcfrom').attr("data-pcfrom");
			var pcto = $('#pcto').attr("data-pcto");
			var type = 'add';
			var modal  = $(this);
			if($('[name="patientname"]').val().length!=0) { 
				if($('[name="transdate"]').val().length!=0) { 
					if($('[name="amount"]').val().length!=0) {
						$('.modal-body').addClass('is-loading');
						$.ajax ({
							url: 'pf-process.php',
							method:'post',
							data: {
								type:type,
								license:license,
								patientname:patientname,
								transdate:transdate,
								amount:amount,
								pcfrom:pcfrom,
								pcto:pcto
							},
							success:function(result) {
								$('.modal-body').removeClass('is-loading');
								$.ajax ({
									url: 'pf-process.php',
									method:'post',
									data: {
										type:'prev',
										from:pcfrom,
										to:pcto,
										license:license
									},
										success:function(result) {
											console.log(result)
											$('.modal-body').html(result);
											
											var addPf = '<form method="POST" id="form" class="mt-3 border border-dark pt-3 pb-3"><div class="row no-gutters"><div class="col"><div class="form-group"><p class="text-dark mb-0">Transaction Date</p><input class="form-control" type="date" name="transdate" required=""></div></div><div class="col"><div class="form-group"><p class="text-dark mb-0">Patient Name</p><input class="form-control" type="text" name="patientname" required=""></div></div><div class="col"><div class="form-group"><p class="text-dark mb-0">Amount</p><input class="form-control" type="text" name="amount" required=""></div></div><div class="col-lg-2"><div class="form-group"><p class="text-white mb-0">Add PF</p><button class="btn btn-primary btn-block add">Add PF</button></div></div></div></form>';
											$('.modal-body').append(addPf);

										}
									}).done(function(result) {
										//console.log(result)
										//done do something here
									});
							}
						}).done(function(result) {
							//console.log(result)
							//done do something here
						});
					}
				}
			}
		});
		$(document).on('click', '.sendgroup', function(e) {
			e.preventDefault();
			var from = e.currentTarget.dataset.from;
			var to = e.currentTarget.dataset.to;
			var license = e.currentTarget.dataset.license;
			var drname = e.currentTarget.dataset.drname;

			$('#modal').modal('show');
			$('#modal').on('shown.bs.modal', function () {
				var type = 'sendgroup';
				var modal  = $(this);
				var title = '<p>Are you sure you want send Dr. '+drname+' his/her PF, period covered: '+from+' to '+to+' ?</p>';
				modal.find('.modal-body').html(title);
				$(document).on('click', '.yes', function(e) { 
					modal.find('.modal-body').addClass('is-loading');
					$.ajax ({
					url: 'pf-process.php',
					method: 'post',
					data: {
						type:type,
						from:from,
						to:to,
						license:license
					},
					success:function(result) {
						
					}
					}).then(function(result){
						
					}).done(function(result){
						if(result!=0) {
							console.log(result);
							modal.find('.modal-body').removeClass('is-loading');
							modal.find('.modal-body').html('<p>Email was sent successfully</p>');
							setInterval(() => {
							$('#modal').modal('hide');		
							}, 5000);
						} else {
							modal.find('.modal-body').html('<p>Something went wrong please try again</p>');
						}
					});
				});
			});
			$('#modal').on('hide.bs.modal', function () {	
				location.reload();
			});
		});
		
		$('.print').on('click', function () {
			$("#modalPrint").print();
		});
	});//end document ready
	</script>
<div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document" style="">
		<div class="modal-content">
			<div class="modal-header bg-primary-gradient">
				<h3 class="modal-title text-light" id="title">MIHMCI PF Mailer</h3>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body" id="modalPrint">
				
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-success yes">Ok</button>
				<button type="button" class="btn btn-warning no" data-dismiss="modal">Close</button>
				<button class="btn btn-secondary print" id="print">Print PF</button>
			</div>
		</div>
	</div>
</div>	
</body>
</html>