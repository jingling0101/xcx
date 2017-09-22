<?php

/**
 * Created by PhpStorm.
 * User: lewis
 * Date: 2017/8/15
 * Time: 23:47
 */
namespace JLWx\Xcx;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    public function createApplication()
    {
        $app = require __DIR__.'/../../../../bootstrap/app.php';

        //$app->make(Kernel::class)->bootstrap();

        return $app;
    }

}
