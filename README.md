# Yesterlinks

Yesterlinks is a database of links to unique or interesting websites.

This is a project, in its infancy, designed to be a directory of links of various websites as well as a 'random page' button that loads a link at random.

It's currently being designed and written with PHP/SQL and Javascript.

If you want to contribute to this project, please go ahead! If you're not tech-savvy I'll be including a description of how it works, and welcome all ideas.

# New Features
- Category checkbox filter
- Search filter
- Tagging system (thank you @amethystcube !!!!)
- JSON file creation when website is changed
- 'Surf' bookmarket link added (drag it to your bookmarks bar and click to surf! the file it references updates the db accordingly)

# Goals

The goal of this project is to create a database of links to cool or interesting lesser-known websites. It is meant to encourage people to "bloomscroll" - the opposite of doomscrolling. 

# Demo

You can play with a live demo at [https://links.yesterweb.org/](https://links.yesterweb.org/)

# The Webpages

Currently there are 3 pages:
- Home (view only, public) /index.php
- Submit (public, anti-bot) /submit-a-link.html
- Admin (requires password, can edit/delete/approve entries) /admin/


# The Database

The database is the back-end which stores the data. Currently, the database exists of five columns:

| id | title | url | descr | category | pending

# How to Contribute

Don't know how to code? No problem! We are looking for all sorts of assistance here. If you play around with the demo and you have some feedback, please reach out! You can open an Issue on here, drop by the Discord server, or email me at sadgrl@riseup.net

# How to Clone & Make a Copy

Detailed instructions coming soon!

# To Do List
- Add category descriptions
- Create authentication (user/admin accounts)
- Figure out how to do a public ranking/comment system?
- Test on mobile
- Update demo