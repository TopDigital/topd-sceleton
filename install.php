<?php

$currentDir = __DIR__;
$targetDir = $_SERVER['PWD'];

$ignoreList = [
    pathinfo(__FILE__, PATHINFO_BASENAME),
    '.git',
    '.idea',
    '.gitignore',
    'LICENSE',
    'README.md',
];

$rewrite = false !== in_array('r', $_SERVER['argv']);
$quiet = false !== in_array('q', $_SERVER['argv']);

print PHP_EOL;
print "Setup from {$currentDir}" . PHP_EOL;
print "Setup to   {$targetDir}" . PHP_EOL;

if (false === in_array('y', $_SERVER['argv'])) {
    $prompt = readline("Continue? (Y/N): ");
    if (strtolower($prompt) != 'y') {
        print "Exit." . PHP_EOL . PHP_EOL;
        exit();
    }
}



class Installer
{
    protected static $forceRewrite = false;
    protected static $quiet = false;

    protected static $ignoreList = [];

    protected static function cloneItem($sourcePath, $targetPath)
    {
        $targetPath = $targetPath . DIRECTORY_SEPARATOR . pathinfo($sourcePath, PATHINFO_BASENAME);

        if (is_file($sourcePath)) {
            static::cloneFile($sourcePath, $targetPath);
        } else {
            static::cloneDir($sourcePath, $targetPath);
        }
    }

    protected static function cloneFile($sourcePath, $targetPath)
    {
        static::$quiet || print PHP_EOL . "{$sourcePath} -> {$targetPath}";
        if (!static::$forceRewrite && file_exists($targetPath)) {
            static::$quiet || print "\33[30;1;33m already exist\33[0m";
        } else {
            if (copy($sourcePath, $targetPath)) {
                static::$quiet || print "\33[30;1;32m copied\33[0m";
            } else {
                static::$quiet || print "\33[30;1;31m copy failed\33[0m";
            }
        }
    }

    protected static function cloneDir($sourcePath, $targetPath)
    {
        static::$quiet || print PHP_EOL . "{$sourcePath} -> {$targetPath}";
        if (is_dir($targetPath)) {
            static::$quiet || print "\33[30;1;33m already exist\33[0m";
            static::cloneDirs($sourcePath, $targetPath);
        } else {
            if (mkdir($targetPath, 0755, true)) {
                static::$quiet || print "\33[30;1;32m created\33[0m";
                static::cloneDirs($sourcePath, $targetPath);
            } else {
                static::$quiet || print "\33[30;1;31m create failed\33[0m";
            }
        }
    }

    protected static function cloneDirs($dirsPath, $targetDir)
    {
        $dirResource = opendir($dirsPath);
        if (!$dirResource) {
            static::$quiet || print "\33[30;1;31mCan't read dir\33[0m" . PHP_EOL;
            return;
        }

        while ($fileName = readdir($dirResource)) {
            if ($fileName == '.' || $fileName == '..') {
                continue;
            }
            if (false !== in_array($fileName, static::$ignoreList)) {
                continue;
            }
            static::cloneItem($dirsPath . DIRECTORY_SEPARATOR . $fileName, $targetDir);
        }

        closedir($dirResource);
    }

    public static function run($sourseDir, $targetDir, $ignoreList = [], $forceRewrite = false, $quiet = false)
    {
        static::$ignoreList = $ignoreList;
        static::$forceRewrite = $forceRewrite;
        static::$quiet = $quiet;
        static::cloneDirs($sourseDir, $targetDir);
        echo PHP_EOL;
    }
}

\Installer::run($currentDir, $targetDir, $ignoreList, $rewrite, $quiet);
