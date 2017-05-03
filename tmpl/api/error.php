<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Entrust Survey</title>
    <link href="<?php echo Env::APP_URL ?>static/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">
    <link href="<?php echo Env::APP_URL ?>static/css/animate.css" rel="stylesheet">
    <link href="<?php echo Env::APP_URL ?>static/css/style.css" rel="stylesheet">
</head>
<body class="gray-bg">
    <div class="middle-box text-center animated fadeInDown">
        <h1>403</h1>
        <h3 class="font-bold"><?php es($data['title']); ?></h3>

        <div class="error-desc">
            <?php es($data['message']); ?>
        </div>
    </div>

    <!-- Mainly scripts -->
    <script src="<?php echo Env::APP_URL ?>static/js/jquery-2.1.1.js"></script>
    <script src="<?php echo Env::APP_URL ?>static/js/bootstrap.min.js"></script>
</body>
</html>