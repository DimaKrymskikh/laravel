#!/bin/bash

set -e

echo "Запущен скрипт $0"

db_testing="${POSTGRES_DB}_testing"
password_testing="'\$2y\$10\$Wu0xePJcm5Rt1A7Jy8LCaemP5tOC0932h78YUCEwooD1Xjnd9TRRS'"

echo "Подключение к базе $POSTGRES_DB"
psql -v ON_ERROR_STOP=1 -U "$POSTGRES_USER" -d "$POSTGRES_DB" <<-EOSQL
    \echo "Создание таблиц в базе $POSTGRES_DB"
    \i './home/create_tables.sql'

    \echo "Заполнение таблицы thesaurus.languages"
    \copy thesaurus.languages(id, name) FROM 'home/languages.csv'

    \echo "Заполнение таблицы dvd.films"
    \copy dvd.films(id, title, description, release_year, language_id) FROM 'home/films.csv'

    \echo "Заполнение таблицы dvd.actors"
    \copy dvd.actors(id, first_name, last_name) FROM 'home/actors.csv'

    \echo "Заполнение таблицы dvd.films_actors"
    \copy dvd.films_actors(actor_id, film_id) FROM 'home/films_actors.csv'

    \echo "Создание тестовой базы" 
    CREATE DATABASE $db_testing;

    \echo "Отключение от базы $POSTGRES_DB"
    \q
EOSQL

echo "Подключение к базе $db_testing"
psql -U "$POSTGRES_USER" -d "$db_testing" <<-EOSQL
    \echo "Создание таблиц в базе $db_testing"
    \i './home/create_tables.sql'

    \echo "Заполнение таблицы thesaurus.languages"
    \copy thesaurus.languages(id, name) FROM 'home/languages.csv'

    \echo "Заполнение таблицы dvd.films"
    \copy dvd.films(id, title, description, release_year, language_id) FROM 'home/films.csv'

    \echo "Заполнение таблицы dvd.actors"
    \copy dvd.actors(id, first_name, last_name) FROM 'home/actors.csv'

    \echo "Заполнение таблицы dvd.films_actors"
    \copy dvd.films_actors(actor_id, film_id) FROM 'home/films_actors.csv'

    \echo "Добавление пользователя BaseTestLogin" 
    INSERT INTO person.users (login, password) VALUES ('BaseTestLogin', $password_testing);

    \echo "Отключение от базы $db_testing"
    \q
EOSQL
