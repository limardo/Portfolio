<ul>
    <?php foreach ( $users as $user ): ?>
        <li>
            <a href="javascript:void(0)"><?php echo $user[ 'text' ]; ?></a>
        </li>
    <?php endforeach; ?>
</ul>