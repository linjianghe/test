#!/usr/bin/python3
# -*- coding: utf-8 -*- 

import pymysql
 
conn = pymysql.connect(host="127.0.0.1",port=3306,user="root",passwd="",db="lin_db",charset="utf8")
 
cur = conn.cursor()
 
sql = "select * from ci_admin limit 1"
 
cur.execute(sql)
 
rows = cur.fetchall()
 
#print(rows)
 
for dr in rows:
    print(dr)