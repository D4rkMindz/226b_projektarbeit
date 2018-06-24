#!/bin/bash
#kill current websocket job
nohup php /home/d4rkmindz/schoolproject/htdocs/htdocs/bin/game-server.php 0 &> /dev/null &
# restart job
disown

