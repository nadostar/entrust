<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Entrust Survey Login</title>

    <link href="<?php echo Env::APP_URL ?>static/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo Env::APP_URL ?>static/font-awesome/css/font-awesome.css" rel="stylesheet">

    <link href="<?php echo Env::APP_URL ?>static/css/animate.css" rel="stylesheet">
    <link href="<?php echo Env::APP_URL ?>static/css/style.css" rel="stylesheet">
    <link href="<?php echo Env::APP_URL ?>static/css/plugins/toastr/toastr.min.css" rel="stylesheet">
</head>

<body class="gray-bg">
    <div class="middle-box text-center loginscreen animated fadeInDown">
        <div>
            <h2>Entrust Survey </h2>
            <p></p>
            
            <?php start_form_tag('support/login_exec/', 'post', 'login-form'); ?>
                <div class="form-group">
                    <input type="email" name="email" class="form-control" placeholder="username@email.com" required="">
                </div>
                <div class="form-group">
                    <input type="password" name="password" class="form-control" placeholder="Password" required="">
                </div>
                <button type="submit" class="btn btn-primary block full-width m-b"> Login</button>
            <?php end_form_tag(); ?>
            <p class="m-t"><strong>Copyright</strong> EntrustSurvey &copy; 2017 </p>
        </div>
    </div>

    <!-- Mainly scripts -->
    <script src="<?php echo Env::APP_URL ?>static/js/jquery-2.1.1.js"></script>
    <script src="<?php echo Env::APP_URL ?>static/js/bootstrap.min.js"></script>
    <script src="<?php echo Env::APP_URL ?>static/js/plugins/toastr/toastr.min.js"></script>

    <script type="text/javascript">
        var error = '<?php es($error); ?>';
        if(error) {
            toastr.options = {
                closeButton: true,
                progressBar: true,
                showMethod: 'slideDown',
                timeOut: 4000
            };
            toastr.error(error);
        }

    </script>
</body>
</html>
