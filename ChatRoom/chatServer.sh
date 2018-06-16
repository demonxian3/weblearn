#!/bin/bash
read ip
addr=`python ./getAddress.py $ip`
echo "Please enter your nickname: "; read nickname
for i in $nickname;do
    nickname=$i
done
echo "$nickname, welcome to join the chatroom";
echo "<span style='color:#0099CC'> $nickname join the chatroom </span>" >> ./content.txt
echo "<span style='color:#99CC66'> $nickname </span> <br>" >> ./people.txt
tail -n 0 -f ./content.txt --pid=$$ &

while true; do
    read msg
    if [[ -z $msg ]]; then
        continue;
    fi
    
    isAtive="";
    for name in `cat ./people.txt`;do
        if [[ $name == $nickname ]];then
            isAtive="yes";
            break;
        fi
    done

    if [[ -z $isAtive ]] ; then
        echo "<span style="color:#99CC66"> $nickname </span> <br>" >> ./people.txt
    fi


    if [[ $msg == "::ifconfig" ]];then
        ifconfig
        continue
    elif [[ $msg == "::people" ]]; then
        cat ./people.txt
        continue
    elif [[ $msg == "::df" ]]; then
        df -Th
        continue
    elif [[ $msg == "::free" ]]; then
        free -m
        continue
    elif [[ $msg == "::getRecord" ]]; then
        cat ./content.txt
        echo "ok"
        continue
    elif [[ $msg == "::whoami" ]]; then
        whoami
        continue
    elif [[ $msg == "::netstat" ]]; then
        netstat -antpl
        continue
    elif [[ $msg == "::clearRecord" ]]; then
        > ./content.txt
        echo "ok"
        continue
    elif [[ $msg == "::history" ]]; then
        history
        continue
    elif [[ $msg == "::lastb" ]]; then
        lastb
        continue
    fi


    echo  "<span style='color:#FF9999'>[$(date +'%Y-%m-%d %H:%M:%S') <span style='color:#99CC99'>$addr</span>] <span style='color:#CC9933'> $nickname</span> </span><br> $msg" >> ./content.txt
done
