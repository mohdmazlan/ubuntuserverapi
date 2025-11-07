<?php

declare(strict_types=1);

namespace UbuntuServerAPI\Services;

class NetworkService
{
    public function getNetworkInterfaces(): array
    {
        $output = $this->execute('ip -j addr show');
        $data = json_decode($output, true);
        
        return $data ?? [];
    }

    public function getNetworkStats(): array
    {
        $output = $this->execute('ip -s link');
        
        return [
            'raw_output' => $output
        ];
    }

    public function getRoutingTable(): array
    {
        $output = $this->execute('ip route');
        $lines = explode("\n", trim($output));
        
        return array_filter($lines, fn($line) => !empty(trim($line)));
    }

    public function getListeningPorts(): array
    {
        $output = $this->execute('ss -tuln');
        $lines = explode("\n", trim($output));
        
        $header = array_shift($lines);
        $ports = [];
        
        foreach ($lines as $line) {
            if (empty(trim($line))) continue;
            
            $parts = preg_split('/\s+/', $line);
            if (count($parts) >= 5) {
                $ports[] = [
                    'protocol' => $parts[0],
                    'state' => $parts[1],
                    'recv_q' => $parts[2],
                    'send_q' => $parts[3],
                    'local_address' => $parts[4],
                    'peer_address' => $parts[5] ?? ''
                ];
            }
        }
        
        return $ports;
    }

    public function getActiveConnections(): array
    {
        $output = $this->execute('ss -tun');
        $lines = explode("\n", trim($output));
        
        $header = array_shift($lines);
        $connections = [];
        
        foreach ($lines as $line) {
            if (empty(trim($line))) continue;
            
            $parts = preg_split('/\s+/', $line);
            if (count($parts) >= 5) {
                $connections[] = [
                    'protocol' => $parts[0],
                    'state' => $parts[1],
                    'recv_q' => $parts[2],
                    'send_q' => $parts[3],
                    'local_address' => $parts[4],
                    'peer_address' => $parts[5] ?? ''
                ];
            }
        }
        
        return $connections;
    }

    public function pingHost(string $host, int $count = 4): array
    {
        $host = escapeshellarg($host);
        $output = $this->execute("ping -c $count $host");
        
        return [
            'host' => $host,
            'count' => $count,
            'output' => $output
        ];
    }

    private function execute(string $command): string
    {
        $output = shell_exec($command . ' 2>&1');
        return trim($output ?? '');
    }
}
