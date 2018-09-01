# Upgrading

DevA 2 doesn't provide an easy upgrade method. If you need to upgrade to a newer version of DevA 2, you can either apply the changes manually (by accessing the image directly), or remove the current image and create a new one with the latest version. For the latter, here's how:

1. Backup your website files and databases
1. [Stop] the current DevA 2 image
1. [Remove] the old DevA 2 image
1. Remove (or rename) the directory where the websites are
1. Install DevA 2 again the [normal way]
1. Restore the backup

[Stop]: (https://docs.docker.com/engine/reference/commandline/stop/)
[Remove]: https://docs.docker.com/engine/reference/commandline/rm/
[normal way]: https://github.com/mignz/DevA2/blob/master/README.md#usage
