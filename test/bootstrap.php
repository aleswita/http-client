<?php

error_reporting(E_ALL);
ini_set('memory_limit', '1024M');
define('AX_SYSTEM_DIR', dirname(dirname(realpath(__FILE__))));
define('AX_APP_PATH', AX_SYSTEM_DIR);

/**
 * Use PSR-0 autoloader for required test libs
 */
require AX_SYSTEM_DIR . '/test/SplClassLoader.php';
(new SplClassLoader('Artax', AX_SYSTEM_DIR . '/src'))->register();

require __DIR__ . '/MagicTestGetTrait.php';


/*
 * --------------------------------------------------------------------
 * DEFAULT ERROR HANDLER
 * --------------------------------------------------------------------
 */
 
set_error_handler(function($errno, $errstr, $errfile, $errline) {
  $levels = [
    E_WARNING           => 'Warning',
    E_NOTICE            => 'Notice',
    E_USER_ERROR        => 'User Error',
    E_USER_WARNING      => 'User Warning',
    E_USER_NOTICE       => 'User Notice',
    E_STRICT            => 'Runtime Notice',
    E_RECOVERABLE_ERROR => 'Catchable Fatal Error',
    E_DEPRECATED        => 'Deprecated Notice',
    E_USER_DEPRECATED   => 'User Deprecated Notice'
  ];
  $msg = $levels[$errno] . ": $errstr in $errfile on line $errline";
  throw new \ErrorException($msg);
});

/*
 * --------------------------------------------------------------------
 * LOAD VIRTUAL FILESYSTEM
 * --------------------------------------------------------------------
 */

require 'vfsStream/vfsStream.php';
$structure = [
  'conf' => [
    'config.php' => '<?php $cfg=["debug"=>1]; ?>',
    'config_no_debug.php' => '<?php $cfg=["debug"=>FALSE]; ?>',
    'invalid_config.php'=>'<?php $cfg = "not an array"; ?>'
  ],
  'controllers' => ['Level1' => ['Level2'=>[]]],
  'src'         => ['Class.php' => '<?php ?>'],
  'views'       => [
    'my_template.php'  => 'Template value: <?php echo $myVar; ?>',
    'bad_template.php' => 'Template value: <?php echo $badVar; ?>'
  ]
];
vfsStreamWrapper::register();
vfsStream::setup('myapp', NULL, $structure);

