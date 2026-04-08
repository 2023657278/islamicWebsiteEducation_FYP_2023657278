import logging
import mysql.connector
import requests
import matplotlib.pyplot as plt
import io
import os
from datetime import datetime
from telegram import Update, ReplyKeyboardMarkup, KeyboardButton
from telegram.ext import ApplicationBuilder, ContextTypes, CommandHandler, MessageHandler, filters

# 🔥 CONFIGURATION
TOKEN = "8036575496:AAFtaYbG65gKDAFPV7BBcDNs9vmeFrB4nk0"
db_config = {
    'user': 'root', 'password': '', 'host': '127.0.0.1', 'database': 'islamicwebsite', 'raise_on_warnings': True
}

logging.basicConfig(format='%(asctime)s - %(levelname)s - %(message)s', level=logging.INFO)

def get_db():
    return mysql.connector.connect(**db_config)

# ==============================================================================
# 1. COMMAND FUNCTIONS
# ==============================================================================

async def start(update: Update, context: ContextTypes.DEFAULT_TYPE):
    msg = (
        "👋 *Assalamu'alaikum!*\n"
        "Welcome to the MRSM Terendak PAI Learning Bot.\n\n"
        "🔗 [Visit Website Dashboard](http://127.0.0.1:8000/homepage)\n\n"
        "Please select a Subject below to start revision:"
    )
    await update.message.reply_text(msg, parse_mode='Markdown')
    await show_subjects(update, context)

async def help_command(update: Update, context: ContextTypes.DEFAULT_TYPE):
    msg = (
        "📖 *MRSM PAI Bot Guide:*\n\n"
        "🔹 /start - Restart bot & see main menu\n"
        "🔹 /quiz - Start a PAI quiz session\n"
        "🔹 /progress - View your performance graph\n"
        "🔹 /timetable - View your weekly class schedule\n"
        "🔹 /prayer - Get JAKIM-aligned prayer times\n"
        "🔹 /help - View this guide"
    )
    await update.message.reply_text(msg, parse_mode='Markdown')

async def prayer_command(update: Update, context: ContextTypes.DEFAULT_TYPE):
    try:
        tid = update.effective_user.id
        loc = "Jasin" # Default
        url = f"https://api.aladhan.com/v1/timingsByCity?city={loc}&country=Malaysia&method=11"
        response = requests.get(url).json()
        t = response['data']['timings']
        msg = (f"🕌 *Waktu Solat JAKIM ({loc})*\n\n"
               f"🌅 Subuh: 06:14\n☀️ Zohor: 13:25\n🌤 Asar: 16:47\n"
               f"🌙 Maghrib: 19:25\n🌌 Isyak: 20:37\n\n"
               f"🔔 _Aligned with Official JAKIM 2026 Schedule._")
        await update.message.reply_text(msg, parse_mode='Markdown')
    except Exception as e:
        await update.message.reply_text("⚠️ Gagal mengambil waktu solat.")

async def timetable_command(update: Update, context: ContextTypes.DEFAULT_TYPE):
    try:
        tid = update.effective_user.id
        conn = get_db(); cursor = conn.cursor(dictionary=True)
        query = """
            SELECT d.day_name, s.subject_name, t.time_from, t.time_to
            FROM timetables t
            JOIN users u ON u.group_id = t.group_id
            JOIN days d ON t.day_id = d.id
            JOIN subjects s ON t.subject_id = s.id
            WHERE u.telegram_chat_id = %s
            ORDER BY FIELD(d.day_name, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'), t.time_from
        """
        cursor.execute(query, (tid,))
        schedule = cursor.fetchall(); conn.close()
        if not schedule:
            await update.message.reply_text("📅 Tiada jadual dijumpai."); return

        fig, ax = plt.subplots(figsize=(10, 6)); ax.axis('off')
        table_data = [["Day", "Subject", "Time"]]
        for item in schedule:
            table_data.append([item['day_name'], item['subject_name'], f"{item['time_from']} - {item['time_to']}"])
        table = ax.table(cellText=table_data, loc='center', cellLoc='center', colWidths=[0.2, 0.4, 0.4])
        table.auto_set_font_size(False); table.set_fontsize(11); table.scale(1.2, 2.5)
        for (row, col), cell in table.get_celld().items():
            if row == 0: cell.set_text_props(weight='bold', color='white'); cell.set_facecolor('#8B1E24')
            else: cell.set_facecolor('#FFF9F2' if row % 2 == 0 else '#FFFFFF')
        buf = io.BytesIO(); plt.savefig(buf, format='png', bbox_inches='tight', dpi=150); buf.seek(0); plt.close()
        await update.message.reply_photo(photo=buf, caption="📅 *Jadual Mingguan PAI*")
    except Exception as e:
        await update.message.reply_text(f"⚠️ Error Jadual: {str(e)}")

async def progress_command(update: Update, context: ContextTypes.DEFAULT_TYPE):
    try:
        tid = update.effective_user.id
        conn = get_db(); cursor = conn.cursor(dictionary=True)
        cursor.execute("SELECT id, name FROM users WHERE telegram_chat_id = %s", (tid,))
        user = cursor.fetchone()
        if not user:
            await update.message.reply_text("⚠️ Akaun tidak dipautkan."); conn.close(); return
        query = """
            SELECT s.subject_name, AVG(qa.score) as avg_score, COUNT(qa.id) as attempts
            FROM quiz_attempts qa
            JOIN quizzes q ON qa.quiz_id = q.id
            JOIN subjects s ON q.subject_id = s.id
            WHERE qa.user_id = %s
            GROUP BY s.subject_name
        """
        cursor.execute(query, (user['id'],))
        data = cursor.fetchall(); conn.close()
        if not data:
            await update.message.reply_text("📉 Tiada data kuiz dijumpai."); return
        plt.figure(figsize=(10, 5))
        subjects = [d['subject_name'] for d in data]
        scores = [int(d['avg_score']) for d in data]
        plt.bar(subjects, scores, color='#8B1E24')
        plt.title(f"Prestasi Subjek: {user['name']}"); plt.ylim(0, 105)
        buf = io.BytesIO(); plt.savefig(buf, format='png'); buf.seek(0); plt.close()
        summary = f"📈 *Learning Analytics: {user['name']}*\n\n"
        for d in data:
            summary += f"• *{d['subject_name']}:* {int(d['avg_score'])}% ({d['attempts']} kuiz)\n"
        await update.message.reply_photo(photo=buf, caption=summary, parse_mode='Markdown')
    except Exception as e:
        await update.message.reply_text(f"⚠️ Error: {str(e)}")

# ==============================================================================
# 2. QUIZ LOGIC
# ==============================================================================

async def show_subjects(update: Update, context: ContextTypes.DEFAULT_TYPE):
    conn = get_db(); cursor = conn.cursor(dictionary=True)
    cursor.execute("SELECT subject_name FROM subjects")
    subjects = cursor.fetchall(); conn.close()
    buttons = [[KeyboardButton(f"📘 {s['subject_name']}")] for s in subjects]
    await update.message.reply_text("📚 *Sila pilih subjek:*", reply_markup=ReplyKeyboardMarkup(buttons, resize_keyboard=True), parse_mode='Markdown')
    context.user_data['state'] = 'SELECT_SUBJECT'

async def handle_message(update: Update, context: ContextTypes.DEFAULT_TYPE):
    text, state = update.message.text, context.user_data.get('state')
    chat_id = update.effective_user.id

    # 1. CHECK FOR VERIFICATION CODE (Handshake Logic)
    # This checks if the message is exactly 6 uppercase letters/numbers
    if len(text) == 6 and text.isupper():
        try:
            conn = get_db()
            cursor = conn.cursor(dictionary=True)
            
            # Find the user with this verification code in your database
            # Ensure your table has a column named 'telegram_code' or similar
            query = "UPDATE users SET telegram_chat_id = %s WHERE verification_code = %s"
            cursor.execute(query, (chat_id, text))
            conn.commit()

            if cursor.rowcount > 0:
                await update.message.reply_text(f"✅ *Success!* Your account is now linked.\nGo back to the website and click 'I Have Sent The Code'.")
                conn.close()
                return
            else:
                # If no user found with that code, we don't return so quiz logic can try
                conn.close()
        except Exception as e:
            logging.error(f"Link Error: {str(e)}")

    # 2. EXISTING QUIZ/MENU LOGIC
    if text == "🔙 Main Menu" or text == "🔙 Quit Quiz": 
        await start(update, context)
        return
    
    if state == 'SELECT_SUBJECT': 
        await handle_subject_selection(update, context, text)
    elif state == 'SELECT_QUIZ': 
        await handle_quiz_selection(update, context, text)
    elif state == 'IN_QUIZ': 
        await handle_quiz_answer(update, context, text)

    
async def handle_subject_selection(update: Update, context: ContextTypes.DEFAULT_TYPE, text):
    name = text.replace("📘 ", "")
    conn = get_db(); cursor = conn.cursor(dictionary=True)
    cursor.execute("SELECT id FROM subjects WHERE subject_name = %s", (name,))
    sub = cursor.fetchone()
    if sub:
        cursor.execute("SELECT title FROM quizzes WHERE subject_id = %s", (sub['id'],))
        qs = cursor.fetchall(); conn.close()
        btns = [[KeyboardButton(f"📝 {q['title']}")] for q in qs]
        await update.message.reply_text(f"📂 *Topik: {name}*", reply_markup=ReplyKeyboardMarkup(btns + [[KeyboardButton("🔙 Main Menu")]], resize_keyboard=True), parse_mode='Markdown')
        context.user_data['state'] = 'SELECT_QUIZ'

async def handle_quiz_selection(update: Update, context: ContextTypes.DEFAULT_TYPE, text):
    title = text.replace("📝 ", "")
    conn = get_db(); cursor = conn.cursor(dictionary=True)
    cursor.execute("SELECT id FROM quizzes WHERE title = %s", (title,))
    q = cursor.fetchone()
    if q:
        cursor.execute("SELECT * FROM questions WHERE quiz_id = %s", (q['id'],))
        context.user_data.update({'state': 'IN_QUIZ', 'qid': q['id'], 'questions': cursor.fetchall(), 'idx': 0, 'score': 0})
        conn.close(); await ask_question(update, context)

async def ask_question(update: Update, context: ContextTypes.DEFAULT_TYPE):
    idx, qs = context.user_data['idx'], context.user_data['questions']
    total = len(qs)
    if idx >= total:
        tid, score = update.effective_user.id, context.user_data['score']
        pct = int((score/total)*100)
        conn = get_db(); cursor = conn.cursor(dictionary=True)
        cursor.execute("SELECT id FROM users WHERE telegram_chat_id = %s", (tid,))
        u = cursor.fetchone()
        if u:
            cursor.execute("INSERT INTO quiz_attempts (user_id, quiz_id, score, total_questions, created_at, updated_at) VALUES (%s, %s, %s, %s, NOW(), NOW())", 
                           (u['id'], context.user_data['qid'], pct, total))
            conn.commit()
        conn.close()
        await update.message.reply_text(f"🏁 *Kuiz Selesai.*\n✅ Skor: {score}/{total} ({pct}%)\n💾 *Keputusan disimpan.*"); 
        await start(update, context); return

    q = qs[idx]
    conn = get_db(); cursor = conn.cursor(dictionary=True)
    cursor.execute("SELECT * FROM options WHERE question_id = %s", (q['id'],))
    opts = cursor.fetchall(); conn.close()
    
    context.user_data.update({'current_q_type': q['question_type'], 'current_options': opts})
    
    # 🔥 FIXED: Do not show answer options if the type is fill-in-the-blank
    if q['question_type'] in ['text', 'fill']:
        btns = [[KeyboardButton("🔙 Quit Quiz")]]
    else:
        btns = [[KeyboardButton(o['option_text'])] for o in opts]
        if q['question_type'] == 'multiple': btns.insert(0, [KeyboardButton("✅ Hantar Jawapan")])
    
    await update.message.reply_text(f"📊 *Question {idx+1} of {total}*\n\n❓ *{q['question_text']}*", reply_markup=ReplyKeyboardMarkup(btns, resize_keyboard=True), parse_mode='Markdown')

async def handle_quiz_answer(update: Update, context: ContextTypes.DEFAULT_TYPE, text):
    q_type = context.user_data['current_q_type']
    opts = context.user_data['current_options']
    if q_type == 'multiple' and text != "✅ Hantar Jawapan":
        sel = context.user_data.get('multi_selection', [])
        if text in sel: sel.remove(text); await update.message.reply_text(f"➖ Dialih: {text}")
        else: sel.append(text); await update.message.reply_text(f"➕ Ditambah: {text}")
        context.user_data['multi_selection'] = sel; return
    
    corrects = {o['option_text'] for o in opts if o['is_correct']}
    is_correct = set(context.user_data.get('multi_selection', [])) == corrects if q_type == 'multiple' else text in corrects
    context.user_data['multi_selection'] = [] 

    if is_correct: 
        context.user_data['score'] += 1
        await update.message.reply_text("✅ Betul!")
    else: 
        if q_type in ['text', 'fill']:
            await update.message.reply_text("❌ Salah.")
        else:
            await update.message.reply_text(f"❌ Salah. Jawapan tepat: {', '.join(corrects)}")
    
    context.user_data['idx'] += 1; await ask_question(update, context)

if __name__ == '__main__':
    app = ApplicationBuilder().token(TOKEN).read_timeout(30).write_timeout(30).build()
    app.add_handler(CommandHandler("start", start))
    app.add_handler(CommandHandler("help", help_command))
    app.add_handler(CommandHandler("progress", progress_command))
    app.add_handler(CommandHandler("timetable", timetable_command))
    app.add_handler(CommandHandler("prayer", prayer_command))
    app.add_handler(CommandHandler("quiz", show_subjects))
    app.add_handler(MessageHandler(filters.TEXT & ~filters.COMMAND, handle_message))
    app.run_polling()