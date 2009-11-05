Justin.tv PHP Client Library
============================

How to use:
-----------

How to be authenticated as a user:
   
1. Put your oauth keys in jtv_constants.inc.php
2. Call start_user_authentication, which will recirect the user to Justin.tv to log in. $redirect should be a page on your site that implements step 3.
3. Create a page that calls recieve_user_authentication, which will return true on success.
4. Do authenticated requests with the get and post functions.


See the test directory for an example usage.

