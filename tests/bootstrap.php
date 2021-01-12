<?php
/** @todo why do i need to create this bootstrap file whereas i thought that on branch 4.* it was not
 * required to read natively the .env.test* file => check if it worked before or not
 */
use Symfony\Component\Dotenv\Dotenv;

require_once __DIR__ . '/../vendor/autoload.php';

if (false === \class_exists(Dotenv::class)) {
    throw new \RuntimeException('You need to install "symfony/dotenv" as a Composer dependency to load variables from a .env.test file.');
}

$dotEnv = new Dotenv();
$dotEnv
    ->usePutenv(true)
    ->loadEnv(__DIR__ . '/../.env', 'APP_ENV', getenv('APP_ENV') ? : 'test');

if (file_exists(__DIR__ . '/../.env.test')) {
    $dotEnv->overload(__DIR__ . '/../.env.test');
}

if (file_exists(__DIR__ . '/../.env.test.local')) {
    $dotEnv->overload(__DIR__ . '/../.env.test.local');
}
