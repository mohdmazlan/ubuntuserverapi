<?php

declare(strict_types=1);

namespace UbuntuServerAPI\Services;

class SSHService
{
    private string $username;
    private string $host;
    private int $port;
    
    public function __construct(string $host = 'localhost', int $port = 22, string $username = 'www-data')
    {
        $this->host = $host;
        $this->port = $port;
        $this->username = $username;
    }
    
    /**
     * Execute a command locally (without SSH)
     */
    public function executeLocal(string $command): array
    {
        $output = [];
        $returnCode = 0;
        
        exec($command . ' 2>&1', $output, $returnCode);
        
        return [
            'command' => $command,
            'output' => implode("\n", $output),
            'exit_code' => $returnCode,
            'success' => $returnCode === 0
        ];
    }
    
    /**
     * Execute multiple commands in sequence
     */
    public function executeMultiple(array $commands): array
    {
        $results = [];
        
        foreach ($commands as $command) {
            $results[] = $this->executeLocal($command);
        }
        
        return $results;
    }
    
    /**
     * Execute command in a specific directory
     */
    public function executeInDirectory(string $directory, string $command): array
    {
        if (!is_dir($directory)) {
            return [
                'command' => $command,
                'output' => "Directory does not exist: $directory",
                'exit_code' => 1,
                'success' => false
            ];
        }
        
        $fullCommand = "cd " . escapeshellarg($directory) . " && $command";
        return $this->executeLocal($fullCommand);
    }
    
    /**
     * Get terminal session info
     */
    public function getTerminalInfo(): array
    {
        $shell = $this->executeLocal('echo $SHELL');
        $user = $this->executeLocal('whoami');
        $pwd = $this->executeLocal('pwd');
        $home = $this->executeLocal('echo $HOME');
        
        return [
            'shell' => trim($shell['output']),
            'user' => trim($user['output']),
            'current_directory' => trim($pwd['output']),
            'home_directory' => trim($home['output']),
            'hostname' => gethostname()
        ];
    }
    
    /**
     * List directory contents
     */
    public function listDirectory(string $path = '.'): array
    {
        $path = escapeshellarg($path);
        $result = $this->executeLocal("ls -lah $path");
        
        return [
            'path' => $path,
            'output' => $result['output'],
            'success' => $result['success']
        ];
    }
    
    /**
     * Read file contents
     */
    public function readFile(string $filepath): array
    {
        if (!file_exists($filepath)) {
            return [
                'file' => $filepath,
                'content' => null,
                'error' => 'File not found',
                'success' => false
            ];
        }
        
        $content = file_get_contents($filepath);
        
        return [
            'file' => $filepath,
            'content' => $content,
            'size' => filesize($filepath),
            'success' => true
        ];
    }
    
    /**
     * Get command history
     */
    public function getHistory(int $lines = 50): array
    {
        $result = $this->executeLocal("history $lines");
        
        return [
            'lines' => $lines,
            'history' => $result['output'],
            'success' => $result['success']
        ];
    }
    
    /**
     * Get environment variables
     */
    public function getEnvironment(): array
    {
        $result = $this->executeLocal('env');
        
        $env = [];
        $lines = explode("\n", $result['output']);
        
        foreach ($lines as $line) {
            if (strpos($line, '=') !== false) {
                [$key, $value] = explode('=', $line, 2);
                $env[$key] = $value;
            }
        }
        
        return [
            'environment' => $env,
            'count' => count($env)
        ];
    }
    
    /**
     * Check if command exists
     */
    public function commandExists(string $command): bool
    {
        $result = $this->executeLocal("which $command");
        return $result['success'];
    }
    
    /**
     * Execute with sudo (requires passwordless sudo)
     */
    public function executeSudo(string $command): array
    {
        $fullCommand = "sudo " . $command;
        return $this->executeLocal($fullCommand);
    }
}
