#!/usr/bin/python3
# -*- coding: utf-8 -*- 

import urllib.request  
url="http://google.cn/"  
response=urllib.request.urlopen(url)  
page=response.read()
#print(page)
f = open('index.html', "wb+") 
f.write(page)  #用readlines()方法写入文件
f.close()  