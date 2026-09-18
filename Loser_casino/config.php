<?php
/**
 * Konfigirasyon aplikasyon an.
 * Pi bon pratik: mete valè sansib yo (modpas DB, elt.) nan varyab anviwònman
 * (.env oswa konfig sèvè a), pa dirèkteman nan kòd la ki nan Git.
 */

define('DB_HOST', getenv('DB_HOST') ?: '127.0.0.1');
define('DB_NAME', getenv('DB_NAME') ?: 'loser_casino');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') ?: '');

// Fòse sesyon yo sekirize (kouki sesyon pa aksesib pa JavaScript, elt.)
ini_set('session.cookie_httponly', 1);
ini_set('session.use_strict_mode', 1);
if (!empty($_SERVER['HTTPS'])) {
    ini_set('session.cookie_secure', 1);
}

error_reporting(E_ALL);
ini_set('display_errors', getenv('APP_DEBUG') === 'true' ? '1' : '0');
