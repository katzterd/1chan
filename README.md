<p align="center">
    <img src="https://raw.githubusercontent.com/katzterd/1chan/master/www/img/ogol.png" alt="1chan">
</p>

![CI](https://img.shields.io/github/actions/workflow/status/katzterd/1chan/docker-build.yml?label=CI&logo=github&style=for-the-badge)

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
$ docker exec -t onechan /docker-entrypoint.sh install
```

#### 4. Restart `onechan` container to run indexer properly after db setup
```
$ docker compose restart onechan
```

frontend will appear on `http://localhost:80`



#### (Optional) Get yggdrasil node address (if enabled)
```
$ docker exec -t yggdrasil /docker-entrypoint.sh getaddr
```

### K8S way
See in [/k8s](https://github.com/katzterd/1chan/tree/master/k8s)
