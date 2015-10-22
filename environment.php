<?php

// ENVIRONMENT CONSTANTS
$PATH = str_replace("index.php", "", $_SERVER['PHP_SELF']);
$PATH_DATA = __DIR__ . "/";

$config["PATH_DATA"] = $PATH_DATA;
$config["DOCUMENT_ROOT"] = $_SERVER["DOCUMENT_ROOT"];
$config["SERVERNAME"] = $_SERVER["HTTP_HOST"];

define("SERVERNAME", $config["SERVERNAME"]);
define("PATH_DATA" , $config["PATH_DATA"]);
define("DOCUMENT_ROOT", $config["DOCUMENT_ROOT"]);
define("APP_PATH", $PATH_DATA);

define("TEMPLATE_PATH", APP_PATH . "templates/");
define("VIEWS_PATH", APP_PATH . "views/");
define("TRANSLATE_PATH", APP_PATH . "locale/");
define("CACHE_PATH", APP_PATH . "cache/");

// custom applications/interface
define("CUSTOM_TEMPLATE_PATH", TEMPLATE_PATH . "custom/");

// urls
define("SEARCH_URL",  "http://" . $_SERVER['HTTP_HOST'] . $PATH);
define("STATIC_URL",  SEARCH_URL . "static/");

// CONFIGURATION
$config = parse_ini_file($PATH_DATA . 'config/config.php', true);
$lang = $config['default_lang'];

$DEFAULT_PARAMS = array();
$DEFAULT_PARAMS['lang'] = $lang;

// log's configuration
$logDir = ( isset( $config->log_dir ) ? $config->log_dir : "logs/");
define('LOG_FILE',"log" . date('Ymd') . "_search.txt");
define('LOG_DIR', $logDir);

// FRAMEWORK
// Initiating Silex framework
require_once 'lib/vendor/autoload.php';
$app = new Silex\Application();

// iniciando o twig, buscando templates em /template
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => TEMPLATE_PATH,
));

// if isn't in debug ambient, create de cache dir and set to be cacheable
if (!DEBUG) {
    if(!is_dir(CACHE_PATH)) {
        if(!mkdir(CACHE_PATH)) {
            die("ERROR: can't create cache's directory.");
        }
    }
    $app['twig.options'] = array('strict_variables' => false, 'cache' => CACHE_PATH);

} else {
    $app['twig.options'] = array('strict_variables' => false);
}


// PREPARING THE ENVIRONMENT
// requiring custom functions
require_once "lib/functions.php";

// registering sessions
use Silex\Provider\SessionServiceProvider;
$app->register(new SessionServiceProvider, array(
    'session.storage.save_path' => '/tmp/sessions/metasearch',
    'session.storage.options' => array(
        'name' => 'iahx',
        'cookie_path' => $PATH,
        'cookie_domain' => '.' . SERVERNAME,
        'cookie_lifetime' => 604800 * 4,  // 4 weeks
    ),

));

$app['twig']->addFunction('custom_template', new Twig_Function_Function('custom_template'));
$app['twig']->addFunction('occ', new Twig_Function_Function('occ'));
$app['twig']->addFunction('translate', new Twig_Function_Function('translate'));
$app['twig']->addFunction('has_translation', new Twig_Function_Function('has_translation'));
$app['twig']->addFilter('substring_before', new Twig_Filter_Function('filter_substring_before'));
$app['twig']->addFilter('substring_after', new Twig_Filter_Function('filter_substring_after'));
$app['twig']->addFilter('contains', new Twig_Filter_Function('filter_contains'));
$app['twig']->addFilter('starts_with', new Twig_Filter_Function('filter_starts_with'));
$app['twig']->addFilter('truncate', new Twig_Filter_Function('filter_truncate'));
$app['twig']->addFilter('slugify', new Twig_Filter_Function('filter_slugify'));
$app['twig']->addFilter('subfield', new Twig_Filter_Function('filter_subfield'));

?>
