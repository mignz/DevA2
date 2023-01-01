#!/bin/bash

# DO NOT RUN THIS SCRIPT UNLESS YOU KNOW WHAT YOU'RE DOING!!!

docker stop "$(docker ps -a -q --filter ancestor=deva2dev)"
docker rm "$(docker ps -a -q --filter ancestor=deva2dev)"
docker system prune -af

docker build --progress=plain -t deva2dev .
docker run -d -it \
    --name deva2dev \
    -p 80:80 -p 443:443 -p 3306:3306 \
    -v "$HOME"/www:/var/www \
    deva2dev

docker exec -it "$(docker ps -a -q --filter ancestor=deva2dev)" ash

# PUSHING

# docker buildx create --use
# docker buildx build -t mnunes/deva2:latest -t mnunes/deva2:1.5.3 --platform linux/amd64,linux/arm64 --push .
# docker buildx build -t mnunes/deva2:amd64 --platform linux/amd64 --push .
# docker buildx build -t mnunes/deva2:arm64 --platform linux/arm64 --push .
