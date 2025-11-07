<?php

declare(strict_types=1);

namespace UbuntuServerAPI\Controllers;

use UbuntuServerAPI\Core\Request;
use UbuntuServerAPI\Core\Response;
use UbuntuServerAPI\Services\SSHService;

class SSHController
{
    public function __construct(
        private readonly SSHService $sshService = new SSHService(),
        private readonly Request $request = new Request()
    ) {
    }

    public function execute(): Response
    {
        try {
            $command = $this->request->getBodyParam('command');
            
            if (empty($command)) {
                return Response::error('Command is required', 400);
            }
            
            // Security: Block dangerous commands
            $blockedCommands = ['rm -rf /', 'mkfs', 'dd if=/dev/zero', ':(){:|:&};:'];
            foreach ($blockedCommands as $blocked) {
                if (stripos($command, $blocked) !== false) {
                    return Response::error('Command blocked for security reasons', 403);
                }
            }
            
            $data = $this->sshService->executeLocal($command);
            return Response::success($data, 'Command executed');
        } catch (\Exception $e) {
            return Response::error($e->getMessage(), 500);
        }
    }

    public function executeMultiple(): Response
    {
        try {
            $commands = $this->request->getBodyParam('commands', []);
            
            if (empty($commands) || !is_array($commands)) {
                return Response::error('Commands array is required', 400);
            }
            
            $data = $this->sshService->executeMultiple($commands);
            return Response::success($data, 'Commands executed');
        } catch (\Exception $e) {
            return Response::error($e->getMessage(), 500);
        }
    }

    public function executeInDirectory(): Response
    {
        try {
            $directory = $this->request->getBodyParam('directory');
            $command = $this->request->getBodyParam('command');
            
            if (empty($directory) || empty($command)) {
                return Response::error('Directory and command are required', 400);
            }
            
            $data = $this->sshService->executeInDirectory($directory, $command);
            return Response::success($data, 'Command executed in directory');
        } catch (\Exception $e) {
            return Response::error($e->getMessage(), 500);
        }
    }

    public function getTerminalInfo(): Response
    {
        try {
            $data = $this->sshService->getTerminalInfo();
            return Response::success($data, 'Terminal info retrieved');
        } catch (\Exception $e) {
            return Response::error($e->getMessage(), 500);
        }
    }

    public function listDirectory(): Response
    {
        try {
            $path = $this->request->getQueryParam('path', '.');
            $data = $this->sshService->listDirectory($path);
            return Response::success($data, 'Directory listed');
        } catch (\Exception $e) {
            return Response::error($e->getMessage(), 500);
        }
    }

    public function readFile(): Response
    {
        try {
            $filepath = $this->request->getQueryParam('file');
            
            if (empty($filepath)) {
                return Response::error('File path is required', 400);
            }
            
            $data = $this->sshService->readFile($filepath);
            
            if (!$data['success']) {
                return Response::error($data['error'], 404);
            }
            
            return Response::success($data, 'File read successfully');
        } catch (\Exception $e) {
            return Response::error($e->getMessage(), 500);
        }
    }

    public function getHistory(): Response
    {
        try {
            $lines = (int) $this->request->getQueryParam('lines', 50);
            $data = $this->sshService->getHistory($lines);
            return Response::success($data, 'History retrieved');
        } catch (\Exception $e) {
            return Response::error($e->getMessage(), 500);
        }
    }

    public function getEnvironment(): Response
    {
        try {
            $data = $this->sshService->getEnvironment();
            return Response::success($data, 'Environment variables retrieved');
        } catch (\Exception $e) {
            return Response::error($e->getMessage(), 500);
        }
    }

    public function executeSudo(): Response
    {
        try {
            $command = $this->request->getBodyParam('command');
            
            if (empty($command)) {
                return Response::error('Command is required', 400);
            }
            
            $data = $this->sshService->executeSudo($command);
            return Response::success($data, 'Sudo command executed');
        } catch (\Exception $e) {
            return Response::error($e->getMessage(), 500);
        }
    }
}
