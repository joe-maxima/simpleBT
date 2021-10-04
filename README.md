# simpleBT
SImple Bug Tracker

## Development Background

I used to handle bug tickets in an Excel file, but it was very inconvenient, so I made one.
I know there are some excellent bug tracking systems out there,
However my boss boss couldn't figure out the structure of the bug tracking system, so I made this.
About 20 hours of development time, including maintenance.


## Overview

This system uses 8 tables.

This system is built with php and smarty.
However, it does not have a maintenance screen for data that is rarely maintained.

Following is functions:
- Registering product defects as the ticket.
- Approve whether or not to respond to a ticket.
- Update status of response to tickets.
- Post a comment.

Available for PHP 7.4.3 and MySQL 8.0.26-0ubuntu0.20.04.2
(But it will work with older versions too)


## CAUTION

The password used to log in is ** not hash-encrypted **.
Therefore, you should not put this system in a place where it can be used by anyone.

Initially, this was written in Japanese.
I have rewritten it in English as best I can, but there may still be some Japanese in there somewhere.


## Author

@joe-maxima
