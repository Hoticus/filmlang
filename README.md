# Filmlang

## Getting Started

> If not already done, install [Docker Compose](https://docs.docker.com/compose/install/) and [Make](https://www.gnu.org/software/make/)

Use `make build` to build fresh images

Use `make up` to start the app (no logs).
The app will be available at https://localhost

Use `make logs` to open live logs

Use `make down` to stop the app

## Getting executables

Use `$(make _composer)` to get composer

Use `$(make _yarn)` to get yarn

Use `$(make _php)` to get php

Use `$(make _symfony)` to get symfony console

## Development

After starting the app you are able to open: 
- [Adminer](https://www.adminer.org/) at http://localhost:8080.

    To open symfony database use:
    ```yaml
    System: PostgreSQL
    Server: database
    Username: a
    Password: a
    ```
- [MailCatcher](https://mailcatcher.me/) at https://localhost:1080
