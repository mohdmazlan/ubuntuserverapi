<?php

declare(strict_types=1);

namespace UbuntuServerAPI\Services;

class UserService
{
    public function listUsers(): array
    {
        $output = $this->execute('cat /etc/passwd');
        $lines = explode("\n", trim($output));
        
        $users = [];
        foreach ($lines as $line) {
            if (empty(trim($line))) continue;
            
            $parts = explode(':', $line);
            if (count($parts) >= 7) {
                $users[] = [
                    'username' => $parts[0],
                    'uid' => $parts[2],
                    'gid' => $parts[3],
                    'home' => $parts[5],
                    'shell' => $parts[6]
                ];
            }
        }
        
        return $users;
    }

    public function getUserInfo(string $username): ?array
    {
        $output = $this->execute("id $username");
        
        if (strpos($output, 'no such user') !== false) {
            return null;
        }
        
        $passwd = $this->execute("getent passwd $username");
        $parts = explode(':', $passwd);
        
        if (count($parts) < 7) {
            return null;
        }
        
        $groups = $this->execute("groups $username");
        
        return [
            'username' => $parts[0],
            'uid' => $parts[2],
            'gid' => $parts[3],
            'info' => $parts[4],
            'home' => $parts[5],
            'shell' => $parts[6],
            'groups' => $groups,
            'id_output' => $output
        ];
    }

    public function getLoggedInUsers(): array
    {
        $output = $this->execute('who');
        $lines = explode("\n", trim($output));
        
        $users = [];
        foreach ($lines as $line) {
            if (empty(trim($line))) continue;
            
            $parts = preg_split('/\s+/', $line);
            if (count($parts) >= 5) {
                $users[] = [
                    'user' => $parts[0],
                    'terminal' => $parts[1],
                    'login_time' => $parts[2] . ' ' . $parts[3],
                    'from' => $parts[4] ?? 'local'
                ];
            }
        }
        
        return $users;
    }

    public function listGroups(): array
    {
        $output = $this->execute('cat /etc/group');
        $lines = explode("\n", trim($output));
        
        $groups = [];
        foreach ($lines as $line) {
            if (empty(trim($line))) continue;
            
            $parts = explode(':', $line);
            if (count($parts) >= 4) {
                $groups[] = [
                    'name' => $parts[0],
                    'gid' => $parts[2],
                    'members' => !empty($parts[3]) ? explode(',', $parts[3]) : []
                ];
            }
        }
        
        return $groups;
    }

    private function execute(string $command): string
    {
        $output = shell_exec($command . ' 2>&1');
        return trim($output ?? '');
    }
}
