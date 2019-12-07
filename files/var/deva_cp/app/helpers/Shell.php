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
     * @return mixed
     */
    public static function getShellOutput(string $cmd)
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
     * Extract tar.gz file
     *
     * @param string $source
     * @param string $destination
     * @return void
     */
    public static function extractTar(string $source, string $destination): void
    {
        $ext = \explode('.', $source);
        $ext = \end($ext);
        $gzip = $ext == 'gz' ? 'z' : null;
        
        self::runShell("tar -x{$gzip}f {$source} -C {$destination}");
    }
    
    /**
     * Dump all MySQL databases
     *
     * @param string $destination
     * @return void
     */
    public static function dumpDb($destination): void
    {
        self::runShell(
            'candidates=$(echo "show databases" | mysql -u root' .
            ' | grep -Ev "^(Database|mysql|performance_schema|information_schema|sys)$") && ' .
            "mysqldump -u root --databases \$candidates > {$destination}"
        );
    }
    
    /**
     * Import MySQL databases dump
     *
     * @param string $source
     * @return void
     */
    public static function importDbDump($source): void
    {
        self::runShell("mysql -u root < {$source}");
    }
    
    /**
     * Run a shell script and show output
     *
     * @param string $version
     * @return void
     */
    public static function runPhalconUpgrade($version): void
    {
        \set_time_limit(0);
        \putenv('PATH=/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin');
        
        \passthru(
            'cd /root && ' .
            'curl -sLO https://github.com/phalcon/cphalcon/archive/' . trim($version) . '.tar.gz && ' .
            'tar xzf /root/' . trim($version) . '.tar.gz && ' .
            'cd /root/cphalcon-' . substr(trim($version), 1) . '/build && ' .
            'sh install 2>&1 && ' .
            'rm -rf /root/cphalcon-' . substr(trim($version), 1) . ' /root/' . trim($version) . '.tar.gz'
        );
    }
    
    /**
     * Check if a service is running
     *
     * @param string $service
     * @return boolean
     */
    public static function serviceRunning(string $service): bool
    {
        $result = self::getShellOutput(
            "ps aux | grep {$service} | grep -v 'grep' | grep -v 's6' > /dev/null && echo $?"
        );
        
        return $result !== null and (int) $result === 0;
    }
    
    /**
     * Use s6 to restart a service
     *
     * @param string $service
     * @param bool $output
     * @return void
     */
    public static function restartService(string $service, $output = true): void
    {
        self::runShell("nohup sh -c \"sleep 2; s6-svc -k /etc/s6/{$service}\" > /dev/null 2>&1 &");
        
        if (true === $output) {
            echo 1;
        }
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
        
        \preg_match('/mailhub=(.*?)\n/', $conf, $server);
        \preg_match('/AuthUser=(.*?)\n/', $conf, $user);
        \preg_match('/AuthPass=(.*?)\n/', $conf, $pass);
        
        $values = [
            'server' => \explode(':', $server[1])[0],
            'port' => \explode(':', $server[1])[1],
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
            "hostname=localhost\nAuthUser={$user}\nAuthPass={$pass}\nFromLineOverride=YES\n";
        
        if ($port > 25) {
            $conf .= "UseSTARTTLS=YES\n";
        }
        
        \file_put_contents('/etc/ssmtp/ssmtp.conf', $conf);
    }
    
    /**
     * Update PHP timezone
     *
     * @param string $timezone
     * @return void
     */
    public static function updateTimezone(string $timezone): void
    {
        self::runShell("sed -i 's%date.timezone.*%date.timezone = \"{$timezone}\"%' /etc/php7/php.ini");
        self::restartService('php-fpm', false);
    }
    
    /**
     * Create a certificate with SAN for a domain
     *
     * @param string $domain
     * @return void
     */
    public static function createCertificate(string $domain): void
    {
        echo self::runShell(
            "SAN=DNS:{$domain},DNS:localhost openssl req -newkey rsa:2048 -x509 -nodes " .
            "-keyout /etc/nginx/deva/ssl/{$domain}.key -new " .
            "-out /etc/nginx/deva/ssl/{$domain}.crt -subj /CN={$domain} " .
            '-extensions san_env -config /etc/nginx/deva/ssl/san.cnf -sha256 -days 3650'
        );
    }
    
    /**
     * Remove certificate files for a domain
     *
     * @param string $domain
     * @return void
     */
    public static function deleteCertificate(string $domain): void
    {
        if (\file_exists('/etc/nginx/deva/ssl/' . $domain . '.crt')) {
            \unlink('/etc/nginx/deva/ssl/' . $domain . '.crt');
        }
        if (\file_exists('/etc/nginx/deva/ssl/' . $domain . '.key')) {
            \unlink('/etc/nginx/deva/ssl/' . $domain . '.key');
        }
    }
}
