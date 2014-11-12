#!/usr/bin/env python3


import time

from diaspy.models import Notification


"""This module abstracts notifications.
"""


class Notifications():
    """This class represents notifications of a user.
    """
    def __init__(self, connection):
        self._connection = connection
        self._notifications = self.get()

    def __iter__(self):
        return iter(self._notifications)

    def __getitem__(self, n):
        return self._notifications[n]

    def last(self):
        """Returns list of most recent notifications.
        """
        params = {'per_page': 5, '_': int(round(time.time(), 3)*1000)}
        headers = {'x-csrf-token': repr(self._connection)}

        request = self._connection.get('notifications.json', headers=headers, params=params)

        if request.status_code != 200:
            raise Exception('status code: {0}: cannot retreive notifications'.format(request.status_code))
        return [Notification(self._connection, n) for n in request.json()]

    def get(self, per_page=5, page=1):
        """Returns list of notifications.
        """
        params = {'per_page': per_page, 'page': page}
        headers = {'x-csrf-token': repr(self._connection)}

        request = self._connection.get('notifications.json', headers=headers, params=params)

        if request.status_code != 200:
            raise Exception('status code: {0}: cannot retreive notifications'.format(request.status_code))

        notifications = request.json()
        return [Notification(self._connection, n) for n in notifications]
