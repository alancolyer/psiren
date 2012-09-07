#! /bin/bash

#file to play an mp3 and fork it, returning a result immediately

killall mpg321 > /dev/null
mpg321 -q "$1" >/dev/null &