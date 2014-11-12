#!/usr/bin/env python
# -*- coding: utf-8 -*-

import MySQLdb as mdb
import time
import facebook

FB_APP_TOKEN = '**!**'


def activBot_Check():
    con = mdb.connect('localhost', '**!**', '**!**', '**!**')

    with con:

        cur = con.cursor(mdb.cursors.DictCursor)
        cur.execute("SELECT * FROM sites WHERE status='0' AND feed_type='facebook'")

        rows = cur.fetchall()

        for row in rows:
            try:
                graph = facebook.GraphAPI(FB_APP_TOKEN)
                feedsUsr = graph.get_object(row["feed_url"])
            except Exception:
                curPost = con.cursor(mdb.cursors.DictCursor)
                curPost.execute("UPDATE sites SET status = %s WHERE id = %s", ("2", row["id"]))
                print "..incorrect facebook account"
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