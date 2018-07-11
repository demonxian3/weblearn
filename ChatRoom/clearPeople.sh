# crontab -e || vim /etc/crontab
# * * * * * /var/run/$0

if [ `pwd`  != "/var/run" ];then
    $(mv $0 /var/run/)
fi

path=/tmp/
peopleFile=$path"/people.txt"
contentFile=$path"/content.txt"
> $peopleFile;
#> $contentFile;
