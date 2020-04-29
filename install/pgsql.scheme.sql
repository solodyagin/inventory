--
-- PostgreSQL database dump
--

-- Dumped from database version 10.12
-- Dumped by pg_dump version 11.2

-- Started on 2020-03-27 22:58:05

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET client_min_messages = warning;
SET row_security = off;

ALTER TABLE IF EXISTS ONLY public.vendor DROP CONSTRAINT IF EXISTS vendor_pkey;
ALTER TABLE IF EXISTS ONLY public.usersroles DROP CONSTRAINT IF EXISTS usersroles_pkey;
ALTER TABLE IF EXISTS ONLY public.users_profile DROP CONSTRAINT IF EXISTS users_profile_usersid_key;
ALTER TABLE IF EXISTS ONLY public.users_profile DROP CONSTRAINT IF EXISTS users_profile_pkey;
ALTER TABLE IF EXISTS ONLY public.users DROP CONSTRAINT IF EXISTS users_pkey;
ALTER TABLE IF EXISTS ONLY public.repair DROP CONSTRAINT IF EXISTS repair_pkey;
ALTER TABLE IF EXISTS ONLY public.places_users DROP CONSTRAINT IF EXISTS places_users_pkey;
ALTER TABLE IF EXISTS ONLY public.places DROP CONSTRAINT IF EXISTS places_pkey;
ALTER TABLE IF EXISTS ONLY public.org DROP CONSTRAINT IF EXISTS org_pkey;
ALTER TABLE IF EXISTS ONLY public.nome DROP CONSTRAINT IF EXISTS nome_pkey;
ALTER TABLE IF EXISTS ONLY public.news DROP CONSTRAINT IF EXISTS news_pkey;
ALTER TABLE IF EXISTS ONLY public.move DROP CONSTRAINT IF EXISTS move_pkey;
ALTER TABLE IF EXISTS ONLY public.mailq DROP CONSTRAINT IF EXISTS mailq_pkey;
ALTER TABLE IF EXISTS ONLY public.knt DROP CONSTRAINT IF EXISTS knt_pkey;
ALTER TABLE IF EXISTS ONLY public.group_param DROP CONSTRAINT IF EXISTS group_param_pkey;
ALTER TABLE IF EXISTS ONLY public.group_nome DROP CONSTRAINT IF EXISTS group_nome_pkey;
ALTER TABLE IF EXISTS ONLY public.files_contract DROP CONSTRAINT IF EXISTS files_contract_pkey;
ALTER TABLE IF EXISTS ONLY public.equipment DROP CONSTRAINT IF EXISTS equipment_pkey;
ALTER TABLE IF EXISTS ONLY public.eq_param DROP CONSTRAINT IF EXISTS eq_param_pkey;
ALTER TABLE IF EXISTS ONLY public.contract DROP CONSTRAINT IF EXISTS contract_pkey;
ALTER TABLE IF EXISTS ONLY public.config DROP CONSTRAINT IF EXISTS config_pkey;
ALTER TABLE IF EXISTS ONLY public.config_common DROP CONSTRAINT IF EXISTS config_common_pkey;
ALTER TABLE IF EXISTS ONLY public.cloud_files DROP CONSTRAINT IF EXISTS cloud_files_pkey;
ALTER TABLE IF EXISTS ONLY public.cloud_dirs DROP CONSTRAINT IF EXISTS cloud_dirs_pkey;
ALTER TABLE IF EXISTS public.vendor ALTER COLUMN id DROP DEFAULT;
ALTER TABLE IF EXISTS public.usersroles ALTER COLUMN id DROP DEFAULT;
ALTER TABLE IF EXISTS public.users_profile ALTER COLUMN id DROP DEFAULT;
ALTER TABLE IF EXISTS public.users ALTER COLUMN id DROP DEFAULT;
ALTER TABLE IF EXISTS public.repair ALTER COLUMN id DROP DEFAULT;
ALTER TABLE IF EXISTS public.places_users ALTER COLUMN id DROP DEFAULT;
ALTER TABLE IF EXISTS public.places ALTER COLUMN id DROP DEFAULT;
ALTER TABLE IF EXISTS public.org ALTER COLUMN id DROP DEFAULT;
ALTER TABLE IF EXISTS public.nome ALTER COLUMN id DROP DEFAULT;
ALTER TABLE IF EXISTS public.news ALTER COLUMN id DROP DEFAULT;
ALTER TABLE IF EXISTS public.move ALTER COLUMN id DROP DEFAULT;
ALTER TABLE IF EXISTS public.mailq ALTER COLUMN id DROP DEFAULT;
ALTER TABLE IF EXISTS public.knt ALTER COLUMN id DROP DEFAULT;
ALTER TABLE IF EXISTS public.group_param ALTER COLUMN id DROP DEFAULT;
ALTER TABLE IF EXISTS public.group_nome ALTER COLUMN id DROP DEFAULT;
ALTER TABLE IF EXISTS public.files_contract ALTER COLUMN id DROP DEFAULT;
ALTER TABLE IF EXISTS public.equipment ALTER COLUMN id DROP DEFAULT;
ALTER TABLE IF EXISTS public.eq_param ALTER COLUMN id DROP DEFAULT;
ALTER TABLE IF EXISTS public.contract ALTER COLUMN id DROP DEFAULT;
ALTER TABLE IF EXISTS public.config_common ALTER COLUMN id DROP DEFAULT;
ALTER TABLE IF EXISTS public.config ALTER COLUMN id DROP DEFAULT;
ALTER TABLE IF EXISTS public.cloud_files ALTER COLUMN id DROP DEFAULT;
ALTER TABLE IF EXISTS public.cloud_dirs ALTER COLUMN id DROP DEFAULT;
DROP SEQUENCE IF EXISTS public.vendor_id_seq;
DROP TABLE IF EXISTS public.vendor;
DROP SEQUENCE IF EXISTS public.usersroles_id_seq;
DROP TABLE IF EXISTS public.usersroles;
DROP SEQUENCE IF EXISTS public.users_profile_id_seq;
DROP TABLE IF EXISTS public.users_profile;
DROP SEQUENCE IF EXISTS public.users_id_seq;
DROP TABLE IF EXISTS public.users;
DROP SEQUENCE IF EXISTS public.repair_id_seq;
DROP TABLE IF EXISTS public.repair;
DROP SEQUENCE IF EXISTS public.places_users_id_seq;
DROP TABLE IF EXISTS public.places_users;
DROP SEQUENCE IF EXISTS public.places_id_seq;
DROP TABLE IF EXISTS public.places;
DROP SEQUENCE IF EXISTS public.org_id_seq;
DROP TABLE IF EXISTS public.org;
DROP SEQUENCE IF EXISTS public.nome_id_seq;
DROP TABLE IF EXISTS public.nome;
DROP SEQUENCE IF EXISTS public.news_id_seq;
DROP TABLE IF EXISTS public.news;
DROP SEQUENCE IF EXISTS public.move_id_seq;
DROP TABLE IF EXISTS public.move;
DROP SEQUENCE IF EXISTS public.mailq_id_seq;
DROP TABLE IF EXISTS public.mailq;
DROP SEQUENCE IF EXISTS public.knt_id_seq;
DROP TABLE IF EXISTS public.knt;
DROP SEQUENCE IF EXISTS public.group_param_id_seq;
DROP TABLE IF EXISTS public.group_param;
DROP SEQUENCE IF EXISTS public.group_nome_id_seq;
DROP TABLE IF EXISTS public.group_nome;
DROP SEQUENCE IF EXISTS public.files_contract_id_seq;
DROP TABLE IF EXISTS public.files_contract;
DROP SEQUENCE IF EXISTS public.equipment_id_seq;
DROP TABLE IF EXISTS public.equipment;
DROP SEQUENCE IF EXISTS public.eq_param_id_seq;
DROP TABLE IF EXISTS public.eq_param;
DROP SEQUENCE IF EXISTS public.contract_id_seq;
DROP TABLE IF EXISTS public.contract;
DROP SEQUENCE IF EXISTS public.config_id_seq;
DROP SEQUENCE IF EXISTS public.config_common_id_seq;
DROP TABLE IF EXISTS public.config_common;
DROP TABLE IF EXISTS public.config;
DROP SEQUENCE IF EXISTS public.cloud_files_id_seq;
DROP TABLE IF EXISTS public.cloud_files;
DROP SEQUENCE IF EXISTS public.cloud_dirs_id_seq;
DROP TABLE IF EXISTS public.cloud_dirs;
DROP FUNCTION IF EXISTS public.sha1(text);
DROP FUNCTION IF EXISTS public.sha1(bytea);
DROP FUNCTION IF EXISTS public.digest(text, text);
DROP FUNCTION IF EXISTS public.digest(bytea, text);
DROP SCHEMA IF EXISTS public;
--
-- TOC entry 3 (class 2615 OID 2200)
-- Name: public; Type: SCHEMA; Schema: -; Owner: -
--

CREATE SCHEMA public;


--
-- TOC entry 3066 (class 0 OID 0)
-- Dependencies: 3
-- Name: SCHEMA public; Type: COMMENT; Schema: -; Owner: -
--

COMMENT ON SCHEMA public IS 'standard public schema';


--
-- TOC entry 243 (class 1255 OID 24627128)
-- Name: digest(bytea, text); Type: FUNCTION; Schema: public; Owner: -
--

CREATE FUNCTION public.digest(bytea, text) RETURNS bytea
    LANGUAGE c IMMUTABLE STRICT PARALLEL SAFE
    AS '$libdir/pgcrypto', 'pg_digest';


--
-- TOC entry 242 (class 1255 OID 24627127)
-- Name: digest(text, text); Type: FUNCTION; Schema: public; Owner: -
--

CREATE FUNCTION public.digest(text, text) RETURNS bytea
    LANGUAGE c IMMUTABLE STRICT PARALLEL SAFE
    AS '$libdir/pgcrypto', 'pg_digest';


--
-- TOC entry 245 (class 1255 OID 24627130)
-- Name: sha1(bytea); Type: FUNCTION; Schema: public; Owner: -
--

CREATE FUNCTION public.sha1(bytea) RETURNS text
    LANGUAGE sql IMMUTABLE STRICT
    AS $_$
		select encode(digest($1, 'sha1'), 'hex')
	$_$;


--
-- TOC entry 244 (class 1255 OID 24627129)
-- Name: sha1(text); Type: FUNCTION; Schema: public; Owner: -
--

CREATE FUNCTION public.sha1(text) RETURNS text
    LANGUAGE sql IMMUTABLE STRICT
    AS $_$
		select encode(digest($1, 'sha1'), 'hex')
	$_$;


SET default_tablespace = '';

SET default_with_oids = false;

--
-- TOC entry 197 (class 1259 OID 24626904)
-- Name: cloud_dirs; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.cloud_dirs (
    id integer NOT NULL,
    parent integer NOT NULL,
    name character varying(100) NOT NULL
);


--
-- TOC entry 196 (class 1259 OID 24626902)
-- Name: cloud_dirs_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.cloud_dirs_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 3067 (class 0 OID 0)
-- Dependencies: 196
-- Name: cloud_dirs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.cloud_dirs_id_seq OWNED BY public.cloud_dirs.id;


--
-- TOC entry 199 (class 1259 OID 24626912)
-- Name: cloud_files; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.cloud_files (
    id integer NOT NULL,
    cloud_dirs_id integer NOT NULL,
    title character varying(150) NOT NULL,
    filename character varying(150) NOT NULL,
    dt timestamp without time zone NOT NULL,
    sz integer NOT NULL
);


--
-- TOC entry 198 (class 1259 OID 24626910)
-- Name: cloud_files_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.cloud_files_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 3068 (class 0 OID 0)
-- Dependencies: 198
-- Name: cloud_files_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.cloud_files_id_seq OWNED BY public.cloud_files.id;


--
-- TOC entry 201 (class 1259 OID 24626920)
-- Name: config; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.config (
    id integer NOT NULL,
    ad smallint DEFAULT 0 NOT NULL,
    domain1 character varying(255),
    domain2 character varying(255),
    ldap character varying(255),
    theme character varying(255),
    sitename character varying(255),
    emailadmin character varying(100),
    smtphost character varying(20),
    smtpauth smallint DEFAULT 0 NOT NULL,
    smtpport character varying(20),
    smtpusername character varying(40),
    smtppass character varying(20),
    emailreplyto character varying(40),
    sendemail smallint DEFAULT 0 NOT NULL,
    version character varying(10),
    urlsite character varying(200)
);


--
-- TOC entry 203 (class 1259 OID 24626934)
-- Name: config_common; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.config_common (
    id integer NOT NULL,
    nameparam character varying(100) NOT NULL,
    valueparam character varying(100) NOT NULL
);


--
-- TOC entry 202 (class 1259 OID 24626932)
-- Name: config_common_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.config_common_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 3069 (class 0 OID 0)
-- Dependencies: 202
-- Name: config_common_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.config_common_id_seq OWNED BY public.config_common.id;


--
-- TOC entry 200 (class 1259 OID 24626918)
-- Name: config_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.config_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 3070 (class 0 OID 0)
-- Dependencies: 200
-- Name: config_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.config_id_seq OWNED BY public.config.id;


--
-- TOC entry 205 (class 1259 OID 24626942)
-- Name: contract; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.contract (
    id integer NOT NULL,
    kntid integer NOT NULL,
    name character varying(255) NOT NULL,
    datestart date NOT NULL,
    dateend date NOT NULL,
    work integer NOT NULL,
    comment character varying(255) NOT NULL,
    active smallint NOT NULL,
    num character varying(20) NOT NULL
);


--
-- TOC entry 204 (class 1259 OID 24626940)
-- Name: contract_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.contract_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 3071 (class 0 OID 0)
-- Dependencies: 204
-- Name: contract_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.contract_id_seq OWNED BY public.contract.id;


--
-- TOC entry 209 (class 1259 OID 24626966)
-- Name: eq_param; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.eq_param (
    id integer NOT NULL,
    grpid integer NOT NULL,
    paramid integer NOT NULL,
    eqid integer NOT NULL,
    param character varying(100) NOT NULL
);


--
-- TOC entry 208 (class 1259 OID 24626964)
-- Name: eq_param_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.eq_param_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 3072 (class 0 OID 0)
-- Dependencies: 208
-- Name: eq_param_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.eq_param_id_seq OWNED BY public.eq_param.id;


--
-- TOC entry 207 (class 1259 OID 24626953)
-- Name: equipment; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.equipment (
    id integer NOT NULL,
    orgid integer NOT NULL,
    placesid integer NOT NULL,
    usersid integer NOT NULL,
    nomeid integer NOT NULL,
    buhname character varying(255) NOT NULL,
    datepost timestamp without time zone NOT NULL,
    cost integer NOT NULL,
    currentcost integer NOT NULL,
    sernum character varying(100) NOT NULL,
    invnum character varying(100) NOT NULL,
    shtrihkod character varying(50) NOT NULL,
    os smallint NOT NULL,
    mode smallint NOT NULL,
    comment text NOT NULL,
    photo character varying(255) NOT NULL,
    repair smallint NOT NULL,
    active smallint NOT NULL,
    ip character varying(100) NOT NULL,
    mapx character varying(8) NOT NULL,
    mapy character varying(8) NOT NULL,
    mapmoved integer NOT NULL,
    mapyet smallint DEFAULT 0 NOT NULL,
    kntid integer NOT NULL,
    dtendgar date NOT NULL,
    tmcgo integer DEFAULT 0 NOT NULL
);


--
-- TOC entry 206 (class 1259 OID 24626951)
-- Name: equipment_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.equipment_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 3073 (class 0 OID 0)
-- Dependencies: 206
-- Name: equipment_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.equipment_id_seq OWNED BY public.equipment.id;


--
-- TOC entry 211 (class 1259 OID 24626974)
-- Name: files_contract; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.files_contract (
    id integer NOT NULL,
    idcontract integer NOT NULL,
    filename character varying(200) NOT NULL,
    userfreandlyfilename character varying(255) NOT NULL
);


--
-- TOC entry 210 (class 1259 OID 24626972)
-- Name: files_contract_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.files_contract_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 3074 (class 0 OID 0)
-- Dependencies: 210
-- Name: files_contract_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.files_contract_id_seq OWNED BY public.files_contract.id;


--
-- TOC entry 213 (class 1259 OID 24626982)
-- Name: group_nome; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.group_nome (
    id integer NOT NULL,
    name character varying(255) NOT NULL,
    comment text NOT NULL,
    active smallint NOT NULL
);


--
-- TOC entry 212 (class 1259 OID 24626980)
-- Name: group_nome_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.group_nome_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 3075 (class 0 OID 0)
-- Dependencies: 212
-- Name: group_nome_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.group_nome_id_seq OWNED BY public.group_nome.id;


--
-- TOC entry 215 (class 1259 OID 24626993)
-- Name: group_param; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.group_param (
    id integer NOT NULL,
    groupid integer NOT NULL,
    name character varying(100) NOT NULL,
    active smallint NOT NULL
);


--
-- TOC entry 214 (class 1259 OID 24626991)
-- Name: group_param_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.group_param_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 3076 (class 0 OID 0)
-- Dependencies: 214
-- Name: group_param_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.group_param_id_seq OWNED BY public.group_param.id;


--
-- TOC entry 217 (class 1259 OID 24627001)
-- Name: knt; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.knt (
    id integer NOT NULL,
    name character varying(100) NOT NULL,
    comment text NOT NULL,
    active smallint NOT NULL,
    fullname character varying(200) NOT NULL,
    erpcode integer NOT NULL,
    inn character varying(20) NOT NULL,
    kpp character varying(20) NOT NULL,
    bayer integer NOT NULL,
    supplier integer NOT NULL,
    dog integer NOT NULL
);


--
-- TOC entry 216 (class 1259 OID 24626999)
-- Name: knt_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.knt_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 3077 (class 0 OID 0)
-- Dependencies: 216
-- Name: knt_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.knt_id_seq OWNED BY public.knt.id;


--
-- TOC entry 219 (class 1259 OID 24627012)
-- Name: mailq; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.mailq (
    id integer NOT NULL,
    "from" character varying(200) NOT NULL,
    "to" character varying(200) NOT NULL,
    title character varying(200) NOT NULL,
    btxt text NOT NULL
);


--
-- TOC entry 218 (class 1259 OID 24627010)
-- Name: mailq_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.mailq_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 3078 (class 0 OID 0)
-- Dependencies: 218
-- Name: mailq_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.mailq_id_seq OWNED BY public.mailq.id;


--
-- TOC entry 221 (class 1259 OID 24627023)
-- Name: move; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.move (
    id integer NOT NULL,
    eqid integer NOT NULL,
    dt timestamp without time zone NOT NULL,
    orgidfrom integer NOT NULL,
    orgidto integer NOT NULL,
    placesidfrom integer NOT NULL,
    placesidto integer NOT NULL,
    useridfrom integer NOT NULL,
    useridto integer NOT NULL,
    comment text NOT NULL
);


--
-- TOC entry 220 (class 1259 OID 24627021)
-- Name: move_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.move_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 3079 (class 0 OID 0)
-- Dependencies: 220
-- Name: move_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.move_id_seq OWNED BY public.move.id;


--
-- TOC entry 223 (class 1259 OID 24627034)
-- Name: news; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.news (
    id integer NOT NULL,
    dt timestamp without time zone NOT NULL,
    title character varying(255) NOT NULL,
    body text NOT NULL,
    stiker smallint DEFAULT 0 NOT NULL
);


--
-- TOC entry 222 (class 1259 OID 24627032)
-- Name: news_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.news_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 3080 (class 0 OID 0)
-- Dependencies: 222
-- Name: news_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.news_id_seq OWNED BY public.news.id;


--
-- TOC entry 225 (class 1259 OID 24627046)
-- Name: nome; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.nome (
    id integer NOT NULL,
    groupid integer NOT NULL,
    vendorid integer NOT NULL,
    name character varying(200) NOT NULL,
    active smallint NOT NULL
);


--
-- TOC entry 224 (class 1259 OID 24627044)
-- Name: nome_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.nome_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 3081 (class 0 OID 0)
-- Dependencies: 224
-- Name: nome_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.nome_id_seq OWNED BY public.nome.id;


--
-- TOC entry 227 (class 1259 OID 24627054)
-- Name: org; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.org (
    id integer NOT NULL,
    name character varying(100) NOT NULL,
    picmap character varying(255),
    active smallint NOT NULL
);


--
-- TOC entry 226 (class 1259 OID 24627052)
-- Name: org_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.org_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 3082 (class 0 OID 0)
-- Dependencies: 226
-- Name: org_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.org_id_seq OWNED BY public.org.id;


--
-- TOC entry 229 (class 1259 OID 24627062)
-- Name: places; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.places (
    id integer NOT NULL,
    orgid integer NOT NULL,
    name character varying(150) NOT NULL,
    comment text NOT NULL,
    active smallint NOT NULL,
    opgroup character varying(100) NOT NULL
);


--
-- TOC entry 228 (class 1259 OID 24627060)
-- Name: places_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.places_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 3083 (class 0 OID 0)
-- Dependencies: 228
-- Name: places_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.places_id_seq OWNED BY public.places.id;


--
-- TOC entry 231 (class 1259 OID 24627073)
-- Name: places_users; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.places_users (
    id integer NOT NULL,
    placesid integer NOT NULL,
    userid integer NOT NULL
);


--
-- TOC entry 230 (class 1259 OID 24627071)
-- Name: places_users_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.places_users_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 3084 (class 0 OID 0)
-- Dependencies: 230
-- Name: places_users_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.places_users_id_seq OWNED BY public.places_users.id;


--
-- TOC entry 233 (class 1259 OID 24627081)
-- Name: repair; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.repair (
    id integer NOT NULL,
    dt date NOT NULL,
    kntid integer NOT NULL,
    eqid integer NOT NULL,
    cost double precision NOT NULL,
    comment text NOT NULL,
    dtend date NOT NULL,
    status smallint NOT NULL,
    userfrom integer NOT NULL,
    userto integer NOT NULL,
    doc text NOT NULL
);


--
-- TOC entry 232 (class 1259 OID 24627079)
-- Name: repair_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.repair_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 3085 (class 0 OID 0)
-- Dependencies: 232
-- Name: repair_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.repair_id_seq OWNED BY public.repair.id;


--
-- TOC entry 235 (class 1259 OID 24627092)
-- Name: users; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.users (
    id integer NOT NULL,
    randomid character varying(100) NOT NULL,
    orgid integer NOT NULL,
    login character varying(50) NOT NULL,
    password character varying(40) NOT NULL,
    salt character varying(10) NOT NULL,
    email character varying(100) NOT NULL,
    mode integer NOT NULL,
    lastdt timestamp without time zone NOT NULL,
    active smallint NOT NULL
);


--
-- TOC entry 234 (class 1259 OID 24627090)
-- Name: users_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.users_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 3086 (class 0 OID 0)
-- Dependencies: 234
-- Name: users_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.users_id_seq OWNED BY public.users.id;


--
-- TOC entry 239 (class 1259 OID 24627108)
-- Name: users_profile; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.users_profile (
    id integer NOT NULL,
    usersid integer,
    fio character varying(100) NOT NULL,
    post character varying(255) NOT NULL,
    telephonenumber character varying(20) NOT NULL,
    homephone character varying(20) NOT NULL,
    jpegphoto character varying(40) NOT NULL
);


--
-- TOC entry 238 (class 1259 OID 24627106)
-- Name: users_profile_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.users_profile_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 3087 (class 0 OID 0)
-- Dependencies: 238
-- Name: users_profile_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.users_profile_id_seq OWNED BY public.users_profile.id;


--
-- TOC entry 237 (class 1259 OID 24627100)
-- Name: usersroles; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.usersroles (
    id integer NOT NULL,
    userid integer NOT NULL,
    role smallint NOT NULL
);


--
-- TOC entry 236 (class 1259 OID 24627098)
-- Name: usersroles_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.usersroles_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 3088 (class 0 OID 0)
-- Dependencies: 236
-- Name: usersroles_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.usersroles_id_seq OWNED BY public.usersroles.id;


--
-- TOC entry 241 (class 1259 OID 24627118)
-- Name: vendor; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.vendor (
    id integer NOT NULL,
    name character varying(155) NOT NULL,
    comment text NOT NULL,
    active smallint NOT NULL
);


--
-- TOC entry 240 (class 1259 OID 24627116)
-- Name: vendor_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.vendor_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 3089 (class 0 OID 0)
-- Dependencies: 240
-- Name: vendor_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.vendor_id_seq OWNED BY public.vendor.id;


--
-- TOC entry 2817 (class 2604 OID 24626907)
-- Name: cloud_dirs id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.cloud_dirs ALTER COLUMN id SET DEFAULT nextval('public.cloud_dirs_id_seq'::regclass);


--
-- TOC entry 2818 (class 2604 OID 24626915)
-- Name: cloud_files id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.cloud_files ALTER COLUMN id SET DEFAULT nextval('public.cloud_files_id_seq'::regclass);


--
-- TOC entry 2819 (class 2604 OID 24626923)
-- Name: config id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.config ALTER COLUMN id SET DEFAULT nextval('public.config_id_seq'::regclass);


--
-- TOC entry 2823 (class 2604 OID 24626937)
-- Name: config_common id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.config_common ALTER COLUMN id SET DEFAULT nextval('public.config_common_id_seq'::regclass);


--
-- TOC entry 2824 (class 2604 OID 24626945)
-- Name: contract id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.contract ALTER COLUMN id SET DEFAULT nextval('public.contract_id_seq'::regclass);


--
-- TOC entry 2828 (class 2604 OID 24626969)
-- Name: eq_param id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.eq_param ALTER COLUMN id SET DEFAULT nextval('public.eq_param_id_seq'::regclass);


--
-- TOC entry 2825 (class 2604 OID 24626956)
-- Name: equipment id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.equipment ALTER COLUMN id SET DEFAULT nextval('public.equipment_id_seq'::regclass);


--
-- TOC entry 2829 (class 2604 OID 24626977)
-- Name: files_contract id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.files_contract ALTER COLUMN id SET DEFAULT nextval('public.files_contract_id_seq'::regclass);


--
-- TOC entry 2830 (class 2604 OID 24626985)
-- Name: group_nome id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.group_nome ALTER COLUMN id SET DEFAULT nextval('public.group_nome_id_seq'::regclass);


--
-- TOC entry 2831 (class 2604 OID 24626996)
-- Name: group_param id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.group_param ALTER COLUMN id SET DEFAULT nextval('public.group_param_id_seq'::regclass);


--
-- TOC entry 2832 (class 2604 OID 24627004)
-- Name: knt id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.knt ALTER COLUMN id SET DEFAULT nextval('public.knt_id_seq'::regclass);


--
-- TOC entry 2833 (class 2604 OID 24627015)
-- Name: mailq id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.mailq ALTER COLUMN id SET DEFAULT nextval('public.mailq_id_seq'::regclass);


--
-- TOC entry 2834 (class 2604 OID 24627026)
-- Name: move id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.move ALTER COLUMN id SET DEFAULT nextval('public.move_id_seq'::regclass);


--
-- TOC entry 2835 (class 2604 OID 24627037)
-- Name: news id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.news ALTER COLUMN id SET DEFAULT nextval('public.news_id_seq'::regclass);


--
-- TOC entry 2837 (class 2604 OID 24627049)
-- Name: nome id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.nome ALTER COLUMN id SET DEFAULT nextval('public.nome_id_seq'::regclass);


--
-- TOC entry 2838 (class 2604 OID 24627057)
-- Name: org id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.org ALTER COLUMN id SET DEFAULT nextval('public.org_id_seq'::regclass);


--
-- TOC entry 2839 (class 2604 OID 24627065)
-- Name: places id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.places ALTER COLUMN id SET DEFAULT nextval('public.places_id_seq'::regclass);


--
-- TOC entry 2840 (class 2604 OID 24627076)
-- Name: places_users id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.places_users ALTER COLUMN id SET DEFAULT nextval('public.places_users_id_seq'::regclass);


--
-- TOC entry 2841 (class 2604 OID 24627084)
-- Name: repair id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.repair ALTER COLUMN id SET DEFAULT nextval('public.repair_id_seq'::regclass);


--
-- TOC entry 2842 (class 2604 OID 24627095)
-- Name: users id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.users ALTER COLUMN id SET DEFAULT nextval('public.users_id_seq'::regclass);


--
-- TOC entry 2844 (class 2604 OID 24627111)
-- Name: users_profile id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.users_profile ALTER COLUMN id SET DEFAULT nextval('public.users_profile_id_seq'::regclass);


--
-- TOC entry 2843 (class 2604 OID 24627103)
-- Name: usersroles id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.usersroles ALTER COLUMN id SET DEFAULT nextval('public.usersroles_id_seq'::regclass);


--
-- TOC entry 2845 (class 2604 OID 24627121)
-- Name: vendor id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.vendor ALTER COLUMN id SET DEFAULT nextval('public.vendor_id_seq'::regclass);


--
-- TOC entry 3016 (class 0 OID 24626904)
-- Dependencies: 197
-- Data for Name: cloud_dirs; Type: TABLE DATA; Schema: public; Owner: -
--



--
-- TOC entry 3018 (class 0 OID 24626912)
-- Dependencies: 199
-- Data for Name: cloud_files; Type: TABLE DATA; Schema: public; Owner: -
--



--
-- TOC entry 3020 (class 0 OID 24626920)
-- Dependencies: 201
-- Data for Name: config; Type: TABLE DATA; Schema: public; Owner: -
--



--
-- TOC entry 3022 (class 0 OID 24626934)
-- Dependencies: 203
-- Data for Name: config_common; Type: TABLE DATA; Schema: public; Owner: -
--



--
-- TOC entry 3024 (class 0 OID 24626942)
-- Dependencies: 205
-- Data for Name: contract; Type: TABLE DATA; Schema: public; Owner: -
--



--
-- TOC entry 3028 (class 0 OID 24626966)
-- Dependencies: 209
-- Data for Name: eq_param; Type: TABLE DATA; Schema: public; Owner: -
--



--
-- TOC entry 3026 (class 0 OID 24626953)
-- Dependencies: 207
-- Data for Name: equipment; Type: TABLE DATA; Schema: public; Owner: -
--



--
-- TOC entry 3030 (class 0 OID 24626974)
-- Dependencies: 211
-- Data for Name: files_contract; Type: TABLE DATA; Schema: public; Owner: -
--



--
-- TOC entry 3032 (class 0 OID 24626982)
-- Dependencies: 213
-- Data for Name: group_nome; Type: TABLE DATA; Schema: public; Owner: -
--



--
-- TOC entry 3034 (class 0 OID 24626993)
-- Dependencies: 215
-- Data for Name: group_param; Type: TABLE DATA; Schema: public; Owner: -
--



--
-- TOC entry 3036 (class 0 OID 24627001)
-- Dependencies: 217
-- Data for Name: knt; Type: TABLE DATA; Schema: public; Owner: -
--



--
-- TOC entry 3038 (class 0 OID 24627012)
-- Dependencies: 219
-- Data for Name: mailq; Type: TABLE DATA; Schema: public; Owner: -
--



--
-- TOC entry 3040 (class 0 OID 24627023)
-- Dependencies: 221
-- Data for Name: move; Type: TABLE DATA; Schema: public; Owner: -
--



--
-- TOC entry 3042 (class 0 OID 24627034)
-- Dependencies: 223
-- Data for Name: news; Type: TABLE DATA; Schema: public; Owner: -
--



--
-- TOC entry 3044 (class 0 OID 24627046)
-- Dependencies: 225
-- Data for Name: nome; Type: TABLE DATA; Schema: public; Owner: -
--



--
-- TOC entry 3046 (class 0 OID 24627054)
-- Dependencies: 227
-- Data for Name: org; Type: TABLE DATA; Schema: public; Owner: -
--



--
-- TOC entry 3048 (class 0 OID 24627062)
-- Dependencies: 229
-- Data for Name: places; Type: TABLE DATA; Schema: public; Owner: -
--



--
-- TOC entry 3050 (class 0 OID 24627073)
-- Dependencies: 231
-- Data for Name: places_users; Type: TABLE DATA; Schema: public; Owner: -
--



--
-- TOC entry 3052 (class 0 OID 24627081)
-- Dependencies: 233
-- Data for Name: repair; Type: TABLE DATA; Schema: public; Owner: -
--



--
-- TOC entry 3054 (class 0 OID 24627092)
-- Dependencies: 235
-- Data for Name: users; Type: TABLE DATA; Schema: public; Owner: -
--



--
-- TOC entry 3058 (class 0 OID 24627108)
-- Dependencies: 239
-- Data for Name: users_profile; Type: TABLE DATA; Schema: public; Owner: -
--



--
-- TOC entry 3056 (class 0 OID 24627100)
-- Dependencies: 237
-- Data for Name: usersroles; Type: TABLE DATA; Schema: public; Owner: -
--



--
-- TOC entry 3060 (class 0 OID 24627118)
-- Dependencies: 241
-- Data for Name: vendor; Type: TABLE DATA; Schema: public; Owner: -
--



--
-- TOC entry 3090 (class 0 OID 0)
-- Dependencies: 196
-- Name: cloud_dirs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.cloud_dirs_id_seq', 1, false);


--
-- TOC entry 3091 (class 0 OID 0)
-- Dependencies: 198
-- Name: cloud_files_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.cloud_files_id_seq', 1, false);


--
-- TOC entry 3092 (class 0 OID 0)
-- Dependencies: 202
-- Name: config_common_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.config_common_id_seq', 1, false);


--
-- TOC entry 3093 (class 0 OID 0)
-- Dependencies: 200
-- Name: config_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.config_id_seq', 1, false);


--
-- TOC entry 3094 (class 0 OID 0)
-- Dependencies: 204
-- Name: contract_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.contract_id_seq', 1, false);


--
-- TOC entry 3095 (class 0 OID 0)
-- Dependencies: 208
-- Name: eq_param_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.eq_param_id_seq', 1, false);


--
-- TOC entry 3096 (class 0 OID 0)
-- Dependencies: 206
-- Name: equipment_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.equipment_id_seq', 1, false);


--
-- TOC entry 3097 (class 0 OID 0)
-- Dependencies: 210
-- Name: files_contract_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.files_contract_id_seq', 1, false);


--
-- TOC entry 3098 (class 0 OID 0)
-- Dependencies: 212
-- Name: group_nome_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.group_nome_id_seq', 1, false);


--
-- TOC entry 3099 (class 0 OID 0)
-- Dependencies: 214
-- Name: group_param_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.group_param_id_seq', 1, false);


--
-- TOC entry 3100 (class 0 OID 0)
-- Dependencies: 216
-- Name: knt_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.knt_id_seq', 1, false);


--
-- TOC entry 3101 (class 0 OID 0)
-- Dependencies: 218
-- Name: mailq_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.mailq_id_seq', 1, false);


--
-- TOC entry 3102 (class 0 OID 0)
-- Dependencies: 220
-- Name: move_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.move_id_seq', 1, false);


--
-- TOC entry 3103 (class 0 OID 0)
-- Dependencies: 222
-- Name: news_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.news_id_seq', 1, false);


--
-- TOC entry 3104 (class 0 OID 0)
-- Dependencies: 224
-- Name: nome_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.nome_id_seq', 1, false);


--
-- TOC entry 3105 (class 0 OID 0)
-- Dependencies: 226
-- Name: org_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.org_id_seq', 1, false);


--
-- TOC entry 3106 (class 0 OID 0)
-- Dependencies: 228
-- Name: places_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.places_id_seq', 1, false);


--
-- TOC entry 3107 (class 0 OID 0)
-- Dependencies: 230
-- Name: places_users_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.places_users_id_seq', 1, false);


--
-- TOC entry 3108 (class 0 OID 0)
-- Dependencies: 232
-- Name: repair_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.repair_id_seq', 1, false);


--
-- TOC entry 3109 (class 0 OID 0)
-- Dependencies: 234
-- Name: users_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.users_id_seq', 1, false);


--
-- TOC entry 3110 (class 0 OID 0)
-- Dependencies: 238
-- Name: users_profile_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.users_profile_id_seq', 1, false);


--
-- TOC entry 3111 (class 0 OID 0)
-- Dependencies: 236
-- Name: usersroles_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.usersroles_id_seq', 1, false);


--
-- TOC entry 3112 (class 0 OID 0)
-- Dependencies: 240
-- Name: vendor_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.vendor_id_seq', 1, false);


--
-- TOC entry 2847 (class 2606 OID 24626909)
-- Name: cloud_dirs cloud_dirs_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.cloud_dirs
    ADD CONSTRAINT cloud_dirs_pkey PRIMARY KEY (id);


--
-- TOC entry 2849 (class 2606 OID 24626917)
-- Name: cloud_files cloud_files_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.cloud_files
    ADD CONSTRAINT cloud_files_pkey PRIMARY KEY (id);


--
-- TOC entry 2853 (class 2606 OID 24626939)
-- Name: config_common config_common_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.config_common
    ADD CONSTRAINT config_common_pkey PRIMARY KEY (id);


--
-- TOC entry 2851 (class 2606 OID 24626931)
-- Name: config config_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.config
    ADD CONSTRAINT config_pkey PRIMARY KEY (id);


--
-- TOC entry 2855 (class 2606 OID 24626950)
-- Name: contract contract_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.contract
    ADD CONSTRAINT contract_pkey PRIMARY KEY (id);


--
-- TOC entry 2859 (class 2606 OID 24626971)
-- Name: eq_param eq_param_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.eq_param
    ADD CONSTRAINT eq_param_pkey PRIMARY KEY (id);


--
-- TOC entry 2857 (class 2606 OID 24626963)
-- Name: equipment equipment_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.equipment
    ADD CONSTRAINT equipment_pkey PRIMARY KEY (id);


--
-- TOC entry 2861 (class 2606 OID 24626979)
-- Name: files_contract files_contract_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.files_contract
    ADD CONSTRAINT files_contract_pkey PRIMARY KEY (id);


--
-- TOC entry 2863 (class 2606 OID 24626990)
-- Name: group_nome group_nome_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.group_nome
    ADD CONSTRAINT group_nome_pkey PRIMARY KEY (id);


--
-- TOC entry 2865 (class 2606 OID 24626998)
-- Name: group_param group_param_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.group_param
    ADD CONSTRAINT group_param_pkey PRIMARY KEY (id);


--
-- TOC entry 2867 (class 2606 OID 24627009)
-- Name: knt knt_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.knt
    ADD CONSTRAINT knt_pkey PRIMARY KEY (id);


--
-- TOC entry 2869 (class 2606 OID 24627020)
-- Name: mailq mailq_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.mailq
    ADD CONSTRAINT mailq_pkey PRIMARY KEY (id);


--
-- TOC entry 2871 (class 2606 OID 24627031)
-- Name: move move_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.move
    ADD CONSTRAINT move_pkey PRIMARY KEY (id);


--
-- TOC entry 2873 (class 2606 OID 24627043)
-- Name: news news_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.news
    ADD CONSTRAINT news_pkey PRIMARY KEY (id);


--
-- TOC entry 2875 (class 2606 OID 24627051)
-- Name: nome nome_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.nome
    ADD CONSTRAINT nome_pkey PRIMARY KEY (id);


--
-- TOC entry 2877 (class 2606 OID 24627059)
-- Name: org org_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.org
    ADD CONSTRAINT org_pkey PRIMARY KEY (id);


--
-- TOC entry 2879 (class 2606 OID 24627070)
-- Name: places places_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.places
    ADD CONSTRAINT places_pkey PRIMARY KEY (id);


--
-- TOC entry 2881 (class 2606 OID 24627078)
-- Name: places_users places_users_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.places_users
    ADD CONSTRAINT places_users_pkey PRIMARY KEY (id);


--
-- TOC entry 2883 (class 2606 OID 24627089)
-- Name: repair repair_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.repair
    ADD CONSTRAINT repair_pkey PRIMARY KEY (id);


--
-- TOC entry 2885 (class 2606 OID 24627097)
-- Name: users users_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_pkey PRIMARY KEY (id);


--
-- TOC entry 2889 (class 2606 OID 24627113)
-- Name: users_profile users_profile_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.users_profile
    ADD CONSTRAINT users_profile_pkey PRIMARY KEY (id);


--
-- TOC entry 2891 (class 2606 OID 24627115)
-- Name: users_profile users_profile_usersid_key; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.users_profile
    ADD CONSTRAINT users_profile_usersid_key UNIQUE (usersid);


--
-- TOC entry 2887 (class 2606 OID 24627105)
-- Name: usersroles usersroles_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.usersroles
    ADD CONSTRAINT usersroles_pkey PRIMARY KEY (id);


--
-- TOC entry 2893 (class 2606 OID 24627126)
-- Name: vendor vendor_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.vendor
    ADD CONSTRAINT vendor_pkey PRIMARY KEY (id);


-- Completed on 2020-03-27 22:58:05

--
-- PostgreSQL database dump complete
--

