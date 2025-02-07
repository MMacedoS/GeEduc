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
const SCHOOL_ADDRESS = "";
const SCHOOL_ZIP_CODE = "";
const MIN_SCORE = 7;

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

const TOKEN_URL_BB = 'https://oauth.sandbox.bb.com.br/oauth/token';
const BASIC_TOKEN = 'Basic ZXlKcFpDSTZJbUV3T1dFek0ySXRZallpTENKamIyUnBaMjlRZFdKc2FXTmhaRzl5SWpvd0xDSmpiMlJwWjI5VGIyWjBkMkZ5WlNJNk1USTBNamsyTENKelpYRjFaVzVqYVdGc1NXNXpkR0ZzWVdOaGJ5STZNWDA6ZXlKcFpDSTZJbUl4WkROaU5qUmtMVFU0TXprdE5HUXdaUzFoWlRSakxUVXlPVE5sWXpFeVptUmhNaUlzSW1OdlpHbG5iMUIxWW14cFkyRmtiM0lpT2pBc0ltTnZaR2xuYjFOdlpuUjNZWEpsSWpveE1qUXlPVFlzSW5ObGNYVmxibU5wWVd4SmJuTjBZV3hoWTJGdklqb3hMQ0p6WlhGMVpXNWphV0ZzUTNKbFpHVnVZMmxoYkNJNk1Td2lZVzFpYVdWdWRHVWlPaUpvYjIxdmJHOW5ZV05oYnlJc0ltbGhkQ0k2TVRjek9EY3hNVEl6TVRjM00zMA==';
const GW_APP_KEY = "5d3a0e9d39378f829daeb7fcea9a3239";
const API_BB_URL = "https://api.hm.bb.com.br/cobrancas/v2/";