#!/bin/bash
function send()
{
    while true; do
        number=$(( $RANDOM % 128 + 16 ))
        tag=`cat /dev/urandom | tr -dc 'a-zA-Z0-9' | fold -w $number | head -n 1`
        tag2=`cat /dev/urandom | tr -dc 'a-zA-Z0-9' | fold -w $number | head -n 1`
        echo -e "test.metric,tag=$tag:+$number|c\ntest.timing,$tag=$tag2:$number|ms" >/dev/udp/127.0.0.1/9125
        echo -e "test.metric,tag=lag:+1|c" >/dev/udp/127.0.0.1/9125
    done;
}
function spawn() {
    for i in {1..10}
    do
        send&
        echo spawning;
    done
    echo "spawn done"
    sleep 1;
}

for z in {1..10}
do
    spawn;
    echo "HERE WE GO AGAIN!"
done;
while true; do sleep 1; done;
