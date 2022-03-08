# Yesterlinks

Yesterlinks is a database of links to unique or interesting websites.

This is a project, in its infancy, designed to be a directory of links of various websites as well as a 'random page' button that loads a link at random.

It's currently being designed and written with PHP/SQL and Javascript.

If you want to contribute to this project, please go ahead! If you're not tech-savvy I'll be including a description of how it works, and welcome all ideas.

# Goals

The goal of this project is to create a database of links to cool or interesting lesser-known websites. It is meant to encourage people to "bloomscroll" - the opposite of doomscrolling. 

# Demo

You can play with a live demo at [https://miau.sadgrl.online/yesterlinks/](https://miau.sadgrl.online/yesterlinks/)

# The Webpages

Currently there are 2 pages:
- Home (view only, public)
- Edit (requires password, can edit/delete entries)

# The Database

The database is the back-end which stores the data. Currently, the database exists of three columns:

| id | title | url | description | category |

# How to Contribute

Don't know how to code? No problem! We are looking for all sorts of assistance here. If you play around with the demo and you have some feedback, please reach out! You can open an Issue on here, drop by the Discord server, or email me at sadgrl@riseup.net

Here are some ideas of what I need assistance with:
- I would love to create a relational database with a fully featured tagging system but I'm afraid this is beyond my particular skills right now. Even if you don't have time to build one, I greatly appreciate any offers for help with figuring out how to do this!
- New category ideas? 
- Some way to encourage a better user experience 'surfing' through webpages. Either that, or a way to programmatically determine whether a link in the list has 1) CORS enabled and/or 2) is http:// only so as to "filter" the iframe-friendly videos for a 'surf' feature.


# How to Clone & Make a Copy

Detailed instructions coming soon!

# To Do List
- Create authentication (user/admin accounts)
- Set up approval system.
- Figure out how to do a public ranking/comment system?