<?php

defined('MOODLE_INTERNAL') || die();

$functions = [
    'block_course_chatbot_process_message' => [
        'classname' => '\block_course_chatbot\api',
        'methodname' => 'process_message',
        'classpath' => 'block/course_chatbot/classes/api.php', // Ruta correcta a tu clase API
        'description' => 'Procesa un mensaje de usuario para el chatbot del curso.',
        'type' => 'write',
        'ajax' => true, // Permite que sea llamado por AJAX desde el frontend
    ]
];