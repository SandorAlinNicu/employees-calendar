# Employees calendar

## Installation
1. Clone project and move inside new folder
        
        git clone git@github.com:AQROBY/employees-calendar.git
        cd employees-calendar
        
2. Run composer install

        composer install
        
3. Local configuration in .env.local file
            
        
        # Database configuration        
        DATABASE_URL=mysql://{db_user}:{db_password}@127.0.0.1:3306/{db_name}
        
        # Google configuration
        OAUTH_GOOGLE_CLIENT_ID=<your_client_id>
        OAUTH_GOOGLE_CLIENT_SECRET=<your_client_secret>
        
        # Sender email address
        EMAIL_SENDING_ADDRESS=<example@gmail.com>
        MAILER_URL=smtp://localhost:25
        
4. Create database and tables
                   
          php bin/console doctrine:database:create
          php bin/console doctrine:schema:update --force
        
5. Add default data

        php bin/console add-default-data
        

        
## Vhost configuration


    <VirtualHost *:80>
    DocumentRoot "path\to\your\project"
    ServerName employees-calendar.com
    <Directory "path\to\your\project"
        AllowOverride All
        Require all granted
    </Directory>
    </VirtualHost>

    <VirtualHost *:443>
    DocumentRoot "path\to\your\project"
    ServerName employees-calendar.com
        SSLEngine On
    SSLCertificateFile "conf/ssl.crt/server.crt"
    SSLCertificateKeyFile "conf/ssl.key/server.key"
    <Directory "path\to\your\project"
        AllowOverride All
        Require all granted
    </Directory>
    </VirtualHost>
    
## Available commands
1. Add admin user


    php bin/console add-admin-user <email> <password>
        
2. Add/update default data 


    php bin/console add-default-data       
        
## Google API Configuration
1. Go to https://console.developers.google.com.
2. Select Credentials tab.
3. Select Oauth Client ID under Create credentials dropdown button:
    * select Web application option
    * add you vhost domain name (use https) to the Authorized JavaScript origins.
    * add https://YOUR-DOMAIN-NAME/connect/google/check to Authorized redirect URIs.
4. Click on the OAuth Consent Screen, and add:
    * application name (hint: you domain name).
    * your vhost domain name to Authorized domains.
    * your vhost domain name to Application Homepage link with (use https).
5. Click edit OAuth Client on your web app in Credentials tab, there you should have the Client ID and Secret.
6. Add to your .env.local file your client id and client secret:    
      
       OAUTH_GOOGLE_CLIENT_ID={your_client_id}
       OAUTH_GOOGLE_CLIENT_SECRET={your_client_secret} 
7. Click on the OAuth Consent Screen, and add your vhost domain name to Authorized domains.
8. Set your Application Homepage link.
