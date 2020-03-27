--
-- PostgreSQL database dump
--

-- Dumped from database version 10.12
-- Dumped by pg_dump version 11.2

-- Started on 2020-03-26 13:18:31

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
ALTER TABLE IF EXISTS public.usersroles ALTER COLUMN userid DROP DEFAULT;
ALTER TABLE IF EXISTS public.usersroles ALTER COLUMN id DROP DEFAULT;
ALTER TABLE IF EXISTS public.users_profile ALTER COLUMN usersid DROP DEFAULT;
ALTER TABLE IF EXISTS public.users_profile ALTER COLUMN id DROP DEFAULT;
ALTER TABLE IF EXISTS public.users ALTER COLUMN orgid DROP DEFAULT;
ALTER TABLE IF EXISTS public.users ALTER COLUMN id DROP DEFAULT;
ALTER TABLE IF EXISTS public.repair ALTER COLUMN eqid DROP DEFAULT;
ALTER TABLE IF EXISTS public.repair ALTER COLUMN kntid DROP DEFAULT;
ALTER TABLE IF EXISTS public.repair ALTER COLUMN id DROP DEFAULT;
ALTER TABLE IF EXISTS public.places_users ALTER COLUMN userid DROP DEFAULT;
ALTER TABLE IF EXISTS public.places_users ALTER COLUMN placesid DROP DEFAULT;
ALTER TABLE IF EXISTS public.places_users ALTER COLUMN id DROP DEFAULT;
ALTER TABLE IF EXISTS public.places ALTER COLUMN orgid DROP DEFAULT;
ALTER TABLE IF EXISTS public.places ALTER COLUMN id DROP DEFAULT;
ALTER TABLE IF EXISTS public.org ALTER COLUMN id DROP DEFAULT;
ALTER TABLE IF EXISTS public.nome ALTER COLUMN vendorid DROP DEFAULT;
ALTER TABLE IF EXISTS public.nome ALTER COLUMN groupid DROP DEFAULT;
ALTER TABLE IF EXISTS public.nome ALTER COLUMN id DROP DEFAULT;
ALTER TABLE IF EXISTS public.news ALTER COLUMN id DROP DEFAULT;
ALTER TABLE IF EXISTS public.move ALTER COLUMN eqid DROP DEFAULT;
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
DROP SEQUENCE IF EXISTS public.usersroles_userid_seq;
DROP SEQUENCE IF EXISTS public.usersroles_id_seq;
DROP TABLE IF EXISTS public.usersroles;
DROP SEQUENCE IF EXISTS public.users_profile_usersid_seq;
DROP SEQUENCE IF EXISTS public.users_profile_id_seq;
DROP TABLE IF EXISTS public.users_profile;
DROP SEQUENCE IF EXISTS public.users_orgid_seq;
DROP SEQUENCE IF EXISTS public.users_id_seq;
DROP TABLE IF EXISTS public.users;
DROP SEQUENCE IF EXISTS public.repair_kntid_seq;
DROP SEQUENCE IF EXISTS public.repair_id_seq;
DROP SEQUENCE IF EXISTS public.repair_eqid_seq;
DROP TABLE IF EXISTS public.repair;
DROP SEQUENCE IF EXISTS public.places_users_userid_seq;
DROP SEQUENCE IF EXISTS public.places_users_placesid_seq;
DROP SEQUENCE IF EXISTS public.places_users_id_seq;
DROP TABLE IF EXISTS public.places_users;
DROP SEQUENCE IF EXISTS public.places_orgid_seq;
DROP SEQUENCE IF EXISTS public.places_id_seq;
DROP TABLE IF EXISTS public.places;
DROP SEQUENCE IF EXISTS public.org_id_seq;
DROP TABLE IF EXISTS public.org;
DROP SEQUENCE IF EXISTS public.nome_vendorid_seq;
DROP SEQUENCE IF EXISTS public.nome_id_seq;
DROP SEQUENCE IF EXISTS public.nome_groupid_seq;
DROP TABLE IF EXISTS public.nome;
DROP SEQUENCE IF EXISTS public.news_id_seq;
DROP TABLE IF EXISTS public.news;
DROP SEQUENCE IF EXISTS public.move_id_seq;
DROP SEQUENCE IF EXISTS public.move_eqid_seq;
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
DROP SCHEMA IF EXISTS public;
--
-- TOC entry 4 (class 2615 OID 2200)
-- Name: public; Type: SCHEMA; Schema: -; Owner: -
--

CREATE SCHEMA public;


--
-- TOC entry 3143 (class 0 OID 0)
-- Dependencies: 4
-- Name: SCHEMA public; Type: COMMENT; Schema: -; Owner: -
--

COMMENT ON SCHEMA public IS 'standard public schema';


--
-- TOC entry 303 (class 1255 OID 24625857)
-- Name: sha1(bytea); Type: FUNCTION; Schema: public; Owner: -
--

CREATE FUNCTION public.sha1(bytea) RETURNS text
    LANGUAGE sql IMMUTABLE STRICT
    AS $_$
SELECT encode(digest($1, 'sha1'), 'hex')
$_$;


--
-- TOC entry 302 (class 1255 OID 24625856)
-- Name: sha1(text); Type: FUNCTION; Schema: public; Owner: -
--

CREATE FUNCTION public.sha1(text) RETURNS text
    LANGUAGE sql IMMUTABLE STRICT
    AS $_$
SELECT encode(digest($1, 'sha1'), 'hex')
$_$;


SET default_tablespace = '';

SET default_with_oids = false;

--
-- TOC entry 198 (class 1259 OID 24625602)
-- Name: cloud_dirs; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.cloud_dirs (
    id integer NOT NULL,
    parent integer NOT NULL,
    name character varying(100) NOT NULL
);


--
-- TOC entry 197 (class 1259 OID 24625600)
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
-- TOC entry 3144 (class 0 OID 0)
-- Dependencies: 197
-- Name: cloud_dirs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.cloud_dirs_id_seq OWNED BY public.cloud_dirs.id;


--
-- TOC entry 200 (class 1259 OID 24625610)
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
-- TOC entry 199 (class 1259 OID 24625608)
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
-- TOC entry 3145 (class 0 OID 0)
-- Dependencies: 199
-- Name: cloud_files_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.cloud_files_id_seq OWNED BY public.cloud_files.id;


--
-- TOC entry 202 (class 1259 OID 24625618)
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
-- TOC entry 204 (class 1259 OID 24625632)
-- Name: config_common; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.config_common (
    id integer NOT NULL,
    nameparam character varying(100) NOT NULL,
    valueparam character varying(100) NOT NULL
);


--
-- TOC entry 203 (class 1259 OID 24625630)
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
-- TOC entry 3146 (class 0 OID 0)
-- Dependencies: 203
-- Name: config_common_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.config_common_id_seq OWNED BY public.config_common.id;


--
-- TOC entry 201 (class 1259 OID 24625616)
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
-- TOC entry 3147 (class 0 OID 0)
-- Dependencies: 201
-- Name: config_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.config_id_seq OWNED BY public.config.id;


--
-- TOC entry 206 (class 1259 OID 24625640)
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
-- TOC entry 205 (class 1259 OID 24625638)
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
-- TOC entry 3148 (class 0 OID 0)
-- Dependencies: 205
-- Name: contract_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.contract_id_seq OWNED BY public.contract.id;


--
-- TOC entry 210 (class 1259 OID 24625664)
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
-- TOC entry 209 (class 1259 OID 24625662)
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
-- TOC entry 3149 (class 0 OID 0)
-- Dependencies: 209
-- Name: eq_param_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.eq_param_id_seq OWNED BY public.eq_param.id;


--
-- TOC entry 208 (class 1259 OID 24625651)
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
-- TOC entry 207 (class 1259 OID 24625649)
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
-- TOC entry 3150 (class 0 OID 0)
-- Dependencies: 207
-- Name: equipment_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.equipment_id_seq OWNED BY public.equipment.id;


--
-- TOC entry 212 (class 1259 OID 24625672)
-- Name: files_contract; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.files_contract (
    id integer NOT NULL,
    idcontract integer NOT NULL,
    filename character varying(200) NOT NULL,
    userfreandlyfilename character varying(255) NOT NULL
);


--
-- TOC entry 211 (class 1259 OID 24625670)
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
-- TOC entry 3151 (class 0 OID 0)
-- Dependencies: 211
-- Name: files_contract_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.files_contract_id_seq OWNED BY public.files_contract.id;


--
-- TOC entry 214 (class 1259 OID 24625680)
-- Name: group_nome; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.group_nome (
    id integer NOT NULL,
    name character varying(255) NOT NULL,
    comment text NOT NULL,
    active smallint NOT NULL
);


--
-- TOC entry 213 (class 1259 OID 24625678)
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
-- TOC entry 3152 (class 0 OID 0)
-- Dependencies: 213
-- Name: group_nome_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.group_nome_id_seq OWNED BY public.group_nome.id;


--
-- TOC entry 216 (class 1259 OID 24625691)
-- Name: group_param; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.group_param (
    id integer NOT NULL,
    groupid integer NOT NULL,
    name character varying(100) NOT NULL,
    active smallint NOT NULL
);


--
-- TOC entry 215 (class 1259 OID 24625689)
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
-- TOC entry 3153 (class 0 OID 0)
-- Dependencies: 215
-- Name: group_param_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.group_param_id_seq OWNED BY public.group_param.id;


--
-- TOC entry 218 (class 1259 OID 24625699)
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
-- TOC entry 217 (class 1259 OID 24625697)
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
-- TOC entry 3154 (class 0 OID 0)
-- Dependencies: 217
-- Name: knt_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.knt_id_seq OWNED BY public.knt.id;


--
-- TOC entry 220 (class 1259 OID 24625710)
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
-- TOC entry 219 (class 1259 OID 24625708)
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
-- TOC entry 3155 (class 0 OID 0)
-- Dependencies: 219
-- Name: mailq_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.mailq_id_seq OWNED BY public.mailq.id;


--
-- TOC entry 223 (class 1259 OID 24625723)
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
-- TOC entry 222 (class 1259 OID 24625721)
-- Name: move_eqid_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.move_eqid_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 3156 (class 0 OID 0)
-- Dependencies: 222
-- Name: move_eqid_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.move_eqid_seq OWNED BY public.move.eqid;


--
-- TOC entry 221 (class 1259 OID 24625719)
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
-- TOC entry 3157 (class 0 OID 0)
-- Dependencies: 221
-- Name: move_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.move_id_seq OWNED BY public.move.id;


--
-- TOC entry 225 (class 1259 OID 24625735)
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
-- TOC entry 224 (class 1259 OID 24625733)
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
-- TOC entry 3158 (class 0 OID 0)
-- Dependencies: 224
-- Name: news_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.news_id_seq OWNED BY public.news.id;


--
-- TOC entry 229 (class 1259 OID 24625751)
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
-- TOC entry 227 (class 1259 OID 24625747)
-- Name: nome_groupid_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.nome_groupid_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 3159 (class 0 OID 0)
-- Dependencies: 227
-- Name: nome_groupid_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.nome_groupid_seq OWNED BY public.nome.groupid;


--
-- TOC entry 226 (class 1259 OID 24625745)
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
-- TOC entry 3160 (class 0 OID 0)
-- Dependencies: 226
-- Name: nome_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.nome_id_seq OWNED BY public.nome.id;


--
-- TOC entry 228 (class 1259 OID 24625749)
-- Name: nome_vendorid_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.nome_vendorid_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 3161 (class 0 OID 0)
-- Dependencies: 228
-- Name: nome_vendorid_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.nome_vendorid_seq OWNED BY public.nome.vendorid;


--
-- TOC entry 231 (class 1259 OID 24625761)
-- Name: org; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.org (
    id integer NOT NULL,
    name character varying(100) NOT NULL,
    picmap character varying(255),
    active smallint NOT NULL
);


--
-- TOC entry 230 (class 1259 OID 24625759)
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
-- TOC entry 3162 (class 0 OID 0)
-- Dependencies: 230
-- Name: org_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.org_id_seq OWNED BY public.org.id;


--
-- TOC entry 234 (class 1259 OID 24625771)
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
-- TOC entry 232 (class 1259 OID 24625767)
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
-- TOC entry 3163 (class 0 OID 0)
-- Dependencies: 232
-- Name: places_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.places_id_seq OWNED BY public.places.id;


--
-- TOC entry 233 (class 1259 OID 24625769)
-- Name: places_orgid_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.places_orgid_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 3164 (class 0 OID 0)
-- Dependencies: 233
-- Name: places_orgid_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.places_orgid_seq OWNED BY public.places.orgid;


--
-- TOC entry 238 (class 1259 OID 24625787)
-- Name: places_users; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.places_users (
    id integer NOT NULL,
    placesid integer NOT NULL,
    userid integer NOT NULL
);


--
-- TOC entry 235 (class 1259 OID 24625781)
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
-- TOC entry 3165 (class 0 OID 0)
-- Dependencies: 235
-- Name: places_users_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.places_users_id_seq OWNED BY public.places_users.id;


--
-- TOC entry 236 (class 1259 OID 24625783)
-- Name: places_users_placesid_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.places_users_placesid_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 3166 (class 0 OID 0)
-- Dependencies: 236
-- Name: places_users_placesid_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.places_users_placesid_seq OWNED BY public.places_users.placesid;


--
-- TOC entry 237 (class 1259 OID 24625785)
-- Name: places_users_userid_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.places_users_userid_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 3167 (class 0 OID 0)
-- Dependencies: 237
-- Name: places_users_userid_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.places_users_userid_seq OWNED BY public.places_users.userid;


--
-- TOC entry 242 (class 1259 OID 24625801)
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
-- TOC entry 241 (class 1259 OID 24625799)
-- Name: repair_eqid_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.repair_eqid_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 3168 (class 0 OID 0)
-- Dependencies: 241
-- Name: repair_eqid_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.repair_eqid_seq OWNED BY public.repair.eqid;


--
-- TOC entry 239 (class 1259 OID 24625795)
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
-- TOC entry 3169 (class 0 OID 0)
-- Dependencies: 239
-- Name: repair_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.repair_id_seq OWNED BY public.repair.id;


--
-- TOC entry 240 (class 1259 OID 24625797)
-- Name: repair_kntid_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.repair_kntid_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 3170 (class 0 OID 0)
-- Dependencies: 240
-- Name: repair_kntid_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.repair_kntid_seq OWNED BY public.repair.kntid;


--
-- TOC entry 245 (class 1259 OID 24625816)
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
-- TOC entry 243 (class 1259 OID 24625812)
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
-- TOC entry 3171 (class 0 OID 0)
-- Dependencies: 243
-- Name: users_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.users_id_seq OWNED BY public.users.id;


--
-- TOC entry 244 (class 1259 OID 24625814)
-- Name: users_orgid_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.users_orgid_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 3172 (class 0 OID 0)
-- Dependencies: 244
-- Name: users_orgid_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.users_orgid_seq OWNED BY public.users.orgid;


--
-- TOC entry 251 (class 1259 OID 24625838)
-- Name: users_profile; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.users_profile (
    id integer NOT NULL,
    usersid integer NOT NULL,
    fio character varying(100) NOT NULL,
    post character varying(255) NOT NULL,
    telephonenumber character varying(20) NOT NULL,
    homephone character varying(20) NOT NULL,
    jpegphoto character varying(40) NOT NULL
);


--
-- TOC entry 249 (class 1259 OID 24625834)
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
-- TOC entry 3173 (class 0 OID 0)
-- Dependencies: 249
-- Name: users_profile_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.users_profile_id_seq OWNED BY public.users_profile.id;


--
-- TOC entry 250 (class 1259 OID 24625836)
-- Name: users_profile_usersid_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.users_profile_usersid_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 3174 (class 0 OID 0)
-- Dependencies: 250
-- Name: users_profile_usersid_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.users_profile_usersid_seq OWNED BY public.users_profile.usersid;


--
-- TOC entry 248 (class 1259 OID 24625827)
-- Name: usersroles; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.usersroles (
    id integer NOT NULL,
    userid integer NOT NULL,
    role smallint NOT NULL
);


--
-- TOC entry 246 (class 1259 OID 24625823)
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
-- TOC entry 3175 (class 0 OID 0)
-- Dependencies: 246
-- Name: usersroles_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.usersroles_id_seq OWNED BY public.usersroles.id;


--
-- TOC entry 247 (class 1259 OID 24625825)
-- Name: usersroles_userid_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.usersroles_userid_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 3176 (class 0 OID 0)
-- Dependencies: 247
-- Name: usersroles_userid_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.usersroles_userid_seq OWNED BY public.usersroles.userid;


--
-- TOC entry 253 (class 1259 OID 24625847)
-- Name: vendor; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.vendor (
    id integer NOT NULL,
    name character varying(155) NOT NULL,
    comment text NOT NULL,
    active smallint NOT NULL
);


--
-- TOC entry 252 (class 1259 OID 24625845)
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
-- TOC entry 3177 (class 0 OID 0)
-- Dependencies: 252
-- Name: vendor_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.vendor_id_seq OWNED BY public.vendor.id;


--
-- TOC entry 2874 (class 2604 OID 24625605)
-- Name: cloud_dirs id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.cloud_dirs ALTER COLUMN id SET DEFAULT nextval('public.cloud_dirs_id_seq'::regclass);


--
-- TOC entry 2875 (class 2604 OID 24625613)
-- Name: cloud_files id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.cloud_files ALTER COLUMN id SET DEFAULT nextval('public.cloud_files_id_seq'::regclass);


--
-- TOC entry 2876 (class 2604 OID 24625621)
-- Name: config id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.config ALTER COLUMN id SET DEFAULT nextval('public.config_id_seq'::regclass);


--
-- TOC entry 2880 (class 2604 OID 24625635)
-- Name: config_common id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.config_common ALTER COLUMN id SET DEFAULT nextval('public.config_common_id_seq'::regclass);


--
-- TOC entry 2881 (class 2604 OID 24625643)
-- Name: contract id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.contract ALTER COLUMN id SET DEFAULT nextval('public.contract_id_seq'::regclass);


--
-- TOC entry 2885 (class 2604 OID 24625667)
-- Name: eq_param id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.eq_param ALTER COLUMN id SET DEFAULT nextval('public.eq_param_id_seq'::regclass);


--
-- TOC entry 2882 (class 2604 OID 24625654)
-- Name: equipment id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.equipment ALTER COLUMN id SET DEFAULT nextval('public.equipment_id_seq'::regclass);


--
-- TOC entry 2886 (class 2604 OID 24625675)
-- Name: files_contract id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.files_contract ALTER COLUMN id SET DEFAULT nextval('public.files_contract_id_seq'::regclass);


--
-- TOC entry 2887 (class 2604 OID 24625683)
-- Name: group_nome id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.group_nome ALTER COLUMN id SET DEFAULT nextval('public.group_nome_id_seq'::regclass);


--
-- TOC entry 2888 (class 2604 OID 24625694)
-- Name: group_param id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.group_param ALTER COLUMN id SET DEFAULT nextval('public.group_param_id_seq'::regclass);


--
-- TOC entry 2889 (class 2604 OID 24625702)
-- Name: knt id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.knt ALTER COLUMN id SET DEFAULT nextval('public.knt_id_seq'::regclass);


--
-- TOC entry 2890 (class 2604 OID 24625713)
-- Name: mailq id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.mailq ALTER COLUMN id SET DEFAULT nextval('public.mailq_id_seq'::regclass);


--
-- TOC entry 2891 (class 2604 OID 24625726)
-- Name: move id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.move ALTER COLUMN id SET DEFAULT nextval('public.move_id_seq'::regclass);


--
-- TOC entry 2892 (class 2604 OID 24625727)
-- Name: move eqid; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.move ALTER COLUMN eqid SET DEFAULT nextval('public.move_eqid_seq'::regclass);


--
-- TOC entry 2893 (class 2604 OID 24625738)
-- Name: news id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.news ALTER COLUMN id SET DEFAULT nextval('public.news_id_seq'::regclass);


--
-- TOC entry 2895 (class 2604 OID 24625754)
-- Name: nome id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.nome ALTER COLUMN id SET DEFAULT nextval('public.nome_id_seq'::regclass);


--
-- TOC entry 2896 (class 2604 OID 24625755)
-- Name: nome groupid; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.nome ALTER COLUMN groupid SET DEFAULT nextval('public.nome_groupid_seq'::regclass);


--
-- TOC entry 2897 (class 2604 OID 24625756)
-- Name: nome vendorid; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.nome ALTER COLUMN vendorid SET DEFAULT nextval('public.nome_vendorid_seq'::regclass);


--
-- TOC entry 2898 (class 2604 OID 24625764)
-- Name: org id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.org ALTER COLUMN id SET DEFAULT nextval('public.org_id_seq'::regclass);


--
-- TOC entry 2899 (class 2604 OID 24625774)
-- Name: places id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.places ALTER COLUMN id SET DEFAULT nextval('public.places_id_seq'::regclass);


--
-- TOC entry 2900 (class 2604 OID 24625775)
-- Name: places orgid; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.places ALTER COLUMN orgid SET DEFAULT nextval('public.places_orgid_seq'::regclass);


--
-- TOC entry 2901 (class 2604 OID 24625790)
-- Name: places_users id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.places_users ALTER COLUMN id SET DEFAULT nextval('public.places_users_id_seq'::regclass);


--
-- TOC entry 2902 (class 2604 OID 24625791)
-- Name: places_users placesid; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.places_users ALTER COLUMN placesid SET DEFAULT nextval('public.places_users_placesid_seq'::regclass);


--
-- TOC entry 2903 (class 2604 OID 24625792)
-- Name: places_users userid; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.places_users ALTER COLUMN userid SET DEFAULT nextval('public.places_users_userid_seq'::regclass);


--
-- TOC entry 2904 (class 2604 OID 24625804)
-- Name: repair id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.repair ALTER COLUMN id SET DEFAULT nextval('public.repair_id_seq'::regclass);


--
-- TOC entry 2905 (class 2604 OID 24625805)
-- Name: repair kntid; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.repair ALTER COLUMN kntid SET DEFAULT nextval('public.repair_kntid_seq'::regclass);


--
-- TOC entry 2906 (class 2604 OID 24625806)
-- Name: repair eqid; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.repair ALTER COLUMN eqid SET DEFAULT nextval('public.repair_eqid_seq'::regclass);


--
-- TOC entry 2907 (class 2604 OID 24625819)
-- Name: users id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.users ALTER COLUMN id SET DEFAULT nextval('public.users_id_seq'::regclass);


--
-- TOC entry 2908 (class 2604 OID 24625820)
-- Name: users orgid; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.users ALTER COLUMN orgid SET DEFAULT nextval('public.users_orgid_seq'::regclass);


--
-- TOC entry 2911 (class 2604 OID 24625841)
-- Name: users_profile id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.users_profile ALTER COLUMN id SET DEFAULT nextval('public.users_profile_id_seq'::regclass);


--
-- TOC entry 2912 (class 2604 OID 24625842)
-- Name: users_profile usersid; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.users_profile ALTER COLUMN usersid SET DEFAULT nextval('public.users_profile_usersid_seq'::regclass);


--
-- TOC entry 2909 (class 2604 OID 24625830)
-- Name: usersroles id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.usersroles ALTER COLUMN id SET DEFAULT nextval('public.usersroles_id_seq'::regclass);


--
-- TOC entry 2910 (class 2604 OID 24625831)
-- Name: usersroles userid; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.usersroles ALTER COLUMN userid SET DEFAULT nextval('public.usersroles_userid_seq'::regclass);


--
-- TOC entry 2913 (class 2604 OID 24625850)
-- Name: vendor id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.vendor ALTER COLUMN id SET DEFAULT nextval('public.vendor_id_seq'::regclass);


--
-- TOC entry 3082 (class 0 OID 24625602)
-- Dependencies: 198
-- Data for Name: cloud_dirs; Type: TABLE DATA; Schema: public; Owner: -
--



--
-- TOC entry 3084 (class 0 OID 24625610)
-- Dependencies: 200
-- Data for Name: cloud_files; Type: TABLE DATA; Schema: public; Owner: -
--



--
-- TOC entry 3086 (class 0 OID 24625618)
-- Dependencies: 202
-- Data for Name: config; Type: TABLE DATA; Schema: public; Owner: -
--



--
-- TOC entry 3088 (class 0 OID 24625632)
-- Dependencies: 204
-- Data for Name: config_common; Type: TABLE DATA; Schema: public; Owner: -
--



--
-- TOC entry 3090 (class 0 OID 24625640)
-- Dependencies: 206
-- Data for Name: contract; Type: TABLE DATA; Schema: public; Owner: -
--



--
-- TOC entry 3094 (class 0 OID 24625664)
-- Dependencies: 210
-- Data for Name: eq_param; Type: TABLE DATA; Schema: public; Owner: -
--



--
-- TOC entry 3092 (class 0 OID 24625651)
-- Dependencies: 208
-- Data for Name: equipment; Type: TABLE DATA; Schema: public; Owner: -
--



--
-- TOC entry 3096 (class 0 OID 24625672)
-- Dependencies: 212
-- Data for Name: files_contract; Type: TABLE DATA; Schema: public; Owner: -
--



--
-- TOC entry 3098 (class 0 OID 24625680)
-- Dependencies: 214
-- Data for Name: group_nome; Type: TABLE DATA; Schema: public; Owner: -
--



--
-- TOC entry 3100 (class 0 OID 24625691)
-- Dependencies: 216
-- Data for Name: group_param; Type: TABLE DATA; Schema: public; Owner: -
--



--
-- TOC entry 3102 (class 0 OID 24625699)
-- Dependencies: 218
-- Data for Name: knt; Type: TABLE DATA; Schema: public; Owner: -
--



--
-- TOC entry 3104 (class 0 OID 24625710)
-- Dependencies: 220
-- Data for Name: mailq; Type: TABLE DATA; Schema: public; Owner: -
--



--
-- TOC entry 3107 (class 0 OID 24625723)
-- Dependencies: 223
-- Data for Name: move; Type: TABLE DATA; Schema: public; Owner: -
--



--
-- TOC entry 3109 (class 0 OID 24625735)
-- Dependencies: 225
-- Data for Name: news; Type: TABLE DATA; Schema: public; Owner: -
--



--
-- TOC entry 3113 (class 0 OID 24625751)
-- Dependencies: 229
-- Data for Name: nome; Type: TABLE DATA; Schema: public; Owner: -
--



--
-- TOC entry 3115 (class 0 OID 24625761)
-- Dependencies: 231
-- Data for Name: org; Type: TABLE DATA; Schema: public; Owner: -
--



--
-- TOC entry 3118 (class 0 OID 24625771)
-- Dependencies: 234
-- Data for Name: places; Type: TABLE DATA; Schema: public; Owner: -
--



--
-- TOC entry 3122 (class 0 OID 24625787)
-- Dependencies: 238
-- Data for Name: places_users; Type: TABLE DATA; Schema: public; Owner: -
--



--
-- TOC entry 3126 (class 0 OID 24625801)
-- Dependencies: 242
-- Data for Name: repair; Type: TABLE DATA; Schema: public; Owner: -
--



--
-- TOC entry 3129 (class 0 OID 24625816)
-- Dependencies: 245
-- Data for Name: users; Type: TABLE DATA; Schema: public; Owner: -
--



--
-- TOC entry 3135 (class 0 OID 24625838)
-- Dependencies: 251
-- Data for Name: users_profile; Type: TABLE DATA; Schema: public; Owner: -
--



--
-- TOC entry 3132 (class 0 OID 24625827)
-- Dependencies: 248
-- Data for Name: usersroles; Type: TABLE DATA; Schema: public; Owner: -
--



--
-- TOC entry 3137 (class 0 OID 24625847)
-- Dependencies: 253
-- Data for Name: vendor; Type: TABLE DATA; Schema: public; Owner: -
--



--
-- TOC entry 3178 (class 0 OID 0)
-- Dependencies: 197
-- Name: cloud_dirs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.cloud_dirs_id_seq', 1, false);


--
-- TOC entry 3179 (class 0 OID 0)
-- Dependencies: 199
-- Name: cloud_files_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.cloud_files_id_seq', 1, false);


--
-- TOC entry 3180 (class 0 OID 0)
-- Dependencies: 203
-- Name: config_common_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.config_common_id_seq', 1, false);


--
-- TOC entry 3181 (class 0 OID 0)
-- Dependencies: 201
-- Name: config_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.config_id_seq', 1, false);


--
-- TOC entry 3182 (class 0 OID 0)
-- Dependencies: 205
-- Name: contract_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.contract_id_seq', 1, false);


--
-- TOC entry 3183 (class 0 OID 0)
-- Dependencies: 209
-- Name: eq_param_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.eq_param_id_seq', 1, false);


--
-- TOC entry 3184 (class 0 OID 0)
-- Dependencies: 207
-- Name: equipment_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.equipment_id_seq', 1, false);


--
-- TOC entry 3185 (class 0 OID 0)
-- Dependencies: 211
-- Name: files_contract_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.files_contract_id_seq', 1, false);


--
-- TOC entry 3186 (class 0 OID 0)
-- Dependencies: 213
-- Name: group_nome_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.group_nome_id_seq', 1, false);


--
-- TOC entry 3187 (class 0 OID 0)
-- Dependencies: 215
-- Name: group_param_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.group_param_id_seq', 1, false);


--
-- TOC entry 3188 (class 0 OID 0)
-- Dependencies: 217
-- Name: knt_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.knt_id_seq', 1, false);


--
-- TOC entry 3189 (class 0 OID 0)
-- Dependencies: 219
-- Name: mailq_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.mailq_id_seq', 1, false);


--
-- TOC entry 3190 (class 0 OID 0)
-- Dependencies: 222
-- Name: move_eqid_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.move_eqid_seq', 1, false);


--
-- TOC entry 3191 (class 0 OID 0)
-- Dependencies: 221
-- Name: move_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.move_id_seq', 1, false);


--
-- TOC entry 3192 (class 0 OID 0)
-- Dependencies: 224
-- Name: news_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.news_id_seq', 1, false);


--
-- TOC entry 3193 (class 0 OID 0)
-- Dependencies: 227
-- Name: nome_groupid_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.nome_groupid_seq', 1, false);


--
-- TOC entry 3194 (class 0 OID 0)
-- Dependencies: 226
-- Name: nome_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.nome_id_seq', 1, false);


--
-- TOC entry 3195 (class 0 OID 0)
-- Dependencies: 228
-- Name: nome_vendorid_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.nome_vendorid_seq', 1, false);


--
-- TOC entry 3196 (class 0 OID 0)
-- Dependencies: 230
-- Name: org_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.org_id_seq', 1, false);


--
-- TOC entry 3197 (class 0 OID 0)
-- Dependencies: 232
-- Name: places_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.places_id_seq', 1, false);


--
-- TOC entry 3198 (class 0 OID 0)
-- Dependencies: 233
-- Name: places_orgid_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.places_orgid_seq', 1, false);


--
-- TOC entry 3199 (class 0 OID 0)
-- Dependencies: 235
-- Name: places_users_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.places_users_id_seq', 1, false);


--
-- TOC entry 3200 (class 0 OID 0)
-- Dependencies: 236
-- Name: places_users_placesid_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.places_users_placesid_seq', 1, false);


--
-- TOC entry 3201 (class 0 OID 0)
-- Dependencies: 237
-- Name: places_users_userid_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.places_users_userid_seq', 1, false);


--
-- TOC entry 3202 (class 0 OID 0)
-- Dependencies: 241
-- Name: repair_eqid_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.repair_eqid_seq', 1, false);


--
-- TOC entry 3203 (class 0 OID 0)
-- Dependencies: 239
-- Name: repair_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.repair_id_seq', 1, false);


--
-- TOC entry 3204 (class 0 OID 0)
-- Dependencies: 240
-- Name: repair_kntid_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.repair_kntid_seq', 1, false);


--
-- TOC entry 3205 (class 0 OID 0)
-- Dependencies: 243
-- Name: users_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.users_id_seq', 1, false);


--
-- TOC entry 3206 (class 0 OID 0)
-- Dependencies: 244
-- Name: users_orgid_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.users_orgid_seq', 1, false);


--
-- TOC entry 3207 (class 0 OID 0)
-- Dependencies: 249
-- Name: users_profile_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.users_profile_id_seq', 1, false);


--
-- TOC entry 3208 (class 0 OID 0)
-- Dependencies: 250
-- Name: users_profile_usersid_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.users_profile_usersid_seq', 1, false);


--
-- TOC entry 3209 (class 0 OID 0)
-- Dependencies: 246
-- Name: usersroles_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.usersroles_id_seq', 1, false);


--
-- TOC entry 3210 (class 0 OID 0)
-- Dependencies: 247
-- Name: usersroles_userid_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.usersroles_userid_seq', 1, false);


--
-- TOC entry 3211 (class 0 OID 0)
-- Dependencies: 252
-- Name: vendor_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.vendor_id_seq', 1, false);


--
-- TOC entry 2915 (class 2606 OID 24625607)
-- Name: cloud_dirs cloud_dirs_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.cloud_dirs
    ADD CONSTRAINT cloud_dirs_pkey PRIMARY KEY (id);


--
-- TOC entry 2917 (class 2606 OID 24625615)
-- Name: cloud_files cloud_files_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.cloud_files
    ADD CONSTRAINT cloud_files_pkey PRIMARY KEY (id);


--
-- TOC entry 2921 (class 2606 OID 24625637)
-- Name: config_common config_common_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.config_common
    ADD CONSTRAINT config_common_pkey PRIMARY KEY (id);


--
-- TOC entry 2919 (class 2606 OID 24625629)
-- Name: config config_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.config
    ADD CONSTRAINT config_pkey PRIMARY KEY (id);


--
-- TOC entry 2923 (class 2606 OID 24625648)
-- Name: contract contract_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.contract
    ADD CONSTRAINT contract_pkey PRIMARY KEY (id);


--
-- TOC entry 2927 (class 2606 OID 24625669)
-- Name: eq_param eq_param_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.eq_param
    ADD CONSTRAINT eq_param_pkey PRIMARY KEY (id);


--
-- TOC entry 2925 (class 2606 OID 24625661)
-- Name: equipment equipment_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.equipment
    ADD CONSTRAINT equipment_pkey PRIMARY KEY (id);


--
-- TOC entry 2929 (class 2606 OID 24625677)
-- Name: files_contract files_contract_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.files_contract
    ADD CONSTRAINT files_contract_pkey PRIMARY KEY (id);


--
-- TOC entry 2931 (class 2606 OID 24625688)
-- Name: group_nome group_nome_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.group_nome
    ADD CONSTRAINT group_nome_pkey PRIMARY KEY (id);


--
-- TOC entry 2933 (class 2606 OID 24625696)
-- Name: group_param group_param_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.group_param
    ADD CONSTRAINT group_param_pkey PRIMARY KEY (id);


--
-- TOC entry 2935 (class 2606 OID 24625707)
-- Name: knt knt_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.knt
    ADD CONSTRAINT knt_pkey PRIMARY KEY (id);


--
-- TOC entry 2937 (class 2606 OID 24625718)
-- Name: mailq mailq_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.mailq
    ADD CONSTRAINT mailq_pkey PRIMARY KEY (id);


--
-- TOC entry 2939 (class 2606 OID 24625732)
-- Name: move move_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.move
    ADD CONSTRAINT move_pkey PRIMARY KEY (id);


--
-- TOC entry 2941 (class 2606 OID 24625744)
-- Name: news news_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.news
    ADD CONSTRAINT news_pkey PRIMARY KEY (id);


--
-- TOC entry 2943 (class 2606 OID 24625758)
-- Name: nome nome_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.nome
    ADD CONSTRAINT nome_pkey PRIMARY KEY (id);


--
-- TOC entry 2945 (class 2606 OID 24625766)
-- Name: org org_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.org
    ADD CONSTRAINT org_pkey PRIMARY KEY (id);


--
-- TOC entry 2947 (class 2606 OID 24625780)
-- Name: places places_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.places
    ADD CONSTRAINT places_pkey PRIMARY KEY (id);


--
-- TOC entry 2949 (class 2606 OID 24625794)
-- Name: places_users places_users_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.places_users
    ADD CONSTRAINT places_users_pkey PRIMARY KEY (id);


--
-- TOC entry 2951 (class 2606 OID 24625811)
-- Name: repair repair_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.repair
    ADD CONSTRAINT repair_pkey PRIMARY KEY (id);


--
-- TOC entry 2953 (class 2606 OID 24625822)
-- Name: users users_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_pkey PRIMARY KEY (id);


--
-- TOC entry 2957 (class 2606 OID 24625844)
-- Name: users_profile users_profile_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.users_profile
    ADD CONSTRAINT users_profile_pkey PRIMARY KEY (id);


--
-- TOC entry 2955 (class 2606 OID 24625833)
-- Name: usersroles usersroles_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.usersroles
    ADD CONSTRAINT usersroles_pkey PRIMARY KEY (id);


--
-- TOC entry 2959 (class 2606 OID 24625855)
-- Name: vendor vendor_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.vendor
    ADD CONSTRAINT vendor_pkey PRIMARY KEY (id);


-- Completed on 2020-03-26 13:18:31

--
-- PostgreSQL database dump complete
--

