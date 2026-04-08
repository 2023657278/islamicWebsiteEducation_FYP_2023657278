@echo off
:: Navigate to your project folder
cd /d "C:\laragon\www\islamicWebsiteEducation_FYP_2023657278"

:: 1. Start Laravel Server
start "Laravel Server" php artisan serve

:: 2. Start NPM (Vite)
start "Vite Assets" npm run dev

:: 3. Start Queue Worker (Important for background tasks/emails)
start "Queue Worker" php artisan queue:listen

:: 4. Start Ngrok with YOUR PERMANENT DOMAIN
start "Ngrok Tunnel" ngrok http --domain=francie-unofficious-tarah.ngrok-free.dev 8000

:: 5. Start Python Telegram Bot (The Brain)
start "Telegram Bot Brain" python final_bot.py

:: Close this launcher window
exit