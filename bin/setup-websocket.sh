#!/bin/bash
#kill current websocket job
kill %1
# restart job
php /home/d4rkmindz/schoolproject/htdocs/htdocs/bin/game-server.php 0 &> /dev/null &

