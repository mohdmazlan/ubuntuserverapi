<?php

declare(strict_types=1);

namespace UbuntuServerAPI\Controllers;

use UbuntuServerAPI\Core\Response;
use UbuntuServerAPI\Services\SystemInfoService;

class SystemController
{
    public function __construct(
        private readonly SystemInfoService $systemService = new SystemInfoService()
    ) {
    }

    public function getInfo(): Response
    {
        try {
            $data = $this->systemService->getSystemInfo();
            return Response::success($data, 'System information retrieved successfully');
        } catch (\Exception $e) {
            return Response::error($e->getMessage(), 500);
        }
    }

    public function getCpu(): Response
    {
        try {
            $data = $this->systemService->getCpuInfo();
            return Response::success($data, 'CPU information retrieved successfully');
        } catch (\Exception $e) {
            return Response::error($e->getMessage(), 500);
        }
    }

    public function getMemory(): Response
    {
        try {
            $data = $this->systemService->getMemoryInfo();
            return Response::success($data, 'Memory information retrieved successfully');
        } catch (\Exception $e) {
            return Response::error($e->getMessage(), 500);
        }
    }
}
