# WordPress Movie Auto Poster to Telegram

PHP script that checks WordPress REST API (`movies` custom post type) every 5 minutes and pushes the latest movie updates with poster images to a Telegram Channel using Render Cron Job.

## Setup Instructions

### 1. Local/GitHub Preparation
1. Open `config.php` and update `TELEGRAM_BOT_TOKEN` with your actual bot token from BotFather.
2. Ensure `TELEGRAM_CHANNEL_ID` matches your targeted channel (`-1002677113544`).
3. Commit all files into a private GitHub repository.

### 2. Deploy to Render.com
1. Log in to **Render.com**.
2. Click **New +** and select **Blueprint**.
3. Connect your GitHub repository containing this project.
4. Render will read `render.yaml` and configure the PHP environment and the 5-minute Cron Job automatically.

## Troubleshooting
- If posters don't arrive, check the **Logs** tab inside your Render Cron Job Dashboard.
- Ensure your Telegram Bot is an **Admin** in the channel with **Post Messages** permission enabled.
