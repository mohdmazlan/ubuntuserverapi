# Ubuntu Server Management API

[![PHP Version](https://img.shields.io/badge/PHP-8.2+-blue.svg)](https://php.net)
[![License](https://img.shields.io/badge/license-MIT-green.svg)](LICENSE)

A comprehensive PHP-based RESTful API with React.js frontend for managing Ubuntu servers, built with Object-Oriented Programming (OOP) structure and Composer.

## 🚀 Features

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

## 📋 Requirements

- PHP 8.2 or higher
- Composer
- Ubuntu/Linux server
- Nginx web server
- PHP-FPM
- Sudo privileges (for service management operations)

## 🔧 Installation

### 1. Clone the Repository

```bash
git clone https://github.com/yourusername/ubuntuserverapi.git
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

```bash
sudo cp nginx.conf /etc/nginx/sites-available/ubuntuserverapi
sudo ln -s /etc/nginx/sites-available/ubuntuserverapi /etc/nginx/sites-enabled/
```

Edit the configuration:
```bash
sudo nano /etc/nginx/sites-available/ubuntuserverapi
```

Update:
- `server_name` with your domain
- `root` path to match your installation directory
- `fastcgi_pass` socket path if using different PHP version

Test and reload Nginx:
```bash
sudo nginx -t
sudo systemctl reload nginx
```

### 5. Set Permissions

```bash
sudo chown -R www-data:www-data /path/to/ubuntuserverapi
sudo chmod -R 755 /path/to/ubuntuserverapi
```

### 6. Configure Sudoers (Optional)

For service management without password:

```bash
sudo visudo
```

Add:
```
www-data ALL=(ALL) NOPASSWD: /bin/systemctl
```

### 7. Setup Cloudflare Tunnel (Optional)

For secure HTTPS access, see [NGINX_SETUP.md](NGINX_SETUP.md) for Cloudflare Tunnel configuration.

## 📚 Documentation

- [Nginx Setup Guide](NGINX_SETUP.md) - Detailed Nginx and Cloudflare Tunnel configuration
- [API Documentation](#api-endpoints) - Complete API reference

## 🌐 API Endpoints

### System Information
- `GET /api/system/info` - Get general system information
- `GET /api/system/cpu` - Get CPU information and usage
- `GET /api/system/memory` - Get memory information and usage

### Process Management
- `GET /api/processes?limit=20` - List running processes
- `GET /api/processes/{pid}` - Get specific process information
- `DELETE /api/processes/{pid}` - Kill a process

### Service Management
- `GET /api/services` - List all systemd services
- `GET /api/services/{name}` - Get service status
- `POST /api/services/{name}/start` - Start a service
- `POST /api/services/{name}/stop` - Stop a service
- `POST /api/services/{name}/restart` - Restart a service
- `POST /api/services/{name}/enable` - Enable a service
- `POST /api/services/{name}/disable` - Disable a service

### Disk Management
- `GET /api/disk/usage` - Get disk usage information
- `GET /api/disk/inodes` - Get inode usage information
- `GET /api/disk/directory-size?path=/var/log` - Get directory size
- `GET /api/disk/block-devices` - List block devices

### User Management
- `GET /api/users` - List all users
- `GET /api/users/{username}` - Get specific user information
- `GET /api/users/logged-in` - List logged-in users
- `GET /api/users/groups` - List all groups

### Network Management
- `GET /api/network/interfaces` - Get network interfaces
- `GET /api/network/stats` - Get network statistics
- `GET /api/network/routes` - Get routing table
- `GET /api/network/listening-ports` - List listening ports
- `GET /api/network/connections` - List active connections
- `GET /api/network/ping?host=google.com&count=4` - Ping a host

### Health Check
- `GET /api/health` - API health check

## 🎯 Usage Examples

### Using cURL

```bash
# Get system information
curl https://your-domain.com/api/system/info

# List processes
curl https://your-domain.com/api/processes?limit=10

# Start a service
curl -X POST https://your-domain.com/api/services/nginx/start
```

### Using JavaScript

```javascript
// Get system info
fetch('https://your-domain.com/api/system/info')
  .then(response => response.json())
  .then(data => console.log(data));
```

## 📁 Project Structure

```
ubuntuserverapi/
├── config/
│   └── config.php              # Configuration settings
├── public/
│   ├── index.html              # React Frontend
│   ├── css/
│   │   └── styles.css          # Dashboard styles
│   ├── js/
│   │   └── app.jsx             # React application
│   └── api/
│       └── index.php           # API entry point
├── src/
│   ├── Controllers/            # API Controllers
│   ├── Services/               # Business logic services
│   └── Core/                   # Core framework classes
├── composer.json               # PHP dependencies
├── package.json                # Frontend info
├── nginx.conf                  # Nginx configuration
└── README.md                   # This file
```

## 🔒 Security

⚠️ **Important Security Notes:**

- **Authentication**: Add API key or OAuth authentication before production use
- **Authorization**: Implement role-based access control
- **Input Validation**: Always validate and sanitize user inputs
- **Rate Limiting**: Implement rate limiting to prevent abuse
- **HTTPS**: Use HTTPS in production (Cloudflare Tunnel recommended)
- **Sudo Access**: Restrict sudo permissions to only necessary commands
- **Firewall**: Configure firewall rules appropriately

## 🤝 Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## 📝 License

This project is licensed under the MIT License.

## 👨‍💻 Author

Created for server management and monitoring purposes.

## ⚠️ Disclaimer

This tool provides powerful server management capabilities. Use with caution and ensure proper security measures are in place before deploying in production.
