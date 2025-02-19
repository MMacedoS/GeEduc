<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title><?=APP_NAME?></title>
        <link rel="shortcut icon" href="<?=URL_PREFIX_APP?>/Public/assets/images/ico-geeduc.png"/>
            <!-- *************
                ************ CSS Files *************
            ************* -->
    <!--===============================================================================================-->
        <link rel="stylesheet" type="text/css" href="<?=URL_PREFIX_APP?>/Public/vendor/bootstrap/css/bootstrap.min.css">
    <!--===============================================================================================-->
        <link rel="stylesheet" type="text/css" href="<?=URL_PREFIX_APP?>/Public/fonts/font-awesome-4.7.0/css/font-awesome.min.css">
    <!--===============================================================================================-->
        <link rel="stylesheet" type="text/css" href="<?=URL_PREFIX_APP?>/Public/fonts/Linearicons-Free-v1.0.0/icon-font.min.css">
    <!--===============================================================================================-->
        <link rel="stylesheet" type="text/css" href="<?=URL_PREFIX_APP?>/Public/vendor/animate/animate.css">
    <!--===============================================================================================-->	
        <link rel="stylesheet" type="text/css" href="<?=URL_PREFIX_APP?>/Public/vendor/css-hamburgers/hamburgers.min.css">
    <!--===============================================================================================-->
        <link rel="stylesheet" type="text/css" href="<?=URL_PREFIX_APP?>/Public/vendor/animsition/css/animsition.min.css">
    <!--===============================================================================================-->
        <link rel="stylesheet" type="text/css" href="<?=URL_PREFIX_APP?>/Public/vendor/select2/select2.min.css">
    <!--===============================================================================================-->	
        <link rel="stylesheet" type="text/css" href="<?=URL_PREFIX_APP?>/Public/vendor/daterangepicker/daterangepicker.css">
    <!--===============================================================================================-->
        <link rel="stylesheet" type="text/css" href="<?=URL_PREFIX_APP?>/Public/css/util.css">
        <link rel="stylesheet" type="text/css" href="<?=URL_PREFIX_APP?>/Public/css/main.css">
    </head>
    <body style="background-color: #666666;">
    
    <div class="limiter">
		<div class="container-login100">
            
			<div class="wrap-login100">
				<form class="login100-form validate-form" method="POST" action="/login">
                <? if(isset($_GET['e'])){?>
                    <div class="alert border border-danger alert-dismissible fade show text-danger" role="alert">
                        <b>usuario ou senha invalidos!</b>.
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <? }?>
					<span class="login100-form-title p-b-43">
						Acessar Sistema
					</span>					
					
					<div class="wrap-input100 validate-input" data-validate = "email é obrigatório e deve ser valido: ex@abc.xyz">
						<input class="input100" type="text" name="email">
						<span class="focus-input100"></span>
						<span class="label-input100">Email</span>
					</div>					
					
					<div class="wrap-input100 validate-input" data-validate="senha é obrigatória">
						<input class="input100" type="password" name="password">
						<span class="focus-input100"></span>
						<span class="label-input100">Senha</span>
					</div>

					<div class="flex-sb-m w-full p-t-3 p-b-32">
						<div class="contact100-form-checkbox">
							<input class="input-checkbox100" id="ckb1" type="checkbox" name="remember-me">
							<label class="label-checkbox100" for="ckb1">
								Lembrar senha
							</label>
						</div>

						<div>
							<a href="\recuperar" class="txt1">
								Esqueceu a senha?
							</a>
						</div>
					</div>
			

					<div class="container-login100-form-btn">
						<button class="login100-form-btn" type="submit">
							Login
						</button>
					</div>
				</form>

				<div class="login100-more" style="background-image: url('<?=URL_PREFIX_APP?>/Public/images/bg-01.jpg');">
				</div>
			</div>
		</div>
	</div>
	
	
    <!--===============================================================================================-->
        <script src="<?=URL_PREFIX_APP?>/Public/vendor/jquery/jquery-3.2.1.min.js"></script>
    <!--===============================================================================================-->
        <script src="<?=URL_PREFIX_APP?>/Public/vendor/animsition/js/animsition.min.js"></script>
    <!--===============================================================================================-->
        <script src="<?=URL_PREFIX_APP?>/Public/vendor/bootstrap/js/popper.js"></script>
        <script src="<?=URL_PREFIX_APP?>/Public/vendor/bootstrap/js/bootstrap.min.js"></script>
    <!--===============================================================================================-->
        <script src="<?=URL_PREFIX_APP?>/Public/vendor/select2/select2.min.js"></script>
    <!--===============================================================================================-->
        <script src="<?=URL_PREFIX_APP?>/Public/vendor/daterangepicker/moment.min.js"></script>
        <script src="<?=URL_PREFIX_APP?>/Public/vendor/daterangepicker/daterangepicker.js"></script>
    <!--===============================================================================================-->
        <script src="<?=URL_PREFIX_APP?>/Public/vendor/countdowntime/countdowntime.js"></script>
    <!--===============================================================================================-->
        <script src="<?=URL_PREFIX_APP?>/Public/js/main.js"></script>
    </body>
</html>