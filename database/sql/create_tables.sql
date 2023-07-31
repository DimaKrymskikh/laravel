
-----------------------------------------------------------------------------------------------------------------------------------------------------
-- Создание схем
-- В схеме dvd будут таблицы для фильмов
-- В схеме person будут таблицы для пользователей
-- В схеме thesaurus будут таблицы общего назначения
-----------------------------------------------------------------------------------------------------------------------------------------------------
CREATE SCHEMA dvd;
CREATE SCHEMA person;
CREATE SCHEMA thesaurus;
CREATE SCHEMA open_weather;
-----------------------------------------------------------------------------------------------------------------------------------------------------


/****************************************************************************************************************************************************/
/********** Схема thesaurus    **********************************************************************************************************************/
/****************************************************************************************************************************************************/

-----------------------------------------------------------------------------------------------------------------------------------------------------
-- Функция триггера, обновляющая поле updated_at таблиц
-----------------------------------------------------------------------------------------------------------------------------------------------------
CREATE OR REPLACE FUNCTION thesaurus.last_updated()
 RETURNS trigger
 LANGUAGE plpgsql
AS 
$function$
BEGIN
    NEW.updated_at = now();
    RETURN NEW;
END 
$function$
;

COMMENT ON FUNCTION thesaurus.last_updated IS
    'Функция триггера, обновляющая поле updated_at таблиц';
-----------------------------------------------------------------------------------------------------------------------------------------------------


-----------------------------------------------------------------------------------------------------------------------------------------------------
CREATE TABLE thesaurus.languages (
    id serial PRIMARY KEY,
    name text NOT NULL,
    created_at timestamptz NOT NULL DEFAULT now(),
    updated_at timestamptz NOT NULL DEFAULT now()
);

CREATE TRIGGER last_updated BEFORE
UPDATE ON thesaurus.languages
FOR EACH ROW EXECUTE FUNCTION thesaurus.last_updated();

COMMENT ON TABLE thesaurus.languages IS
    'Языки народов';

COMMENT ON COLUMN thesaurus.languages.name IS 
    'Название';

COMMENT ON COLUMN thesaurus.languages.created_at IS 
    'Время создания записи';

COMMENT ON COLUMN thesaurus.languages.updated_at IS 
    'Время последнего изменения записи';
-----------------------------------------------------------------------------------------------------------------------------------------------------


-----------------------------------------------------------------------------------------------------------------------------------------------------
CREATE TABLE thesaurus.cities (
    id serial PRIMARY KEY,
    name text NOT NULL,
    open_weather_id bigint NOT NULL UNIQUE,
    created_at timestamptz NOT NULL DEFAULT now(),
    updated_at timestamptz NOT NULL DEFAULT now()
);

CREATE TRIGGER last_updated BEFORE
UPDATE ON thesaurus.cities
FOR EACH ROW EXECUTE FUNCTION thesaurus.last_updated();

COMMENT ON TABLE thesaurus.cities IS
    'Города';

COMMENT ON COLUMN thesaurus.cities.name IS 
    'Название';

COMMENT ON COLUMN thesaurus.cities.open_weather_id IS 
    'ID города в службе OpenWeather';

COMMENT ON COLUMN thesaurus.cities.created_at IS 
    'Время создания записи';

COMMENT ON COLUMN thesaurus.cities.updated_at IS 
    'Время последнего изменения записи';
-----------------------------------------------------------------------------------------------------------------------------------------------------


-----------------------------------------------------------------------------------------------------------------------------------------------------
CREATE TABLE thesaurus.failed_jobs (
    id bigserial PRIMARY KEY NOT NULL,
    uuid varchar(255) UNIQUE NOT NULL,
    "connection" text NOT NULL,
    queue text NOT NULL,
    payload text NOT NULL,
    "exception" text NOT NULL,
    failed_at timestamp(0) NOT NULL DEFAULT CURRENT_TIMESTAMP
);
-----------------------------------------------------------------------------------------------------------------------------------------------------

/****************************************************************************************************************************************************/
/*****************************************************************************************************************************************************/


-----------------------------------------------------------------------------------------------------------------------------------------------------
CREATE TABLE dvd.films (
    id bigserial PRIMARY KEY,
    title text NOT NULL,
    description text,
    release_year int,
    language_id int REFERENCES thesaurus.languages(id),
    created_at timestamptz NOT NULL DEFAULT now(),
    updated_at timestamptz NOT NULL DEFAULT now()
);

CREATE TRIGGER last_updated BEFORE
UPDATE ON dvd.films
FOR EACH ROW EXECUTE FUNCTION thesaurus.last_updated();

COMMENT ON TABLE dvd.films IS
    'Художественные фильмы';

COMMENT ON COLUMN dvd.films.title IS 
    'Название фильма';

COMMENT ON COLUMN dvd.films.description IS 
    'Краткое описание фильма';

COMMENT ON COLUMN dvd.films.release_year IS 
    'Год выхода фильма';

COMMENT ON COLUMN dvd.films.language_id IS 
    'Язык, на котором снят фильм';

COMMENT ON COLUMN dvd.films.created_at IS 
    'Время создания записи';

COMMENT ON COLUMN dvd.films.updated_at IS 
    'Время последнего изменения данных о фильме';
-----------------------------------------------------------------------------------------------------------------------------------------------------


-----------------------------------------------------------------------------------------------------------------------------------------------------
CREATE TABLE dvd.actors (
    id bigserial PRIMARY KEY,
    first_name text NOT NULL,
    last_name text NOT NULL,
    created_at timestamptz NOT NULL DEFAULT now(),
    updated_at timestamptz NOT NULL DEFAULT now()
);
CREATE INDEX ON dvd.actors(last_name);

CREATE TRIGGER last_updated BEFORE
UPDATE ON dvd.actors
FOR EACH ROW EXECUTE FUNCTION thesaurus.last_updated();

COMMENT ON TABLE dvd.actors IS
    'Актёры';

COMMENT ON COLUMN dvd.actors.first_name IS 
    'Имя актёра';

COMMENT ON COLUMN dvd.actors.last_name IS 
    'Фамилия актёра';

COMMENT ON COLUMN dvd.actors.created_at IS 
    'Время создания записи';

COMMENT ON COLUMN dvd.actors.updated_at IS 
    'Время последнего изменения данных об актёре';
-----------------------------------------------------------------------------------------------------------------------------------------------------


-----------------------------------------------------------------------------------------------------------------------------------------------------
CREATE TABLE dvd.films_actors (
    film_id int REFERENCES dvd.films(id),
    actor_id int  REFERENCES dvd.actors(id),
    created_at timestamptz NOT NULL DEFAULT now(),
    PRIMARY KEY (film_id, actor_id)
);
CREATE INDEX ON dvd.films_actors(film_id);

COMMENT ON TABLE dvd.films_actors IS
    'Таблица, связывающая таблицы dvd.films и dvd.actors';

COMMENT ON COLUMN dvd.films_actors.created_at IS 
    'Время создания записи';
-----------------------------------------------------------------------------------------------------------------------------------------------------



/****************************************************************************************************************************************************/
/**********    Схема person    **********************************************************************************************************************/
/****************************************************************************************************************************************************/

-----------------------------------------------------------------------------------------------------------------------------------------------------
CREATE TABLE person.users(
    id bigserial PRIMARY KEY,
    login text NOT NULL,
    password text NOT NULL,
    created_at timestamptz not null DEFAULT now(),
    updated_at timestamptz not null DEFAULT now()
);

CREATE UNIQUE INDEX ON person.users(login);

CREATE TRIGGER last_updated 
BEFORE UPDATE ON person.users
FOR EACH ROW EXECUTE FUNCTION thesaurus.last_updated();

COMMENT ON TABLE person.users IS
    'Пользователи';

COMMENT ON COLUMN person.users.login IS 
    'Логин';

COMMENT ON COLUMN person.users.password IS 
    'Пароль';

COMMENT ON COLUMN person.users.created_at IS 
    'Время создания аккаунта';

COMMENT ON COLUMN person.users.updated_at IS 
    'Время последнего изменения аккаунта';

ALTER TABLE person.users ADD CHECK (login ~ '^[A-Z]\w{3,17}$');

ALTER TABLE person.users ADD COLUMN email text;
ALTER TABLE person.users ADD CONSTRAINT users_email_unique UNIQUE (email);
ALTER TABLE person.users ALTER COLUMN email SET NOT NULL;

ALTER TABLE person.users ADD COLUMN is_admin boolean DEFAULT false;
ALTER TABLE person.users ALTER COLUMN is_admin SET NOT NULL;

COMMENT ON COLUMN person.users.email IS 
    'email пользователя';

ALTER TABLE person.users ADD COLUMN email_verified_at timestamptz;

COMMENT ON COLUMN person.users.email_verified_at IS 
    'Время подтверждения email пользователя';
-----------------------------------------------------------------------------------------------------------------------------------------------------


-----------------------------------------------------------------------------------------------------------------------------------------------------
CREATE TABLE person.users_films (
    user_id int4 REFERENCES person.users(id) ON DELETE CASCADE,
    film_id int4 REFERENCES dvd.films(id),
    created_at timestamptz NOT NULL DEFAULT now(),
    PRIMARY KEY (user_id, film_id)
);

CREATE INDEX ON person.users_films(film_id);

COMMENT ON TABLE person.users_films IS
    'Таблица, связывающая таблицы person.users и dvd.films';

COMMENT ON COLUMN dvd.films_actors.created_at IS 
    'Время создания записи';
-----------------------------------------------------------------------------------------------------------------------------------------------------


-----------------------------------------------------------------------------------------------------------------------------------------------------
CREATE TABLE person.password_resets (
    email text PRIMARY KEY NOT NULL,
    token text NOT NULL,
    created_at timestamp(0)
);
-----------------------------------------------------------------------------------------------------------------------------------------------------


-----------------------------------------------------------------------------------------------------------------------------------------------------
CREATE TABLE person.personal_access_tokens (
    id bigserial PRIMARY KEY,
    tokenable_type varchar(255) NOT NULL,
    tokenable_id int8 NOT NULL,
    "name" varchar(255) NOT NULL,
    "token" varchar(64) NOT NULL UNIQUE,
    abilities text NULL,
    last_used_at timestamp(0) NULL,
    expires_at timestamp(0) NULL,
    created_at timestamp(0) NULL,
    updated_at timestamp(0) NULL
);
CREATE INDEX ON person.personal_access_tokens USING btree (tokenable_type, tokenable_id);
-----------------------------------------------------------------------------------------------------------------------------------------------------

/****************************************************************************************************************************************************/
/****************************************************************************************************************************************************/


/****************************************************************************************************************************************************/
/********** Схема open_weather    *******************************************************************************************************************/
/****************************************************************************************************************************************************/

-----------------------------------------------------------------------------------------------------------------------------------------------------
CREATE TABLE open_weather.weather (
    id bigserial PRIMARY KEY,
    city_id int NOT NULL REFERENCES thesaurus.cities(id) ON DELETE CASCADE,
    weather_description text,
    main_temp float,
    main_feels_like float,
    main_pressure int,
    main_humidity int,
    visibility int,
    wind_speed float,
    wind_deg int,
    clouds_all int,
    created_at timestamptz NOT NULL DEFAULT now()
);

COMMENT ON TABLE open_weather.weather IS
    'Таблица данных о погоде с сервиса OpenWeather';

COMMENT ON COLUMN open_weather.weather.weather_description IS 
    'Описание погодных условий';

COMMENT ON COLUMN open_weather.weather.main_temp IS 
    'Температура, C';

COMMENT ON COLUMN open_weather.weather.main_feels_like IS 
    'Этот температурный параметр определяет человеческое восприятие погоды, C';

COMMENT ON COLUMN open_weather.weather.main_pressure IS 
    'Атмосферное давление, hPa';

COMMENT ON COLUMN open_weather.weather.main_humidity IS 
    'Влажность, %';

COMMENT ON COLUMN open_weather.weather.visibility IS 
    'Видимость, m';

COMMENT ON COLUMN open_weather.weather.wind_speed IS 
    'Скорость ветра, m/s';

COMMENT ON COLUMN open_weather.weather.wind_deg IS 
    'Направление ветра, градусы (метеорологические)';

COMMENT ON COLUMN open_weather.weather.clouds_all IS 
    'Облачность, %';

COMMENT ON COLUMN open_weather.weather.created_at IS 
    'Время создания записи';
-----------------------------------------------------------------------------------------------------------------------------------------------------

/****************************************************************************************************************************************************/
/****************************************************************************************************************************************************/
