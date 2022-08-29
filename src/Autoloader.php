<?php

namespace Jasmine\helper;

class Autoloader
{
    protected static $mappings = [];
    protected static $registered = false;

    /**
     * @param array $prefixArr
     * itwri 2020/7/30 14:18
     */
    protected static function extend(array $prefixArr): bool
    {
        $count = 0;
        foreach ($prefixArr as $prefix => $baseDirectory) {
            self::$mappings[$prefix] = $baseDirectory;
            $count++;
        }
        return $count > 0;
    }

    /**
     * Registers the autoloader class with the PHP SPL autoloader.
     * Prepend the autoloader on the stack instead of appending it.
     * @param $prefix
     * @param $baseDir
     * @return bool
     * @author zzp
     * @date 2022/4/15
     */
    public static function register($prefix, $baseDir = null): bool
    {
        if(self::$registered == false){
            self::$registered = spl_autoload_register(implode('::', [__CLASS__, 'autoload']), true, true);
        }
        if(is_array($prefix)){
            return self::extend($prefix);
        }
        return self::extend([$prefix=>$baseDir]);
    }

    /**
     * Loads a class from a file using its fully qualified name.
     *
     * @param mixed $className Fully qualified name of a class.
     */
    public static function autoload($className)
    {
        $className = $className[0] == '\\' ? substr($className, 1) : $className;
        foreach (static::$mappings as $prefix => $baseDirectory) {
            $prefix = '\\' == $prefix ? '' : $prefix;
            $parts = explode('\\', $className);
            if (!empty($prefix) && 0 === strpos($className, $prefix)) {
                $parts = explode('\\', substr($className, strlen($prefix)));
            }
            $filePath = $baseDirectory . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $parts) . '.php';
            if (is_file($filePath)) {
                require $filePath;
                break;
            }
        }
    }
}
