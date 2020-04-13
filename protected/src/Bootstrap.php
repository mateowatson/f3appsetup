<?php
// Define root directory constant
define('ROOT_DIR', dirname(__DIR__));
// Autoload vendor classes, aka f3's classes and any others from composer
require ROOT_DIR . '/vendor/autoload.php';
// Set language to English
putenv('LC_ALL=en_EN');
setlocale(LC_ALL, 'en_EN');
// Specify location of translation tables
bindtextdomain("f3appsetup", ROOT_DIR . '/vendor/locale');
// Choose domain
textdomain("f3appsetup");
// Instantiate f3 and save the instance to a variable to continue bootstrapping
$f3 = \Base::instance();
// Configure setup global variables and routes
$f3->config(ROOT_DIR . '/setup.cfg');
$f3->config(ROOT_DIR . '/routes.cfg');
// Connect to db, utf8mb4 is for emoji support :-)
$f3->set('DB', new \DB\SQL(
    'mysql:host='.$f3->get('DB_HOST').
        ';port='.$f3->get('DB_PORT').
        ';dbname='.$f3->get('DB_NAME'),
    $f3->get('DB_USER'),
    $f3->get('DB_PASSWORD'),
    [\PDO::MYSQL_ATTR_INIT_COMMAND=>'
        SET NAMES utf8mb4;
    ']
));
// Initiate session using sessions sql table, store to global f3 variable 'CSRF',
// although this f3 variable is not currently being used in this boilerplate,
// because it is tied to the session AND the request, meaning if you open a new
// tab, your csrf token in the previous tab is obsolete. Which is annoying,
// although more secure. Instead we create a SESSION.csrf variable in the Main
// Controller class that is tied only to the session. You may want to use CSRF,
// though, if you need to tie a csrf token to each request.
new \DB\SQL\Session($f3->get('DB'), 'sessions', TRUE, NULL, 'CSRF');
// Autoload views templates
$f3->set('UI', ROOT_DIR . '/src/views/');
// Autoload everything in the src/autoload directory, using f3's autoloader
//$f3->set('AUTOLOAD', ROOT_DIR . '/src/classes/');
// Set debug level based on environment
if($f3->get('SITE_ENV') === 'development') {
    $f3->set('DEBUG',3);
} else {
    $f3->set('DEBUG',0);
}
// Set error view
$f3->set('ONERROR', function($f3){
    echo \View::instance()->render('error.php');
});
// Set is email enabled global variable
if(
    $f3->get('SMTP_HOST') &&
    $f3->get('SMTP_USERNAME') &&
    $f3->get('SMTP_PASSWORD') &&
    $f3->get('SMTP_PORT') &&
    $f3->get('USERSIGNUP') !== 'anonymous'
) {
    $f3->set('EMAIL_ENABLED', true);
} else {
    $f3->set('EMAIL_ENABLED', false);
}
// Remove trailing slash(es) of site url
$original_site_url = $f3->get('SITE_URL');
$f3->set('SITE_URL', rtrim($original_site_url, '/'));
// Sync PHP and db timezone to admin-defined global
// Credit: https://www.sitepoint.com/synchronize-php-mysql-timezone-configuration/
if($f3->get('SITE_TIMEZONE')) {
    define('TIMEZONE', $f3->get('SITE_TIMEZONE'));
} else {
    define('TIMEZONE', 'America/Chicago');
}
// date_default_timezone_set(TIMEZONE);
$tz_now = new DateTime();
$tz_mins = $tz_now->getOffset() / 60;
$tz_sgn = ($tz_mins < 0 ? -1 : 1);
$tz_mins = abs($tz_mins);
$tz_hrs = floor($tz_mins / 60);
$tz_mins -= $tz_hrs * 60;
$tz_offset = sprintf('%+d:%02d', $tz_hrs*$tz_sgn, $tz_mins);
$db = $f3->get('DB');
$db->exec("SET time_zone='$tz_offset';");
//$f3->set('view_errors', json_decode($f3->get('SESSION.view_errors')) ? : array());
// Run f3
$f3->run();