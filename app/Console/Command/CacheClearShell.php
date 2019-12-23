<?php

/**
 * Class CacheClearShell
 *
 * Usage: app/Console/cake cache_clear
 */
class CacheClearShell extends AppShell {
    function main(){
        $config_list = Cache::configured();
        foreach ($config_list as $value) {
            Cache::clear(false, $value);
        }
        clearCache();
    }
}
