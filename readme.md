	--read-only \
## Run container
docker run -it -d \
  -p 80:80 \
  --env WORDPRESS_DB_HOST=10.40.0.60 \
  --env WORDPRESS_DB_USER=root \
  --env WORDPRESS_DB_PASSWORD='' \
  --env WORDPRESS_DB_NAME=wordpress \
	--name wordpress-hmdcc \
  wordpress:hmdcc