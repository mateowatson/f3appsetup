# F3 App Setup

Starter files and boilerplate for making a web app with the [Fat Free Framework](https://fatfreeframework.com/). Comes with a users table with sign up, log in, log out, and a variety of options available in `setup-example.cfg`, which should be copied to a not-git-tracked `setup.cfg`.

## Overview

Clone and run `composer install` from the `protected` directory, assuming you have Composer installed on your computer.

Copy `setup-example.cfg` in the `protected` directory to a file named `setup.cfg` and add the database creds for your SQL instance. It needs just an empty database. Also add site name, site url, and, optionally, timezone and email service features like [Mailgun](https://www.mailgun.com/). The example cfg file has further explanations for each of the variables you can set.

The files in `public` need to be served from your web root, or a subdirectory in web root (**subdirectory installation has not been tested at this time**). This is called `public` or `public_html` on many hosting providers. The `protected` directory should be placed one level above the publically acessible web directory. If you put it in a directory other than that and/or rename `protected`, you will need to change the require statement in `public/index.php` to match. (The name "protected," for what it's worth, comes from the fact that we are [NearlyFreeSpeech.Net](https://www.nearlyfreespeech.net/) (NFSN) fans, and that is the directory they provide to place files protected from web access.)

## Directory Structure

- `protected`: The directories and files in this directory should be added to your server in a directory not available to the web, such as NearlyFreeSpeech.net's `protected` directory. That is why it is called "protected" but if you put these files in a directory named something else, you will need to update `/public/index.php` appropriately.
    - `locale`: Where translations go. All strings in the boilerplate meant for frontend users are wrapped in the \_() function (aka gettext), and doing this is encouraged if you will want to do translations later.
    - `migrations`: Nothing fancy here, just a place to add sql files. You can either continue this way, running your sql files "manually" on your database instance, or you can go through the trouble of using a PHP database migration library.
    - `src`: This is where the composer autoload settings point. This contains the core of your application.
        - `Controller`: These files are the classes that directly control the get and post requests, as defined in the `/protected/routes.cfg` file. Each Controller class you write should extend the `Main` class or one of the classes located in `Middleware`.
            - `Middleware`: Extend from `Guest` to block a page from logged-in users. Extend from `User` to block pages from non-logged-in users.
        - `Domain`: This is where you can put classes to handle different services or layers of your app, like the one already there, `SMTP.php`.
        - `Model`: These generally should match the tables in your database and extend F3's `DB\SQL\Mapper` class.
        - `views`: These are where you put your F3 "UI" templates. By default, we are using F3's plain php templating, but you could just as well use the F3 templating engine.
            - `partials`: This just contains a default header and footer.
        - `Bootstrap.php`: Bootstraps the app and runs F3. The comments document what this file is doing, and you may need to alter it for your specific needs or add logic that needs to happen before running F3.
    - `composer.json`: This is where F3 is requires and where the `F3AppSetup` namespace is set, pointing to the `src` directory.
    - `composer.lock`: Don't edit this but still commit it to the repo.
    - `routes.cfg`: This is where the default F3 routes are defined, enabling sign ups, log in, etc. Change/add to as needed.
    - `setup-example.cfg`: Don't change this file, but rather copy it to a file named `setup.cfg` and don't track it in git. This is where a lot of options are available, and some are required. Read the comments for each variable.
- `public`: This contains the `index.php` file that points to the `protected` directory, as well as any necessary frontend assets you need, such as javascript and css files. Empty css and js files are included by default but can be deleted or arranged however desired.
- `.gitignore`: Be sure to always ignore the setup.cfg file you create, or any other environment-specific files, especially ones containing sensitive information.
