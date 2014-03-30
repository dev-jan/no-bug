no-bug FAQ
======

__What is no-bug?__

No-bug is a simple, stylish and feature-rich platform for bugtracking (mainly for software developers).
You can manage the requirements, bugs and suggestings of your project in the no-bug platform.


__How to install no-bug?__

Follow this simple steps:
1. Download the actual version of nobug as ZIP
2. Unzip this zip to your webspace
3. Grant write access to the nobug directory
4. Browse to your webspace with the browser 
5. Follow the steps in the setup


__Can I install no-bug on the webspace of my freehoster?__

Yes, of course. As long as your hoster met our Requirements (php/mySQL).


__I have forgotten my Adminpassword, how can I reset it?__

You need to have access to the database instance of your nobug installation (e.g. with phpMyAdmin) and
execute this query:

````
UPDATE user 
SET 
  password=SHA2('changeMEjzMFHKWvxdfju3SCE5kT4jS4Pvxdq',256),
  salt='jzMFHKWvxdfju3SCE5kT4jS4Pvxdq' 
WHERE id=1 
````

After that you can login with your adminaccount and the password "changeME" (without the quotion marks).
First you should change your password!


__How can I update my no-bug platform?__

Download the newest nobug zip and upload it (Administration -> Global Settings -> Check for updates)