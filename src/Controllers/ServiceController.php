<?php

declare(strict_types=1);

namespace UbuntuServerAPI\Controllers;

use UbuntuServerAPI\Core\Request;
use UbuntuServerAPI\Core\Response;
use UbuntuServerAPI\Services\ServiceManagerService;

class ServiceController
{
    public function __construct(
        private readonly ServiceManagerService $serviceManager = new ServiceManagerService(),
        private readonly Request $request = new Request()
    ) {
    }

    public function list(): Response
    {
        try {
            $data = $this->serviceManager->listServices();
            return Response::success($data, 'Services retrieved successfully');
        } catch (\Exception $e) {
            return Response::error($e->getMessage(), 500);
        }
    }

    public function status(string $name): Response
    {
        try {
            $data = $this->serviceManager->getServiceStatus($name);
            return Response::success($data, 'Service status retrieved successfully');
        } catch (\Exception $e) {
            return Response::error($e->getMessage(), 500);
        }
    }

    public function start(string $name): Response
    {
        try {
            $data = $this->serviceManager->startService($name);
            return Response::success($data, 'Service started successfully');
        } catch (\Exception $e) {
            return Response::error($e->getMessage(), 500);
        }
    }

    public function stop(string $name): Response
    {
        try {
            $data = $this->serviceManager->stopService($name);
            return Response::success($data, 'Service stopped successfully');
        } catch (\Exception $e) {
            return Response::error($e->getMessage(), 500);
        }
    }

    public function restart(string $name): Response
    {
        try {
            $data = $this->serviceManager->restartService($name);
            return Response::success($data, 'Service restarted successfully');
        } catch (\Exception $e) {
            return Response::error($e->getMessage(), 500);
        }
    }

    public function enable(string $name): Response
    {
        try {
            $data = $this->serviceManager->enableService($name);
            return Response::success($data, 'Service enabled successfully');
        } catch (\Exception $e) {
            return Response::error($e->getMessage(), 500);
        }
    }

    public function disable(string $name): Response
    {
        try {
            $data = $this->serviceManager->disableService($name);
            return Response::success($data, 'Service disabled successfully');
        } catch (\Exception $e) {
            return Response::error($e->getMessage(), 500);
        }
    }
}
