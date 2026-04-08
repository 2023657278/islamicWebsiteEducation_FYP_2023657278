import logging
from telegram import Update, InlineKeyboardButton, InlineKeyboardMarkup
from telegram.ext import ApplicationBuilder, ContextTypes, CommandHandler, CallbackQueryHandler

# 🔥 YOUR TOKEN
TOKEN = "8036575496:AAFtaYbG65gKDAFPV7BBcDNs9vmeFrB4nk0"

# 1. Enable DEBUG Logging (This shows RAW data from Telegram)
logging.basicConfig(
    format='%(asctime)s - %(name)s - %(levelname)s - %(message)s',
    level=logging.DEBUG  # <--- CHANGED TO DEBUG
)

async def start(update: Update, context: ContextTypes.DEFAULT_TYPE):
    # Send Fake Buttons (No Database)
    keyboard = [
        [InlineKeyboardButton("📘 TEST SUBJECT 1", callback_data="sub_1")],
        [InlineKeyboardButton("📘 TEST SUBJECT 2", callback_data="sub_2")]
    ]
    await update.message.reply_text("🛠️ DEBUG MODE: Click a button:", reply_markup=InlineKeyboardMarkup(keyboard))

async def button_handler(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    
    # 2. Print the click to the terminal
    print(f"\n👉 BUTTON CLICK DETECTED! Data: {query.data}\n")
    
    await query.answer() # Stop spinner
    
    if query.data.startswith("sub_"):
        await query.edit_message_text(text=f"✅ SUCCESS! You clicked Subject ID {query.data.split('_')[1]}")

if __name__ == '__main__':
    print("🚀 DEBUG BOT RUNNING... (Press Ctrl+C to stop)")
    
    app = ApplicationBuilder().token(TOKEN).build()
    app.add_handler(CommandHandler("quiz", start))
    app.add_handler(CallbackQueryHandler(button_handler))
    
    app.run_polling()