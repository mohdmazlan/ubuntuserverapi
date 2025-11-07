# GitHub Publish Instructions

## Files Protected from GitHub

The following files contain your personal configuration and will NOT be pushed to GitHub:

✅ **Protected (in .gitignore):**
- `README.md` - Your personal setup guide (panel.mikael.my)
- `README_PRIVATE.md` - Private notes
- `config/config.php` - Your actual configuration
- `.env` files
- Cloudflare credentials (*.json)
- Any SSL certificates

✅ **Public (will be pushed):**
- `README_PUBLIC.md` - Generic public documentation
- `config/config.example.php` - Example configuration template
- All source code (src/, public/)
- `nginx.conf` - With generic domain placeholder
- Documentation files

## How to Push to GitHub

### 1. Create a New Repository on GitHub

Go to https://github.com/new and create a new repository (e.g., `ubuntu-server-api`)

### 2. Rename Public README

```bash
# Use the public README for GitHub
Remove-Item README.md -Force
Move-Item README_PUBLIC.md README.md
git add .
git commit -m "Use public README for GitHub"
```

### 3. Add Remote and Push

```bash
# Add your GitHub repository as remote
git remote add origin https://github.com/mohdmazlan/ubuntu-server-api.git

# Push to GitHub
git branch -M main
git push -u origin main
```

### 4. Verify

Check your GitHub repository - it should NOT contain:
- Your domain (panel.mikael.my) 
- Your actual config/config.php
- Any cloudflare credentials
- Private README files

## What Gets Published

✅ **Source Code**: Complete PHP OOP API structure  
✅ **Frontend**: React.js dashboard  
✅ **Documentation**: Setup guides (generic)  
✅ **Configuration**: Example templates only  
✅ **License**: MIT License  

❌ **Private Info**: Domain, credentials, actual config  
❌ **Secrets**: API keys, passwords, certificates  
❌ **Personal Notes**: Private README, local configs  

## Future Updates

To update the public repository:

```bash
# Make your changes
git add .
git commit -m "Your commit message"
git push origin main
```

The .gitignore ensures sensitive files stay local.
