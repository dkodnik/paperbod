#!/usr/bin/env python
# -*- coding: utf-8 -*-

#ver:2014.07.04-2

import MySQLdb as mdb
import html2text.html2text as h2t
import feedparser
import diaspy.client as dsc
import hashlib
from datetime import datetime, date, time
import time as tm
import sys

import re

def remove_img_tags(data):
    #p = re.compile(r'<img.*?/>')
    p = re.compile(r'<img.*?>')
    return p.sub('', data)

def activBotRSS():

    try:
        con = mdb.connect('localhost', '**!**', '**!**', '**!**')
    except Exception:
        print "Error: connect db"
        return False

    with con:

        cur = con.cursor(mdb.cursors.DictCursor)
        cur.execute("SELECT * FROM sites WHERE status='1' AND feed_type='rss'")

        rows = cur.fetchall()

        for row in rows:
            try:
                feed = feedparser.parse(row["feed_url"])
            except Exception:
                print "Error: loading feeds"
                continue #Оператор continue начинает следующий проход цикла, минуя оставшееся тело цикла
            print "url=%s amnt=%s \n" % (row["feed_url"], len( feed['entries'] ))

            try:
                c = dsc.Client(row["pod_url"], row["usrnm"], row["pswrd"])
            except Exception as inst:
                print "Error: connect pod ", sys.exc_info()[0]
                print inst
                continue #Оператор continue начинает следующий проход цикла, минуя оставшееся тело цикла

            entries = []
            for pFone in feed['entries']:
                strTgs = ""
                dataP=[{"tags":"", "published":"", "author":"", "summary":"", "link":"", "title":"", "dt":""}]
                if hasattr(pFone, 'published'):
                    dataP[0]["published"]=pFone.published
                if hasattr(pFone, 'author'):
                    dataP[0]["author"]=pFone.author
                if hasattr(pFone, 'tags'):
                    strTgs = "\n_ _ _ \n"
                    for tag in pFone.tags:
                        try:
                            tgX=tag.term.replace(" ", "-")
                            strTgs += " #"+tgX
                        except Exception:
                            # просто пропускаем
                            erStp=1
                    dataP[0]["tags"]=strTgs
                if hasattr(pFone, 'link'):
                    dataP[0]["link"]=pFone.link
                if hasattr(pFone, 'summary'):
                    dataP[0]["summary"]=pFone.summary
                dataP[0]["title"]=pFone.title
                try:
                    dataP[0]["dt"]=datetime.strptime(str(dataP[0]["published"]), "%Y-%m-%d %H:%M:%S")
                except Exception:
                    dataP[0]["dt"]=datetime.now()
                entries.extend( dataP )

            #sorted_entries = sorted(entries, key=lambda entry: entry["published"])
            sorted_entries = sorted(entries, key=lambda entry: entry["dt"])
            sorted_entries.reverse() # for most recent entries first

            insertNewFeedAmnt=0

            for post in sorted_entries:
                try:
                    oneURLcheck = hashlib.sha256(post["link"].encode('cp866')).hexdigest()
                except Exception:
                    print "Error: hash URL link"
                    continue #Оператор continue начинает следующий проход цикла, минуя оставшееся тело цикла
                
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

                        h = h2t.HTML2Text()
                        retTxt=h.handle( remove_img_tags(post["summary"]) )

                        strTgs = post["tags"]
                        if post["author"]!="":
                            authPost = post["author"]
                        else:
                            authPost = "none"
                        strAuthDate = "*"+post["published"]+", by "+authPost+"*"+ "\n"

                        if post["link"]!="":
                            if row["view_url"] == 1:
                                postD="### ["+post["title"] + "](" + post["link"]+ ") \n" + strAuthDate + "\n"+ retTxt.replace('\n', "\n") +"\n"+strTgs
                            else:
                                postD="### "+post["title"] + "\n" + strAuthDate + "\n"+ retTxt.replace('\n', "\n") +"\n"+strTgs
                        else:
                            postD="### "+post["title"] + " \n" + strAuthDate + "\n"+ retTxt.replace('\n', "\n") +"\n"+strTgs

                        if row["string_footer"] != "":
                            dataUTF8=row["string_footer"]
                            udata=dataUTF8.decode("utf-8","ignore")
                            asciidata=udata.encode("ascii","ignore")
                            postD += "\n\n %s" % (asciidata)

                        #print postD + "\n";
                        try:
                            c.post(postD);
                        except Exception:
                            print "Error: post message"
                            continue #Оператор continue начинает следующий проход цикла, минуя оставшееся тело цикла

                        curPostEx.execute("UPDATE feeds SET addfeed = %s WHERE hash = %s AND idusr = %s", ("1", oneURLcheck, row["idusr"]))

            if insertNewFeedAmnt!=0:
                print "....add feeds = %s" % (insertNewFeedAmnt)
    return True


timeOut = 600
timeOutBad = 1200
iStep = 1
while True:
    if activBotRSS()==True:
        print "step №%s time: %s" % (iStep, datetime.now())
        iStep += 1
        print "...pause 10min..."
        tm.sleep(timeOut)
    else:
        print "step №%s time: %s" % (iStep, datetime.now())
        iStep += 1
        print "...pause 20min..."
        tm.sleep(timeOutBad)