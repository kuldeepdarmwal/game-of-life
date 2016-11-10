# Place any notes or comments you have here
# It scott make any customisation easier to understand in the weeks to come

# domain: domain1.com
# public: /home/demo/public_html/domain1.com/

<VirtualHost *:80>

  # Admin email, Server Name (domain name) and any aliases
  ServerAdmin webmaster@vantagelocal.com
  ServerName  christopher.vantagelocaldev.com
  ServerAlias *.christopher.vantagelocaldev.com

  <Directory />
        Options FollowSymLinks
        AllowOverride FileInfo
  </Directory>
  <Directory /home/dev_christopher/public/vantagelocal.com>
        Options Indexes FollowSymLinks MultiViews
        AllowOverride FileInfo
        Order allow,deny
        allow from all
  </Directory>


  # Index file and Document Root (where the public files are located)
   DirectoryIndex index.php index.htm index.html
   DocumentRoot /home/dev_christopher/public/vantagelocal.com



  # Custom log file locations
  LogLevel warn
  ErrorLog  /home/dev_christopher/public/log/error.log
  CustomLog /home/dev_christopher/public/log/access.log combined

</VirtualHost>

<VirtualHost _default_:443>

  # Admin email, Server Name (domain name) and any aliases
  ServerAdmin webmaster@vantagelocal.com
  ServerName  christopher.vantagelocaldev.com
  ServerAlias *.christopher.vantagelocaldev.com
  # Add Request header required
#  RequestHeader set X_FORWARDED_PROTO 'https'

  <Directory />
        Options FollowSymLinks
        AllowOverride FileInfo
  </Directory>
 <Directory /home/dev_christopher/public/vantagelocal.com>
        Options Indexes FollowSymLinks MultiViews
        AllowOverride FileInfo
        Order allow,deny
        allow from all
  </Directory>


  # Index file and Document Root (where the public files are located)
   DirectoryIndex index.php
   DocumentRoot /home/dev_christopher/public/vantagelocal.com

  SSLEngine ON
	SSLProtocol All -SSLv2 -SSLv3

   #SSLCertificateFile /home/vladmin/ssl/crt/www.vantagelocalbranding.crt
   #SSLCertificateKeyFile /home/vladmin/ssl/key/www.vantagelocalbranding.com.key
   SSLCertificateFile /home/dev_christopher/public/ssl/scott.vantagelocaldev.com.crt
   SSLCertificateKeyFile /home/dev_christopher/public/ssl/scott.vantagelocaldev.com.key


  # Custom log file locations
  LogLevel warn
  ErrorLog  /home/dev_christopher/public/log/error.log
  CustomLog /home/dev_christopher/public/log/access.log combined

</VirtualHost>

