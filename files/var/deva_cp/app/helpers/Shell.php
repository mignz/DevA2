<?php
/*! DevA 2 - github.com/mignz/DevA2 */

namespace DevA2\Helpers;

/**
 * DevA2\Helpers\Shell
 *
 * Shell functions
 */
class Shell
{
    /**
     * Execute shell command and get output
     *
     * @param string $cmd
     * @return string
     */
    public static function getShellOutput(string $cmd): string
    {
        return \shell_exec($cmd);
    }
    
    /**
     * Execute shell command
     *
     * @param string $cmd
     * @return void
     */
    public static function runShell(string $cmd): void
    {
        \exec($cmd);
    }
    
    /**
     * Tar.gz directory content or file
     *
     * @param string $source
     * @param string $destination
     * @return void
     */
    public static function tarDir(string $source, string $destination): void
    {
        $ext = \explode('.', $destination);
        $ext = \end($ext);
        $gzip = $ext == 'gz' ? 'z' : null;
        
        self::runShell("tar -{$gzip}cf {$destination} {$source}");
    }
    
    /**
     * Dump all MySQL databases
     *
     * @param string $destination
     * @return void
     */
    public static function dumpDb($destination): void
    {
        self::runShell("mysqldump -u root --all-databases --skip-lock-tables > {$destination}");
    }
    
    /**
     * Import MySQL databases dump
     *
     * @param string $source
     * @return void
     */
    public static function importDbDump($source): void
    {
        self::runShell("mysqldump -u root < {$source}");
    }
    
    /**
     * Run a shell script and show output
     *
     * @param string $file
     * @return void
     */
    public static function runPhalconUpgrade(): void
    {
        \set_time_limit(0);
        \putenv('PATH=/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin');
        
        \passthru(
            'cd /root && ' .
            'curl -sLO https://github.com/phalcon/cphalcon/archive/master.tar.gz && ' .
            'tar xzf /root/master.tar.gz && ' .
            'cd /root/cphalcon-master/build && ' .
            'sh install 2>&1 && ' .
            'rm -rf /root/cphalcon-master /root/master.tar.gz'
        );
    }
    
    /**
     * Request supervisor to restart a service
     *
     * @param string $service
     * @return void
     */
    public static function restartService(string $service): void
    {
        switch ($service) {
            case 'nginx':
                $proc = '[n]ginx';
                if (self::configIsInvalid('nginx')) {
                    return;
                }
                break;
            case 'php-fpm':
                $proc = '[p]hp';
                if (self::configIsInvalid('php-fpm')) {
                    return;
                }
                break;
            case 'mysql':
                $proc = '[m]ysql';
                break;
            default:
                return;
        }
        
        self::runShell(
            'nohup sh -c "sleep 2; ' .
            'kill $(ps aux | grep \'' . $proc . '\' | awk \'{print $1}\'); ' .
            'supervisorctl restart ' . $service . '" > /dev/null 2>&1 &'
        );
        
        echo 1;
    }
    
    /**
     * Test service configuration files
     *
     * @param string $service
     * @return mixed
     */
    public static function configIsInvalid(string $service)
    {
        \exec($service . ' -t 2>&1', $output, $status);
        
        if ((int) $status > 0) {
            return $output ? \implode('<br>', $output) : 'No error output.';
        }
        
        return false;
    }
    
    /**
     * Get SSMTP config values
     *
     * @return array
     */
    public static function getSSMTPConf(): array
    {
        $conf = \file_get_contents('/etc/ssmtp/ssmtp.conf');
        
        preg_match('/mailhub=(.*?)\n/', $conf, $server);
        preg_match('/AuthUser=(.*?)\n/', $conf, $user);
        preg_match('/AuthPass=(.*?)\n/', $conf, $pass);
        
        $values = [
            'server' => explode(':', $server[1])[0],
            'port' => explode(':', $server[1])[1],
            'user' => $user[1],
            'pass' => $pass[1]
        ];
        
        return $values;
    }
    
    /**
     * Update SSMTP config values
     *
     * @param string $server
     * @param int $port
     * @param string $user
     * @param string $pass
     * @return void
     */
    public static function updateSSMTPConf(string $server, int $port, string $user, string $pass): void
    {
        $conf = "root={$user}\nmailhub={$server}:{$port}\n" .
            "hostname=localhost\nAuthUser={$user}\nAuthPass={$pass}\n";
        
        \file_put_contents('/etc/ssmtp/ssmtp.conf', $conf);
    }
}
