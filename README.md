
Description:
    Locating mobile nodes using tower ids which translate to lat-long using Google’s MyLocation API (with Facebook Connect)

Directroy Structur/Flow:
    mobileproject   -   Contains code for mobile phone
        README      -   Installation steps for phone

    Developement    -   Contains some initial prototypes and other useful information that 
                        may help to understand the system better. 
    
    Diagram         -   The picture describes the working of system at higher level.

    socialmaps      -   The Actual code that will run on server.


You need to change the following entries with your own values in order to make it work like a complete system.
A simple grep may suffice.

<example.domain.net> - grep recursively and replace with your domain name(including <>s)
example@gmail.com - grep recursively and replace with your email id.
define('username', '<db_user>'); - grep and replace db_user with your database user name.
define('password', '<db_passwd>') - grep and replace db_passwd with your database user password. 
<app_id> - Replace with your own App Id.
<client_Token> - Replace with correct Client Token.

Platform: Google Location API (LBS without GPS based on GSM Location),PHP, Python, Symbian S60, 
          Google Maps APIv3 with Facebook Connect

