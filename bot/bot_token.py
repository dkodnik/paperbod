#!/usr/bin/env python
# -*- coding: utf-8 -*-

#ver:2014.04.01-1

import diaspy.client as dsc
import MySQLdb as mdb
import hashlib
from datetime import datetime, date, time
import time as tm
import sys

def activBotTokenAPI():

    try:
        con = mdb.connect('localhost', '**!**', '**!**', '**!**')
    except Exception:
        print "Error: connect db"
        return False

    with con:

        cur = con.cursor(mdb.cursors.DictCursor)
        cur.execute("SELECT * FROM sites WHERE status='1' AND feed_type='token'")

        rows = cur.fetchall()

        for row in rows:
            curFdMsg = con.cursor(mdb.cursors.DictCursor)
            curFdMsg.execute("SELECT * FROM tkn_post WHERE idSites='%s' AND status='0'",(row["id"]))
            rowsFM = curFdMsg.fetchall()

            try:
                c = dsc.Client(row["pod_url"], row["usrnm"], row["pswrd"])
            except Exception as inst:
                print "Error: connect pod ", sys.exc_info()[0]
                print inst
                continue #Оператор continue начинает следующий проход цикла, минуя оставшееся тело цикла

            insertNewFeedAmnt=0

            for rowFM in rowsFM:
                urlLink = "pd://%s/post/%s" % (row["feed_url"], rowFM["id"])

                oneURLcheck = hashlib.sha256(urlLink).hexdigest()
                curPost = con.cursor(mdb.cursors.DictCursor)
                curPost.execute("SELECT COUNT(*) as amnt FROM feeds WHERE hash=%s",(oneURLcheck))
                rowsPost = curPost.fetchall()
                for rowPost in rowsPost:
                    if rowPost["amnt"] == 0:
                        curPostEx = con.cursor(mdb.cursors.DictCursor)
                        curPostEx.execute("INSERT INTO feeds (`hash`, `idusr`, `idst`) VALUES (%s, %s, %s)", (oneURLcheck, row["idusr"], row["id"]))

                        insertNewFeedAmnt+=1

                        strTxt = rowFM["message"]
                        strTxt = strTxt.replace('\n', " ")
                        strTxt = strTxt.replace("\n", " ")
                        
                        try:
                            c.post(strTxt);
                        except Exception:
                            print "Error: post message"
                            continue #Оператор continue начинает следующий проход цикла, минуя оставшееся тело цикла

                        curPostEx.execute("UPDATE feeds SET addfeed = %s WHERE hash = %s AND idusr = %s", ("1", oneURLcheck, row["idusr"]))
                        curPostEx.execute("UPDATE tkn_post SET status=%s WHERE id = %s", ("1", rowFM["id"]))

                if insertNewFeedAmnt!=0:
                    print "....add post api = %s" % (insertNewFeedAmnt)
    return True



timeOut = 600
timeOutBad = 1200
iStep = 1
while True:
    if activBotTokenAPI()==True:
        print "step №%s time: %s" % (iStep, datetime.now())
        iStep += 1
        print "...pause 10min..."
        tm.sleep(timeOut)
    else:
        print "step №%s time: %s" % (iStep, datetime.now())
        iStep += 1
        print "...pause 20min..."
        tm.sleep(timeOutBad)