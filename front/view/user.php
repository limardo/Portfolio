<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Template</title>
        <?php $this->head(); ?>
    </head>
    <body>
        <h1>Hello2, world!</h1>
        <p><?php $this->block( 'menu' ); ?></p>
        <?php $this->footer(); ?>
    </body>
</html>