import logging
import mysql.connector
import requests
import matplotlib.pyplot as plt
import io
import os
from datetime import datetime
from dotenv import load_dotenv
from telegram import Update, ReplyKeyboardMarkup, KeyboardButton
from telegram.ext import ApplicationBuilder, ContextTypes, CommandHandler, MessageHandler, filters

# Load secret environment variables from local .env file
load_dotenv()

# 🔥 SYSTEM ENVIRONMENT CONFIGURATION
TOKEN = os.getenv("BOT_TOKEN")
db_config = {
    'user': 'adminuser', 
    'password': 'Password123!', 
    'host': '127.0.0.1', 
    'database': 'islamicwebsite', 
    'raise_on_warnings': True
}

if not TOKEN:
    raise ValueError("BOT_TOKEN is missing! Please check your .env file.")

logging.basicConfig(format='%(asctime)s - %(levelname)s - %(message)s', level=logging.INFO)

def get_db():
    return mysql.connector.connect(**db_config)

# ==============================================================================
# 1. APPLICATION COMMAND HANDLERS
# ==============================================================================

async def start(update: Update, context: ContextTypes.DEFAULT_TYPE):
    msg = (
        "✨ *Assalamu'alaikum W.R.T!* ✨\n\n"
        "Welcome to the *MRSM Terendak PAI Platform Portal*.\n"
        "Your synchronized companion for quiz analytics and notifications.\n\n"
        "🌐 *Web Portal:* [Click to Visit Dashboard](https://islamic-lms.online)\n\n"
        "Select an action from the keyboard below or use /help to begin."
    )
    context.user_data.clear() 
    
    dashboard_buttons = [
        [KeyboardButton("📘 Start Quiz Revision")],
        [KeyboardButton("📊 View My Progress Analytics")],
        [KeyboardButton("🕌 Check Prayer Times"), KeyboardButton("📅 Weekly Timetable")]
    ]
    
    await update.message.reply_text(
        msg, 
        parse_mode='Markdown', 
        reply_markup=ReplyKeyboardMarkup(dashboard_buttons, resize_keyboard=True)
    )

async def help_command(update: Update, context: ContextTypes.DEFAULT_TYPE):
    msg = (
        "📖 *MRSM PAI System Command Guide:*\n\n"
        "🔹 `/start` — Reset interface layout grid and view home dashboard\n"
        "🔹 `/quiz` — Fetch dynamic subjects available from system data rows\n"
        "🔹 `/progress` — Render real-time learning metrics bar chart\n"
        "🔹 `/timetable` — Export physical graphic of your weekly classes\n"
        "🔹 `/prayer` — Fetch production API real-time prayer schedule with offline fallback\n"
        "🔹 `/disconnect` — Sign out / Unlink your profile parameters from the platform\n"
        "🔹 `/help` — Display this documentation module"
    )
    await update.message.reply_text(msg, parse_mode='Markdown')

async def disconnect_command(update: Update, context: ContextTypes.DEFAULT_TYPE):
    chat_id = update.effective_user.id
    try:
        with get_db() as conn:
            with conn.cursor() as cursor:
                query = "UPDATE users SET telegram_chat_id = NULL WHERE telegram_chat_id = %s"
                cursor.execute(query, (chat_id,))
                conn.commit()
                
                if cursor.rowcount > 0:
                    msg = "✔ *Account disconnected successfully!*\nYour Telegram profile is no longer linked to the platform. You can now link a fresh account."
                else:
                    msg = "❌ *No active link found.*\nYour Telegram profile wasn't connected to any student accounts."
                    
        await update.message.reply_text(msg, parse_mode='Markdown')
    except Exception as e:
        await update.message.reply_text(f"⚠️ Error during disconnection process: {str(e)}")

async def prayer_command(update: Update, context: ContextTypes.DEFAULT_TYPE):
    try:
        url = "https://api.aladhan.com/v1/timingsByCity"
        query_params = {
            'city': 'Melaka',
            'country': 'Malaysia',
            'method': '11'
        }
        headers = {'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)'}
        
        # Keep timeout short so fallback triggers instantly if network fails
        response = requests.get(url, params=query_params, headers=headers, timeout=4).json()
        
        t = response['data']['timings']
        status_tag = "✅ _Live Cloud API Sync Mode_"
    except Exception as e:
        logging.warning(f"Prayer API unreachable, activating local static schedule array fallback: {str(e)}")
        # 🟢 BULLETPROOF FALLBACK ARRAY (JAKIM Aligned 2026 Reference Array)
        t = {
            'Imsak': '06:04',
            'Fajr': '06:14',
            'Sunrise': '07:08',
            'Dhuhr': '13:25',
            'Asr': '16:47',
            'Maghrib': '19:25',
            'Isha': '20:37'
        }
        status_tag = "⚠️ _System Safe Offline Mode (Pre-Cached)_"

    msg = (
        f"🕌 *Waktu Solat Rasmi Melaka ({datetime.now().strftime('%d %B %Y')})*\n"
        f"{status_tag}\n\n"
        f"🌅 *Imsak:* {t['Imsak']} | *Subuh:* {t['Fajr']}\n"
        f"☀️ *Syuruk:* {t['Sunrise']}\n"
        f"🕛 *Zohor:* {t['Dhuhr']}\n"
        f"🌤 *Asar:* {t['Asr']}\n"
        f"🌆 *Maghrib:* {t['Maghrib']}\n"
        f"🌌 *Isyak:* {t['Isha']}\n\n"
        f"🔔 _\"Sesungguhnya solat itu adalah kewajipan yang ditentukan waktunya bagi orang yang beriman.\"_"
    )
    await update.message.reply_text(msg, parse_mode='Markdown')

async def timetable_command(update: Update, context: ContextTypes.DEFAULT_TYPE):
    try:
        tid = update.effective_user.id
        with get_db() as conn:
            with conn.cursor(dictionary=True) as cursor:
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
                schedule = cursor.fetchall()

        if not schedule:
            await update.message.reply_text("📅 Tiada jadual kelas dijumpai untuk akaun anda."); return

        fig, ax = plt.subplots(figsize=(9, 5))
        ax.axis('off')
        
        table_data = [["Hari", "Mata Pelajaran (Subject)", "Masa / Tempoh Kelas"]]
        for item in schedule:
            table_data.append([item['day_name'], item['subject_name'], f"{item['time_from']} - {item['time_to']}"])
            
        table = ax.table(cellText=table_data, loc='center', cellLoc='center', colWidths=[0.18, 0.42, 0.40])
        table.auto_set_font_size(False)
        table.set_fontsize(10)
        table.scale(1.2, 2.2)
        
        for (row, col), cell in table.get_celld().items():
            if row == 0:
                cell.set_text_props(weight='bold', color='white')
                cell.set_facecolor('#008f78') 
            else:
                cell.set_facecolor('#F8FAFC' if row % 2 == 0 else '#FFFFFF')
                cell.set_text_props(color='#1E293B')
                
        buf = io.BytesIO()
        plt.savefig(buf, format='png', bbox_inches='tight', dpi=180)
        buf.seek(0)
        plt.close()
        
        await update.message.reply_photo(photo=buf, caption="📅 *Jadual Mingguan PAI Kelas Anda*")
    except Exception as e:
        await update.message.reply_text(f"⚠️ Error Jadual Engine: {str(e)}")

async def progress_command(update: Update, context: ContextTypes.DEFAULT_TYPE):
    try:
        tid = update.effective_user.id
        with get_db() as conn:
            with conn.cursor(dictionary=True) as cursor:
                cursor.execute("SELECT id, name FROM users WHERE telegram_chat_id = %s", (tid,))
                user = cursor.fetchone()
                if not user:
                    await update.message.reply_text("⚠️ Akaun tidak dipautkan. Sila semak profil portal anda."); return
                
                query = """
                    SELECT s.subject_name, AVG(qa.score) as avg_score, COUNT(qa.id) as attempts
                    FROM quiz_attempts qa
                    JOIN quizzes q ON qa.quiz_id = q.id
                    JOIN subjects s ON q.subject_id = s.id
                    WHERE qa.user_id = %s
                    GROUP BY s.subject_name
                """
                cursor.execute(query, (user['id'],))
                data = cursor.fetchall()

        if not data:
            await update.message.reply_text("📉 Tiada rekod kuiz dijumpai dalam pangkalan data."); return
            
        subjects = [d['subject_name'] for d in data]
        scores = [int(d['avg_score']) for d in data]
        
        plt.figure(figsize=(9, 4.5))
        bars = plt.bar(subjects, scores, color='#008f78', width=0.45, edgecolor='#004D40', linewidth=1.2)
        plt.title(f"Analisis Prestasi: {user['name']}", fontsize=12, fontweight='bold', pad=15, color='#1E293B')
        plt.ylim(0, 110)
        plt.ylabel("Purata Skor (%)", fontsize=10, fontweight='bold')
        plt.grid(axis='y', linestyle='--', alpha=0.5)
        
        for bar in bars:
            height = bar.get_height()
            plt.text(bar.get_x() + bar.get_width()/2., height + 2, f'{int(height)}%', ha='center', va='bottom', fontweight='bold', color='#334155')
            
        buf = io.BytesIO()
        plt.savefig(buf, format='png', bbox_inches='tight', dpi=150)
        buf.seek(0)
        plt.close()
        
        summary = f"📊 *Learning Analytics Dashboard*\n👤 *Pelajar:* {user['name']}\n\n"
        for d in data:
            summary += f"• *{d['subject_name']}:* `{int(d['avg_score'])}%` _({d['attempts']} kali cubaan)_\n"
            
        await update.message.reply_photo(photo=buf, caption=summary, parse_mode='Markdown')
    except Exception as e:
        await update.message.reply_text(f"⚠️ Error Analytics Rendering Model: {str(e)}")

# ==============================================================================
# 2. RUN-TIME REVISION & QUIZ INPUT MATCHERS
# ==============================================================================

async def show_subjects(update: Update, context: ContextTypes.DEFAULT_TYPE):
    with get_db() as conn:
        with conn.cursor(dictionary=True) as cursor:
            cursor.execute("SELECT subject_name FROM subjects")
            subjects = cursor.fetchall()
            
    buttons = [[KeyboardButton(f"📘 {s['subject_name']}")] for s in subjects]
    buttons.append([KeyboardButton("🔙 Main Menu")])
    
    await update.message.reply_text(
        "📚 *Sila Pilih Subjek Pembelajaran:*", 
        reply_markup=ReplyKeyboardMarkup(buttons, resize_keyboard=True), 
        parse_mode='Markdown'
    )
    context.user_data['state'] = 'SELECT_SUBJECT'

async def handle_message(update: Update, context: ContextTypes.DEFAULT_TYPE):
    text = update.message.text.strip()
    state = context.user_data.get('state')
    chat_id = update.effective_user.id

    if text in ["🔙 Main Menu", "🔙 Quit Quiz"]: 
        await start(update, context)
        return
        
    if text == "📘 Start Quiz Revision":
        await show_subjects(update, context)
        return
        
    if text == "📊 View My Progress Analytics":
        await progress_command(update, context)
        return
        
    if text == "🕌 Check Prayer Times":
        await prayer_command(update, context)
        return
        
    if text == "📅 Weekly Timetable":
        await timetable_command(update, context)
        return

    # FLEXIBLE STUDENT TRACKING-CODE HANDSHAKE ROUTER
    if (6 <= len(text) <= 12):
        try:
            # Extract numbers or clean text for matching parameters
            user_code = text.strip()
            # If your database column stores ONLY numbers (e.g. '1234'), we strip letters:
            # user_code = ''.join(filter(str.isdigit, text)) 
            
            short_match = user_code[-6:] if len(user_code) >= 6 else user_code
            
            with get_db() as conn:
                with conn.cursor(dictionary=True) as cursor:
                    query = """
                        SELECT id FROM users 
                        WHERE telegram_chat_id IS NULL 
                        AND no_maktab IS NOT NULL
                        AND (
                            no_maktab LIKE %s 
                            OR %s LIKE CONCAT('%', no_maktab)
                        )
                    """
                    search_pattern = f"%{short_match}"
                    cursor.execute(query, (search_pattern, user_code))
                    student = cursor.fetchone()

                    if student:
                        update_query = "UPDATE users SET telegram_chat_id = %s WHERE id = %s"
                        cursor.execute(update_query, (chat_id, student['id']))
                        conn.commit()
                        
                        await update.message.reply_text(
                            f"✅ *Success!* Your account tracking profile parameters are linked.\n\n"
                            f"Go back to the layout website page and click the *'I Have Sent The Code'* confirmation button.",
                            parse_mode='Markdown'
                        )
                        return
                    else:
                        # Optional: debug message if code matches format but no student record exists
                        logging.warning(f"Code {text} received but no unmatched student found in database.")
                        
        except Exception as e:
            logging.error(f"Handshake Link System Failure: {str(e)}")

    if state == 'SELECT_SUBJECT': 
        await handle_subject_selection(update, context, text)
    elif state == 'SELECT_QUIZ': 
        await handle_quiz_selection(update, context, text)
    elif state == 'IN_QUIZ': 
        await handle_quiz_answer(update, context, text)
    else:
        await update.message.reply_text("❓ Sila gunakan arahan menu atau taip /start untuk memulakan.")

async def handle_subject_selection(update: Update, context: ContextTypes.DEFAULT_TYPE, text):
    name = text.replace("📘 ", "")
    with get_db() as conn:
        with conn.cursor(dictionary=True) as cursor:
            cursor.execute("SELECT id FROM subjects WHERE subject_name = %s", (name,))
            sub = cursor.fetchone()
            if sub:
                cursor.execute("SELECT title FROM quizzes WHERE subject_id = %s AND topic != 'PVP_ARENA_BATTLE'", (sub['id'],))
                qs = cursor.fetchall()
                
                btns = [[KeyboardButton(f"📝 {q['title']}")] for q in qs]
                await update.message.reply_text(
                    f"📂 *Topik Kuiz Dibawah: {name}*", 
                    reply_markup=ReplyKeyboardMarkup(btns + [[KeyboardButton("🔙 Main Menu")]], resize_keyboard=True), 
                    parse_mode='Markdown'
                )
                context.user_data['state'] = 'SELECT_QUIZ'

async def handle_quiz_selection(update: Update, context: ContextTypes.DEFAULT_TYPE, text):
    title = text.replace("📝 ", "")
    with get_db() as conn:
        with conn.cursor(dictionary=True) as cursor:
            cursor.execute("SELECT id FROM quizzes WHERE title = %s", (title,))
            q = cursor.fetchone()
            if q:
                cursor.execute("SELECT * FROM questions WHERE quiz_id = %s", (q['id'],))
                context.user_data.update({'state': 'IN_QUIZ', 'qid': q['id'], 'questions': cursor.fetchall(), 'idx': 0, 'score': 0})
                await ask_question(update, context)

async def ask_question(update: Update, context: ContextTypes.DEFAULT_TYPE):
    idx, qs = context.user_data['idx'], context.user_data['questions']
    total = len(qs)
    
    if idx >= total:
        tid, score = update.effective_user.id, context.user_data['score']
        pct = int((score/total)*100)
        
        with get_db() as conn:
            with conn.cursor(dictionary=True) as cursor:
                cursor.execute("SELECT id FROM users WHERE telegram_chat_id = %s", (tid,))
                u = cursor.fetchone()
                if u:
                    cursor.execute(
                        "INSERT INTO quiz_attempts (user_id, quiz_id, score, total_questions, created_at, updated_at) VALUES (%s, %s, %s, %s, NOW(), NOW())", 
                        (u['id'], context.user_data['qid'], pct, total)
                    )
                    conn.commit()
                    
        await update.message.reply_text(
            f"🏁 *Kuiz Selesai!*\n\n"
            f"✅ *Skor Keputusan:* `{score} / {total}` ({pct}%)\n"
            f"💾 _Keputusan telah disimpan automatik ke dalam database portal web._"
        ) 
        await start(update, context); return

    q = qs[idx]
    with get_db() as conn:
        with conn.cursor(dictionary=True) as cursor:
            cursor.execute("SELECT * FROM options WHERE question_id = %s", (q['id'],))
            opts = cursor.fetchall()
    
    context.user_data.update({'current_q': q, 'current_q_type': q['question_type'], 'current_options': opts})
    
    if q['question_type'] in ['text', 'fill', 'fill_in_the_blank']:
        btns = [[KeyboardButton("🔙 Quit Quiz")]]
    else:
        btns = [[KeyboardButton(o['option_text'])] for o in opts]
        if q['question_type'] == 'multiple': 
            btns.insert(0, [KeyboardButton("✅ Hantar Jawapan")])
    
    await update.message.reply_text(
        f"📊 *Soalan {idx+1} daripada {total}*\n"
        f"⏳ _Jenis Soalan: {q['question_type'].upper()}_\n\n"
        f"❓ *{q['question_text']}*", 
        reply_markup=ReplyKeyboardMarkup(btns, resize_keyboard=True), 
        parse_mode='Markdown'
    )

async def handle_quiz_answer(update: Update, context: ContextTypes.DEFAULT_TYPE, text):
    q = context.user_data['current_q']
    opts = context.user_data['current_options']
    
    if q['question_type'] == 'multiple' and text != "✅ Hantar Jawapan":
        sel = context.user_data.get('multi_selection', [])
        if text in sel: 
            sel.remove(text)
            await update.message.reply_text(f"➖ Dialih: {text}")
        else: 
            sel.append(text)
            await update.message.reply_text(f"➕ Ditambah: {text}")
        context.user_data['multi_selection'] = sel; return
    
    if q['question_type'] in ['text', 'fill', 'fill_in_the_blank']:
        actual_correct = q['correct_answer_text'].strip().lower()
        is_correct = text.strip().lower() == actual_correct
    else:
        corrects = {o['option_text'] for o in opts if o['is_correct']}
        is_correct = set(context.user_data.get('multi_selection', [])) == corrects if q['question_type'] == 'multiple' else text in corrects
    
    context.user_data['multi_selection'] = [] 

    if is_correct: 
        context.user_data['score'] += 1
        await update.message.reply_text("🎉 Betul! Tahniah.")
    else: 
        if q['question_type'] in ['text', 'fill', 'fill_in_the_blank']:
            await update.message.reply_text(f"❌ Salah.\nJawapan tepat: *{q['correct_answer_text']}*", parse_mode='Markdown')
        else:
            correct_answers_str = ', '.join(corrects)
            await update.message.reply_text(f"❌ Salah.\nJawapan tepat: *{correct_answers_str}*", parse_mode='Markdown')
    
    context.user_data['idx'] += 1
    await ask_question(update, context)

if __name__ == '__main__':
    app = ApplicationBuilder().token(TOKEN).read_timeout(30).write_timeout(30).build()
    
    # Command Dispatch Registration Matrix
    app.add_handler(CommandHandler("start", start))
    app.add_handler(CommandHandler("help", help_command))
    app.add_handler(CommandHandler("disconnect", disconnect_command))
    app.add_handler(CommandHandler("progress", progress_command))
    app.add_handler(CommandHandler("timetable", timetable_command))
    app.add_handler(CommandHandler("prayer", prayer_command))
    app.add_handler(CommandHandler("quiz", show_subjects))
    
    app.add_handler(MessageHandler(filters.TEXT & ~filters.COMMAND, handle_message))
    
    logging.info("Production PAI Engine initialized successfully. Processing dynamic background pooling...")
    app.run_polling()