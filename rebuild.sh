#!/bin/bash

# DO NOT RUN THIS SCRIPT UNLESS YOU KNOW WHAT YOU'RE DOING!!!

docker stop "$(docker ps -a -q --filter ancestor=deva2dev)"
docker rm "$(docker ps -a -q --filter ancestor=deva2dev)"
docker system prune -af

docker build -t deva2dev .
docker run -d -it \
    --name deva2dev \
    -p 80:80 -p 443:443 -p 3306:3306 \
    -v "$HOME"/www:/var/www \
    deva2dev

docker exec -it "$(docker ps -a -q --filter ancestor=deva2dev)" /bin/sh -c "apk add nano bash && echo \"alias ll='ls -alFh --color=auto'\" > ~/.bashrc"
docker exec -it "$(docker ps -a -q --filter ancestor=deva2dev)" bash
