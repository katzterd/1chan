<p align="center">
    <img src="https://raw.githubusercontent.com/rsddsdr/1chan/master/www/img/ogol.png" alt="1chan">
</p>

![CI](https://img.shields.io/github/actions/workflow/status/rsddsdr/rsddsdr/build.yml?label=CI&logo=github&style=for-the-badge)

## Installation

### Docker compose way

#### 1. Prepare .env, instance-config.php, and base themes
```
$ cp .env-dist .env
$ cp instance-config.php.example instance-config.php
$ cp /www/css/themes/normal.custom.example.css /www/css/themes/normal.custom.css
$ cp /www/css/themes/omsk.custom.example.css /www/css/themes/omsk.custom.css
```
Then fill fields in `.env` and `instance-config.php` by your text editor with needed values
Optionally, you can edit base css themes, if you want to customize the appearance of your WC

#### 2. Deploy
```
$ docker compose up -d
```

#### 3. Setup db and admin account
```
$ docker exec -t 1chan /1chan/scripts/config/docker-entrypoint.sh install
```

frontend will appear on `http://localhost:80`

### K8S way
See in **https://github.com/rsddsdr/1chan/tree/main/k8s**