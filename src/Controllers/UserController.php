<?php

declare(strict_types=1);

namespace UbuntuServerAPI\Controllers;

use UbuntuServerAPI\Core\Response;
use UbuntuServerAPI\Services\UserService;

class UserController
{
    public function __construct(
        private readonly UserService $userService = new UserService()
    ) {
    }

    public function list(): Response
    {
        try {
            $data = $this->userService->listUsers();
            return Response::success($data, 'Users retrieved successfully');
        } catch (\Exception $e) {
            return Response::error($e->getMessage(), 500);
        }
    }

    public function get(string $username): Response
    {
        try {
            $data = $this->userService->getUserInfo($username);
            
            if ($data === null) {
                return Response::error('User not found', 404);
            }
            
            return Response::success($data, 'User information retrieved successfully');
        } catch (\Exception $e) {
            return Response::error($e->getMessage(), 500);
        }
    }

    public function loggedIn(): Response
    {
        try {
            $data = $this->userService->getLoggedInUsers();
            return Response::success($data, 'Logged in users retrieved successfully');
        } catch (\Exception $e) {
            return Response::error($e->getMessage(), 500);
        }
    }

    public function groups(): Response
    {
        try {
            $data = $this->userService->listGroups();
            return Response::success($data, 'Groups retrieved successfully');
        } catch (\Exception $e) {
            return Response::error($e->getMessage(), 500);
        }
    }
}
