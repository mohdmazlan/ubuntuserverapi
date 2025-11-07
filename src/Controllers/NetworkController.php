<?php

declare(strict_types=1);

namespace UbuntuServerAPI\Controllers;

use UbuntuServerAPI\Core\Request;
use UbuntuServerAPI\Core\Response;
use UbuntuServerAPI\Services\NetworkService;

class NetworkController
{
    public function __construct(
        private readonly NetworkService $networkService = new NetworkService(),
        private readonly Request $request = new Request()
    ) {
    }

    public function interfaces(): Response
    {
        try {
            $data = $this->networkService->getNetworkInterfaces();
            return Response::success($data, 'Network interfaces retrieved successfully');
        } catch (\Exception $e) {
            return Response::error($e->getMessage(), 500);
        }
    }

    public function stats(): Response
    {
        try {
            $data = $this->networkService->getNetworkStats();
            return Response::success($data, 'Network stats retrieved successfully');
        } catch (\Exception $e) {
            return Response::error($e->getMessage(), 500);
        }
    }

    public function routes(): Response
    {
        try {
            $data = $this->networkService->getRoutingTable();
            return Response::success($data, 'Routing table retrieved successfully');
        } catch (\Exception $e) {
            return Response::error($e->getMessage(), 500);
        }
    }

    public function listeningPorts(): Response
    {
        try {
            $data = $this->networkService->getListeningPorts();
            return Response::success($data, 'Listening ports retrieved successfully');
        } catch (\Exception $e) {
            return Response::error($e->getMessage(), 500);
        }
    }

    public function connections(): Response
    {
        try {
            $data = $this->networkService->getActiveConnections();
            return Response::success($data, 'Active connections retrieved successfully');
        } catch (\Exception $e) {
            return Response::error($e->getMessage(), 500);
        }
    }

    public function ping(): Response
    {
        try {
            $host = $this->request->getQueryParam('host');
            $count = $this->request->getQueryParam('count', 4);
            
            if (!$host) {
                return Response::error('Host parameter is required', 400);
            }
            
            $data = $this->networkService->pingHost($host, (int)$count);
            return Response::success($data, 'Ping executed successfully');
        } catch (\Exception $e) {
            return Response::error($e->getMessage(), 500);
        }
    }
}
