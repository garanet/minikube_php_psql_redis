# minikube_php_psql_redis
The environment using Minikube to show a simple PHP page with PostgreSQL and Redis.
###
# Install, Start, Dashboard minikube
```sh
brew install minikube
eval $(minikube docker-env)
minikube start
minikube dashboard
```

###
# steps to create
```sh
kubectl create -f minikube/php/
kubectl create -f minikube/postgres/
kubectl create -f minikube/redis/
```
# ssh into nginx container
```sh
kubectl exec -it [pod name] -c my-nginx bash
kubectl exec -it [pod name] -c phpfpm bash
```
###
# Visit the PHP page connected to the PGSQL
```sh
minikube service my-nginx
```
#note: change password for the postgres user run > echo -n "test" | base64

