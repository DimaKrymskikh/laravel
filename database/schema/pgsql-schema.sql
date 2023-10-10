--
-- PostgreSQL database dump
--

-- Dumped from database version 15.4 (Debian 15.4-1.pgdg120+1)
-- Dumped by pg_dump version 15.4 (Ubuntu 15.4-1.pgdg22.04+1)

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

--
-- Name: dvd; Type: SCHEMA; Schema: -; Owner: -
--

CREATE SCHEMA dvd;


--
-- Name: open_weather; Type: SCHEMA; Schema: -; Owner: -
--

CREATE SCHEMA open_weather;


--
-- Name: person; Type: SCHEMA; Schema: -; Owner: -
--

CREATE SCHEMA person;


--
-- Name: thesaurus; Type: SCHEMA; Schema: -; Owner: -
--

CREATE SCHEMA thesaurus;


--
-- Name: last_updated(); Type: FUNCTION; Schema: thesaurus; Owner: -
--

CREATE FUNCTION thesaurus.last_updated() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
BEGIN
    NEW.updated_at = now();
    RETURN NEW;
END 
$$;


--
-- Name: FUNCTION last_updated(); Type: COMMENT; Schema: thesaurus; Owner: -
--

COMMENT ON FUNCTION thesaurus.last_updated() IS 'Функция триггера, обновляющая поле updated_at таблиц';


SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- Name: actors; Type: TABLE; Schema: dvd; Owner: -
--

CREATE TABLE dvd.actors (
    id bigint NOT NULL,
    first_name text NOT NULL,
    last_name text NOT NULL,
    created_at timestamp with time zone DEFAULT now() NOT NULL,
    updated_at timestamp with time zone DEFAULT now() NOT NULL
);


--
-- Name: TABLE actors; Type: COMMENT; Schema: dvd; Owner: -
--

COMMENT ON TABLE dvd.actors IS 'Актёры';


--
-- Name: COLUMN actors.first_name; Type: COMMENT; Schema: dvd; Owner: -
--

COMMENT ON COLUMN dvd.actors.first_name IS 'Имя актёра';


--
-- Name: COLUMN actors.last_name; Type: COMMENT; Schema: dvd; Owner: -
--

COMMENT ON COLUMN dvd.actors.last_name IS 'Фамилия актёра';


--
-- Name: COLUMN actors.created_at; Type: COMMENT; Schema: dvd; Owner: -
--

COMMENT ON COLUMN dvd.actors.created_at IS 'Время создания записи';


--
-- Name: COLUMN actors.updated_at; Type: COMMENT; Schema: dvd; Owner: -
--

COMMENT ON COLUMN dvd.actors.updated_at IS 'Время последнего изменения данных об актёре';


--
-- Name: actors_id_seq; Type: SEQUENCE; Schema: dvd; Owner: -
--

CREATE SEQUENCE dvd.actors_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: actors_id_seq; Type: SEQUENCE OWNED BY; Schema: dvd; Owner: -
--

ALTER SEQUENCE dvd.actors_id_seq OWNED BY dvd.actors.id;


--
-- Name: films; Type: TABLE; Schema: dvd; Owner: -
--

CREATE TABLE dvd.films (
    id bigint NOT NULL,
    title text NOT NULL,
    description text,
    release_year integer,
    language_id integer,
    created_at timestamp with time zone DEFAULT now() NOT NULL,
    updated_at timestamp with time zone DEFAULT now() NOT NULL
);


--
-- Name: TABLE films; Type: COMMENT; Schema: dvd; Owner: -
--

COMMENT ON TABLE dvd.films IS 'Художественные фильмы';


--
-- Name: COLUMN films.title; Type: COMMENT; Schema: dvd; Owner: -
--

COMMENT ON COLUMN dvd.films.title IS 'Название фильма';


--
-- Name: COLUMN films.description; Type: COMMENT; Schema: dvd; Owner: -
--

COMMENT ON COLUMN dvd.films.description IS 'Краткое описание фильма';


--
-- Name: COLUMN films.release_year; Type: COMMENT; Schema: dvd; Owner: -
--

COMMENT ON COLUMN dvd.films.release_year IS 'Год выхода фильма';


--
-- Name: COLUMN films.language_id; Type: COMMENT; Schema: dvd; Owner: -
--

COMMENT ON COLUMN dvd.films.language_id IS 'Язык, на котором снят фильм';


--
-- Name: COLUMN films.created_at; Type: COMMENT; Schema: dvd; Owner: -
--

COMMENT ON COLUMN dvd.films.created_at IS 'Время создания записи';


--
-- Name: COLUMN films.updated_at; Type: COMMENT; Schema: dvd; Owner: -
--

COMMENT ON COLUMN dvd.films.updated_at IS 'Время последнего изменения данных о фильме';


--
-- Name: films_actors; Type: TABLE; Schema: dvd; Owner: -
--

CREATE TABLE dvd.films_actors (
    film_id integer NOT NULL,
    actor_id integer NOT NULL,
    created_at timestamp with time zone DEFAULT now() NOT NULL
);


--
-- Name: TABLE films_actors; Type: COMMENT; Schema: dvd; Owner: -
--

COMMENT ON TABLE dvd.films_actors IS 'Таблица, связывающая таблицы dvd.films и dvd.actors';


--
-- Name: COLUMN films_actors.created_at; Type: COMMENT; Schema: dvd; Owner: -
--

COMMENT ON COLUMN dvd.films_actors.created_at IS 'Время создания записи';


--
-- Name: films_id_seq; Type: SEQUENCE; Schema: dvd; Owner: -
--

CREATE SEQUENCE dvd.films_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: films_id_seq; Type: SEQUENCE OWNED BY; Schema: dvd; Owner: -
--

ALTER SEQUENCE dvd.films_id_seq OWNED BY dvd.films.id;


--
-- Name: weather; Type: TABLE; Schema: open_weather; Owner: -
--

CREATE TABLE open_weather.weather (
    id bigint NOT NULL,
    city_id integer NOT NULL,
    weather_description text,
    main_temp double precision,
    main_feels_like double precision,
    main_pressure integer,
    main_humidity integer,
    visibility integer,
    wind_speed double precision,
    wind_deg integer,
    clouds_all integer,
    created_at timestamp with time zone DEFAULT now() NOT NULL
);


--
-- Name: TABLE weather; Type: COMMENT; Schema: open_weather; Owner: -
--

COMMENT ON TABLE open_weather.weather IS 'Таблица данных о погоде с сервиса OpenWeather';


--
-- Name: COLUMN weather.weather_description; Type: COMMENT; Schema: open_weather; Owner: -
--

COMMENT ON COLUMN open_weather.weather.weather_description IS 'Описание погодных условий';


--
-- Name: COLUMN weather.main_temp; Type: COMMENT; Schema: open_weather; Owner: -
--

COMMENT ON COLUMN open_weather.weather.main_temp IS 'Температура, C';


--
-- Name: COLUMN weather.main_feels_like; Type: COMMENT; Schema: open_weather; Owner: -
--

COMMENT ON COLUMN open_weather.weather.main_feels_like IS 'Этот температурный параметр определяет человеческое восприятие погоды, C';


--
-- Name: COLUMN weather.main_pressure; Type: COMMENT; Schema: open_weather; Owner: -
--

COMMENT ON COLUMN open_weather.weather.main_pressure IS 'Атмосферное давление, hPa';


--
-- Name: COLUMN weather.main_humidity; Type: COMMENT; Schema: open_weather; Owner: -
--

COMMENT ON COLUMN open_weather.weather.main_humidity IS 'Влажность, %';


--
-- Name: COLUMN weather.visibility; Type: COMMENT; Schema: open_weather; Owner: -
--

COMMENT ON COLUMN open_weather.weather.visibility IS 'Видимость, m';


--
-- Name: COLUMN weather.wind_speed; Type: COMMENT; Schema: open_weather; Owner: -
--

COMMENT ON COLUMN open_weather.weather.wind_speed IS 'Скорость ветра, m/s';


--
-- Name: COLUMN weather.wind_deg; Type: COMMENT; Schema: open_weather; Owner: -
--

COMMENT ON COLUMN open_weather.weather.wind_deg IS 'Направление ветра, градусы (метеорологические)';


--
-- Name: COLUMN weather.clouds_all; Type: COMMENT; Schema: open_weather; Owner: -
--

COMMENT ON COLUMN open_weather.weather.clouds_all IS 'Облачность, %';


--
-- Name: COLUMN weather.created_at; Type: COMMENT; Schema: open_weather; Owner: -
--

COMMENT ON COLUMN open_weather.weather.created_at IS 'Время создания записи';


--
-- Name: weather_id_seq; Type: SEQUENCE; Schema: open_weather; Owner: -
--

CREATE SEQUENCE open_weather.weather_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: weather_id_seq; Type: SEQUENCE OWNED BY; Schema: open_weather; Owner: -
--

ALTER SEQUENCE open_weather.weather_id_seq OWNED BY open_weather.weather.id;


--
-- Name: password_resets; Type: TABLE; Schema: person; Owner: -
--

CREATE TABLE person.password_resets (
    email text NOT NULL,
    token text NOT NULL,
    created_at timestamp(0) without time zone
);


--
-- Name: personal_access_tokens; Type: TABLE; Schema: person; Owner: -
--

CREATE TABLE person.personal_access_tokens (
    id bigint NOT NULL,
    tokenable_type character varying(255) NOT NULL,
    tokenable_id bigint NOT NULL,
    name character varying(255) NOT NULL,
    token character varying(64) NOT NULL,
    abilities text,
    last_used_at timestamp(0) without time zone,
    expires_at timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: personal_access_tokens_id_seq; Type: SEQUENCE; Schema: person; Owner: -
--

CREATE SEQUENCE person.personal_access_tokens_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: personal_access_tokens_id_seq; Type: SEQUENCE OWNED BY; Schema: person; Owner: -
--

ALTER SEQUENCE person.personal_access_tokens_id_seq OWNED BY person.personal_access_tokens.id;


--
-- Name: users; Type: TABLE; Schema: person; Owner: -
--

CREATE TABLE person.users (
    id bigint NOT NULL,
    login text NOT NULL,
    password text NOT NULL,
    created_at timestamp with time zone DEFAULT now() NOT NULL,
    updated_at timestamp with time zone DEFAULT now() NOT NULL,
    email text NOT NULL,
    email_verified_at timestamp with time zone,
    is_admin boolean DEFAULT false NOT NULL,
    CONSTRAINT users_login_check CHECK ((login ~ '^[A-Z]\w{3,17}$'::text))
);


--
-- Name: TABLE users; Type: COMMENT; Schema: person; Owner: -
--

COMMENT ON TABLE person.users IS 'Пользователи';


--
-- Name: COLUMN users.login; Type: COMMENT; Schema: person; Owner: -
--

COMMENT ON COLUMN person.users.login IS 'Логин';


--
-- Name: COLUMN users.password; Type: COMMENT; Schema: person; Owner: -
--

COMMENT ON COLUMN person.users.password IS 'Пароль';


--
-- Name: COLUMN users.created_at; Type: COMMENT; Schema: person; Owner: -
--

COMMENT ON COLUMN person.users.created_at IS 'Время создания аккаунта';


--
-- Name: COLUMN users.updated_at; Type: COMMENT; Schema: person; Owner: -
--

COMMENT ON COLUMN person.users.updated_at IS 'Время последнего изменения аккаунта';


--
-- Name: COLUMN users.email; Type: COMMENT; Schema: person; Owner: -
--

COMMENT ON COLUMN person.users.email IS 'email пользователя';


--
-- Name: COLUMN users.email_verified_at; Type: COMMENT; Schema: person; Owner: -
--

COMMENT ON COLUMN person.users.email_verified_at IS 'Время подтверждения email пользователя';


--
-- Name: users_cities; Type: TABLE; Schema: person; Owner: -
--

CREATE TABLE person.users_cities (
    user_id integer NOT NULL,
    city_id integer NOT NULL,
    created_at timestamp with time zone DEFAULT now() NOT NULL
);


--
-- Name: TABLE users_cities; Type: COMMENT; Schema: person; Owner: -
--

COMMENT ON TABLE person.users_cities IS 'Таблица, связывающая таблицы person.users и thesaurus.cities';


--
-- Name: COLUMN users_cities.created_at; Type: COMMENT; Schema: person; Owner: -
--

COMMENT ON COLUMN person.users_cities.created_at IS 'Время создания записи';


--
-- Name: users_films; Type: TABLE; Schema: person; Owner: -
--

CREATE TABLE person.users_films (
    user_id integer NOT NULL,
    film_id integer NOT NULL,
    created_at timestamp with time zone DEFAULT now() NOT NULL
);


--
-- Name: TABLE users_films; Type: COMMENT; Schema: person; Owner: -
--

COMMENT ON TABLE person.users_films IS 'Таблица, связывающая таблицы person.users и dvd.films';


--
-- Name: COLUMN users_films.created_at; Type: COMMENT; Schema: person; Owner: -
--

COMMENT ON COLUMN person.users_films.created_at IS 'Время создания записи';


--
-- Name: users_id_seq; Type: SEQUENCE; Schema: person; Owner: -
--

CREATE SEQUENCE person.users_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: users_id_seq; Type: SEQUENCE OWNED BY; Schema: person; Owner: -
--

ALTER SEQUENCE person.users_id_seq OWNED BY person.users.id;


--
-- Name: migrations; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.migrations (
    id integer NOT NULL,
    migration character varying(255) NOT NULL,
    batch integer NOT NULL
);


--
-- Name: migrations_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.migrations_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: migrations_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.migrations_id_seq OWNED BY public.migrations.id;


--
-- Name: cities; Type: TABLE; Schema: thesaurus; Owner: -
--

CREATE TABLE thesaurus.cities (
    id integer NOT NULL,
    name text NOT NULL,
    open_weather_id bigint NOT NULL,
    created_at timestamp with time zone DEFAULT now() NOT NULL,
    updated_at timestamp with time zone DEFAULT now() NOT NULL,
    timezone_id integer
);


--
-- Name: TABLE cities; Type: COMMENT; Schema: thesaurus; Owner: -
--

COMMENT ON TABLE thesaurus.cities IS 'Города';


--
-- Name: COLUMN cities.name; Type: COMMENT; Schema: thesaurus; Owner: -
--

COMMENT ON COLUMN thesaurus.cities.name IS 'Название';


--
-- Name: COLUMN cities.open_weather_id; Type: COMMENT; Schema: thesaurus; Owner: -
--

COMMENT ON COLUMN thesaurus.cities.open_weather_id IS 'ID города в службе OpenWeather';


--
-- Name: COLUMN cities.created_at; Type: COMMENT; Schema: thesaurus; Owner: -
--

COMMENT ON COLUMN thesaurus.cities.created_at IS 'Время создания записи';


--
-- Name: COLUMN cities.updated_at; Type: COMMENT; Schema: thesaurus; Owner: -
--

COMMENT ON COLUMN thesaurus.cities.updated_at IS 'Время последнего изменения записи';


--
-- Name: COLUMN cities.timezone_id; Type: COMMENT; Schema: thesaurus; Owner: -
--

COMMENT ON COLUMN thesaurus.cities.timezone_id IS 'Часовой пояс';


--
-- Name: cities_id_seq; Type: SEQUENCE; Schema: thesaurus; Owner: -
--

CREATE SEQUENCE thesaurus.cities_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: cities_id_seq; Type: SEQUENCE OWNED BY; Schema: thesaurus; Owner: -
--

ALTER SEQUENCE thesaurus.cities_id_seq OWNED BY thesaurus.cities.id;


--
-- Name: failed_jobs; Type: TABLE; Schema: thesaurus; Owner: -
--

CREATE TABLE thesaurus.failed_jobs (
    id bigint NOT NULL,
    uuid character varying(255) NOT NULL,
    connection text NOT NULL,
    queue text NOT NULL,
    payload text NOT NULL,
    exception text NOT NULL,
    failed_at timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL
);


--
-- Name: failed_jobs_id_seq; Type: SEQUENCE; Schema: thesaurus; Owner: -
--

CREATE SEQUENCE thesaurus.failed_jobs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: failed_jobs_id_seq; Type: SEQUENCE OWNED BY; Schema: thesaurus; Owner: -
--

ALTER SEQUENCE thesaurus.failed_jobs_id_seq OWNED BY thesaurus.failed_jobs.id;


--
-- Name: languages; Type: TABLE; Schema: thesaurus; Owner: -
--

CREATE TABLE thesaurus.languages (
    id integer NOT NULL,
    name text NOT NULL,
    created_at timestamp with time zone DEFAULT now() NOT NULL,
    updated_at timestamp with time zone DEFAULT now() NOT NULL
);


--
-- Name: TABLE languages; Type: COMMENT; Schema: thesaurus; Owner: -
--

COMMENT ON TABLE thesaurus.languages IS 'Языки народов';


--
-- Name: COLUMN languages.name; Type: COMMENT; Schema: thesaurus; Owner: -
--

COMMENT ON COLUMN thesaurus.languages.name IS 'Название';


--
-- Name: COLUMN languages.created_at; Type: COMMENT; Schema: thesaurus; Owner: -
--

COMMENT ON COLUMN thesaurus.languages.created_at IS 'Время создания записи';


--
-- Name: COLUMN languages.updated_at; Type: COMMENT; Schema: thesaurus; Owner: -
--

COMMENT ON COLUMN thesaurus.languages.updated_at IS 'Время последнего изменения записи';


--
-- Name: languages_id_seq; Type: SEQUENCE; Schema: thesaurus; Owner: -
--

CREATE SEQUENCE thesaurus.languages_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: languages_id_seq; Type: SEQUENCE OWNED BY; Schema: thesaurus; Owner: -
--

ALTER SEQUENCE thesaurus.languages_id_seq OWNED BY thesaurus.languages.id;


--
-- Name: timezones; Type: TABLE; Schema: thesaurus; Owner: -
--

CREATE TABLE thesaurus.timezones (
    id integer NOT NULL,
    name text NOT NULL
);


--
-- Name: TABLE timezones; Type: COMMENT; Schema: thesaurus; Owner: -
--

COMMENT ON TABLE thesaurus.timezones IS 'Временные зоны';


--
-- Name: COLUMN timezones.name; Type: COMMENT; Schema: thesaurus; Owner: -
--

COMMENT ON COLUMN thesaurus.timezones.name IS 'Часовой пояс';


--
-- Name: timezones_id_seq; Type: SEQUENCE; Schema: thesaurus; Owner: -
--

CREATE SEQUENCE thesaurus.timezones_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: timezones_id_seq; Type: SEQUENCE OWNED BY; Schema: thesaurus; Owner: -
--

ALTER SEQUENCE thesaurus.timezones_id_seq OWNED BY thesaurus.timezones.id;


--
-- Name: actors id; Type: DEFAULT; Schema: dvd; Owner: -
--

ALTER TABLE ONLY dvd.actors ALTER COLUMN id SET DEFAULT nextval('dvd.actors_id_seq'::regclass);


--
-- Name: films id; Type: DEFAULT; Schema: dvd; Owner: -
--

ALTER TABLE ONLY dvd.films ALTER COLUMN id SET DEFAULT nextval('dvd.films_id_seq'::regclass);


--
-- Name: weather id; Type: DEFAULT; Schema: open_weather; Owner: -
--

ALTER TABLE ONLY open_weather.weather ALTER COLUMN id SET DEFAULT nextval('open_weather.weather_id_seq'::regclass);


--
-- Name: personal_access_tokens id; Type: DEFAULT; Schema: person; Owner: -
--

ALTER TABLE ONLY person.personal_access_tokens ALTER COLUMN id SET DEFAULT nextval('person.personal_access_tokens_id_seq'::regclass);


--
-- Name: users id; Type: DEFAULT; Schema: person; Owner: -
--

ALTER TABLE ONLY person.users ALTER COLUMN id SET DEFAULT nextval('person.users_id_seq'::regclass);


--
-- Name: migrations id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.migrations ALTER COLUMN id SET DEFAULT nextval('public.migrations_id_seq'::regclass);


--
-- Name: cities id; Type: DEFAULT; Schema: thesaurus; Owner: -
--

ALTER TABLE ONLY thesaurus.cities ALTER COLUMN id SET DEFAULT nextval('thesaurus.cities_id_seq'::regclass);


--
-- Name: failed_jobs id; Type: DEFAULT; Schema: thesaurus; Owner: -
--

ALTER TABLE ONLY thesaurus.failed_jobs ALTER COLUMN id SET DEFAULT nextval('thesaurus.failed_jobs_id_seq'::regclass);


--
-- Name: languages id; Type: DEFAULT; Schema: thesaurus; Owner: -
--

ALTER TABLE ONLY thesaurus.languages ALTER COLUMN id SET DEFAULT nextval('thesaurus.languages_id_seq'::regclass);


--
-- Name: timezones id; Type: DEFAULT; Schema: thesaurus; Owner: -
--

ALTER TABLE ONLY thesaurus.timezones ALTER COLUMN id SET DEFAULT nextval('thesaurus.timezones_id_seq'::regclass);


--
-- Name: actors actors_pkey; Type: CONSTRAINT; Schema: dvd; Owner: -
--

ALTER TABLE ONLY dvd.actors
    ADD CONSTRAINT actors_pkey PRIMARY KEY (id);


--
-- Name: films_actors films_actors_pkey; Type: CONSTRAINT; Schema: dvd; Owner: -
--

ALTER TABLE ONLY dvd.films_actors
    ADD CONSTRAINT films_actors_pkey PRIMARY KEY (film_id, actor_id);


--
-- Name: films films_pkey; Type: CONSTRAINT; Schema: dvd; Owner: -
--

ALTER TABLE ONLY dvd.films
    ADD CONSTRAINT films_pkey PRIMARY KEY (id);


--
-- Name: weather weather_pkey; Type: CONSTRAINT; Schema: open_weather; Owner: -
--

ALTER TABLE ONLY open_weather.weather
    ADD CONSTRAINT weather_pkey PRIMARY KEY (id);


--
-- Name: password_resets password_resets_pkey; Type: CONSTRAINT; Schema: person; Owner: -
--

ALTER TABLE ONLY person.password_resets
    ADD CONSTRAINT password_resets_pkey PRIMARY KEY (email);


--
-- Name: personal_access_tokens personal_access_tokens_pkey; Type: CONSTRAINT; Schema: person; Owner: -
--

ALTER TABLE ONLY person.personal_access_tokens
    ADD CONSTRAINT personal_access_tokens_pkey PRIMARY KEY (id);


--
-- Name: personal_access_tokens personal_access_tokens_token_key; Type: CONSTRAINT; Schema: person; Owner: -
--

ALTER TABLE ONLY person.personal_access_tokens
    ADD CONSTRAINT personal_access_tokens_token_key UNIQUE (token);


--
-- Name: users_cities users_cities_pkey; Type: CONSTRAINT; Schema: person; Owner: -
--

ALTER TABLE ONLY person.users_cities
    ADD CONSTRAINT users_cities_pkey PRIMARY KEY (user_id, city_id);


--
-- Name: users users_email_unique; Type: CONSTRAINT; Schema: person; Owner: -
--

ALTER TABLE ONLY person.users
    ADD CONSTRAINT users_email_unique UNIQUE (email);


--
-- Name: users_films users_films_pkey; Type: CONSTRAINT; Schema: person; Owner: -
--

ALTER TABLE ONLY person.users_films
    ADD CONSTRAINT users_films_pkey PRIMARY KEY (user_id, film_id);


--
-- Name: users users_pkey; Type: CONSTRAINT; Schema: person; Owner: -
--

ALTER TABLE ONLY person.users
    ADD CONSTRAINT users_pkey PRIMARY KEY (id);


--
-- Name: migrations migrations_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.migrations
    ADD CONSTRAINT migrations_pkey PRIMARY KEY (id);


--
-- Name: cities cities_open_weather_id_key; Type: CONSTRAINT; Schema: thesaurus; Owner: -
--

ALTER TABLE ONLY thesaurus.cities
    ADD CONSTRAINT cities_open_weather_id_key UNIQUE (open_weather_id);


--
-- Name: cities cities_pkey; Type: CONSTRAINT; Schema: thesaurus; Owner: -
--

ALTER TABLE ONLY thesaurus.cities
    ADD CONSTRAINT cities_pkey PRIMARY KEY (id);


--
-- Name: failed_jobs failed_jobs_pkey; Type: CONSTRAINT; Schema: thesaurus; Owner: -
--

ALTER TABLE ONLY thesaurus.failed_jobs
    ADD CONSTRAINT failed_jobs_pkey PRIMARY KEY (id);


--
-- Name: failed_jobs failed_jobs_uuid_key; Type: CONSTRAINT; Schema: thesaurus; Owner: -
--

ALTER TABLE ONLY thesaurus.failed_jobs
    ADD CONSTRAINT failed_jobs_uuid_key UNIQUE (uuid);


--
-- Name: languages languages_pkey; Type: CONSTRAINT; Schema: thesaurus; Owner: -
--

ALTER TABLE ONLY thesaurus.languages
    ADD CONSTRAINT languages_pkey PRIMARY KEY (id);


--
-- Name: timezones timezones_pkey; Type: CONSTRAINT; Schema: thesaurus; Owner: -
--

ALTER TABLE ONLY thesaurus.timezones
    ADD CONSTRAINT timezones_pkey PRIMARY KEY (id);


--
-- Name: actors_last_name_idx; Type: INDEX; Schema: dvd; Owner: -
--

CREATE INDEX actors_last_name_idx ON dvd.actors USING btree (last_name);


--
-- Name: films_actors_film_id_idx; Type: INDEX; Schema: dvd; Owner: -
--

CREATE INDEX films_actors_film_id_idx ON dvd.films_actors USING btree (film_id);


--
-- Name: personal_access_tokens_tokenable_type_tokenable_id_idx; Type: INDEX; Schema: person; Owner: -
--

CREATE INDEX personal_access_tokens_tokenable_type_tokenable_id_idx ON person.personal_access_tokens USING btree (tokenable_type, tokenable_id);


--
-- Name: users_films_film_id_idx; Type: INDEX; Schema: person; Owner: -
--

CREATE INDEX users_films_film_id_idx ON person.users_films USING btree (film_id);


--
-- Name: users_login_idx; Type: INDEX; Schema: person; Owner: -
--

CREATE UNIQUE INDEX users_login_idx ON person.users USING btree (login);


--
-- Name: timezones_name_idx; Type: INDEX; Schema: thesaurus; Owner: -
--

CREATE UNIQUE INDEX timezones_name_idx ON thesaurus.timezones USING btree (name);


--
-- Name: actors last_updated; Type: TRIGGER; Schema: dvd; Owner: -
--

CREATE TRIGGER last_updated BEFORE UPDATE ON dvd.actors FOR EACH ROW EXECUTE FUNCTION thesaurus.last_updated();


--
-- Name: films last_updated; Type: TRIGGER; Schema: dvd; Owner: -
--

CREATE TRIGGER last_updated BEFORE UPDATE ON dvd.films FOR EACH ROW EXECUTE FUNCTION thesaurus.last_updated();


--
-- Name: users last_updated; Type: TRIGGER; Schema: person; Owner: -
--

CREATE TRIGGER last_updated BEFORE UPDATE ON person.users FOR EACH ROW EXECUTE FUNCTION thesaurus.last_updated();


--
-- Name: cities last_updated; Type: TRIGGER; Schema: thesaurus; Owner: -
--

CREATE TRIGGER last_updated BEFORE UPDATE ON thesaurus.cities FOR EACH ROW EXECUTE FUNCTION thesaurus.last_updated();


--
-- Name: languages last_updated; Type: TRIGGER; Schema: thesaurus; Owner: -
--

CREATE TRIGGER last_updated BEFORE UPDATE ON thesaurus.languages FOR EACH ROW EXECUTE FUNCTION thesaurus.last_updated();


--
-- Name: films_actors films_actors_actor_id_fkey; Type: FK CONSTRAINT; Schema: dvd; Owner: -
--

ALTER TABLE ONLY dvd.films_actors
    ADD CONSTRAINT films_actors_actor_id_fkey FOREIGN KEY (actor_id) REFERENCES dvd.actors(id);


--
-- Name: films_actors films_actors_film_id_fkey; Type: FK CONSTRAINT; Schema: dvd; Owner: -
--

ALTER TABLE ONLY dvd.films_actors
    ADD CONSTRAINT films_actors_film_id_fkey FOREIGN KEY (film_id) REFERENCES dvd.films(id);


--
-- Name: films films_language_id_fkey; Type: FK CONSTRAINT; Schema: dvd; Owner: -
--

ALTER TABLE ONLY dvd.films
    ADD CONSTRAINT films_language_id_fkey FOREIGN KEY (language_id) REFERENCES thesaurus.languages(id);


--
-- Name: weather weather_city_id_fkey; Type: FK CONSTRAINT; Schema: open_weather; Owner: -
--

ALTER TABLE ONLY open_weather.weather
    ADD CONSTRAINT weather_city_id_fkey FOREIGN KEY (city_id) REFERENCES thesaurus.cities(id) ON DELETE CASCADE;


--
-- Name: users_cities users_cities_city_id_fkey; Type: FK CONSTRAINT; Schema: person; Owner: -
--

ALTER TABLE ONLY person.users_cities
    ADD CONSTRAINT users_cities_city_id_fkey FOREIGN KEY (city_id) REFERENCES thesaurus.cities(id);


--
-- Name: users_cities users_cities_user_id_fkey; Type: FK CONSTRAINT; Schema: person; Owner: -
--

ALTER TABLE ONLY person.users_cities
    ADD CONSTRAINT users_cities_user_id_fkey FOREIGN KEY (user_id) REFERENCES person.users(id) ON DELETE CASCADE;


--
-- Name: users_films users_films_film_id_fkey; Type: FK CONSTRAINT; Schema: person; Owner: -
--

ALTER TABLE ONLY person.users_films
    ADD CONSTRAINT users_films_film_id_fkey FOREIGN KEY (film_id) REFERENCES dvd.films(id);


--
-- Name: users_films users_films_user_id_fkey; Type: FK CONSTRAINT; Schema: person; Owner: -
--

ALTER TABLE ONLY person.users_films
    ADD CONSTRAINT users_films_user_id_fkey FOREIGN KEY (user_id) REFERENCES person.users(id) ON DELETE CASCADE;


--
-- Name: cities cities_timezone_id_fkey; Type: FK CONSTRAINT; Schema: thesaurus; Owner: -
--

ALTER TABLE ONLY thesaurus.cities
    ADD CONSTRAINT cities_timezone_id_fkey FOREIGN KEY (timezone_id) REFERENCES thesaurus.timezones(id) ON DELETE SET NULL (timezone_id);


--
-- PostgreSQL database dump complete
--

--
-- PostgreSQL database dump
--

-- Dumped from database version 15.4 (Debian 15.4-1.pgdg120+1)
-- Dumped by pg_dump version 15.4 (Ubuntu 15.4-1.pgdg22.04+1)

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

--
-- Data for Name: migrations; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.migrations (id, migration, batch) FROM stdin;
1	2023_09_09_175426_create_thesaurus_timezone_table	1
2	2023_09_12_163234_add-column_timezone_id_in_thesaurus_timesones_table	2
3	2023_09_17_211442_create_person_users_cities_table	3
\.


--
-- Name: migrations_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.migrations_id_seq', 3, true);


--
-- PostgreSQL database dump complete
--

