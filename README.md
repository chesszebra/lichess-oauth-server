# lichess-oauth-server

An OAuth server application that handles incoming OAuth requests 
for Lichess.

## Installation

All commands are meant to be ran from the root of your project directory.

Clone the repository to your favourite destination path.

```
git clone https://github.com/chesszebra/lichess-oauth-server.git
```

From here choose your preferred way of working.

### Docker

The OAuth server requires a public and private key, let's create them:

```
openssl genrsa -out data/private.key 2048
openssl rsa -in data/private.key -pubout -out data/public.key
```

We also need an encryption key, let's create it:

```
docker run --rm -it -v $(pwd):/data chesszebra/php:7.2-cli -r 'echo base64_encode(random_bytes(32)), PHP_EOL;'
```

## Configuration

Enter the correct values inside the configuration files we are about
to create now. The configuration files should be self explanatory.

Create the local configuration file used to run the application:

```
cp config/autoload/local.php.dist config/autoload/local.php
```

### Cache

**NOTE:** It could be that a config cache is created, make sure
to remove `data/config-cache.php` in order for the config to be reloaded.

### Database

Depending on your preferred storage system, either enable the PDO or MongoDB dependencies.
Also enter the correct database information.

### Templates

If you want to override the HTML template, create a new Twig template
`templates/app/oauth-authorize-custom.html.twig`. For an example, have a 
look at `templates/app/oauth-authorize.html.twig`. If the custom template 
does not exists, the application will automatically fallback to 
`templates/app/oauth-authorize.html.twig`. 

### Development

If you are developing the application, it might be wise to enable
the development configuration files. This will make development easier.

```
cp config/development.config.php.dist config/development.config.php
cp config/autoload/development.local.php.dist config/autoload/development.local.php
```

## Demo Client

One could create a file `public/demo-client.php` or simply copy 
`examples/demo-client.php` to test the server.
