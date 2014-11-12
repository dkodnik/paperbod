# -*- coding: utf-8 -*-

import MySQLdb as mdb

con = mdb.connect('localhost', '**!**', '**!**', '**!**')

with con:
    curPost = con.cursor(mdb.cursors.DictCursor)
    curPost.execute("SELECT * FROM sites")
    rowsPost = curPost.fetchall()
    for rowPost in rowsPost:
    	curPost.execute("UPDATE sites SET feed_type = %s WHERE id = %s", ("rss", rowPost["id"]))