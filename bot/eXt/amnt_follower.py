#!/usr/bin/env python
# -*- coding: utf-8 -*-

#ver:2014.03.07-2

import MySQLdb as mdb
import diaspy
import time

#http://pad.spored.de/ro/r.qWmvhSZg7rk4OQam

def activBotAmntFollowers():

    try:
        con = mdb.connect('localhost', '**!**', '**!**', '**!**')
    except Exception:
        print "Error: connect db"
        return False

    with con:

        cur = con.cursor(mdb.cursors.DictCursor)
        cur.execute("SELECT * FROM sites WHERE status='1'")

        rows = cur.fetchall()

        for row in rows:
            amntFlwrs=0

            print "%s  --- №%s" % (row['usrnm'],row['id'])

            try:
                c = diaspy.connection.Connection(row["pod_url"], row["usrnm"], row["pswrd"])
                c.login()
            except Exception:
                print "Error: connect pod"
                continue #Оператор continue начинает следующий проход цикла, минуя оставшееся тело цикла

            try:
                cc =diaspy.people.Contacts(c)
                ccc = cc.get('only_sharing')
            except Exception:
                print "Error: post message"
                continue #Оператор continue начинает следующий проход цикла, минуя оставшееся тело цикла

            for ccrow in ccc:
                #print ccrow['handle']
                curFlwSt = con.cursor(mdb.cursors.DictCursor)
                curFlwSt.execute("INSERT INTO flwrs_sites (address,idst) VALUES(%s,%s) ON DUPLICATE KEY UPDATE idst=%s", (ccrow['handle'], row['id'], row['id']))
                amntFlwrs+=1

            print "--- %s --- №%s" % (amntFlwrs, row['id'])
            curPostEx = con.cursor(mdb.cursors.DictCursor)
            curPostEx.execute("UPDATE sites SET followers = %s WHERE id = %s", (amntFlwrs, row['id']))
    return True

timeOut = 3*60*60
iStep = 1
while True:
    if activBotAmntFollowers()==True:
        print "Ok"
    else:
        print "ERROR"
    print iStep
    iStep += 1
    print "...pause 3h..."
    time.sleep(timeOut)
#activBotAmntFollowers()