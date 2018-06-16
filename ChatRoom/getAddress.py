import urllib2
import sys

ip = sys.argv[1]

url = "http://ip138.com/ips138.asp?ip=" + ip

headers= {"User-Agent": "Mozilla/5.0 (Windows NT 6.1; rv:60.0) Gecko/20100101 Firefox/60.0"}

req = urllib2.Request(url,headers=headers);
rep = urllib2.urlopen(req);

html = rep.read().split("\n");


for i in html:
    if "<li>" in i:
        print i.split("<li>")[1].decode("gb2312")[5:].split("<")[0].encode("utf-8")


