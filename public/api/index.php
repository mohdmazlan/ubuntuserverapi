<?php

declare(strict_types=1);

require_once __DIR__ . '/../../vendor/autoload.php';

use UbuntuServerAPI\Core\Router;
use UbuntuServerAPI\Core\Request;
use UbuntuServerAPI\Controllers\SystemController;
use UbuntuServerAPI\Controllers\ProcessController;
use UbuntuServerAPI\Controllers\ServiceController;
use UbuntuServerAPI\Controllers\DiskController;
use UbuntuServerAPI\Controllers\UserController;
use UbuntuServerAPI\Controllers\NetworkController;
use UbuntuServerAPI\Controllers\SSHController;

// Enable error reporting for development
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set headers
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Initialize request and router
$request = new Request();
$router = new Router($request);

// System routes
$router->get('/api/system/info', [SystemController::class, 'getInfo']);
$router->get('/api/system/cpu', [SystemController::class, 'getCpu']);
$router->get('/api/system/memory', [SystemController::class, 'getMemory']);

// Process routes
$router->get('/api/processes', [ProcessController::class, 'list']);
$router->get('/api/processes/{pid}', [ProcessController::class, 'get']);
$router->delete('/api/processes/{pid}', [ProcessController::class, 'kill']);

// Service routes
$router->get('/api/services', [ServiceController::class, 'list']);
$router->get('/api/services/{name}', [ServiceController::class, 'status']);
$router->post('/api/services/{name}/start', [ServiceController::class, 'start']);
$router->post('/api/services/{name}/stop', [ServiceController::class, 'stop']);
$router->post('/api/services/{name}/restart', [ServiceController::class, 'restart']);
$router->post('/api/services/{name}/enable', [ServiceController::class, 'enable']);
$router->post('/api/services/{name}/disable', [ServiceController::class, 'disable']);

// Disk routes
$router->get('/api/disk/usage', [DiskController::class, 'usage']);
$router->get('/api/disk/inodes', [DiskController::class, 'inodes']);
$router->get('/api/disk/directory-size', [DiskController::class, 'directorySize']);
$router->get('/api/disk/block-devices', [DiskController::class, 'blockDevices']);

// User routes
$router->get('/api/users', [UserController::class, 'list']);
$router->get('/api/users/logged-in', [UserController::class, 'loggedIn']);
$router->get('/api/users/groups', [UserController::class, 'groups']);
$router->get('/api/users/{username}', [UserController::class, 'get']);

// Network routes
$router->get('/api/network/interfaces', [NetworkController::class, 'interfaces']);
$router->get('/api/network/stats', [NetworkController::class, 'stats']);
$router->get('/api/network/routes', [NetworkController::class, 'routes']);
$router->get('/api/network/listening-ports', [NetworkController::class, 'listeningPorts']);
$router->get('/api/network/connections', [NetworkController::class, 'connections']);
$router->get('/api/network/ping', [NetworkController::class, 'ping']);

// SSH/Terminal routes
$router->post('/api/ssh/execute', [SSHController::class, 'execute']);
$router->post('/api/ssh/execute-multiple', [SSHController::class, 'executeMultiple']);
$router->post('/api/ssh/execute-in-directory', [SSHController::class, 'executeInDirectory']);
$router->post('/api/ssh/sudo', [SSHController::class, 'executeSudo']);
$router->get('/api/ssh/terminal-info', [SSHController::class, 'getTerminalInfo']);
$router->get('/api/ssh/list-directory', [SSHController::class, 'listDirectory']);
$router->get('/api/ssh/read-file', [SSHController::class, 'readFile']);
$router->get('/api/ssh/history', [SSHController::class, 'getHistory']);
$router->get('/api/ssh/environment', [SSHController::class, 'getEnvironment']);

// Health check route
$router->get('/api/health', function() {
    return new \UbuntuServerAPI\Core\Response([
        'status' => 'healthy',
        'timestamp' => date('Y-m-d H:i:s')
    ]);
});

// Resolve and send response
$response = $router->resolve();
$response->send();
