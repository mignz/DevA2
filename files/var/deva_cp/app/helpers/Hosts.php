<?php
/*! DevA 2 - github.com/mignz/DevA2 */

namespace DevA2\Helpers;

/**
 * DevA2\Helpers\Hosts
 *
 * Hosts functions
 */
class Hosts
{
    public const WWWPATH = '/var/www';
    
    public const CONF_BACKUP = 1;
    public const CONF_RESTORE = 2;
    public const CONF_DELETE = 3;
    
    public const WITH_TLD = 1;
    public const WITHOUT_TLD = 0;
    
    /**
     * List virtual host files with their correct domains
     *
     * @return array
     */
    public static function listVirtualHosts(): array
    {
        $hostFiles = \glob('/etc/nginx/deva/vhosts/*.conf');
        \usort($hostFiles, function ($a, $b) {
            $a = \str_replace('___', null, $a);
            $b = \str_replace('___', null, $b);
            return \strcasecmp($a, $b);
        });
        
        $hosts = [];
        
        foreach ($hostFiles as $hostFile) {
            $hosts[\basename($hostFile)] = [
                'domain' => self::getHostDomain(\substr(\basename($hostFile), 0, -5)),
                'default' => \substr(\basename($hostFile), 0, 3) == '___' ? true : false
            ];
        }
        
        return $hosts;
    }
    
    /**
     * Create virtual host config
     *
     * @param string $domain
     * @param string $config
     * @param string $path
     * @return string
     */
    public static function createVirtualHost(string $domain, string $config, string $path): void
    {
        switch ($config) {
            case 'html':
                $config = 'include /etc/nginx/deva/vhost_html.conf;';
                break;
            case 'php':
                $config = 'include /etc/nginx/deva/vhost_php.conf;';
                break;
            case 'phalcon':
                $config = 'include /etc/nginx/deva/vhost_phalcon.conf;';
                break;
            default:
                $config = "\n# === CUSTOM CONFIG\n{$config}\n# === /CUSTOM CONFIG";
        }
        
        $file = "# GENERATED BY DEVA2, DO NOT MODIFY\n\nserver {\n    include /etc/nginx/deva/common.conf;\n    " .
            "server_name {$domain};\n    root " . self::WWWPATH . "/{$path};\n    {$config}\n}\n";
        \file_put_contents('/etc/nginx/deva/vhosts/' . $domain . '.conf', $file);
    }
    
    /**
     * Delete virtual host config
     *
     * @param string $domain
     * @param string $config
     * @param string $path
     * @return string
     */
    public static function deleteVirtualHost(string $domain): void
    {
        \unlink('/etc/nginx/deva/vhosts/' . $domain . '.conf');
    }
    
    /**
     * Default a virtual host
     *
     * @param string $domain
     * @return void
     */
    public static function defaultVirtualHost(string $domain): void
    {
        if (\substr($domain, 0, 3) != '___' and \file_exists('/etc/nginx/deva/vhosts/' . $domain . '.conf')) {
            $currentDefault = \glob('/etc/nginx/deva/vhosts/___*');
            foreach ($currentDefault as $config) {
                \rename($config, \str_replace('___', null, $config));
            }
            \rename('/etc/nginx/deva/vhosts/' . $domain . '.conf', '/etc/nginx/deva/vhosts/___' . $domain . '.conf');
        }
    }
    
    /**
     * Get host relative path
     *
     * @param string $domain
     * @return string
     */
    public static function getHostPath(string $domain): string
    {
        $conf = self::getConfig($domain);
        \preg_match('/root\s(.*?);/', $conf, $matches);
        return \str_replace([self::WWWPATH . '/', self::WWWPATH], null, $matches[1]);
    }
    
    /**
     * Backup manager for a virtual host config file
     *
     * @param string $domain
     * @param int $action
     * @return void
     */
    public static function backupHost(string $domain, int $action): void
    {
        switch ($action) {
            case 1:
                if (\file_exists('/etc/nginx/deva/vhosts/' . $domain . '.conf')) {
                    \rename(
                        '/etc/nginx/deva/vhosts/' . $domain . '.conf',
                        '/etc/nginx/deva/vhosts/' . $domain . '.conf.backup'
                    );
                } else {
                    \rename(
                        '/etc/nginx/deva/vhosts/___' . $domain . '.conf',
                        '/etc/nginx/deva/vhosts/___' . $domain . '.conf.backup'
                    );
                }
                break;
            case 2:
                if (\file_exists('/etc/nginx/deva/vhosts/' . $domain . '.conf.backup')) {
                    \rename(
                        '/etc/nginx/deva/vhosts/' . $domain . '.conf.backup',
                        '/etc/nginx/deva/vhosts/' . $domain . '.conf'
                    );
                } else {
                    \rename(
                        '/etc/nginx/deva/vhosts/___' . $domain . '.conf.backup',
                        '/etc/nginx/deva/vhosts/___' . $domain . '.conf'
                    );
                }
                break;
            case 3:
                if (\file_exists('/etc/nginx/deva/vhosts/' . $domain . '.conf.backup')) {
                    \unlink('/etc/nginx/deva/vhosts/' . $domain . '.conf.backup');
                } else {
                    \unlink('/etc/nginx/deva/vhosts/___' . $domain . '.conf.backup');
                }
        }
    }
    
    /**
     * Create specified directory if it doesn't exist
     *
     * @param string $path
     * @return void
     */
    public static function createHostRootDir(string $path): void
    {
        if (!\file_exists(self::WWWPATH . '/' . $path)) {
            \mkdir(self::WWWPATH . '/' . $path, 0777, true);
        }
    }
    
    /**
     * Add/remove TLD on domain
     *
     * @param string $domain
     * @param int $action
     * @return void
     */
    public static function tld(string $domain, int $action): string
    {
        if (\substr($domain, -5) == '.test') {
            $domain = \substr($domain, 0, -5);
        }
        
        return $action === 1 ? $domain . '.test' : $domain;
    }
    
    /**
     * Extract values from virtual host configuration file
     *
     * @param string $domain
     * @return array
     */
    public static function extractConfig(string $domain): array
    {
        $config = [];
        $file = self::getConfig($domain);
        if (\strpos($file, 'include /etc/nginx/deva/vhost_html.conf;') !== false) {
            $config['app'] = 'html';
        } elseif (\strpos($file, 'include /etc/nginx/deva/vhost_php.conf;') !== false) {
            $config['app'] = 'php';
        } elseif (\strpos($file, 'include /etc/nginx/deva/vhost_phalcon.conf;') !== false) {
            $config['app'] = 'phalcon';
        } elseif (\strpos($file, '# === CUSTOM CONFIG') !== false) {
            $config['app'] = 'custom';
            \preg_match('/#\s===\sCUSTOM\sCONFIG(.*?)#\s===\s\/CUSTOM\sCONFIG/s', $file, $matches);
            $config['custom'] = \trim($matches[1]);
        }
        
        return $config;
    }
    
    /**
     * Obtain the domain name from a virtual host configuration file
     *
     * @param string $domain
     * @return string
     */
    private static function getHostDomain(string $domain): string
    {
        $config = self::getConfig($domain);
        \preg_match('/server_name\s(.*?);/', $config, $matches);
        return $matches[1] == '_' ? 'localhost' : $matches[1];
    }
    
    /**
     * Get the configuration file path for a virtual host
     *
     * @param string $domain
     * @return string
     */
    public static function getConfigPath(string $domain): string
    {
        if (\file_exists('/etc/nginx/deva/vhosts/' . $domain . '.conf')) {
            return '/etc/nginx/deva/vhosts/' . $domain . '.conf';
        } else {
            return '/etc/nginx/deva/vhosts/___' . $domain . '.conf';
        }
    }
    
    /**
     * Get the configuration file for a virtual host
     *
     * @param string $domain
     * @return string
     */
    private static function getConfig(string $domain): string
    {
        return \file_get_contents(self::getConfigPath($domain));
    }
}
