<?php
/*! DevA 2 - github.com/mignz/DevA2 */

namespace DevA2;

use DevA2\Helpers\Hosts;
use DevA2\Helpers\Shell;
use DevA2\Helpers\Timezones;

/**
 * Main page
 */
$app->get(
    '/',
    function () use ($app) {
        echo $app['view']->render('index', [
            'ver_deva2' => \trim(\file_get_contents('/etc/deva_version')),
            'ver_alpine' => \trim(\file_get_contents('/etc/alpine-release')),
            'ver_nginx' => Shell::getShellOutput('nginx -v 2>&1 | cut -d/ -f2'),
            'ver_php' => Shell::getShellOutput('php -r "echo phpversion();"'),
            'ver_db' => Shell::getShellOutput('mysql -V | awk \'{print $5}\' | cut -d- -f1'),
            'ver_phalcon' => Shell::getShellOutput('php -r "echo Phalcon\Version::get();"'),
            'sql_status' => Shell::serviceRunning('mysql') ? 'RUNNING' : 'STOPPED',
            'sql_badge' => Shell::serviceRunning('mysql') ? 'success' : 'danger',
            'redis_status' => Shell::serviceRunning('redis') ? 'RUNNING' : 'STOPPED',
            'redis_badge' => Shell::serviceRunning('redis') ? 'success' : 'danger',
            'smtp' => Shell::getSSMTPConf(),
            'hosts' => Hosts::listVirtualHosts(),
            'get' => $app['request']->getQuery(),
            'files' => \glob(Hosts::WWWPATH . '/DevA2_Backup*.tar.gz'),
            'timezones' => Timezones::getTimezones(),
            'timezone' => \date_default_timezone_get()
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
        \header('Content-type: application/json; charset=utf-8');
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
        Shell::runPhalconUpgrade($app['request']->getQuery('v'));
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
 * Update PHP Timezone
 */
$app->post(
    '/timezone',
    function () use ($app) {
        Shell::updateTimezone($app['request']->getPost('timezone'));
        echo $app['view']->render('timezone');
    }
);

/**
 * Restart service
 */
$app->get(
    '/cert/{domain}',
    function ($domain) use ($app) {
        if (\file_exists('/etc/nginx/deva/ssl/' . $domain . '.crt')) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . $domain . '.crt"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize('/etc/nginx/deva/ssl/' . $domain . '.crt'));
            readfile('/etc/nginx/deva/ssl/' . $domain . '.crt');
            exit(0);
        } else {
            echo 'Error: Certificate does not exist!';
        }
    }
);

/**
 * Test PHP Mailer
 */
$app->get(
    '/test/{email}',
    function ($email) use ($app) {
        \mail($email, 'DevA2 Mail Test', 'It works! You can now test your email PHP scripts before production.');
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
            Shell::createCertificate($domain);
            if ($output = Shell::configIsInvalid('nginx')) {
                Hosts::deleteVirtualHost($domain);
                Shell::deleteCertificate($domain);
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
            if ($domain != $newDomain) {
                Shell::deleteCertificate($domain);
            }
            if (!\file_exists('/etc/nginx/deva/ssl/' . $newDomain . '.key')) {
                Shell::createCertificate($newDomain);
            }
            if ($output = Shell::configIsInvalid('nginx')) {
                Hosts::deleteVirtualHost($newDomain);
                Shell::deleteCertificate($newDomain);
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
            Shell::deleteCertificate($domain);
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
        
        if ($app['request']->getQuery('w') === '1') {
            Shell::tarDir(Hosts::WWWPATH, $tempPath . '/www.tar');
        }
        if ($app['request']->getQuery('d') === '1') {
            Shell::dumpDb($tempPath . '/databases.sql');
        }
        if ($app['request']->getQuery('v') === '1') {
            Shell::tarDir('/etc/nginx/deva/vhosts', $tempPath . '/vhosts.tar');
            Shell::tarDir('/etc/nginx/deva/ssl', $tempPath . '/ssl.tar');
        }
        \file_put_contents(
            $tempPath . '/info.txt',
            'DevA2 Version used to create this backup: ' . \file_get_contents('/etc/deva_version')
        );
        
        $compressedFile = 'DevA2_Backup_' . \date('Y-m-d_Hi') . '.tar.gz';
        Shell::runShell('cd ' . $tempPath . ' && tar -zcf /tmp/' . $compressedFile . ' * .??*');
        
        Shell::runShell('rm -rf ' . $tempPath);
        \rename('/tmp/' . $compressedFile, Hosts::WWWPATH . '/' . $compressedFile);
        
        echo $app['view']->render('backup', [
            'file' => $compressedFile
        ]);
    }
);

/**
 * Restore
 */
$app->get(
    '/restore/{file}',
    function ($file) use ($app) {
        \set_time_limit(0);
        $salt = \md5(\random_bytes(10));
        $tempPath = "/tmp/{$salt}";
        \mkdir($tempPath);
        
        try {
            if (!\file_exists(Hosts::WWWPATH . '/' . $file)) {
                throw new \Exception('Specified backup file does not exist!');
            }
            
            Shell::extractTar(Hosts::WWWPATH . '/' . $file, $tempPath);
            if (\file_exists($tempPath . '/vhosts.tar')) {
                Shell::runShell('rm -f /etc/nginx/deva/vhosts/*.conf');
                Shell::extractTar($tempPath . '/vhosts.tar', '/');
                if (\file_exists($tempPath . '/ssl.tar')) {
                    Shell::extractTar($tempPath . '/ssl.tar', '/');
                }
            }
            if (\file_exists($tempPath . '/databases.sql')) {
                Shell::importDbDump($tempPath . '/databases.sql');
            }
            if (\file_exists($tempPath . '/www.tar')) {
                Shell::extractTar($tempPath . '/www.tar', '/');
            }
        } catch (\Exception $e) {
            $stop = true;
            echo $app['view']->render('errors/woops', [
                'error' => $e->getMessage
            ]);
        }
        
        Shell::runShell('rm -rf ' . $tempPath);
        
        if (!isset($stop)) {
            Shell::restartService('nginx');
            \header('Location: /');
        }
    }
);

/**
 * Delete Backup
 */
$app->get(
    '/deletebackup/{file}',
    function ($file) use ($app) {
        \set_time_limit(0);
        $file = \preg_replace('/[^a-zA-Z0-9_\-.]/', null, $file);
        if (\file_exists(Hosts::WWWPATH . '/' . $file)) {
            \unlink(Hosts::WWWPATH . '/' . $file);
            \header('Location: /');
        }
    }
);
