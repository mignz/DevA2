#!/bin/bash

docker stop "$(docker ps -aq)"
docker rmi "$(docker ps -aq)"
docker system prune -af

docker build -t deva2 .
docker run -d -it \
    -p 80:80 -p 443:443 -p 3306:3306 \
    -v "$HOME"/www:/var/www \
    deva2

docker exec -it "$(docker ps -q)" ash
