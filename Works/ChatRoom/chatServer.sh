#!/bin/bash
# websocketd --pid=4321 --address="0.0.0.0" bash chatServer.sh
# killall bash
# killall websocketd 
# killall tail

datapath=/tmp/
contentFile=$datapath"content.txt"
peopleFile=$datapath"people.txt"

echo "<?php echo file_get_contents('$peopleFile');?>" > getPeople.php
read ip

addr=`python ./getAddress.py $ip`
echo "Please enter your nickname: "; read nickname
for i in $nickname;do
    nickname=$i
done
echo "$nickname, welcome to join the chatroom";
echo "<span style='color:#0099CC'> $nickname join the chatroom </span>" >> $contentFile
echo "<span style='color:#99CC66'> $nickname </span> <br>" >> $peopleFile
tail -n 0 -f $contentFile --pid=$$ &

while true; do
    read msg
    if [[ -z $msg ]]; then
        continue;
    fi
    
    isAtive="";
    for name in `cat $peopleFile`;do
        if [[ $name == $nickname ]];then
            isAtive="yes";
            break;
        fi
    done

    if [[ -z $isAtive ]] ; then
        echo "<span style="color:#99CC66"> $nickname </span> <br>" >> $peopleFile
    fi


    if [[ $msg == "::ifconfig" ]];then
        ifconfig
        continue
    elif [[ $msg == "::people" ]]; then
        cat $peopleFile
        continue
    elif [[ $msg == "::df" ]]; then
        df -Th
        continue
    elif [[ $msg == "::free" ]]; then
        free -m
        continue
    elif [[ $msg == "::getRecord" ]]; then
        cat $contentFile
        echo "ok"
        continue
    elif [[ $msg == "::whoami" ]]; then
        whoami
        continue
    elif [[ $msg == "::netstat" ]]; then
        netstat -antpl
        continue
    elif [[ $msg == "::clearRecord" ]]; then
        > $contentFile
        echo "ok"
        continue
    elif [[ $msg == "::history" ]]; then
        history
        continue
    elif [[ $msg == "::lastb" ]]; then
        lastb
        continue
    fi


    echo  "<span style='color:#FF9999'>[$(date +'%Y-%m-%d %H:%M:%S') <span style='color:#99CC99'>$addr</span>] <span style='color:#CC9933'> $nickname</span> </span><br> $msg" >> $contentFile
done
