<?php

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'Course Chatbot';
$string['pluginname_desc'] = 'A chatbot block that can be configured per course topic with custom JSON data.';
$string['coursetopichatbot'] = 'Course Topic Chatbot';
$string['settings'] = 'Chatbot Settings';
$string['uploadjson'] = 'Upload Chatbot Configuration (JSON)';
$string['uploadjson_help'] = 'Upload a JSON file containing the chatbot\'s FAQs, resolution steps, etc.';
$string['pastejson'] = 'Or paste JSON configuration directly';
$string['pastejson_help'] = 'You can paste the JSON content directly here instead of uploading a file.';
$string['jsoninfo'] = 'The JSON configuration defines the chatbot\'s knowledge base (FAQs, problem-solving steps). Refer to the documentation for the expected JSON structure.';
$string['jsonrequired'] = 'Either upload a JSON file or paste the JSON text.';
$string['invalidjsonfile'] = 'The uploaded file is not a valid JSON: {$a}';
$string['invalidjsontext'] = 'The pasted text is not a valid JSON: {$a}';
$string['noconfigset'] = 'No chatbot configuration has been set for this block. Please configure it.';
$string['invalidconfig'] = 'The loaded chatbot configuration is invalid. Please check the JSON data: {$a}';
$string['initialmessage'] = 'Hello! I am your course assistant. How can I help you today?';
$string['displayname'] = 'Chatbot Display Name';
$string['defaultchatbotname'] = 'Course Assistant';
$string['sendmessage'] = 'Send';
$string['typemessage'] = 'Type your message...';
$string['welcome'] = 'Welcome!';