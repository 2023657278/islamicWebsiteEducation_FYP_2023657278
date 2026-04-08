import logging
import mysql.connector
from telegram import Update, InlineKeyboardButton, InlineKeyboardMarkup
from telegram.ext import ApplicationBuilder, ContextTypes, CommandHandler, CallbackQueryHandler

# 🔥 CONFIGURATION
TOKEN = "8036575496:AAFtaYbG65gKDAFPV7BBcDNs9vmeFrB4nk0"

# DATABASE CONNECTION (Matches your .env)
db_config = {
    'user': 'root',
    'password': '',
    'host': '127.0.0.1',
    'database': 'islamicwebsite', # CHECK IF THIS NAME IS CORRECT
    'raise_on_warnings': True
}

# SETUP LOGGING (So you see errors immediately)
logging.basicConfig(
    format='%(asctime)s - %(name)s - %(levelname)s - %(message)s',
    level=logging.INFO
)

def get_db_connection():
    return mysql.connector.connect(**db_config)

# --- 1. START / MENU ---
async def start(update: Update, context: ContextTypes.DEFAULT_TYPE):
    await show_subjects(update, context)

async def show_subjects(update: Update, context: ContextTypes.DEFAULT_TYPE):
    try:
        conn = get_db_connection()
        cursor = conn.cursor(dictionary=True)
        cursor.execute("SELECT * FROM subjects")
        subjects = cursor.fetchall()
        conn.close()

        if not subjects:
            await send_text(update, "❌ No subjects found in database.")
            return

        keyboard = []
        for s in subjects:
            keyboard.append([InlineKeyboardButton(f"📘 {s['subject_name']}", callback_data=f"sub_{s['id']}")])
        
        reply_markup = InlineKeyboardMarkup(keyboard)
        await send_text(update, "📚 *Select a Subject:*", reply_markup)

    except Exception as e:
        logging.error(f"DATABASE ERROR: {e}")
        await send_text(update, f"⚠️ Database Error: {e}")

# --- 2. SHOW QUIZZES ---
async def show_quizzes(update: Update, context: ContextTypes.DEFAULT_TYPE, sub_id):
    try:
        conn = get_db_connection()
        cursor = conn.cursor(dictionary=True)
        # Using simple SQL instead of Eloquent
        cursor.execute("SELECT * FROM quizzes WHERE subject_id = %s", (sub_id,))
        quizzes = cursor.fetchall()
        conn.close()

        if not quizzes:
            await send_text(update, "📂 Folder is Empty.")
            return

        keyboard = []
        for q in quizzes:
            keyboard.append([InlineKeyboardButton(f"📝 {q['title']}", callback_data=f"view_{q['id']}")])
        
        keyboard.append([InlineKeyboardButton("🔙 Back", callback_data="menu_subjects")])
        
        reply_markup = InlineKeyboardMarkup(keyboard)
        await send_text(update, "📂 Select a Quiz:", reply_markup)

    except Exception as e:
        logging.error(f"ERROR: {e}")

# --- 3. SHOW SUMMARY ---
async def show_summary(update: Update, context: ContextTypes.DEFAULT_TYPE, quiz_id):
    try:
        conn = get_db_connection()
        cursor = conn.cursor(dictionary=True)
        cursor.execute("SELECT * FROM quizzes WHERE id = %s", (quiz_id,))
        quiz = cursor.fetchone()
        
        # Count questions
        cursor.execute("SELECT COUNT(*) as count FROM questions WHERE quiz_id = %s", (quiz_id,))
        count = cursor.fetchone()['count']
        conn.close()

        text = f"🎓 *{quiz['title']}*\n\n❓ Questions: {count}\n\nReady to start?"
        keyboard = [
            [InlineKeyboardButton("🚀 Start Quiz", callback_data=f"start_{quiz['id']}")],
            [InlineKeyboardButton("🔙 Back", callback_data=f"sub_{quiz['subject_id']}")]
        ]
        await send_text(update, text, InlineKeyboardMarkup(keyboard))

    except Exception as e:
        logging.error(f"ERROR: {e}")

# --- 4. START QUIZ ---
async def start_quiz(update: Update, context: ContextTypes.DEFAULT_TYPE, quiz_id):
    # Initialize User State
    context.user_data['quiz_id'] = quiz_id
    context.user_data['idx'] = 0
    context.user_data['score'] = 0
    await ask_question(update, context)

async def ask_question(update: Update, context: ContextTypes.DEFAULT_TYPE):
    quiz_id = context.user_data['quiz_id']
    idx = context.user_data['idx']

    conn = get_db_connection()
    cursor = conn.cursor(dictionary=True)
    
    # Get all questions
    cursor.execute("SELECT * FROM questions WHERE quiz_id = %s", (quiz_id,))
    questions = cursor.fetchall()
    
    if idx >= len(questions):
        # FINISH QUIZ
        score = context.user_data['score']
        total = len(questions)
        await send_text(update, f"🏆 *Finished!* Score: {score} / {total}")
        await show_subjects(update, context)
        conn.close()
        return

    q = questions[idx]
    cursor.execute("SELECT * FROM options WHERE question_id = %s", (q['id'],))
    options = cursor.fetchall()
    conn.close()

    text = f"❓ *Q{idx+1}*: {q['question_text']}"
    keyboard = []
    
    for opt in options:
        # Check if option is correct (1 or True)
        is_correct = 1 if opt['is_correct'] else 0
        keyboard.append([InlineKeyboardButton(opt['option_text'], callback_data=f"ans_{is_correct}")])

    await send_text(update, text, InlineKeyboardMarkup(keyboard))

# --- 5. HANDLE ANSWER ---
async def handle_answer(update: Update, context: ContextTypes.DEFAULT_TYPE, is_correct):
    if is_correct == '1':
        context.user_data['score'] += 1
        await send_text(update, "✅ Correct!")
    else:
        await send_text(update, "❌ Wrong.")

    context.user_data['idx'] += 1
    await ask_question(update, context)

# --- HELPER: BUTTON HANDLER ---
async def button_handler(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    await query.answer() # Stop spinner
    data = query.data

    if data == "menu_subjects":
        await show_subjects(update, context)
    elif data.startswith("sub_"):
        await show_quizzes(update, context, data.split("_")[1])
    elif data.startswith("view_"):
        await show_summary(update, context, data.split("_")[1])
    elif data.startswith("start_"):
        await start_quiz(update, context, data.split("_")[1])
    elif data.startswith("ans_"):
        await handle_answer(update, context, data.split("_")[1])

# --- HELPER: SEND TEXT ---
async def send_text(update: Update, text, reply_markup=None):
    if update.callback_query:
        await update.callback_query.message.reply_text(text, reply_markup=reply_markup, parse_mode='Markdown')
    else:
        await update.message.reply_text(text, reply_markup=reply_markup, parse_mode='Markdown')

# --- MAIN LOOP ---
if __name__ == '__main__':
    app = ApplicationBuilder().token(TOKEN).build()

    app.add_handler(CommandHandler("start", start))
    app.add_handler(CommandHandler("quiz", start))
    app.add_handler(CallbackQueryHandler(button_handler))

    print("🚀 Python Bot is Running...")
    app.run_polling()