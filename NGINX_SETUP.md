# Nginx Configuration Guide

## Installation Steps

### 1. Install Nginx and PHP-FPM

```bash
sudo apt update
sudo apt install nginx php8.2-fpm php8.2-cli php8.2-mbstring php8.2-xml php8.2-curl
```

### 2. Deploy Application

```bash
# Copy project to web directory
sudo mkdir -p /var/www/html
sudo cp -r ubuntuserverapi /var/www/html/
cd /var/www/html/ubuntuserverapi

# Install dependencies
composer install

# Set permissions
sudo chown -R www-data:www-data /var/www/html/ubuntuserverapi
sudo chmod -R 755 /var/www/html/ubuntuserverapi
```

### 3. Configure Nginx

```bash
# Copy Nginx configuration
sudo cp nginx.conf /etc/nginx/sites-available/ubuntuserverapi

# Edit the configuration (update server_name and paths)
sudo nano /etc/nginx/sites-available/ubuntuserverapi

# Enable the site
sudo ln -s /etc/nginx/sites-available/ubuntuserverapi /etc/nginx/sites-enabled/

# Remove default site (optional)
sudo rm /etc/nginx/sites-enabled/default

# Test configuration
sudo nginx -t

# Reload Nginx
sudo systemctl reload nginx
```

### 4. Configure PHP-FPM

Check your PHP-FPM socket path:

```bash
ls -la /var/run/php/
```

You should see something like `php8.2-fpm.sock`. Update the `nginx.conf` file if the version differs:

```nginx
fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;  # Update version here
```

### 5. Configure Firewall

```bash
sudo ufw allow 'Nginx Full'
sudo ufw enable
```

### 6. Enable Sudo for Service Management (Optional)

```bash
sudo visudo
```

Add this line:

```
www-data ALL=(ALL) NOPASSWD: /bin/systemctl
```

## Verification

### Test Nginx Configuration

```bash
sudo nginx -t
```

### Check Nginx Status

```bash
sudo systemctl status nginx
```

### Check PHP-FPM Status

```bash
sudo systemctl status php8.2-fpm
```

### View Nginx Logs

```bash
# Error log
sudo tail -f /var/log/nginx/error.log

# Access log
sudo tail -f /var/log/nginx/access.log
```

## Troubleshooting

### API Returns 404

1. Check Nginx configuration is correct
2. Verify the document root path
3. Check file permissions
4. Review Nginx error logs

### PHP Not Processing

1. Ensure PHP-FPM is running: `sudo systemctl status php8.2-fpm`
2. Check the socket path in nginx.conf matches actual socket
3. Restart PHP-FPM: `sudo systemctl restart php8.2-fpm`

### Permission Denied

```bash
sudo chown -R www-data:www-data /var/www/html/ubuntuserverapi
sudo chmod -R 755 /var/www/html/ubuntuserverapi
```

### CORS Issues

The nginx.conf includes CORS headers. If you need to restrict origins, modify:

```nginx
add_header Access-Control-Allow-Origin "https://your-domain.com" always;
```

## Performance Optimization

### Gzip Compression (Already included)

The configuration includes gzip compression for better performance.

### PHP-FPM Tuning

Edit `/etc/php/8.2/fpm/pool.d/www.conf`:

```ini
pm = dynamic
pm.max_children = 50
pm.start_servers = 5
pm.min_spare_servers = 5
pm.max_spare_servers = 35
```

Restart PHP-FPM:

```bash
sudo systemctl restart php8.2-fpm
```

## Cloudflare Tunnel Configuration

This setup uses Cloudflare Tunnel for secure HTTPS access. Nginx runs on HTTP (port 80) locally, and Cloudflare Tunnel handles SSL/TLS termination.

### Install Cloudflared

```bash
# Download and install cloudflared
wget https://github.com/cloudflare/cloudflared/releases/latest/download/cloudflared-linux-amd64.deb
sudo dpkg -i cloudflared-linux-amd64.deb

# Verify installation
cloudflared --version
```

### Configure Tunnel

```bash
# Authenticate with Cloudflare
cloudflared tunnel login

# Create a tunnel
cloudflared tunnel create mikael-panel

# Create config file
sudo mkdir -p /etc/cloudflared
sudo nano /etc/cloudflared/config.yml
```

Add this configuration to `/etc/cloudflared/config.yml`:

```yaml
tunnel: <TUNNEL-ID>
credentials-file: /root/.cloudflared/<TUNNEL-ID>.json

ingress:
  - hostname: your-domain.com
    service: http://localhost:80
  - service: http_status:404
```

### Start Cloudflared Service

```bash
# Install as a service
sudo cloudflared service install

# Start the service
sudo systemctl start cloudflared
sudo systemctl enable cloudflared

# Check status
sudo systemctl status cloudflared
```

### DNS Configuration

In your Cloudflare dashboard:
1. Go to your domain's DNS settings
2. Add a CNAME record:
   - Name: `panel`
   - Target: `<TUNNEL-ID>.cfargotunnel.com`
   - Proxy status: Proxied (orange cloud)

The tunnel will automatically provide HTTPS through Cloudflare's SSL certificate.

## Notes

- **No local SSL needed**: Cloudflare Tunnel handles SSL/TLS
- **Port 80 only**: Nginx listens on HTTP port 80 locally
- **Security**: Traffic is encrypted between client and Cloudflare
- **Firewall**: No need to open port 80/443 to the internet
