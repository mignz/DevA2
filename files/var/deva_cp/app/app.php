<?php
/*! DevA 2 - github.com/mignz/DevA2 */

namespace DevA2;

use DevA2\Helpers\Hosts;
use DevA2\Helpers\Shell;

/**
 * Main page
 */
$app->get(
    '/',
    function () use ($app) {
        $mysqlStatus = \trim(
            Shell::getShellOutput('supervisorctl status mysql | sed \'s/\s\+/ /g\' | cut -d \' \' -f2')
        );
        $redisStatus = \trim(
            Shell::getShellOutput('supervisorctl status redis | sed \'s/\s\+/ /g\' | cut -d \' \' -f2')
        );
        echo $app['view']->render('index', [
            'ver_deva2' => \trim(\file_get_contents('/etc/deva_version')),
            'ver_alpine' => \trim(\file_get_contents('/etc/alpine-release')),
            'ver_nginx' => Shell::getShellOutput('nginx -v 2>&1 | cut -d/ -f2'),
            'ver_php' => Shell::getShellOutput('php -r "echo phpversion();"'),
            'ver_db' => Shell::getShellOutput('mysql -V | awk \'{print $5}\' | cut -d- -f1'),
            'ver_phalcon' => Shell::getShellOutput('php -r "echo Phalcon\Version::get();"'),
            'sql_status' => $mysqlStatus == 'RUNNING' ? 'RUNNING' : 'STOPPED',
            'sql_badge' => $mysqlStatus == 'RUNNING' ? 'success' : 'danger',
            'redis_status' => $redisStatus == 'RUNNING' ? 'RUNNING' : 'STOPPED',
            'redis_badge' => $redisStatus == 'RUNNING' ? 'success' : 'danger',
            'smtp' => Shell::getSSMTPConf(),
            'hosts' => Hosts::listVirtualHosts(),
            'get' => $app['request']->getQuery()
        ]);
    }
);

/**
 * Show PHPInfo
 */
$app->get(
    '/phpinfo',
    function () use ($app) {
        \phpinfo();
    }
);

/**
 * Get latest PHP versions
 */
$app->get(
    '/version/php',
    function () use ($app) {
        header('Content-type: application/json; charset=utf-8');
        echo \file_get_contents('https://secure.php.net/releases/index.php?json');
    }
);

/**
 * Upgrade phalcon page
 */
$app->get(
    '/upgrade',
    function () use ($app) {
        echo $app['view']->render('upgrade');
    }
);

/**
 * Upgrade phalcon, ajax call
 */
$app->get(
    '/upgrade/sh',
    function () use ($app) {
        Shell::runPhalconUpgrade();
    }
);

/**
 * Restart service
 */
$app->get(
    '/reload/{service}',
    function ($service) use ($app) {
        Shell::restartService($service);
    }
);

/**
 * Update SMTP configuration
 */
$app->post(
    '/smtp',
    function () use ($app) {
        Shell::updateSSMTPConf(
            $app['request']->getPost('server'),
            $app['request']->getPost('port'),
            $app['request']->getPost('user'),
            $app['request']->getPost('pass')
        );
        echo $app['view']->render('smtp');
    }
);

/**
 * Test PHP Mailer
 */
$app->get(
    '/test/{email}',
    function ($email) use ($app) {
        mail($email, 'DevA2 Mail Test', 'It works! You can now test your email PHP scripts before production.');
        echo $app['view']->render('test', [
            'email' => $email
        ]);
    }
);

/**
 * Add Virtual Host
 */
$app->get(
    '/add',
    function () use ($app) {
        echo $app['view']->render('add');
    }
);
$app->post(
    '/add',
    function () use ($app) {
        $domain = \preg_replace('/\.+/', '.', \trim($app['request']->getPost('domainname')));
        $domain = Hosts::tld($domain, Hosts::WITH_TLD);
        $config = $app['request']->getPost('websiteconfig');
        $custom = \trim($app['request']->getPost('customconfig'));
        $path = \trim(\trim($app['request']->getPost('websitepath')), '/.');
        
        try {
            if (\in_array($domain, ['cp.test'])) {
                throw new \Exception('The virtual host "' . $domain . '" is invalid!');
            }
            if (\file_exists('/etc/nginx/deva/vhosts/' . $domain . '.conf')) {
                throw new \Exception('The virtual host "' . $domain . '" already exists!');
            }
            Hosts::createHostRootDir($path);
            if (!\is_dir(Hosts::WWWPATH . '/' . $path)) {
                throw new \Exception('The specified path "' . Hosts::WWWPATH . '/' . $path . '" is not a directory!');
            }
            
            Hosts::createVirtualHost($domain, $config == 'custom' ? $custom : $config, $path);
            if ($output = Shell::configIsInvalid('nginx')) {
                Hosts::deleteVirtualHost($domain);
                throw new \Exception('Nginx configuration test failed!|' . $output);
            }
            if ($app['request']->hasPost('default')) {
                Hosts::defaultVirtualHost($domain);
            }
            
            Shell::restartService('nginx');
            \header('Location: /?h=' . $domain);
        } catch (\Exception $e) {
            $msg = \explode('|', $e->getMessage());
            echo $app['view']->render('add', [
                'error' => $msg[0],
                'error_output' => isset($msg[1]) ? $msg[1] : false,
                'post' => $app['request']->getPost()
            ]);
        }
    }
);

/**
 * Edit Virtual Host
 */
$app->get(
    '/edit/{domain}',
    function ($domain) use ($app) {
        if (\in_array($domain, ['cp.test', 'localhost'])) {
            \header('Location: /');
        }
        if (!\file_exists('/etc/nginx/deva/vhosts/' . $domain . '.conf')
            and !\file_exists('/etc/nginx/deva/vhosts/___' . $domain . '.conf')) {
            \header('Location: /');
        }
        $hostField = Hosts::tld($domain, Hosts::WITHOUT_TLD);
        $config = Hosts::extractConfig($domain);
        
        echo $app['view']->render('edit', [
            'domain' => $domain,
            'hostfield' => $hostField,
            'config' => isset($config['app']) ? $config['app'] : null,
            'custom' => isset($config['custom']) ? $config['custom'] : null,
            'path' => Hosts::getHostPath($domain)
        ]);
    }
);
$app->post(
    '/edit/{domain}',
    function ($domain) use ($app) {
        $configFile = Hosts::getConfigPath($domain);
        if (\in_array($domain, ['cp.test', 'localhost'])) {
            \header('Location: /');
        }
        if (!\file_exists($configFile)) {
            \header('Location: /');
        }
        $newDomain = \preg_replace('/\.+/', '.', \trim($app['request']->getPost('domainname')));
        if (\substr($newDomain, -5) == '.test') {
            $newDomain = \substr($newDomain, 0, -5);
        }
        $newDomain .= '.test';
        $config = $app['request']->getPost('websiteconfig');
        $custom = \trim($app['request']->getPost('customconfig'));
        $path = \trim(\trim($app['request']->getPost('websitepath')), '/.');
        $isDefault = (\substr(\basename($configFile), 0, 3) == '___');
        
        try {
            if (\in_array($newDomain, ['cp.test'])) {
                throw new \Exception('The virtual host "' . $newDomain . '" is invalid!');
            }
            if ((\file_exists('/etc/nginx/deva/vhosts/' . $newDomain . '.conf')
                or \file_exists('/etc/nginx/deva/vhosts/___' . $newDomain . '.conf')) and $domain != $newDomain) {
                throw new \Exception('The virtual host "' . $newDomain . '" already exists!');
            }
            Hosts::createHostRootDir($path);
            if (!\is_dir(Hosts::WWWPATH . '/' . $path)) {
                throw new \Exception('The specified path "' . Hosts::WWWPATH . '/' . $path . '" is not a directory!');
            }
            
            Hosts::backupHost($domain, Hosts::CONF_BACKUP);
            Hosts::deleteVirtualHost($domain);
            Hosts::createVirtualHost($newDomain, $config == 'custom' ? $custom : $config, $path);
            if ($output = Shell::configIsInvalid('nginx')) {
                Hosts::deleteVirtualHost($newDomain);
                Hosts::backupHost($domain, Hosts::CONF_RESTORE);
                throw new \Exception('Nginx configuration test failed!|' . $output);
            }
            Hosts::backupHost($domain, Hosts::CONF_DELETE);
            if (true === $isDefault) {
                Hosts::defaultVirtualHost($domain);
            }
            
            Shell::restartService('nginx');
            \header('Location: /?h=' . $newDomain);
        } catch (\Exception $e) {
            $msg = \explode('|', $e->getMessage());
            $hostField = Hosts::tld($domain, Hosts::WITHOUT_TLD);
            $config = Hosts::extractConfig($domain);
            echo $app['view']->render('edit', [
                'domain' => $domain,
                'hostfield' => $hostField,
                'config' => isset($config['app']) ? $config['app'] : null,
                'custom' => isset($config['custom']) ? $config['custom'] : null,
                'path' => Hosts::getHostPath($domain),
                'error' => $msg[0],
                'error_output' => isset($msg[1]) ? $msg[1] : false,
                'post' => $app['request']->getPost()
            ]);
        }
    }
);

/**
 * Delete Virtual Host
 */
$app->get(
    '/delete/{domain}',
    function ($domain) use ($app) {
        if (!\in_array($domain, ['cp.test', 'localhost'])) {
            Hosts::deleteVirtualHost($domain);
            Shell::restartService('nginx');
        }
        \header('Location: /');
    }
);

/**
 * Set Virtual Host as default
 */
$app->get(
    '/default/{domain}',
    function ($domain) use ($app) {
        Hosts::defaultVirtualHost($domain);
        Shell::restartService('nginx');
        \header('Location: /');
    }
);

/**
 * Backup
 */
$app->get(
    '/backup',
    function () use ($app) {
        \set_time_limit(0);
        $salt = \md5(\random_bytes(10));
        $tempPath = "/tmp/{$salt}";
        \mkdir($tempPath);
        
        Shell::tarDir(Hosts::WWWPATH, $tempPath . '/www.tar');
        Shell::dumpDb($tempPath . '/databases.sql');
        Shell::tarDir('/etc/nginx/deva/vhosts', $tempPath . '/vhosts.tar');
        
        \file_put_contents(
            $tempPath . '/info.txt',
            'DevA2 Version used to create this backup: ' . \file_get_contents('/etc/deva_version')
        );
        
        $compressedFile = 'DevA2_Backup_' . date('Y-m-d') . '.tar.gz';
        Shell::runShell('cd ' . $tempPath . ' && tar -zcf /tmp/' . $compressedFile . ' * .??*');
        
        \ignore_user_abort(true);
        
        \header('Content-type: application/gzip');
        \header('Content-Disposition: attachment; filename=' . $compressedFile);
        \header('Pragma: no-cache');
        \header('Expires: 0');
        \readfile('/tmp/' . $compressedFile);
        
        Shell::runShell('rm -rf ' . $tempPath);
        \unlink('/tmp/' . $compressedFile);
    }
);

/**
 * Restore
 */
$app->post(
    '/restore',
    function () use ($app) {
        \set_time_limit(0);
        $salt = \md5(\random_bytes(10));
        $tempPath = "/tmp/{$salt}";
        \mkdir($tempPath);
        
        if ($app['request']->hasFiles() == true) {
            foreach ($app['request']->getUploadedFiles() as $file) {
                $file->moveTo($tempPath . '/restore.tar.gz');
                
                try {
                    $gz = new \PharData($tempPath . '/restore.tar.gz');
                    $gz->decompress();
                    $tar = new \PharData($tempPath . '/restore.tar');
                    $tar->extractTo($tempPath . '/restore');
                    
                    if (!file_exists($tempPath . '/restore/databases.sql')
                        or !file_exists($tempPath . '/restore/vhosts.tar')
                        or !file_exists($tempPath . '/restore/www.tar')
                        or !file_exists($tempPath . '/restore/info.txt')) {
                        throw new \Exception('Invalid backup file!');
                    }
                    
                    Shell::importDbDump($tempPath . '/restore/databases.sql');
                    $tar = new \PharData($tempPath . '/restore/vhosts.tar');
                    $tar->extractTo('/');
                    $tar = new \PharData($tempPath . '/restore/www.tar');
                    $tar->extractTo('/');
                    
                    Shell::runShell('rm -rf ' . $tempPath);
                    Shell::restartService('nginx');
                } catch (\Exception $e) {
                    echo $app['view']->render('errors/woops', [
                        'error' => ':(',
                        'error_output' => $e->getMessage()
                    ]);
                }
                
                break;
            }
        }
    }
);
