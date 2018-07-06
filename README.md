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

_**Note:** DevA2's intended use is for development only. It is NOT suitable for production!_

## Usage

1. Create the directory where the websites will be located
1. Run the following command (change "$HOME"/Sites to that directory)

```shell
docker run -dit --restart unless-stopped \
    --name deva2 \
    -p 80:80 -p 443:443 -p 3306:3306 \
    -v "$HOME"/Sites:/var/www \
    mnunes/deva2:latest
```

Open [http://localhost](https://localhost) in your browser.

## Versions

|   DevA 2   | Alpine |  Nginx  | MariaDB |  PHP  | Phalcon | Redis  |
|------------|--------|---------|---------|-------|---------|--------|
| **latest** | 3.8    | 1.14.0  | 10.2.15 | 7.2.x | 3.4.0   | 4.0.10 |
| 1.0.0      | 3.7    | 1.12.2  | 10.1.32 | 7.2.x | 3.4.0   | 4.0.6  |

## Ports

- **MySQL:** 3306
- **Redis:** 6379
- **HTTP:** 80
- **HTTPS:** 443

## Database Access

- **Host:** 127.0.0.1
- **Username:** root
- **Password:**

## Changelog

See [CHANGELOG.md](https://github.com/mignz/DevA2/blob/master/CHANGELOG.md).

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

I followed [this](https://grzegorowski.com/docker-with-xdebug-and-vdebug/) guide to configure Xdebug. You'll need to run the following command to allow Xdebug to access the host machine:

`sudo ifconfig lo0 alias 10.254.254.254`
