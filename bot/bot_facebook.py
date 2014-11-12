#!/usr/bin/env python
# -*- coding: utf-8 -*-

#ver:2014.07.04-3

import facebook
import diaspy.client as dsc
import MySQLdb as mdb
import hashlib
from datetime import datetime, date, time
import time as tm
import sys

FB_APP_TOKEN = '**!**'

def activBotFacebook():
    try:
        con = mdb.connect('localhost', '**!**', '**!**', '**!**')
    except Exception:
        print "Error: connect db"
        return False

    try:
        graph = facebook.GraphAPI(FB_APP_TOKEN)
    except Exception:
        print "Error: connect facebook"
        return False

    with con:

        cur = con.cursor(mdb.cursors.DictCursor)
        cur.execute("SELECT * FROM sites WHERE status='1' AND feed_type='facebook'")

        rows = cur.fetchall()

        for row in rows:
            try:
                feedsUsr = graph.get_connections(row["feed_url"], 'feed', limit=20) #ищем посты указанного аккаунта (последние 20 штук)
                statuses = feedsUsr['data']
            except Exception:
                print "Error: loading feeds"
                continue #Оператор continue начинает следующий проход цикла, минуя оставшееся тело цикла
            print "fb url=%s amnt=%s \n" % (row["feed_url"], len( statuses ))

            if statuses:
                try:
                    c = dsc.Client(row["pod_url"], row["usrnm"], row["pswrd"])
                except Exception as inst:
                    print "Error: connect pod ", sys.exc_info()[0]
                    print inst
                    continue #Оператор continue начинает следующий проход цикла, минуя оставшееся тело цикла

                insertNewFeedAmnt=0

                for status in statuses:
                    strID=status['id']
                    urlLink = "https://www.facebook.com/%s" % (strID.replace('_', "/posts/"))

                    if status['type'] == 'status':
                        # пропускаем!!!
                        continue

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

                            if status['type'] == 'photo':
                                createdTime = "%s" % (status['created_time'])
                                if status['created_time']!=status['updated_time']:
                                    createdTime += " (update: %s)" % (status['updated_time'])
                                msgD = ""
                                try:
                                    msgD = "%s" % (status['message'])
                                except Exception: # пропускаем
                                    er=1
                                msgD += "\n\n![img](%s)\n\n" % (status['picture'])
                                msgD += "\n\n[link](%s)" % (status['link'])
                                postD="%s \n\n *%s*\n\n[goto facebook](%s)" % (msgD, createdTime, urlLink)

                            elif status['type'] == 'link':
                                createdTime = "%s" % (status['created_time'])
                                if status['created_time']!=status['updated_time']:
                                    createdTime += " (update: %s)" % (status['updated_time'])
                                msgD = ""
                                try:
                                    msgD += "%s\n" % (status['name'])
                                except Exception: # пропускаем
                                    er=1
                                try:
                                    msgD += "\n\n%s\n\n" % (status['message'])
                                except Exception: # пропускаем
                                    er=1
                                try:
                                    msgD += "\n\n%s\n\n" % (status['description'])
                                except Exception: # пропускаем
                                    er=1
                                try:
                                    msgD += "\n\n![img](%s)\n\n" % (status['picture'])
                                except Exception: # пропускаем
                                    er=1
                                msgD += "\n\n[link](%s)" % (status['link'])

                                postD="%s \n\n *%s*" % (msgD, createdTime)

                                if row["view_url"] == 1:
                                    postD += "\n\n[goto facebook](%s)" % (urlLink)
                                if row["string_footer"] != "":
                                    dataUTF8=row["string_footer"]
                                    udata=dataUTF8.decode("utf-8","ignore")
                                    asciidata=udata.encode("ascii","ignore")
                                    postD += "\n\n %s" % (asciidata)

                            try:
                                c.post(postD)
                            except Exception:
                                print "Error: post message"
                                continue #Оператор continue начинает следующий проход цикла, минуя оставшееся тело цикла

                            curPostEx.execute("UPDATE feeds SET addfeed = %s WHERE hash = %s AND idusr = %s", ("1", oneURLcheck, row["idusr"]))

                if insertNewFeedAmnt!=0:
                    print "....add FB posts = %s" % (insertNewFeedAmnt)
    return True



timeOut = 600
timeOutBad = 1200
iStep = 1
while True:
    if activBotFacebook()==True:
        print "step №%s time: %s" % (iStep, datetime.now())
        iStep += 1
        print "...pause 10min..."
        tm.sleep(timeOut)
    else:
        print "step №%s time: %s" % (iStep, datetime.now())
        iStep += 1
        print "...pause 20min..."
        tm.sleep(timeOutBad)