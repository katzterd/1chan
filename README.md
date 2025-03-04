<p align="center">
    <img src="https://raw.githubusercontent.com/katzterd/1chan/master/www/img/ogol.png" alt="1chan">
</p>

![CI](https://img.shields.io/github/actions/workflow/status/katzterd/1chan/build.yml?label=CI&logo=github&style=for-the-badge)

## Installation

### Docker compose way

#### 1. Prepare `.env`
```
$ cp .env-dist .env
```
Then fill fields in `.env` by your text editor with needed values

You can create your own css themes or edit existing ones. Script will copy all css files which ends with `.example`. See in [/www/css/themes](https://github.com/katzterd/1chan-docker/tree/master/www/css/themes)

#### 2. Deploy
```
$ docker compose up -d
```

#### 3. Setup db and admin account
```
$ docker exec -t 1chan-service /docker-entrypoint.sh install
```

#### 4. Restart `1chan-service` container to run indexer properly after db setup
```
$ docker compose restart 1chan-service
```

frontend will appear on `http://localhost:80`



#### (Optional) get Yggdrasil address
```
$ docker exec -t yggdrasil /docker-entrypoint.sh getaddr
```
