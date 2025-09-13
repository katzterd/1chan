# k8s workflow

## Requirements:

- [kubectl](https://kubernetes.io/docs/tasks/tools/install-kubectl/)
- [helm](https://helm.sh/docs/intro/install/)

## Quick start

#### 1. Prepare .env

```console
cp .env-dist .env
```

Then fill fields in `.env` by your text editor with desired values

#### 2. Create secrets

```console
kubectl create namespace <namespace>
kubectl create -n <namespace> secret generic <secretsName> --from-env-file=.env
```

#### 3. Deploy

See deploy instruction and configurable options in [./helm/charts/1chan](https://github.com/katzterd/1chan/tree/main/k8s/helm/charts/1chan)

#### 4. Set up db and admin account

```console
kubectl -n <namespace> exec -t deployments/<my-release>-app -- /docker-entrypoint.sh install
```

#### 5. Restart `1chan` deployment to run indexer properly after db setup

```console
kubectl -n <namespace> rollout restart deployments/<my-release>-app
```
