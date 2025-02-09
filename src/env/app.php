<?php

const APP_NAME = 'GeEduc - Gestor Educacional';
const URL_PREFIX_APP = "http://app1.localhost:8080";
const DB_HOST = 'mysql';
const DB_NAME = 'cesp-geeduc';
const DB_USER = 'root';
const DB_PASS = '12345';
const SECRET_KEY = '1SDS-AKLSDK7.SDAK.33DSAK.SDAK';
const SECRET_KEY_DAYLI = 'fbc761f5e0a5a34e5a1ec23ff671e1f87525e5a8756ed5fe14eab3e1a97af274';
const NAME_SCHOOL = "Centro de Educação Souza Pimentel";
const ACRONYM_SCHOOL = "CESP";
const SCHOOL_ADDRESS = "RUA LUIZ FERNANDO DE QUEIROZ, Nº 235 - CENTRO - CALDAS DO JORRO/BA";
const SCHOOL_ZIP_CODE = "48.793-000";
const SCHOOL_CNPJ = "08.586.470/0001-90";
const OPERATING_AUTHORIZATION = "004/00/12 D.O 22/09/2000";
const ACCREDITATION = "nº 7.915/2013";
const PHONES_SCHOOL = ["(75) 99140-6871", "(75)  99156-9651"];
const MIN_SCORE = 7;
const URL_AUTENTIQUE = "https://api.autentique.com.br/v2/graphql";
const TOKEN_AUTENTIQUE = "5935126255a6ed4b56a789cd76b11362cd0b9165b9ae7ce965e42dc923107173";
const CONTRACT_START = "03/02/";
const CONTRACT_END = "12/12/";
const EMAIL_SCHOOL = "cespsecretaria2021@gmail.com";
//remetent

const CONFIG_EMAIL = [
    'host' => 'smtp.gmail.com', // Servidor SMTP
    'port' => 587, // Porta do servidor
    'username' => 'geeducsoftware@gmail.com', // E-mail do remetente
    'password' => 'bfik kbbt oukv bfaz', // Senha do e-mail
    'encryption' => 'tls', // Tipo de criptografia (tls/ssl)
    'from_email' => 'contato@escolacesp.com.br', // E-mail do remetente
    'from_name' => 'Gestor Educacional' // Nome do remetente
];