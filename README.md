# KMK Entertainment Ticket System

A comprehensive ticket management and support platform built on CodeIgniter.

## 🚀 Deployment Workflow

This project uses an automated CI/CD pipeline via GitHub Actions.

### How it Works

1. **Push to GitHub:** Whenever code is pushed to the `main` branch.
2. **Automated Tests:**
   - PHP Syntax Check (`phplint`) on the `application/` directory.
3. **Auto-Deploy:**
   - If tests pass, the workflow automatically SSHs into the production server.
   - Runs `git pull origin main` in `/var/www/html`.
4. **Live Site:** The changes are immediately available at `https://ticket.kmkentertainment.com`.

### Manual Deployment

If you need to manually update the server:
```bash
ssh root@104.225.221.55
cd /var/www/html
git pull origin main
```

### Workflow Configuration

- **File:** `.github/workflows/deploy.yml`
- **Secrets Required:**
  - `SERVER_HOST`: Production server IP (`104.225.221.55`)
  - `SERVER_USER`: SSH username (`root`)
  - `SSH_PRIVATE_KEY`: The private key for server authentication (stored in GitHub Secrets)

## 📂 Project Structure

```text
/var/www/html/
├── application/        # Core PHP application (CodeIgniter)
├── system/             # CodeIgniter framework files
├── assets/             # Static files (CSS, JS, Images)
├── uploads/            # User uploads (ignored by git)
└── .github/            # CI/CD workflow configuration
```

## ⚠️ Important Notes

- **Uploads Directory:** User attachments in `uploads/` are NOT tracked by git.
- **Database:** Database changes must be managed manually via SQL scripts or migrations.
- **Config Files:** Sensitive configuration files (e.g., `database.php`) are excluded from the repository for security.

## 🔧 Development

- **Branching:** Push to `main` for automatic deployment. Use feature branches for work in progress.
- **Linting:** Ensure all PHP files pass syntax checks before pushing to avoid deployment blocks.
