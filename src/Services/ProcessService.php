<?php

declare(strict_types=1);

namespace UbuntuServerAPI\Services;

class ProcessService
{
    public function listProcesses(int $limit = 20): array
    {
        $output = $this->execute("ps aux --sort=-%cpu | head -n " . ($limit + 1));
        $lines = explode("\n", trim($output));
        
        $header = array_shift($lines);
        $processes = [];
        
        foreach ($lines as $line) {
            if (empty(trim($line))) continue;
            
            $parts = preg_split('/\s+/', $line, 11);
            if (count($parts) >= 11) {
                $processes[] = [
                    'user' => $parts[0],
                    'pid' => $parts[1],
                    'cpu' => $parts[2],
                    'mem' => $parts[3],
                    'vsz' => $parts[4],
                    'rss' => $parts[5],
                    'tty' => $parts[6],
                    'stat' => $parts[7],
                    'start' => $parts[8],
                    'time' => $parts[9],
                    'command' => $parts[10]
                ];
            }
        }
        
        return $processes;
    }

    public function getProcessInfo(int $pid): ?array
    {
        $output = $this->execute("ps -p $pid -o user,pid,%cpu,%mem,vsz,rss,tty,stat,start,time,comm --no-headers");
        
        if (empty($output)) {
            return null;
        }
        
        $parts = preg_split('/\s+/', trim($output), 11);
        
        if (count($parts) < 11) {
            return null;
        }
        
        return [
            'user' => $parts[0],
            'pid' => $parts[1],
            'cpu' => $parts[2],
            'mem' => $parts[3],
            'vsz' => $parts[4],
            'rss' => $parts[5],
            'tty' => $parts[6],
            'stat' => $parts[7],
            'start' => $parts[8],
            'time' => $parts[9],
            'command' => $parts[10]
        ];
    }

    public function killProcess(int $pid, bool $force = false): bool
    {
        $signal = $force ? '-9' : '-15';
        $this->execute("kill $signal $pid");
        
        sleep(1);
        $check = $this->execute("ps -p $pid -o pid --no-headers");
        
        return empty(trim($check));
    }

    private function execute(string $command): string
    {
        $output = shell_exec($command . ' 2>&1');
        return trim($output ?? '');
    }
}
