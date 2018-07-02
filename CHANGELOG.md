# Changelog

## 1.1.0 - 2nd of July, 2018

- `[+]` Enabled PHP's short open tag
- `[*]` Completely restructured the backup/restore logic and improved UI
- `[-]` Increased the PHP max execution time for the CP to avoid timeouts

## 1.0.2 - 20th of June, 2018

- `[+]` Added STARTTLS to SMTP configuration
- `[-]` Increased Nginx time limit when creating a backup
- `[-]` Mysql database was being included in a mysql dump
- `[-]` Backup was creating a corrupt file
- `[-]` Nginx was creating a huge log after being restarted through the CP

## 1.0.1 - 19th of June, 2018

- `[+]` Enabled Xdebug remote
- `[+]` Added some messages in the Control Panel
- `[*]` Updated readme

## 1.0.0 - 18th of June, 2018

- `[+]` Initial Release

---

**Legend:**

- `[+]` New
- `[-]` Fixed
- `[*]` Improvement
