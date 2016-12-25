# Slim Framework 3 Skeleton Application

Package with an easy-to-start skeleton of Slim Framework 3 application, with use of RequireJS, Gulp and EJS templates.

## Quick description

Project contains few directories:

* **app** - contains all PHP server files
* **resources** - contains global js, css files, which are then compiled to a **public** directory by gulp.
* **views** - contains all views (html) files and also EJS templates and local CSS/JS files. Every view can be found in it's own directory, where
should always be an index.twig file and also js and css directories (with local JS, CSS files). Additionally, one can create subdirectory called **precompiled_templates**
with .ejs files in it. Using _gulp templates_ command will compile those .ejs files into .jst files in **templates** subdirectory.
