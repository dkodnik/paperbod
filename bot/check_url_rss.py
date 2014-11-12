#!/usr/bin/env python
# -*- coding: utf-8 -*-

import MySQLdb as mdb
import feedparser
import diaspy.client as dsc
import time

def activBot_Check():
    con = mdb.connect('localhost', '**!**', '**!**', '**!**')

    with con:

        cur = con.cursor(mdb.cursors.DictCursor)
        cur.execute("SELECT * FROM sites WHERE status='0' AND feed_type='rss'")

        rows = cur.fetchall()

        for row in rows:
            try:
                feed = feedparser.parse(row["feed_url"])
            except Exception:
                curPost = con.cursor(mdb.cursors.DictCursor)
                curPost.execute("UPDATE sites SET status = %s WHERE id = %s", ("2", row["id"]))
                print "..incorrect Feed URL"
            else:
                # Ok

                try:
                    c = dsc.Client(row["pod_url"], row["usrnm"], row["pswrd"])
                except Exception:
                    curPost = con.cursor(mdb.cursors.DictCursor)
                    curPost.execute("UPDATE sites SET status = %s WHERE id = %s", ("2", row["id"]))
                    print "..incorrect POD data"
                else:
                    # Ok
                    curPost = con.cursor(mdb.cursors.DictCursor)
                    curPost.execute("UPDATE sites SET status = %s WHERE id = %s", ("1", row["id"]))



timeOut = 600
iStep = 1
while True:
    activBot_Check()
    print iStep
    iStep += 1
    print "...pause 10min..."
    time.sleep(timeOut)