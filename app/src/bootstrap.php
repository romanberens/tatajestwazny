<?php

declare(strict_types=1);

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

const APP_ROOT = __DIR__ . '/..';
const APP_RUNTIME_LOG = APP_ROOT . '/logs/diagnostics/runtime.log';
const APP_DB_FILE = APP_ROOT . '/db/tjw.sqlite';

require_once __DIR__ . '/Logger.php';
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/Db.php';
require_once __DIR__ . '/Repositories/AbstractRepository.php';
require_once __DIR__ . '/Repositories/PagesRepository.php';
require_once __DIR__ . '/Repositories/PostsRepository.php';
require_once __DIR__ . '/Repositories/MenuRepository.php';
require_once __DIR__ . '/Repositories/BlocksRepository.php';
