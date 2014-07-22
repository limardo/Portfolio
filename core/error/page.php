<html lang="en" class="error">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Fatal Error</title>
        <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
          <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
        <?php include APP_PATH . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR . 'error' . DIRECTORY_SEPARATOR . 'style.php'; ?>
    </head>
    <body>
        <div class="box">
            <header>
                <h1>Fatal Error</h1>
            </header>
            <section>
                <?php echo self::$_error_output; ?>
            </section>
        </div>
    </body>
</html>