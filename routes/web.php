<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

require __DIR__.'/web/guest.php';

require __DIR__.'/web/auth.php';

require __DIR__.'/web/admin.php';

/**
 * Просмотр писем и оповещений в браузере
 */
$testFile = __DIR__.'/technical/test.php';

if(config('app.env') === 'local' && file_exists($testFile)) {
    require $testFile;
}
