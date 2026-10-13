<?php 
include('db/config.php');
include('includes/header.php');
include('lock.php');
?>
	<!--begin::Body-->
	<body id="kt_body" class="header-fixed header-mobile-fixed subheader-enabled subheader-fixed aside-enabled aside-fixed aside-minimize-hoverable page-loading">
		<!--begin::Main-->
		<div class="d-flex flex-column flex-root">
			<!--begin::Login-->
			<div class="login login-1 login-signin-on d-flex flex-column flex-lg-row flex-column-fluid bg-white" id="kt_login">
				<!--begin::Aside-->
				<div class="login-aside d-flex flex-column flex-row-auto" style="background-color: #ffffff;">
					<!--begin::Aside Top-->
					<div class="d-flex flex-column-auto flex-column pt-lg-40 pt-15">
						<!--begin::Aside header-->
						<a href="#" class="text-center mb-10">
							<img src="<?=BASE_URL?>assets/media/logos/logo-letter-1.png" class="max-h-70px" alt="" />
						</a>
						<!--end::Aside header-->
						
					</div>
					<!--end::Aside Top-->
					<!--begin::Aside Bottom-->
					<div class="aside-img d-flex flex-row-fluid bgi-no-repeat bgi-position-y-bottom bgi-position-x-center" style="background-image: url(<?=BASE_URL?>assets/media/svg/illustrations/login-visual-2.svg)"></div>
					<!--end::Aside Bottom-->
				</div>
				<!--begin::Aside-->
				<!--begin::Content-->
				<div class="login-content flex-row-fluid d-flex flex-column justify-content-center position-relative overflow-hidden p-7 mx-auto">
					<!--begin::Content body-->
					<div class="d-flex flex-column-fluid flex-center">
						<!--begin::login-->
						<div class="login-form login-signin">
							<!--begin::Form-->
							<form action="" method="post" id="login-form">
								<!--begin::Title-->
								<div class="pb-13 pt-lg-0 pt-5">
									<h3 class="font-weight-bolder text-dark font-size-h4 font-size-h1-lg">Welcome to  Admin Panel</h3>
								</div>
								<!--begin::Title-->
								<div class="err_msg"></div>
								<!--begin::Form group-->
								<div class="form-group">
									<label class="font-size-h6 font-weight-bolder text-dark">Email</label>
									<input class="form-control form-control-solid h-auto py-6 px-6 rounded-lg" type="text" name="login_email" autocomplete="off" placeholder="Email ID" />
								</div>
								<!--end::Form group-->
								<!--begin::Form group-->
								<div class="form-group">
									<div class="d-flex justify-content-between mt-n5">
										<label class="font-size-h6 font-weight-bolder text-dark pt-5">Password</label>
									</div>
									<input class="form-control form-control-solid h-auto py-6 px-6 rounded-lg" type="password" name="login_password" placeholder="Password" autocomplete="off" />
								</div>
								<!--end::Form group-->
								<!--begin::Action-->
								<div class="pb-lg-0 pb-5">
									<button type="submit" name="login_submit" class="btn btn-primary font-weight-bolder font-size-h6 px-8 py-4 my-3 mr-3">Log In</button>
									<!-- Shown only on a device with a fingerprint reader or face check the browser can use. The words are set for the device below. -->
									<button type="button" id="bioLogin" class="btn btn-light-primary font-weight-bolder font-size-h6 px-8 py-4 my-3" hidden><i class="bi bi-fingerprint"></i> <span>Sign in with fingerprint or face</span></button>
									<div class="text-muted font-size-sm" id="bioLoginNote" hidden>Works after you set it up under My Profile on this device.</div>
								</div>
								<!--end::Action-->
							</form>
							<!--end::Form-->
						</div>
						<!--end::login-->
					
					
					</div>
					<!--end::Content body-->
					<script src="<?=BASE_URL?>assets/js/dre-biometric.js?v=3"></script>
					<script>
					// Sign-in page: the fingerprint / face button. The device shows the accounts it has a key for, so no email is typed.
					(function () {
						var button = document.getElementById('bioLogin');
						var note = document.getElementById('bioLoginNote');
						var message = document.querySelector('.err_msg');
						var method = DreBiometric.method();
						button.querySelector('i').className = 'bi ' + method.icon;
						button.querySelector('span').textContent = 'Sign in with ' + method.name;
						DreBiometric.available().then(function (yes) {
							button.hidden = note.hidden = !yes;
						});
						button.addEventListener('click', function () {
							var label = button.innerHTML;
							button.disabled = true;
							button.textContent = 'WAIT...';
							DreBiometric.signIn().then(function (answer) {
								if (answer.status === 'success') {
									window.location = base_url + 'dashboard.php';
									return;
								}
								message.className = 'err_msg alert alert-danger';
								message.textContent = answer.msg;
								button.innerHTML = label;
								button.disabled = false;
							});
						});
					})();
					</script>
	<?php include('includes/footer.php');?>				