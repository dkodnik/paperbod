#!/usr/bin/env python
# -*- coding: utf-8 -*-

import MySQLdb as mdb
import time
import tweepy

consumer_key = "**!**"
consumer_secret = "**!**"
access_key="**!**"
access_secret="**!**"


def activBot_Check():
    con = mdb.connect('localhost', '**!**', '**!**', '**!**')

    with con:

        cur = con.cursor(mdb.cursors.DictCursor)
        cur.execute("SELECT * FROM sites WHERE status='0' AND feed_type='twitter'")

        rows = cur.fetchall()

        for row in rows:
            try:
                auth = tweepy.OAuthHandler(consumer_key, consumer_secret)
                auth.set_access_token(access_key, access_secret)
                api=tweepy.API(auth)
                statuses=api.user_timeline(row["feed_url"])
            except Exception:
                curPost = con.cursor(mdb.cursors.DictCursor)
                curPost.execute("UPDATE sites SET status = %s WHERE id = %s", ("2", row["id"]))
                print "..incorrect twitter account"
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