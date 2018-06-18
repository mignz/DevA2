# DevA 2

This is a [Docker](https://www.docker.com/) container providing a development environment with PHP-FPM, Nginx, MySQL (MariaDB), Redis and SSMTP. It uses the lightweight Alpine Linux. PHP is installed along with the Phalcon framework, XDebug and other extensions that you would find available in most production servers.

I also include a web based control panel where you can:

- Fully manage virtual hosts without worrying about config files
- See the current version and the latest version of each service
- Update the Phalcon framework when a new version is available
- Restart the services (start Redis, which isn't started by default)
- Configure SMTP for PHP to be able to send emails
- Create a full backup (website data, databases, virtual hosts) and restore

_**Note:** DevA2's intended use is for development only. It is not suitable for production!_

## Usage

Run:

```shell
mkdir ~/Sites
sudo docker run -dit --restart unless-stopped --name deva2 -p 80:80 -p 443:443 -p 3306:3306 -v "$HOME"/Sites:/var/www -d mnunes/deva2:latest
```

Open [https://localhost](https://localhost) in your browser.

_**Note:** Change the source to wherever you'd like the websites to be located in your machine. By default they're in the "Sites" directory located in your user's home directory._

## Versions

|   DevA 2   |  PHP  |  Nginx  | MariaDB | Phalcon | Redis  | Alpine |
|------------|-------|---------|---------|---------|--------|--------|
| **latest** | 7.2.x | 1.12.2  | 10.1.32 | 3.4.0   | 4.0.6  | 3.7    |

DevA2 was built to "simulate" the Plesk Onyx environment I use at my company and at home. These services may be updated only when Plesk supports the newest versions.

_**Note:** It is possible to upgrade Phalcon through the web control panel._

## Changelog

See [CHANGELOG.md](CHANGELOG.md).

## Build

```shell
git clone git@github.com:mignz/DevA2.git
cd DevA2 && docker build -t deva2 .
```

---

## Bonus

### Route .test domains

Install dnsmasq to route all .test domains to 127.0.0.1 without having to alter the hosts file every time you add a new virtual host.

[http://www.thekelleys.org.uk/dnsmasq/doc.html](http://www.thekelleys.org.uk/dnsmasq/doc.html)
