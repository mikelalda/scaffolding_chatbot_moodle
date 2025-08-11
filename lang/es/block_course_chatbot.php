<?php

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'Chatbot del Curso';
$string['pluginname_desc'] = 'Un bloque de chatbot que se puede configurar por tema del curso con datos JSON personalizados.';
$string['coursetopichatbot'] = 'Chatbot por Tema del Curso';
$string['settings'] = 'Configuración del Chatbot';
$string['uploadjson'] = 'Subir Configuración del Chatbot (JSON)';
$string['uploadjson_help'] = 'Sube un archivo JSON que contenga las preguntas frecuentes, pasos de resolución, etc. del chatbot.';
$string['pastejson'] = 'O pegar la configuración JSON directamente';
$string['pastejson_help'] = 'Puedes pegar el contenido JSON directamente aquí en lugar de subir un archivo.';
$string['jsoninfo'] = 'La configuración JSON define la base de conocimiento del chatbot (FAQs, pasos de resolución de problemas). Consulta la documentación para la estructura JSON esperada.';
$string['jsonrequired'] = 'Debes subir un archivo JSON o pegar el texto JSON.';
$string['invalidjsonfile'] = 'El archivo subido no es un JSON válido: {$a}';
$string['invalidjsontext'] = 'El texto pegado no es un JSON válido: {$a}';
$string['noconfigset'] = 'No se ha configurado el chatbot para este bloque. Por favor, configúralo.';
$string['invalidconfig'] = 'La configuración del chatbot cargada no es válida. Por favor, revisa los datos JSON: {$a}';
$string['initialmessage'] = '¡Hola! Soy tu asistente del curso. ¿En qué puedo ayudarte hoy?';
$string['displayname'] = 'Nombre del Chatbot';
$string['defaultchatbotname'] = 'Asistente del Curso';
$string['sendmessage'] = 'Enviar';
$string['typemessage'] = 'Escribe tu mensaje...';
$string['welcome'] = '¡Bienvenido!';