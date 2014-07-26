<?php self::block( 'common/header' ); ?>
<h1>Hello, world!</h1>
<nav><?php self::block( 'common/menu' ); ?></nav>
<table border="1">
    <thead>
        <tr>
            <th>ID</th>
            <th>Text</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ( $users as $user ): ?>
            <tr>
                <td><?php echo $user[ 'id' ]; ?></td>
                <td><?php echo $user[ 'text' ]; ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php self::block( 'common/footer' ); ?>
