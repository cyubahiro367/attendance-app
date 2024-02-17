# Attendance Application

### Description

Attendance Application is an aplication which is used in employee management where helps them to do attendance when they arrives at work and when theyleave.

### Setup

These are the procedures to follow when setting up Attendance Application for the first time using sail with docker

-   Copy the .env.example into .env
    ```shell
    cp .env.example .env
    ```
-   Copy the .env.testing.example into .env.testing and un-comment the `Sail` section then comment the `Github actions` section

    ```shell
    cp .env.testing.example .env.testing
    ```

-   Install composer dependencies
    ```shell
    docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php81-composer:latest \
    composer install --ignore-platform-reqs
    ```

- Build the project **(keep this running in a tab in order to get access to the sail command)**
    ```shell
    vendor/bin/sail up
    ```
- Generate the application key
    ```shell
    vendor/bin/sail artisan key:generate
    ```
- Run migrations
    ```shell
    vendor/bin/sail artisan migrate:fresh
    ```
