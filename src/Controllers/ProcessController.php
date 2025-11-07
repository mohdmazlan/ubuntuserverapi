<?php

declare(strict_types=1);

namespace UbuntuServerAPI\Controllers;

use UbuntuServerAPI\Core\Request;
use UbuntuServerAPI\Core\Response;
use UbuntuServerAPI\Services\ProcessService;

class ProcessController
{
    public function __construct(
        private readonly ProcessService $processService = new ProcessService(),
        private readonly Request $request = new Request()
    ) {
    }

    public function list(): Response
    {
        try {
            $limit = $this->request->getQueryParam('limit', 20);
            $data = $this->processService->listProcesses((int)$limit);
            return Response::success($data, 'Processes retrieved successfully');
        } catch (\Exception $e) {
            return Response::error($e->getMessage(), 500);
        }
    }

    public function get(string $pid): Response
    {
        try {
            $data = $this->processService->getProcessInfo((int)$pid);
            
            if ($data === null) {
                return Response::error('Process not found', 404);
            }
            
            return Response::success($data, 'Process information retrieved successfully');
        } catch (\Exception $e) {
            return Response::error($e->getMessage(), 500);
        }
    }

    public function kill(string $pid): Response
    {
        try {
            $force = $this->request->getBodyParam('force', false);
            $success = $this->processService->killProcess((int)$pid, (bool)$force);
            
            if ($success) {
                return Response::success([], 'Process killed successfully');
            } else {
                return Response::error('Failed to kill process', 500);
            }
        } catch (\Exception $e) {
            return Response::error($e->getMessage(), 500);
        }
    }
}
