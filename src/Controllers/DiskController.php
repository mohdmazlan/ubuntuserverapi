<?php

declare(strict_types=1);

namespace UbuntuServerAPI\Controllers;

use UbuntuServerAPI\Core\Request;
use UbuntuServerAPI\Core\Response;
use UbuntuServerAPI\Services\DiskService;

class DiskController
{
    public function __construct(
        private readonly DiskService $diskService = new DiskService(),
        private readonly Request $request = new Request()
    ) {
    }

    public function usage(): Response
    {
        try {
            $data = $this->diskService->getDiskUsage();
            return Response::success($data, 'Disk usage retrieved successfully');
        } catch (\Exception $e) {
            return Response::error($e->getMessage(), 500);
        }
    }

    public function inodes(): Response
    {
        try {
            $data = $this->diskService->getInodeUsage();
            return Response::success($data, 'Inode usage retrieved successfully');
        } catch (\Exception $e) {
            return Response::error($e->getMessage(), 500);
        }
    }

    public function directorySize(): Response
    {
        try {
            $path = $this->request->getQueryParam('path', '/');
            $data = $this->diskService->getDirectorySize($path);
            return Response::success($data, 'Directory size retrieved successfully');
        } catch (\Exception $e) {
            return Response::error($e->getMessage(), 500);
        }
    }

    public function blockDevices(): Response
    {
        try {
            $data = $this->diskService->listBlockDevices();
            return Response::success($data, 'Block devices retrieved successfully');
        } catch (\Exception $e) {
            return Response::error($e->getMessage(), 500);
        }
    }
}
