<?php

declare(strict_types=1);

namespace UbuntuServerAPI\Services;

class DiskService
{
    public function getDiskUsage(): array
    {
        $output = $this->execute('df -h');
        $lines = explode("\n", trim($output));
        
        $header = array_shift($lines);
        $disks = [];
        
        foreach ($lines as $line) {
            if (empty(trim($line))) continue;
            
            $parts = preg_split('/\s+/', $line);
            if (count($parts) >= 6) {
                $disks[] = [
                    'filesystem' => $parts[0],
                    'size' => $parts[1],
                    'used' => $parts[2],
                    'available' => $parts[3],
                    'use_percent' => $parts[4],
                    'mounted_on' => $parts[5]
                ];
            }
        }
        
        return $disks;
    }

    public function getInodeUsage(): array
    {
        $output = $this->execute('df -i');
        $lines = explode("\n", trim($output));
        
        $header = array_shift($lines);
        $inodes = [];
        
        foreach ($lines as $line) {
            if (empty(trim($line))) continue;
            
            $parts = preg_split('/\s+/', $line);
            if (count($parts) >= 6) {
                $inodes[] = [
                    'filesystem' => $parts[0],
                    'inodes' => $parts[1],
                    'iused' => $parts[2],
                    'ifree' => $parts[3],
                    'iuse_percent' => $parts[4],
                    'mounted_on' => $parts[5]
                ];
            }
        }
        
        return $inodes;
    }

    public function getDirectorySize(string $path): array
    {
        if (!file_exists($path)) {
            return ['error' => 'Path does not exist'];
        }
        
        $output = $this->execute("du -sh " . escapeshellarg($path));
        $parts = preg_split('/\s+/', $output, 2);
        
        return [
            'path' => $path,
            'size' => $parts[0] ?? 'unknown'
        ];
    }

    public function listBlockDevices(): array
    {
        $output = $this->execute('lsblk -J');
        $data = json_decode($output, true);
        
        return $data['blockdevices'] ?? [];
    }

    private function execute(string $command): string
    {
        $output = shell_exec($command . ' 2>&1');
        return trim($output ?? '');
    }
}
