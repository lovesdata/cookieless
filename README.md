# Google Analytics Cookieless Tracking

This is a PHP script that allows you to track into Google Analytics even if JavaScript and cookies are disabled. It replicates the Google Analytics tracking code and sends data to Google Analytics. If JavaScript and cookies are disabled it used a browser fingerprint.

Place the files on your server and edit /track/config.php to add your Google Analytics property ID.

There is also an option to send hits to your own MySQL database.
