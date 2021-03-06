# minikube_php_psql_redis
The environment using Minikube to show a simple PHP page with PostgreSQL and Redis.

Tested on MacOsx with:
- Docker version 18.09.1, build 4c52b90
- minikube version: v0.33.1
- Client/Server Version: version.Info{Major:"1", Minor:"13", GitVersion:"v1.13.2"
- Redis not tested yet!

###
# Install, Start, Dashboard minikube
```sh
brew install minikube
eval $(minikube docker-env)
minikube start
minikube dashboard
```

###
# Steps to create the environment
```sh
git clone https://github.com/garanet/minikube_php_psql_redis.git
cd minikube_php_psql_redis/
kubectl create -f minikube/php/
kubectl create -f minikube/postgres/
kubectl create -f minikube/redis/
```
# ssh into nginx or phpfpm containers
```sh
kubectl exec -it [pod name] -c my-nginx bash
kubectl exec -it [pod name] -c phpfpm bash
```

###
# Visit the PHP page connected to the PGSQL
```sh
minikube service my-nginx
```
# Note before you run the kubectl commands

#note: You need to change the HostPath in minikube/php/php7fpm-rc.yaml with your relative volumes path

#note: You need to change the HostPath in minikube/postgres/volume.yaml with your relative volumes path

#note: If you need to change the password for the postgres user run > echo -n "YourPassword" | base64 and change it on minikube/postgres/secret.yaml

#note: Developers can use the Shared Public folder to change the php code.
