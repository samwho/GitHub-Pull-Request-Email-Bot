The templating in this request bot is a very simple placeholder replacement
system. Nothing fancy, nothing like Smarty templates, just placeholder
replacement.

It is, however, pretty customisable. By default, placeholders are delimited
[+like this+]. So the token there is the "like this" and the "delimiters" are
the square brackets and the plus symbols.

All template parsing happens in classes/class.TemplateParser.php. In this class
you can change the delimiters and you can change the tokens (if you want to).

The currently available tokens are:

'title' - pull request title
'user_real_name' - the full, real name of the user who issued the pull.
'user_login' - the login name of the user who issued the pull.
'gravatar_id' - the id of the user's gravatar.
'gravatar' - an img html tag containing the gravatar url and alt text.
'created_at' - the date and time that the pull was created.
'body' - the contents of the pull request body.
'link' - a link to the pull request. Not in an <a> tag, just a URL.

These placeholders persist through all .tpl files apart from
'group_request_email.tpl' that only has one placeholder and that is the
"pull_request" token that contains all pull requests after being parsed through
the 'pull_request_group.tpl' template.

Current .tpl files available:

'pull_request_single.tpl' - this is used when you are sending an individual
email for each pull request the crawler finds. In the config file, if you
set the 'group_requests' to false, this is the .tpl file that will be used.

'pull_request_group.tpl' - If you set the 'group_requests' value to true in the
config file, this .tpl is used FOR EACH PULL REQUEST. It is for a SINGLE PULL
REQUEST. All pull requests will be formatted like this. Look at
'group_request_email.tpl' for the formatting of the entire email.

'pull_request_single_subject_line.tpl' - This is the email subject line for
single pull request emails. 