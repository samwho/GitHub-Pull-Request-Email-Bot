# Git Pull Request Email Bot

## What is this?

The Git Pull Request Email Bot is a simple project that sends emails to a
specified address every time a repository of your choice gets a pull request.

It works by adding the run.php file to a Cron job and running it fairly often
(though it doesn't really matter how long you leave it, it remembers what
pull request it emailed you about last and won't email duplicates).

It was originally written for the [ThinkUp](http://thinkupapp.com)
project to post pull requests to the mailing list and promote code review.

## How does it work?

There are only a few files that you as a user need to worry about:

config.inc.php - This is where all of the configuration information is stored.
You will need to change this file to make it suit your purpose.

run.php - This is the file you will need to attach to a Cron job. This file is
where the magic happens: pull requests are crawled, the crawler filters out the
ones it has already emailed you about and sends emails about for the new ones.

Template files - All of the template files are stored in the templates/
directory. These handle the presentation of the emails through a placeholder
system. Details of this can be found in templates/template_readme.txt.

## Can I contribute?

Uhm... yeah, sure, I don't see why not. I haven't really prepared the project
for contributions just yet but if you want to browse the code and you see
something you think you could improve then go for it! Fork it, branch it,
pull it :)