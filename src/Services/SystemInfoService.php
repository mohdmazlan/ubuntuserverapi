<?php

declare(strict_types=1);

namespace UbuntuServerAPI\Services;

class SystemInfoService
{
    public function getSystemInfo(): array
    {
        return [
            'hostname' => $this->execute('hostname'),
            'uptime' => $this->execute('uptime -p'),
            'kernel' => $this->execute('uname -r'),
            'os' => $this->execute('lsb_release -d | cut -f2'),
            'architecture' => $this->execute('uname -m'),
            'load_average' => $this->getLoadAverage(),
            'timestamp' => date('Y-m-d H:i:s')
        ];
    }

    public function getCpuInfo(): array
    {
        $cpuInfo = $this->execute('lscpu');
        $usage = $this->execute("top -bn1 | grep 'Cpu(s)' | sed 's/.*, *\([0-9.]*\)%* id.*/\1/' | awk '{print 100 - $1}'");
        
        return [
            'info' => $cpuInfo,
            'usage' => floatval($usage),
            'cores' => $this->execute('nproc')
        ];
    }

    public function getMemoryInfo(): array
    {
        $memInfo = $this->execute('free -h');
        $memArray = $this->execute('free -m | grep Mem');
        
        preg_match_all('/\d+/', $memArray, $matches);
        $numbers = $matches[0] ?? [];
        
        $total = $numbers[0] ?? 0;
        $used = $numbers[1] ?? 0;
        $free = $numbers[2] ?? 0;
        
        return [
            'output' => $memInfo,
            'total_mb' => $total,
            'used_mb' => $used,
            'free_mb' => $free,
            'usage_percent' => $total > 0 ? round(($used / $total) * 100, 2) : 0
        ];
    }

    private function getLoadAverage(): array
    {
        $loadavg = sys_getloadavg();
        return [
            '1min' => $loadavg[0],
            '5min' => $loadavg[1],
            '15min' => $loadavg[2]
        ];
    }

    private function execute(string $command): string
    {
        $output = shell_exec($command . ' 2>&1');
        return trim($output ?? '');
    }
}
