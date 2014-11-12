#!/usr/bin/env python
# -*- coding: utf-8 -*-

#ver:2014.03.07-2

import diaspy

c = diaspy.connection.Connection("https://joindiaspora.com", "**!**", "**!**")
c.login()
#stream = diaspy.streams.Stream(c)

# Показываем мою ленту моих сообщений. Пригодится для создания блогов или встраиваемых модулей на сайты
stream = diaspy.streams.Activity(c)
for post in stream:
    print u"%s" % (post)