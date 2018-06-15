#!/bin/bash

docker stop "$(docker ps -aq)"
docker rm "$(docker ps -aq)"
#docker system prune -af

docker build -t deva2 .
docker run -d -it --name=deva2 \
    -p 80:80 -p 443:443 -p 3306:3306 \
    --mount type=bind,source="$HOME"/www,destination=/var/www \
    deva2

docker exec -it "$(docker ps -q)" ash
