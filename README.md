# Ubuntu Server Management API

A comprehensive PHP-based RESTful API with React.js frontend for managing Ubuntu servers, built with Object-Oriented Programming (OOP) structure and Composer.

## Features

### Frontend (React.js Dashboard)
- **Real-time Monitoring**: Live system information display
- **Interactive UI**: Modern, responsive dashboard
- **Multiple Views**: System info, processes, services, disk, and network management
- **Single Page Application**: Fast navigation without page reloads

### Backend (PHP API)
- **System Information**: Get system details, CPU, and memory information
- **Process Management**: List, monitor, and kill processes
- **Service Management**: Control systemd services (start, stop, restart, enable, disable)
- **Disk Management**: Monitor disk usage, inodes, and block devices
- **User Management**: List users, groups, and logged-in sessions
- **Network Management**: View network interfaces, connections, ports, and ping hosts

## Requirements

- PHP 8.2 or higher
- Composer
- Ubuntu/Linux server
- Nginx web server
- PHP-FPM
- Sudo privileges (for service management operations)

## Installation

### 1. Clone or Download the Project

```bash
cd /var/www/html
git clone <repository-url> ubuntuserverapi
cd ubuntuserverapi
```

### 2. Install Dependencies

```bash
composer install
```

### 3. Install PHP-FPM

```bash
sudo apt update
sudo apt install php8.2-fpm php8.2-cli php8.2-mbstring php8.2-xml php8.2-curl
```

### 4. Configure Nginx

Copy the provided Nginx configuration:

```bash
sudo cp nginx.conf /etc/nginx/sites-available/ubuntuserverapi
sudo ln -s /etc/nginx/sites-available/ubuntuserverapi /etc/nginx/sites-enabled/
```

**Important:** Edit `/etc/nginx/sites-available/ubuntuserverapi` and update:
- `root` path to match your installation directory
- `fastcgi_pass` socket path if using different PHP version

Test and reload Nginx:

```bash
sudo nginx -t
sudo systemctl reload nginx
```

### 5. Set Permissions

```bash
sudo chown -R www-data:www-data /var/www/html/ubuntuserverapi
sudo chmod -R 755 /var/www/html/ubuntuserverapi
```

### 6. Configure Sudoers (Optional - for service management)

To allow the web server to manage services without password:

```bash
sudo visudo
```

Add this line:

```
www-data ALL=(ALL) NOPASSWD: /bin/systemctl
```

### 7. Setup Cloudflare Tunnel (For HTTPS)

This project uses Cloudflare Tunnel for secure HTTPS access. See `NGINX_SETUP.md` for detailed Cloudflare Tunnel configuration instructions.

Nginx runs on HTTP (port 80) locally, and Cloudflare Tunnel handles SSL/TLS termination automatically.

To allow the web server to manage services without password:

```bash
sudo visudo
```

Add this line:

```
www-data ALL=(ALL) NOPASSWD: /bin/systemctl
```

## API Endpoints

### System Information

- **GET** `/api/system/info` - Get general system information
- **GET** `/api/system/cpu` - Get CPU information and usage
- **GET** `/api/system/memory` - Get memory information and usage

### Process Management

- **GET** `/api/processes?limit=20` - List running processes
- **GET** `/api/processes/{pid}` - Get specific process information
- **DELETE** `/api/processes/{pid}` - Kill a process (add `{"force": true}` in body for SIGKILL)

### Service Management

- **GET** `/api/services` - List all systemd services
- **GET** `/api/services/{name}` - Get service status
- **POST** `/api/services/{name}/start` - Start a service
- **POST** `/api/services/{name}/stop` - Stop a service
- **POST** `/api/services/{name}/restart` - Restart a service
- **POST** `/api/services/{name}/enable` - Enable a service
- **POST** `/api/services/{name}/disable` - Disable a service

### Disk Management

- **GET** `/api/disk/usage` - Get disk usage information
- **GET** `/api/disk/inodes` - Get inode usage information
- **GET** `/api/disk/directory-size?path=/var/log` - Get directory size
- **GET** `/api/disk/block-devices` - List block devices

### User Management

- **GET** `/api/users` - List all users
- **GET** `/api/users/{username}` - Get specific user information
- **GET** `/api/users/logged-in` - List logged-in users
- **GET** `/api/users/groups` - List all groups

### Network Management

- **GET** `/api/network/interfaces` - Get network interfaces
- **GET** `/api/network/stats` - Get network statistics
- **GET** `/api/network/routes` - Get routing table
- **GET** `/api/network/listening-ports` - List listening ports
- **GET** `/api/network/connections` - List active connections
- **GET** `/api/network/ping?host=google.com&count=4` - Ping a host

### Health Check

- **GET** `/api/health` - API health check

## Usage Examples

### Using cURL

```bash
# Get system information
curl https://panel.mikael.my/api/system/info

# List processes
curl https://panel.mikael.my/api/processes?limit=10

# Get service status
curl https://panel.mikael.my/api/services/nginx

# Start a service
curl -X POST https://panel.mikael.my/api/services/nginx/start

# Kill a process
curl -X DELETE https://panel.mikael.my/api/processes/1234

# Get disk usage
curl https://panel.mikael.my/api/disk/usage

# Ping a host
curl https://panel.mikael.my/api/network/ping?host=8.8.8.8&count=3
```

### Using the React Dashboard

1. Open your browser and navigate to `https://panel.mikael.my/`
2. The dashboard will automatically load and connect to the API
3. Use the navigation tabs to switch between different management views
4. Click buttons to perform actions (start/stop services, kill processes, etc.)

### Using JavaScript/Fetch

```javascript
// Get system info
fetch('https://panel.mikael.my/api/system/info')
  .then(response => response.json())
  .then(data => console.log(data));

// Start a service
fetch('https://panel.mikael.my/api/services/nginx/start', {
  method: 'POST'
})
  .then(response => response.json())
  .then(data => console.log(data));
```

## Response Format

All responses follow this format:

### Success Response
```json
{
  "success": true,
  "message": "Operation completed successfully",
  "data": { ... }
}
```

### Error Response
```json
{
  "success": false,
  "message": "Error description",
  "errors": []
}
```

## Project Structure

```
ubuntuserverapi/
├── config/
│   └── config.php              # Configuration settings
├── public/
│   ├── index.html              # React Frontend entry point
│   ├── css/
│   │   └── styles.css          # Dashboard styles
│   ├── js/
│   │   └── app.jsx             # React application
│   └── api/
│       └── index.php           # API entry point
├── src/
│   ├── Controllers/            # API Controllers
│   │   ├── SystemController.php
│   │   ├── ProcessController.php
│   │   ├── ServiceController.php
│   │   ├── DiskController.php
│   │   ├── UserController.php
│   │   └── NetworkController.php
│   ├── Services/               # Business logic services
│   │   ├── SystemInfoService.php
│   │   ├── ProcessService.php
│   │   ├── ServiceManagerService.php
│   │   ├── DiskService.php
│   │   ├── UserService.php
│   │   └── NetworkService.php
│   └── Core/                   # Core framework classes
│       ├── Router.php
│       ├── Request.php
│       ├── Response.php
│       └── Config.php
├── composer.json               # Composer dependencies
├── package.json                # Frontend package info
├── nginx.conf                  # Nginx configuration
└── README.md                   # Documentation
```

## URL Structure

- **Frontend Dashboard**: `https://panel.mikael.my/` (via Cloudflare Tunnel)
- **API Endpoints**: `https://panel.mikael.my/api/*` (via Cloudflare Tunnel)
- **Local Nginx**: `http://localhost:80` (HTTP only, not exposed to internet)

The `nginx.conf` routes:
- `/api/*` requests to the PHP API (`public/api/index.php`)
- All other requests to the React frontend (`public/index.html`)

**Note:** Nginx runs on HTTP locally. Cloudflare Tunnel provides SSL/TLS encryption and exposes the site securely at `https://panel.mikael.my`.

## Security Considerations

⚠️ **Important Security Notes:**

1. **HTTPS/SSL**: Handled by Cloudflare Tunnel - no local SSL configuration needed
2. **Authentication**: This API currently has no authentication. Add API key or OAuth before production use.
3. **Authorization**: Implement role-based access control for sensitive operations.
4. **Input Validation**: Always validate and sanitize user inputs.
5. **Rate Limiting**: Implement rate limiting to prevent abuse (can be done via Cloudflare).
6. **Sudo Access**: Restrict sudo permissions to only necessary commands.
7. **Firewall**: No need to expose ports 80/443 - Cloudflare Tunnel handles external access.
8. **Cloudflare**: Use Cloudflare's security features (WAF, DDoS protection, rate limiting).

## Development

### Running Tests

```bash
composer test
```

### Debug Mode

Debug mode is enabled by default in `config/config.php`. Disable it in production:

```php
'debug' => false,
```

## License

MIT License

## Contributing

Contributions are welcome! Please submit pull requests or open issues.

## Support

For issues and questions, please open an issue in the repository.
