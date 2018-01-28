# lichess-oauth-server

An OAuth server application that handles incoming OAuth requests 
for Lichess.

## Installation

All commands are meant to be ran from the root of your project directory.

### Using Docker

Clone the repository to your favourite destination path.

```
git clone https://github.com/chesszebra/lichess-oauth-server.git
```

Install the PHP dependencies via Docker:

```
docker run --rm -it -v $(pwd):/data chesszebra/composer:7.0 install
```

Create the local configuration file used to run the application:

```
cp config/autoload/local.php.dist config/autoload/local.php
```

The OAuth server requires a public and private key, let's create them:

```
openssl genrsa -out private.key 2048
openssl rsa -in data/private.key -pubout -out data/public.key
```

We also need an encryption key, let's create it:

```
docker run --rm -it -v $(pwd):/data chesszebra/php:7.0-cli -r 'echo base64_encode(random_bytes(32)), PHP_EOL;'
```

Now enter the correct values inside the newly configuration files. 
The configuration files should be self explanatory.

**NOTE:** It could be that a config cache is created, make sure
to remove `data/config-cache.php`.

### Developers

For developers also create the development configuration files:

```
cp config/development.config.php.dist config/development.config.php
cp config/autoload/development.local.php.dist config/autoload/development.local.php
```
