![DevA2](https://raw.githubusercontent.com/mignz/DevA2/master/deva.png)

[![Build Status](https://travis-ci.org/mignz/DevA2.svg?branch=master)](https://travis-ci.org/mignz/DevA2)
[![License: GPL v3](https://img.shields.io/badge/License-GPL%20v3-blue.svg)](https://www.gnu.org/licenses/gpl-3.0)

This is a [Docker](https://www.docker.com/) image providing a development environment (LEMP) with Nginx, PHP 7, MySQL (MariaDB), Redis and SSMTP (for testing emails in PHP). It uses the lightweight Alpine Linux. PHP is installed along with the Phalcon framework, XDebug and other extensions that you would find available in most production servers.

It also includes a **web** based **control panel** where you can:

- Manage virtual hosts
- See service current and latest versions
- Upgrade the Phalcon framework
- Restart services
- Configure SMTP
- Backup and restore

_**Note:** DevA2's intended use is for development in a safe environment only. It is NOT suitable for hosting websites in production!_

## Usage

1. Create the directory where the websites will be located
1. Run the following command (change `"$HOME"/Sites` to that directory)

```shell
docker run -dit --restart unless-stopped \
    --name deva2 \
    -p 80:80 -p 443:443 -p 3306:3306 \
    -v "$HOME"/Sites:/var/www \
    mnunes/deva2:latest
```

Open [http://localhost](http://localhost) in your browser.

## Versions

|   DevA 2   | Alpine |  Nginx  | MariaDB |  PHP   | Phalcon | Redis  |
|------------|--------|---------|---------|--------|---------|--------|
| **latest** | 3.10   | 1.16.1  | 10.3.20 | 7.4.3  | 4.0.5   | 5.0.5  |
| 1.2.4      | 3.10   | 1.16.1  | 10.3.20 | 7.3.11 | 3.4.5   | 5.0.5  |
| 1.2.3      | 3.10   | 1.16.1  | 10.3.18 | 7.3.10 | 3.4.4   | 5.0.5  |
| 1.2.2      | 3.10   | 1.16.1  | 10.3.17 | 7.3.8  | 3.4.4   | 5.0.5  |
| 1.2.1      | 3.9    | 1.14.2  | 10.3.12 | 7.2.x  | 3.4.2   | 4.0.12 |
| 1.2.0      | 3.8    | 1.14.0  | 10.2.15 | 7.2.x  | 3.4.1   | 4.0.11 |
| 1.1.1      | 3.8    | 1.14.0  | 10.2.15 | 7.2.x  | 3.4.0   | 4.0.10 |
| 1.0.0      | 3.7    | 1.12.2  | 10.1.32 | 7.2.x  | 3.4.0   | 4.0.6  |

## Ports

- **HTTP:** 80
- **HTTPS:** 443
- **MySQL:** 3306
- **Redis:** 6379

## Database Credentials

- **Host:** 127.0.0.1
- **Username:** root
- **Password:**

## Changelog

See [CHANGELOG.md](https://github.com/mignz/DevA2/blob/master/CHANGELOG.md).

## Upgrading

DevA 2 doesn't provide an easy upgrade method. If you need to upgrade to a newer version of DevA 2, you can either upgrade the packages manually (by accessing the image directly), or remove the current image and create a new one with the latest version. For the latter, here's how:

1. Backup your website virtual hosts and databases (no websites)
1. [Stop] the current DevA 2 image
1. [Remove] the old DevA 2 image
1. Leave the directory where the websites are as it is
1. Install DevA 2 again the [normal way]
1. Restore the backup

[Stop]: (https://docs.docker.com/engine/reference/commandline/stop/)
[Remove]: https://docs.docker.com/engine/reference/commandline/rm/
[normal way]: https://github.com/mignz/DevA2/blob/master/README.md#usage

## Build

```shell
git clone git@github.com:mignz/DevA2.git
cd DevA2 && docker build -t deva2 .
```

---

## Bonus

### Route .test domains

Install dnsmasq to route all .test domains to 127.0.0.1 without having to alter the hosts file every time you add a new virtual host. There are some guides on how to do exactly that on macOS and Linux.

[http://www.thekelleys.org.uk/dnsmasq/doc.html](http://www.thekelleys.org.uk/dnsmasq/doc.html)

### Access Xdebug

I followed [this](https://grzegorowski.com/docker-with-xdebug-and-vdebug/) guide for the Xdebug configuration. You'll need to run the following command to give Xdebug access to the host machine:

`sudo ifconfig lo0 alias 10.254.254.254`

### Trust self-signed certificates

Download all the certificates you need from cp.test.

- [Instructions for macOS](https://tosbourn.com/getting-os-x-to-trust-self-signed-ssl-certificates/)
- [Instructions on Linux](https://unix.stackexchange.com/a/90607)
