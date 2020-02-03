# Auth Service

REST API service for user authorization

* Service can work on several replicas (k8s) with load balancers (autoscaling).
* Email sending command can work in several parallel flows.

## Requirements

* PHP >= 7.2
* PHP extensions: mongodb
* Composer
* MongoDB
* One email provider from list (smtp, sendmail, mailgun, ses, postmark, log) 

## Configuration

* Copy `.env.example` file to `.env`
* Edit `.env` to configure app, db connection, jwt secret, mail

## Install and Run

```sh
composer install

php artisan migrate

php -S localhost:8000 -t public

```

## Run tests

```sh
vendor/bin/phpunit
```


## API endpoints

Default HOST: localhost:8000

```
POST /api/v1/user/register
POST /api/v1/user/activate
POST /api/v1/user/auth
```

### POST /api/v1/user/register
Request for user registration

**Example request:**
```json
{
    "email": "user@example.com",
    "password": "examplepassword"
}
```

*Example response:*
HTTP Status: 200
```json
{
    "success": true,
    "message": {
        "email": "user@example.com",
        "updated_at": "2020-02-03 12:50:28",
        "created_at": "2020-02-03 12:50:28",
        "_id": "5e381714ae94e249652497bc"
    }
}
```
OR HTTP Status: 422, 500, ...
```json
{
    "success": false,
    "message": "The given data was invalid.",
    "errors": {
        "email": [
            "validation.unique"
        ]
    }
}
```

### POST /api/v1/user/activate
Request for user activation

**Example request:**
```json
{
	"email_hash": "1f60448c5a74863d96a9db3be33b9c0116c101b882585db4f872a6168e167f2a"
}
```

*Example response:*
HTTP Status: 200
```json
{
    "success": true
}
```
OR HTTP Status: 422, 500, ...
```json
{
    "success": false,
    "message": "The given data was invalid.",
    "errors": {
        "email_hash": [
            "validation.exists"
        ]
    }
}
```

### POST /api/v1/user/auth
Request for user authentication

**Example request:**
```json
{
    "success": true,
    "message": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJhdXRoLWp3dCIsInN1YiI6IjVlMzgzM2JkYWU5NGUyNDk2NTI0OTdiZSIsImlhdCI6MTU4MDc0MTg2NywiZXhwIjoxNTgwNzQxODY3fQ.qXbq_0a4FGKUSmo9uroU3fliDyBiL2dGwpEWGpmlTQA"
}
```

*Example response:*
HTTP Status: 200
```json
{
    "success": true,
    "message": {
        "email": "user@example.com",
        "updated_at": "2020-02-03 12:50:28",
        "created_at": "2020-02-03 12:50:28",
        "_id": "5e381714ae94e249652497bc"
    }
}
```
OR HTTP Status: 422, 500, ...
```json
{
    "success": false,
    "message": "The given data was invalid.",
    "errors": {
        "email": [
            "validation.email"
        ]
    }
}
```
```json
{
    "success": false,
    "message": "Internal Server Error",
    "errors": "not_found"
}
````
```json
{
    "success": false,
    "message": "Internal Server Error",
    "errors": "not_activated"
}
```

## Commands 

### email-queue:send {limit} {sleep} 

Command with default values `limit=100`, `sleep=1` sec

```sh
php artisan email-queue:send
```

Custom command
```sh
php artisan email-queue:send 10 2
```

Email example:
```html
Subject: Activation email
From: auth <auth@example.com>
To: user@example.com

Activation code: 6ad22f2c91983bc2a4c89fb6c0bf4c4427ca67ca0224bb467524547b00f6e7e5        
```

### Nice logs with time
![](docs/images/log_info.png)
![](docs/images/log_error.png)
![](docs/images/log_warn.png)

## Notes
1. I use readme for service documentation. Also I can use some service for auto-generation documentation (Swagger, ApiDoc, PhpDoc). 
2. I use firebase/php-jwt for generation expired tokens 
3. I do request validation in controllers (it's common solution). So tests for validation should be in controller tests. If it's important validation can be moved inside models.   
4. Email queue can be configured with expired records from mongodb (expireAfterSeconds). (ex. for clear queue from old records)   
5. Service sends only new emails. It is possible to implement logic for resending emails with errors.
6. Email activation kod is not expiring. For expired kod I can store expire time to db or use expired token.
7. It's nice to have registration captcha for filtering bots.
8. I can create dockerfile, k8s config, CI config. I spent free time to write docs, check and clean code.



  
