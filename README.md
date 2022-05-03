# Yesterlinks

Yesterlinks is a database of links to unique or interesting websites.

This is a project, in its infancy, designed to be a directory of links of various websites as well as a 'random page' button that loads a link at random.

It's currently being designed and written with PHP/SQL and Javascript.

If you want to contribute to this project, please go ahead! If you're not tech-savvy I'll be including a description of how it works, and welcome all ideas.

## Goals

The goal of this project is to create a database of links to cool or interesting lesser-known websites. It is meant to encourage people to "bloomscroll" - the opposite of doomscrolling.

## New Features

- Category checkbox filters
- Search filter
- Tagging system (thank you @amethystcube !!!!)
- JSON file creation when website is changed
- 'Surf' bookmarket link added (drag it to your bookmarks bar and click to surf!)

## Demo

You can play with a live demo at [https://links.yesterweb.org/](https://links.yesterweb.org/)

## The Webpages

1. Home: `/index.php`
   - public, view only; this is the main page
2. Submit: `/submit-a-link.html`
   - public, anti-bot: submit a link to the directory
3. Admin: `/admin`
   - **requires a password:** the place to edit/delete/approve entries
4. Login: `/login`
   - public: enter credentials for access to `/admin`


## The Database

The database is the back-end which stores the data; it has the following tables:

### Table: `websites`

Each row of the `websites` table has six columns:
1. `id`: unique identifier for each row
2. `title`: website title
3. `url`: website URL
4. `descr`: a description for the website
5. `category`: give the site a category
6. `pending`: boolean; toggle visibility in the public list
   - `0`: false, will be visible
   - `1`: true, will not be visible

### Table: `tags`

Each row of the `tags` table has two columns:
1. `id`: unique identifier for each row
2. `name`: the name of the tag

### Table: `taglist`

Each row of the `taglist` table has 3 columns:
1. `id`: unique identifier for each row
2. `tag_id`: the ID of the tag being added
3. `site_id`: the ID of the site being tagged

This table is added to the results [using a `JOIN` clause](https://learn.sadgrl.online/joining-tables-with-sql/), allowing the tags for each site to be listed in the table

### Table: `votelist`

Each row of the `votelist` table has 5 columns:
1. `id`: unique identifier for each row
2. `time_cast`: a timestamp of when the vote was cast (used for vote weighting)
3. `voter_id`: a hash indicating who cast the vote
4. `site_id`: the ID of the site for which the vote was cast
5. `vote`: a boolean indicating the type of vote
   - `0`: downvote (disabled)
   - `1`: upvote/recommend

### Table: `users`

[Table description in progress]

### Table: `password_reset_temp`

[Table description in progress]

## How to Clone & Make a Copy

1. Download a `.zip` of the repo or [`git clone` it](https://docs.github.com/en/repositories/creating-and-managing-repositories/cloning-a-repository).
2. [Create a database](https://learn.sadgrl.online/create-a-user-and-database-on-leprd/) and [import the schema](https://learn.sadgrl.online/import-a-database-schema-into-phpmyadmin/) from `sadness_websurferdb.sql`
3. Update the `config.php` file:
   - `$host`: (in most cases, you can leave this one alone)
   - `$user`: the account name you set for access to the database
   - `$password`: the password for the account name you just listed
   - `$dbname`: the name of the database with the directory tables
4. If you visit `index.php`, you should see the directory table!

## How to Contribute

Don't know how to code? No problem! We are looking for all sorts of assistance here. If you play around with the demo and you have some feedback, please reach out! You can [open an Issue](https://github.com/sadgrlonline/yesterlinks/issues/new) on here, drop by [the Discord server](https://yesterweb.org/community/), or email me at sadgrl@riseup.net.

## Contributors

- [Sadness](https://sadgrl.online)
- [Amethyst](https://amethystcu.be)
- [your name could go here!]
