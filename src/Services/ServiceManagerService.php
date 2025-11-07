<?php

declare(strict_types=1);

namespace UbuntuServerAPI\Services;

class ServiceManagerService
{
    public function listServices(): array
    {
        $output = $this->execute('systemctl list-units --type=service --all --no-pager');
        $lines = explode("\n", trim($output));
        
        $services = [];
        $started = false;
        
        foreach ($lines as $line) {
            if (strpos($line, 'UNIT') !== false && strpos($line, 'LOAD') !== false) {
                $started = true;
                continue;
            }
            
            if (!$started || empty(trim($line)) || strpos($line, '●') === 0) {
                continue;
            }
            
            if (preg_match('/^([\w\-@:.]+\.service)\s+(\w+)\s+(\w+)\s+(\w+)\s+(.+)$/', trim($line), $matches)) {
                $services[] = [
                    'name' => $matches[1],
                    'load' => $matches[2],
                    'active' => $matches[3],
                    'sub' => $matches[4],
                    'description' => trim($matches[5])
                ];
            }
        }
        
        return $services;
    }

    public function getServiceStatus(string $serviceName): array
    {
        $output = $this->execute("systemctl status $serviceName --no-pager");
        $isActive = $this->execute("systemctl is-active $serviceName");
        $isEnabled = $this->execute("systemctl is-enabled $serviceName");
        
        return [
            'name' => $serviceName,
            'active' => trim($isActive) === 'active',
            'enabled' => trim($isEnabled) === 'enabled',
            'status_output' => $output
        ];
    }

    public function startService(string $serviceName): array
    {
        $output = $this->execute("sudo systemctl start $serviceName");
        return $this->getServiceStatus($serviceName);
    }

    public function stopService(string $serviceName): array
    {
        $output = $this->execute("sudo systemctl stop $serviceName");
        return $this->getServiceStatus($serviceName);
    }

    public function restartService(string $serviceName): array
    {
        $output = $this->execute("sudo systemctl restart $serviceName");
        return $this->getServiceStatus($serviceName);
    }

    public function enableService(string $serviceName): array
    {
        $output = $this->execute("sudo systemctl enable $serviceName");
        return $this->getServiceStatus($serviceName);
    }

    public function disableService(string $serviceName): array
    {
        $output = $this->execute("sudo systemctl disable $serviceName");
        return $this->getServiceStatus($serviceName);
    }

    private function execute(string $command): string
    {
        $output = shell_exec($command . ' 2>&1');
        return trim($output ?? '');
    }
}
