# 1chan Helm Chart
![CI](https://img.shields.io/github/actions/workflow/status/katzterd/1chan/helm-build.yml?label=Chart&logo=helm&style=for-the-badge)

## Get Repository

```console
helm repo add 1chan https://katzterd.github.io/1chan
helm repo update
```

## Install chart

```console
helm install <my-release> (--set <key1=val1,key2=val2,...>) 1chan/1chan -n <namespace> --create-namespace
```

## Uninstall chart

```console
helm -n <namespace> delete <my-release>
```

## Configuration

| Parameter                                  | Description                                   | Default Value                                           |
|--------------------------------------------|-----------------------------------------------|---------------------------------------------------------|
| `svc.torgate.enable`                       | "true" to enable torgate                      | None (Disabled)                                         |
| `svc.i2pgate.enable`                       | "true" to enable i2pgate                      | None (Disabled)                                         |
| `svc.yggdrasilgate.enable`                 | "true" to enable yggdrasilgate                | None (Disabled)                                         |
| `registry`                                 | Override Container registry                   | `ghcr.io/katzterd/1chan`                                |
| `secretsName`                              | Override secrets name                         | `1chan-secrets`                                         |
| `storageClass.name`                        | Override storage class name                   | `1chan-sc`                                              |
| `dbSpace`                                  | Size of database free space (in Gi)           | `10Gi`                                                  |
| `wwwSpace`                                 | Size of storage free space (in Gi)            | `25Gi`                                                  |
| `redisSpace`                               | Size of redis free space (in Gi)              | `5Gi`                                                   |
| `imagePullSecretName`                      | For pulling from private registry             | None                                                    |


### Pulling from private registry
```console
kubectl -n <namespace> create secret generic <imagePullSecretName> \ 
    --from-file=.dockerconfigjson=/path/to/.docker/config.json \
    --type=kubernetes.io/dockerconfigjson
```

## Get yggdrasil node address (if enabled)
```console
kubectl -n <namespace> exec -t deployments/yggdrasil -- /docker-entrypoint.sh getaddr
```
