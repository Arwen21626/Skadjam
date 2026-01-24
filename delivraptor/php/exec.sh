#!/bin/bash

file="chronos"
dir="$(cd "$(dirname "$0")" &&pwd)"
echo "* * * * * php ${dir}/${file}.php" > mycron.txt
crontab mycron.txt