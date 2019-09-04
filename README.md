# Employees calendar

## Installation
1. Clone project and move inside new folder
        
        git clone git@github.com:AQROBY/employees-calendar.git
        cd employees-calendar
        
2. Run composer install

        composer install
        
3. Add default data

        php bin/console add-default-data

## Available commands
1. Add admin user

        php bin/console add-admin-user <email> <password>
        
## Google API Configuration
1. Go to https://console.developers.google.com.
2. Select Credentials tab.
3. Select Oauth Client ID under Create credentials dropdown button.
4. Select Web Application option, give a name to your app, and:
    * add you vhost domain name (use https) to the Authorized JavaScript origins.
    * add https://<YOUR-DOMAN-NAME>/connect/google/check.
5. Click edit OAuth Client on your web app in Credentials tab
6. Add to your .env.local file your client id and client secret:    
      
        OAUTH_GOOGLE_CLIENT_ID=<your_client_id>
        OAUTH_GOOGLE_CLIENT_SECRET=<your_client_secret> 
7. Click on the OAuth Consent Screen, and add your vhost domain name to Authorized domains.
8. Set your Application Homepage link.

## Configure email sender name
1. In .env.local set the desired sending email address.

        EMAIL_SENDING_ADDRESS=<example@gmail.com>

