@ECHO OFF
setlocal DISABLEDELAYEDEXPANSION
SET BIN_TARGET=%~dp0/vendor/phpunit/phpunit/phpunit
echo where php
php "%BIN_TARGET%" %*
