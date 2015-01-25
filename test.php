<pre>
    <?php
    /**
     * Test
     */
    \Core\Engine\Test::add( function()
    {
        $cache = new \Core\Engine\Cache();
        return ($cache instanceof \Core\Engine\Cache);
    }, 'Cache init', 'Cache' );

    \Core\Engine\Test::add( function()
    {
        $cache = new \Core\Engine\Cache( array(
                    'driver' => 'memcache'
                ) );

        $cache = $cache->get_service();

        $cache->disconnect();

        try
        {
            $cache->get( 'anything' );
        }
        catch ( \Exception $ex )
        {
            return ($cache instanceof \Core\Cache\Memcache\Driver);
        }

        return false;
    }, 'Cache disconnect', 'Cache' );

    \Core\Engine\Test::add( function()
    {
        $cache = new \Core\Engine\Cache( array(
                    'driver' => 'memcache'
                ) );

        $cache = $cache->get_service();

        return ($cache->set( 'foo', 'bar' ) instanceof \Core\Cache\Memcache\Driver);
    }, 'Cache set value', 'Cache' );

    \Core\Engine\Test::add( function()
    {
        $cache = new \Core\Engine\Cache( array(
                    'driver' => 'memcache'
                ) );

        $cache = $cache->get_service();

        return ($cache->set( 'foo' ) == 'bar');
    }, 'Cache get value', 'Cache' );

    \Core\Engine\Test::add( function()
    {
        $cache = new \Core\Engine\Cache( array(
                    'driver' => 'memcache'
                ) );

        $cache = $cache->get_service();

        return ($cache->get( '404' ) == 'baz');
    }, 'Cache get value default', 'Cache' );

    \Core\Engine\Test::add( function()
    {
        $cache = new \Core\Engine\Cache( array(
                    'driver' => 'memcache'
                ) );

        $cache = $cache->get_service();

        sleep( 1 );

        return ($cache->get( 'foo' ) == null);
    }, 'Cache get value expires', 'Cache' );

    \Core\Engine\Test::add( function()
    {
        $cache = new \Core\Engine\Cache( array(
                    'driver' => 'memcache'
                ) );

        $cache = $cache->get_service();

        $cache->set( 'hello', 'world' );
        $cache->erase( 'hello' );

        return ($cache->get( 'hello' ) == null && $cache instanceof \Core\Cache\Memcache\Driver);
    }, 'Cache erase value', 'Cache' );

    var_dump( \Core\Engine\Test::run() );
    ?>
</pre>