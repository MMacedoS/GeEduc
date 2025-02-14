<?php

const APP_NAME = 'GeEduc - Gestor Educacional';
const URL_PREFIX_APP = "http://app1.localhost:8080";
const DB_HOST = 'mysql';
const DB_NAME = 'cesp-geeduc';
const DB_USER = 'root';
const DB_PASS = '12345';
const SECRET_KEY = '1SDS-AKLSDK7.SDAK.33DSAK.SDAK';
const SECRET_KEY_DAYLI = 'fbc761f5e0a5a34e5a1ec23ff671e1f87525e5a8756ed5fe14eab3e1a97af274';
const NAME_SCHOOL = "Instituto Social de Tucano";
const ACRONYM_SCHOOL = "IST";
const SCHOOL_ADDRESS = "CENTRO - TUCANO/BA";
const SCHOOL_ZIP_CODE = "48.793-000";
const SCHOOL_CNPJ = "00.000.470/0001-90";
const OPERATING_AUTHORIZATION = "004/00/12 D.O 22/09/2000";
const ACCREDITATION = "nº 7.915/2013";
const PHONES_SCHOOL = ["(75) 00000-6871", "(75)  00000-9651"];
const MIN_SCORE = 7;
const URL_AUTENTIQUE = "https://api.autentique.com.br/v2/graphql";
const TOKEN_AUTENTIQUE = "2b4f9e72c2e5685f0418adfc220e927b8f339766ba37cc320ea2302ab41082ec";
const SECRET_AUTENTIQUE = "01JKPQJA4W6F2MVXP8XWEXKBFK";
const SECRET_AUTENTIQUE_CREATE_DOC = "01JKPW31XFBR17RH2RPZ9Q9J0X";
const CONTRACT_START = "03/02/";
const CONTRACT_END = "12/12/";
const EMAIL_SCHOOL = "contato@escolaisttucano.com.br";
//remetent

const CONFIG_EMAIL = [
    'host' => 'smtp.gmail.com', // Servidor SMTP
    'port' => 587, // Porta do servidor
    'username' => 'geeducsoftware@gmail.com', // E-mail do remetente
    'password' => 'bfik kbbt oukv bfaz', // Senha do e-mail
    'encryption' => 'tls', // Tipo de criptografia (tls/ssl)
    'from_email' => 'contato@escolaisttucano.com.br', // E-mail do remetente
    'from_name' => 'Gestor Educacional' // Nome do remetente
];