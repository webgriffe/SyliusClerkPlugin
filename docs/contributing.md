# Contributing

To contribute you need to:

1. Clone this repository into your development environment

2. [OPTIONAL] Copy the `.env` file inside the test application directory to the `.env.local` file:

   ```bash
   cp tests/Application/.env tests/Application/.env.local
   ```

   Then edit the `tests/Application/.env.local` file by setting configuration specific for you development environment.

3. Then, from the plugin's root directory, run the following commands:

   ```bash
   (cd tests/Application && yarn install)
   (cd tests/Application && yarn build)
   (cd tests/Application && APP_ENV=test bin/console assets:install public)
   (cd tests/Application && APP_ENV=test bin/console doctrine:database:create)
   (cd tests/Application && APP_ENV=test bin/console doctrine:schema:create)
   ```
4. Run test application's webserver on `127.0.0.1:8080`:

      ```bash
      symfony server:ca:install
      APP_ENV=test symfony server:start --port=8080 --dir=tests/Application/public --daemon
      ```

4. Now at https://127.0.0.1:8080/ you have a full Sylius testing application which runs the plugin

### Testing

After your changes you must ensure that the tests are still passing. The current CI suite runs the following tests:

* Easy Coding Standard

  ```bash
  vendor/bin/ecs check src/ tests/Behat/
  ```

* PHPStan

  ```bash
  vendor/bin/phpstan analyse -c phpstan.neon -l max src/
  ```

* PHPUnit

  ```bash
  vendor/bin/phpunit
  ```

* PHPSpec

  ```bash
  vendor/bin/phpspec run
  ```

* Behat

  ```bash
  vendor/bin/behat --strict -vvv --no-interaction || vendor/bin/behat --strict -vvv --no-interaction --rerun
  ```

To run them all with a single command run:

```bash
composer suite
```

To run Behat's JS scenarios you need to setup Selenium and Chromedriver. Do the following:

1. [Install Symfony CLI command](https://symfony.com/download).

2. Start Headless Chrome:

      ```bash
      google-chrome-stable --enable-automation --disable-background-networking --no-default-browser-check --no-first-run --disable-popup-blocking --disable-default-apps --allow-insecure-localhost --disable-translate --disable-extensions --no-sandbox --enable-features=Metal --headless --remote-debugging-port=9222 --window-size=2880,1800 --proxy-server='direct://' --proxy-bypass-list='*' http://127.0.0.1
      ```

4. Remember that the test application webserver must be up and running as described above:

      ```bash
      symfony server:ca:install
      APP_ENV=test symfony server:start --port=8080 --dir=tests/Application/public --daemon
      ```
