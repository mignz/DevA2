# DevA 2

A Dockerfile providing a development environment with PHP-FPM, Nginx and MySQL (MariaDB). All using the lightweight Alpine Linux. PHP is installed with the Phalcon framework, XDebug and other extensions that you would find available in Plesk where I host personal and company projects. The list of these installed extensions is specified in the Dockerfile.

DevA 2 also includes a web based control panel to manage the virtual hosts and export/import all websites data and virtual hosts.

## Usage

Pull the image from the Docker Hub:

```shell
docker pull mnunes/deva2:latest
```

Run:

```shell
sudo docker run -d -it --privileged=true --name=deva2 -p 80:80 -p 443:443 -p 3306:3306 --mount type=bind,source="$HOME"/Sites,destination=/var/www -d deva2
```

Open [https://localhost](https://localhost) in your browser.

_**Note:** Change the source to wherever you'd like the websites to be located in your machine. By default they're in the "Sites" directory located in your user's home directory._

## Versions

|   DevA 2   |  PHP  |  Nginx  | MariaDB | Phalcon | Alpine |
|------------|-------|---------|---------|---------|--------|
| **latest** | 7.2.x | 1.12.2  | 10.1.32 | 3.4.0   | 3.7    |

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
