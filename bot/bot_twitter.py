#!/usr/bin/env python
# -*- coding: utf-8 -*-

#ver:2014.07.04-3

import tweepy
import diaspy.client as dsc
import MySQLdb as mdb
import hashlib
from datetime import datetime, date, time
import time as tm
import sys

consumer_key = "**!**"
consumer_secret = "**!**"
access_key="**!**"
access_secret="**!**"

def activBotTwitter():

    try:
        con = mdb.connect('localhost', '**!**', '**!**', '**!**')
    except Exception:
        print "Error: connect db"
        return False

    try:
        auth = tweepy.OAuthHandler(consumer_key, consumer_secret)
        auth.set_access_token(access_key, access_secret)
        api=tweepy.API(auth)
    except Exception:
        print "Error: connect twitter"
        return False

    with con:

        cur = con.cursor(mdb.cursors.DictCursor)
        cur.execute("SELECT * FROM sites WHERE status='1' AND feed_type='twitter'")

        rows = cur.fetchall()

        for row in rows:
            try:
                # https://dev.twitter.com/docs/api/1/get/statuses/user_timeline
                statuses=api.user_timeline(row["feed_url"]) #ищем твиты указанного аккаунта (последние 20 штук)
            except Exception:
                print "Error: loading feeds"
                continue #Оператор continue начинает следующий проход цикла, минуя оставшееся тело цикла
            print "twtr url=%s amnt=%s \n" % (row["feed_url"], len( statuses ))

            if statuses:
                try:
                    c = dsc.Client(row["pod_url"], row["usrnm"], row["pswrd"])
                except Exception as inst:
                    print "Error: connect pod ", sys.exc_info()[0]
                    print inst
                    continue #Оператор continue начинает следующий проход цикла, минуя оставшееся тело цикла

                insertNewFeedAmnt=0

                for status in statuses:
                    urlLink = "https://twitter.com/%s/status/%s" % (row["feed_url"], status.id)

                    oneURLcheck = hashlib.sha256(urlLink).hexdigest()
                    curPost = con.cursor(mdb.cursors.DictCursor)
                    curPost.execute("SELECT COUNT(*) as amnt FROM feeds WHERE hash=%s",(oneURLcheck))
                    #curPost.execute("SELECT COUNT(*) as amnt FROM feeds WHERE idst=%s hash=%s",(row["id"],oneURLcheck))
                    rowsPost = curPost.fetchall()
                    for rowPost in rowsPost:
                        if rowPost["amnt"] == 0:
                            curPostEx = con.cursor(mdb.cursors.DictCursor)
                            curPostEx.execute("INSERT INTO feeds (`hash`, `idusr`, `idst`) VALUES (%s, %s, %s)", (oneURLcheck, row["idusr"], row["id"]))
                            #curPostEx.execute("INSERT INTO feeds (`hash`, `idusr`) VALUES (%s, %s)", (oneURLcheck, row["idusr"]))
                            insertNewFeedAmnt+=1

                            strTxt = status.text
                            strTxt = strTxt.replace('\n', " ")
                            strTxt = strTxt.replace("\n", " ")
                            for entitieOne in status.entities:
                                if entitieOne=="media":
                                    mediaOneThs= status.entities[entitieOne]
                                    for mediaOne in mediaOneThs:
                                        strTxt += "\n\n![img](%s)\n\n" % (mediaOne['media_url_https'])
                            #postD="### %s \n *%s*\n\n[View tweet](%s)" % (strTxt, status.created_at, urlLink)
                            
                            #print status.id #Ok!
                            #print status.created_at #Ok!
                            #print status.source #Ok!
                            #print status.geo #Ok!
                            #print status.text #Ok!
                            #for hashtag in status.entities['hashtags']:
                            #	print hashtag['text']

                            postD="%s \n *%s*" % (strTxt, status.created_at)

                            if row["view_url"] == 1:
                                postD += "\n\n[View tweet](%s)" % (urlLink)
                            if row["string_footer"] != "":
                                dataUTF8=row["string_footer"]
                                udata=dataUTF8.decode("utf-8","ignore")
                                asciidata=udata.encode("ascii","ignore")
                                postD += "\n\n %s" % (asciidata)

                            try:
                                c.post(postD);
                            except Exception:
                                print "Error: post message"
                                continue #Оператор continue начинает следующий проход цикла, минуя оставшееся тело цикла

                            curPostEx.execute("UPDATE feeds SET addfeed = %s WHERE hash = %s AND idusr = %s", ("1", oneURLcheck, row["idusr"]))

                if insertNewFeedAmnt!=0:
                    print "....add tweets = %s" % (insertNewFeedAmnt)
    return True



timeOut = 600
timeOutBad = 1200
iStep = 1
while True:
    if activBotTwitter()==True:
        print "step №%s time: %s" % (iStep, datetime.now())
        iStep += 1
        print "...pause 10min..."
        tm.sleep(timeOut)
    else:
        print "step №%s time: %s" % (iStep, datetime.now())
        iStep += 1
        print "...pause 20min..."
        tm.sleep(timeOutBad)