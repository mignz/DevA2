#!/bin/bash

docker stop "$(docker ps -aq)"
docker rm "$(docker ps -aq)"

docker-compose up -d --build

docker exec -it "$(docker ps -q)" ash
