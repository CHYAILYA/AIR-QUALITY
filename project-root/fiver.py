import asyncio
import websockets
import requests
import json
import ssl
import certifi
import re
import os # For environment variables
import time # For current timestamp
from datetime import datetime, timedelta, time as dt_time # For precise timestamps and time differences
import logging # Import logging module for better logging
import mysql.connector # Added for database interaction
from bs4 import BeautifulSoup # Import BeautifulSoup here for consistency

# Configure basic logging
logging.basicConfig(level=logging.INFO, format='%(asctime)s - %(levelname)s - %(message)s')

# --- IMPORTANT NOTES FOR USERS ---
# 1. Ensure you REGULARLY update YOUR_FIVERR_COOKIE_STRING and YOUR_X_CSRF_TOKEN_VALUE for ALL accounts.
#    These values will expire! You must obtain them from your active Fiverr browser session.
# 2. NEVER publish your GEMINI_API_KEY. Consider storing it as an
#    environment variable.
# 3. Adjust CHECK_INBOX_INTERVAL_SECONDS and ACTIVITY_PING_INTERVAL_SECONDS as needed.
#    COMPLETELY REMOVING THESE DELAYS IS HIGHLY DISCOURAGED AND MAY LEAD TO ACCOUNT BLOCKING.

# --- MULTI-USER CONFIGURATION ---
# Define configurations for each Fiverr account you want to manage.
# IMPORTANT: Replace the placeholder values with your actual, live Fiverr data.
ACCOUNT_CONFIGS = [
       {
        "USERNAME": "chyailya",
        "USER_ID": 195867416,
        "YOUR_FIVERR_COOKIE_STRING": "u_guid=1740287816000-103ef8347d1f6b8162af1793537698af6a810618; _pxvid=65d6b8b6-f1a5-11ef-ae69-b98b65577a4b; _fbp=fb.1.1740287819430.800190960325300680; __pxvid=6877c814-f1a5-11ef-bef4-0242ac120003; __pdst=c1138763411f4f0da632df66b4015f59; logged_in_currency_v2=USD; _tt_enable_cookie=1; _ttp=qVdJkU6KYbPreaRWg5PUjFU6lsH.tt.1; hubspotutk=33db8c403522543e24171411fb57dccc; _vwo_uuid_v2=DD7F6F3E0C61037A21CD0B94D8407C423|32cfed34aa3ed0e121bb529f4bb733fe; _ga_TS343SBWS8=GS1.1.1743023840.1.1.1743023878.21.0.0; _gcl_gs=2.1.k1$i1743798160$u141243429; _gcl_aw=GCL.1743798165.Cj0KCQjwhr6_BhD4ARIsAH1YdjDcWESyJ49ZmHTQOEBnOYLgZlwPeGLzcISI_8av1PvZqIB1pevLInYaAk1yEALw_wcB; _fiverr_session_key=73e2f790b71639dbbc04b3584ca765f8; session_locale=en; new_guid=1748787776690-fe17feb6-8aaf-46bf-ae14-ccc59d846783; page_views=4; _rdt_uuid=1740287827239.0c54281f-53de-4231-a520-71a4097baded; tatari-cookie-test=59898800; tatari-user-cookie=5a93a1812b3c499b765f12e33d2423685f50ba4a1e5740a9b0556113f9f93c67; tatari-session-cookie=da09f0db-16a8-6389-a538-633a3375bb96; _uetvid=6a76d6b0f1a511ef9a40a5846276373f; ttcsid=1749291004444::Z4qHySJekzjr4ONN4NX7.3.1749291004457; _tq_id.TV-18900945-1.8951=38f072a48b552564.1740287828.0.1749291005..; ttcsid_C2IG4QAQV140ORDJ7BR0=1749291004441::94Dvx24wjhKpZs0agzFY.3.1749291004781; ttcsid_C6IPOEIO6DGUDE0A23MG=1749291004447::Fo3pvnXQdRbVuCT1zK9T.3.1749291004782; ttcsid_C3VBNIPU9OSLU1GC4A70=1749291004454::vT0xoQ9OIgyJKuutShK2.3.1749291004783; ttcsid_CAFNM6RC77U9UO5AT1FG=1749291004456::MrID6NB_3o-qBW0kmWLK.3.1749291004784; _clck=3qu47o%7C2%7Cfwk%7C0%7C1880; hodor_creds=eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJmaXZlcnIvaG9kb3JfcGVnYXN1cyIsInVpZCI6MTk1ODY3NDE2LCJzaWQiOiIwZTMzYjcxMjM5OTEzNmY3OWYzMjFiMDg1NDI5MGE3NSIsImlhdCI6MTc0OTI5MTAwNSwiZXhwIjoxNzgwODQ4NjA1fQ.1HqIvGFZ0pJVkcmMp9-_A4A6DjujitZmUnZCDg9g8Ms; cpra_opt_out_status_external=true; cpra_opt_out_permanent_user=true; __hstc=156287140.33db8c403522543e24171411fb57dccc.1740287832729.1749402000629.1749710196393.83; _ga_XHGGCMRHEC=GS2.1.s1749710194$o16$g0$t1749710198$j56$l0$h0; pxcts=bd9b887f-4a85-11f0-8928-9f720de96b9b; hp_view=seller; QSI_SI_9zDAfsiXglaJDp3_intercept=true; forterToken=d9c2d61f37f24a179a016a03111908ac_1750589695097__UDF43-m4_17ck; _ga_GK1NB7HX40=deleted; _ga_K3ZPNQPZ79=GS2.1.s1750675588$o1$g1$t1750676299$j8$l0$h0; _ga=GA1.1.false; _ga_XBEB8JPBY8=GS2.2.s1750676331$o89$g0$t1750676331$j60$l0$h0; OptanonConsent=isGpcEnabled=0&datestamp=Mon+Jun+23+2025+17%3A58%3A52+GMT%2B0700+(Indochina+Time)&version=202211.1.0&isIABGlobal=false&hosts=&consentId=d8637baa-cca1-440b-8671-01fa49fab889&interactionCount=0&landingPath=NotLandingPage&groups=C0001%3A1%2CC0002%3A1%2CC0004%3A1&AwaitingReconsent=false; _ga_311202432=GS2.1.s1750676331$o100$g1$t1750676394$j60$l0$h0; _pxhd=ivWwcssq/j6iEd/-lELF2VJHEKwgbpSpn1Dsk41W9uPAMJdxgx/rYcztxNiJyRiDp9UrF5HpOFOPey0bhceW0g==:MUujBK7S9HCW1RB3pzBxsgftIiQ9reoTKeL9YMUxBgmLyfBwOMRYUnBApK7spsQVsYmhZgyCpMrfwcDnEXXEqGnfGP9LkEE5C5RW/QQcA8E=; last_content_pages_=gigs%7C%7C%7Cshow%7C%7C%7C422209994%3Bgigs%7C%7C%7Cshow%7C%7C%7C422205889; _gcl_au=1.1.1782661838.1748067352.1793423165.1751210941.1751210940; redirect_url=%2Fusers%2Fchyailya%2Fseller_analytics_dashboard%3Fsource%3Dheader_nav%26tab%3Doverview; visited_fiverr=true; go_back_to_fiverr_url=%2Finbox%2F; ftr_blst_1h=1751244846418; QSI_HistorySession=https%3A%2F%2Fwww.fiverr.com%2Finbox%2F~1751244848822; _pxcrs=1%3A1751247849263; access_token=eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1aWQiOjE5NTg2NzQxNiwic2lkIjoiMGUzM2I3MTIzOTkxMzZmNzlmMzIxYjA4NTQyOTBhNzUiLCJwcnYiOm51bGwsImV4cCI6MTc1MTI0Nzk4MH0.w2EFBn36Nj4eFtQn8C2cCfB4a1vKNvSdFrNenjrZrdw; _ga_GK1NB7HX40=GS2.1.s1751214344$o322$g1$t1751247955$j52$l0$h0; _px3=84806efaabfbf951f078b3f65aebff5d37fd3f00fd910adb5687f632d728e231:+kDijn+s9EYETbwordNOD2pdDWBYveRGebKAtD3rVKmhfmVeSwNrITopxRvy9aaycwrXMzNWFu0AforiqWtO1A==:1000:zfWaIgKSVQBSEbrGO4QdkYShlDyMkZOt39UPS81F508ffsWuAvmol4fSCCiouA34/OvOCWTLvgdJRM5Y48/OxecbITnWnTwEFV7ZJ1XF7AmJzThbNJ1Aqc93fqEuD6MIaTmevcggWnINetJT/0ZE6vGXddY8zkWXqZKxLLIf2qQeqGBP7pm67oL1Pd7csrnnngEEFlFDAhmDcXk5ErtBbQ1meWLvA4UVuGjHYDdiBbM=; _pxde=9260485610d64baeb4de412cef543f2706997284388497e165cea2d5bc200422:eyJ0aW1lc3RhbXAiOjE3NTEyNDc5NjAyNTMsImZfa2IiOjAsImlwY19pZCI6W119; _cfuvid=t7DaQAoBbhiNys6VlM7gMXr91yVwyW4XdZ4GwGD0L04-1751247961213-0.0.1.1-604800000; forterToken=d9c2d61f37f24a179a016a03111908ac_1751247959395_134752_UDF43_17ck",
        "YOUR_X_CSRF_TOKEN_VALUE": "1752457557.sERoTg4x5WGWG7PFp17TvjMRBOe70JhYe5/3rdtPmJs=",
        "WHATSAPP_CHAT_ID": "120363419669386785@g.us", # Your primary WhatsApp group/chat ID
        "WHATSAPP_ADMIN_CHAT_ID": "6285966302755@c.us", # New: WhatsApp Admin Chat ID for custom offer notifications
        "WHATSAPP_API_URL": "http://103.85.60.82:3001/api/sendText?apikey=m0h4mm4d",
        "WHATSAPP_AUTH_USER": "ridwan",
        "WHATSAPP_AUTH_PASS": "m0h4mm4d"
  },
    {
        "USERNAME": "ahmadfikri820",
        "USER_ID": 126643296,
        "YOUR_FIVERR_COOKIE_STRING": "u_guid=1749101553000-23ae52f3a9ee91ce9e38f3d962c5ccdb297763ae; cpra_opt_out_status_external=false; _pxvid=7b6af7f7-41ce-11f0-8dd4-b95205ee21be; __pxvid=7c775803-41ce-11f0-9370-f6aa54fbac45; _fbp=fb.1.1749101556502.310666059334163989; __pdst=d8f7230dc92142c2a7de27722dc5814e; _tt_enable_cookie=1; _ttp=01JWZ97TAGX6YBTZT8G3BBT3G0_.tt.1; hubspotutk=afb3e6ed0df10bd2bbb9d1913d18bd43; session_locale=en-US; logged_in_currency_v2=USD; _fiverr_session_key=94c7c52dec07e581581b5fdfb72a910f; new_guid=1749345009322-4f1eba57-3621-4c25-9369-fa1c23fb0891; page_views=25; hodor_creds=eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJmaXZlcnIvaG9kb3JfcGVnYXN1cyIsInVpZCI6MTI2NjQzMjk2LCJzaWQiOiIzNzk2YTAzZmViNTcwMzExNzU4Nzg4ZTQwOGI5NGQ2NSIsImlhdCI6MTc0OTM0NTA0MSwiZXhwIjoxNzgwOTAyNjQxfQ.14grIYozg_ntEdb1eXwrXcY-H2qrzac7_XxL6hf9QMo; cpra_opt_out_permanent_user=false; _ga_XHGGCMRHEC=GS2.1.s1749355142$o3$g0$t1749355142$j60$l0$h0; _ga_XBEB8JPBY8=GS2.2.s1749487347$o1$g0$t1749487356$j60$l0$h0; _ga=GA1.1.103881883.1749101570; OptanonConsent=isGpcEnabled=0&datestamp=Mon+Jun+09+2025+23%3A42%3A51+GMT%2B0700+(Indochina+Time)&version=202211.1.0&isIABGlobal=false&hosts=&consentId=a64aa9a4-6cc3-42b0-a9c6-5aa1667fdf30&interactionCount=1&landingPath=https%3A%2F%2Fhelp.fiverr.com%2Fhc%2Fen-us%2Farticles%2F21099496893585-Fiverr-agencies&groups=C0001%3A1%2CC0002%3A1%2CC0004%3A1; _ga_311202432=GS2.1.s1749487347$o1$g1$t1749487381$j26$l0$h0; pxcts=e70b74d8-4b3d-11f0-ae57-d3446f3fa297; __hssrc=1; QSI_SI_9zDAfsiXglaJDp3_intercept=true; _uetvid=855b88c041ce11f0a6636386cf3eede3; _cfuvid=nu2DVWuLnwYmjuJ5KlqAPgQrHWInsAtv8h3iZBIExmA-1750704813347-0.0.1.1-604800000; hp_view=seller; last_content_pages_=gigs%7C%7C%7Cshow%7C%7C%7C422524717%3Bgigs%7C%7C%7Cshow%7C%7C%7C422209994; _pxhd=4lY5YzYiF6B64jwNpV2U3/i4bCU1N//qDidikz5rya24q1OnSzfD068DG6lPaJhBoVDHXRRbbUxCfCCmVyhzeA==:fN3gYLVx3OnMapK2UePRhW2E-oogGIFTn2zW2hCBXPqjaHrN/ZQuwqREaldCOeM-CxAFKeRbTFrgROrUG3oG9TkmzY9aIOIHw/OnUV5Wjy0=; redirect_url=%2Fusers%2Fahmadfikri820%2Fmanage_gigs; tatari-user-cookie=71f9b5f0692c47d24723a054aacf15239ce57f55e577c3e2e9d2c3ce4a837f0a; tatari-session-cookie=21e8a66c-4de2-db74-6750-eb8b1f41c976; go_back_to_fiverr_url=%2Finbox%2F; QSI_HistorySession=https%3A%2F%2Fwww.fiverr.com%2Finbox~1751201041458%7Chttps%3A%2F%2Fwww.fiverr.com%2Fusers%2Fahmadfikri820%2Fmanage_gigs~1751214339765%7Chttps%3A%2F%2Fwww.fiverr.com%2Finbox~1751216847227%7Chttps%3A%2F%2Fwww.fiverr.com%2Finbox%2Fandrew_neal680_~1751221118697%7Chttps%3A%2F%2Fwww.fiverr.com%2Fusers%2Fahmadfikri820%2Fmanage_gigs~1751221297538%7Chttps%3A%2F%2Fwww.fiverr.com%2Finbox%2F~1751235973339; _clck=1xverqg%7C2%7Cfx7%7C0%7C1982; ftr_blst_1h=1751244885177; __hstc=156287140.afb3e6ed0df10bd2bbb9d1913d18bd43.1749101570728.1751235976029.1751244902595.153; _gcl_au=1.1.1650952914.1749101554.662480560.1751247414.1751247413; visited_fiverr=true; t-ip=1; access_token=eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1aWQiOjEyNjY0MzI5Niwic2lkIjoiMzc5NmEwM2ZlYjU3MDMxMTc1ODc4OGU0MDhiOTRkNjUiLCJwcnYiOm51bGwsImV4cCI6MTc1MTI0ODE4MH0.XqxH_NYaA_QydiKLfV49cRY2-A8B9iO-FHdUS0Z18kI; _px3=9d9e42f79de7c4c81195fa5f8c0bcf05120a5ba9c409a0d4dae1d6d38b3641bd:Kg7/Paw7gEkTjGyIvo9pKfiEG6YTmjTFl+qHYzoW8/DaAb30higK8ghNSsgRR9ckkaBeFTvBkFDafXvVNO8JLQ==:1000:HuMSVbFa18f4IQ+GQOKEsDuQLS7zTZLQ1nPPB09mZTLV8YKFl2S0cJ/1/WNSMXmsmin3X3G7cV5Gz4Wqe5M9inSTQAfYJh5iJia7UNklHP9wjfNrqMu1lvTtIbT8IizmABxLmERJcQ23zGT0WsM3MLSDgQgsinTgqlfMDHr76xoK9O4Q2E3NRlKwl890gg1Vkn7N4dMFJ0D1goOqDKkN1RLCVTS/Nn+7Ae1JUtekeuc=; _ga_GK1NB7HX40=GS2.1.s1751244877$o156$g1$t1751248149$j60$l0$h0; _tq_id.TV-18900945-1.8951=c90e45ba9c563226.1715000829.0.1751248150..; _rdt_uuid=1749101569995.ad0380ce-2738-492d-9c20-5f9eba4f1d41; tatari-cookie-test=14032332; _uetsid=855b6f0041ce11f08c3ce7b0a88b96fb; ttcsid=1751244902457::XUX-q34Uno3pb6aNaiLM.159.1751248155401; __hssc=156287140.24.1751244902595; ttcsid_C2IG4QAQV140ORDJ7BR0=1751244902455::P2BjFhiAeyZvvd9Rl9wV.159.1751248155969; ttcsid_C6IPOEIO6DGUDE0A23MG=1751244902459::vpuWmpBWo8KqyEBw-_AH.159.1751248155969; ttcsid_C3VBNIPU9OSLU1GC4A70=1751244902460::l3gnUSl3Qeoh29EWtFSX.159.1751248155970; ttcsid_CAFNM6RC77U9UO5AT1FG=1751244902462::SJKPvuEmM-t8g7IiLrxN.159.1751248155971; _clsk=12q84pv%7C1751248159393%7C86%7C0%7Ci.clarity.ms%2Fcollect; _pxde=147a3b864274492f80d4dd1dd4cf7fa979db173b3aab964412f1ae4390b36a47:eyJ0aW1lc3RhbXAiOjE3NTEyNDgxNzA4OTQsImZfa2IiOjAsImlwY19pZCI6W119; forterToken=92fc06594bc54bb2bba0551ef4a5183e_1751248170294__UDF43_17ck",
        "YOUR_X_CSRF_TOKEN_VALUE": "1752457768.KuFjmGweFf5W/PtYwhwc2kXL4Ng7wGEecAaNz7BJiD8=",
        "WHATSAPP_CHAT_ID": "120363419669386785@g.us", # Your primary WhatsApp group/chat ID
        "WHATSAPP_ADMIN_CHAT_ID": "6285966302755@c.us", # New: WhatsApp Admin Chat ID for custom offer notifications
        "WHATSAPP_API_URL": "http://103.85.60.82:3001/api/sendText?apikey=m0h4mm4d",
        "WHATSAPP_AUTH_USER": "ridwan",
        "WHATSAPP_AUTH_PASS": "m0h4mm4d"
   }
]

# --- GEMINI API CONFIGURATION ---
# Get your Gemini API key from Google AI Studio: https://aistudio.google.com/app/apikey
# WARNING: Never publish your API key!
# It's recommended to use environment variables: os.getenv("GEMINI_API_KEY")

# >>>>>>> YOU MUST REPLACE "YOUR_ACTUAL_GEMINI_API_KEY_HERE" WITH YOUR REAL GEMINI API KEY <<<<<<<
# >>>>>>> OR, SET IT AS AN ENVIRONMENT VARIABLE NAMED GEMINI_API_KEY <<<<<<<
GEMINI_API_KEY = os.getenv("GEMINI_API_KEY", "AIzaSyD651sbL63dIIdMe84peOZmcLxwoa46Mio")

GEMINI_MODEL_NAME = "gemini-2.5-flash-preview-04-17"
GEMINI_API_URL = f"https://generativelanguage.googleapis.com/v1beta/models/{GEMINI_MODEL_NAME}:generateContent"

# --- FIVERR FORBIDDEN KEYWORDS LIST (FOR AUTO-REPLY) ---
# Using more sophisticated regex patterns to match various variations
FIVERR_FORBIDDEN_KEYWORDS = [
    r"\b(?:e-?mail|email\s*address)\b",
    r"\b(?:link|url|site)\b",
    r"\.(?:com|net|org|id|co|io|me)\b", # Common domain examples
    r"\b(?:discord|skype|telegram|whatsapp|wa|line)\b",
    r"\b(?:contact\s*me|reach\s*out|get\s*in\s*touch)\b",
    r"\b(?:off\s*fiverr|outside\s*platform|off\s*platform)\b",
    r"\bphone\b", r"\bphone\s*number\b", r"\btel\b",
    r"\bget\s*in\s*touch\b", r"\bread\s*my\s*profile\b" # Additional common keywords
]

# --- FIVERR ENDPOINTS ---
WEBSOCKET_INIT_URL = "https://www.fiverr.com/realtime/websocket"
INBOX_CONTACTS_URL = "https://www.fiverr.com/inbox/contacts"
MESSAGES_SEND_URL = "https://www.fiverr.com/inbox/conversations/messages"
ACTIVITY_URL = "https://www.fiverr.com/api/v1/activities" # For sending activity pings
CONVERSION_TRACK_URL = "https://insight.adsrvr.org/track/realtimeconversion" # Conversion Tracking URL
CONVERSATION_HISTORY_URL = "https://www.fiverr.com/inbox/contacts/{recipient_username}/conversation" # Modified URL
GIGS_API_URL = "https://www.fiverr.com/custom_offers/seller/eligible_options?" # New: URL to get seller gigs
FIVERR_GIG_UPDATE_BASE_URL = "https://www.fiverr.com/users/{username}/manage_gigs/{gig_slug}/edit" # Base URL for gig update

# SSL configuration for WSS (WebSocket Secure) connection
ssl_context = ssl.create_default_context(cafile=certifi.where())

# --- CONVERSATION HISTORY STORAGE (In-memory, for this session only) ---
# This will now be nested:
# {
#     "username": {
#         "channel_id": {
#             "messages": [{"role": "user", "text": "Hi"}],
#             "offer_status": "none", # "none", "proposed", "template_sent"
#             "forbidden_keyword_warned": False # NEW FIELD: Track if a warning has been sent for this conversation
#         }
#     }
# }
conversation_history = {}

# --- Store previously seen orders for each account ---
# { "username": set(order_ids) }
previously_seen_orders = {}

# --- Global variable to store gig information for each seller ---
# { "username": [{"title": "Gig Title", "description": "Gig description", "min_price": "50", "gig_id": "...", "slug": "...", "category_id": "...", "sub_category_id": "...", "service_type_id": "...", "packages": [...], "gig_items_attributes": [...]}, ...] }
# This will now store more detailed gig info for updates
SELLER_GIGS_INFO = {}

# --- Database Configuration (from coba2.py) ---
DB_CONFIG = {
    'hostname': 'localhost',
    'username': 'udara',
    'password': '@UdaraUnis2024',
    'database': 'udara'
}

# --- Custom Offer Request Format ---
# This is the format the bot will send to the buyer if Gemini recommends a custom offer.
CUSTOM_OFFER_REQUEST_FORMAT = """
Please provide the following details for your custom offer:
1.  **Project Description:** Briefly describe what you need done.
2.  **Desired Features/Scope:** What specific features or tasks should be included?
3.  **Target Delivery Time:** When do you need this completed (e.g., 1 day, 3 days, 1 week)?
4.  **Budget (Optional but helpful):** What is your estimated budget for this project?

Once I receive these details, I will review them and send you a custom offer.
"""

# --- Regex to detect if the buyer has filled out the custom offer format ---
# This regex looks for patterns like "Project Description:", "Desired Features:", etc.
CUSTOM_OFFER_FILLED_REGEX = re.compile(
    r".*project\s*description:.*desired\s*features.*target\s*delivery\s*time:.*",
    re.IGNORECASE | re.DOTALL
)

# Add new regex for buyer's affirmation for custom offer
BUYER_AFFIRM_CUSTOM_OFFER_REGEX = re.compile(
    r"\b(?:ya|ok|oke|deal|setuju|bisa|iya|mau custom offer|custom offer dong|buatkan custom offer|yes|create custom offer for me)\b",
    re.IGNORECASE
)

# --- Helper to get current formatted time ---
def get_current_time_formatted():
    return datetime.now().strftime("%Y-%m-%d %H:%M:%S")

# --- NEW FUNCTION: SEND WHATSAPP NOTIFICATION ---
async def send_whatsapp_notification(account_config, message_text, to_admin=False):
    if to_admin:
        whatsapp_chat_id = account_config.get("WHATSAPP_ADMIN_CHAT_ID")
        log_prefix = "ADMIN WhatsApp"
    else:
        whatsapp_chat_id = account_config.get("WHATSAPP_CHAT_ID")
        log_prefix = "WhatsApp"

    whatsapp_api_url = account_config.get("WHATSAPP_API_URL")
    whatsapp_auth_user = account_config.get("WHATSAPP_AUTH_USER")
    whatsapp_auth_pass = account_config.get("WHATSAPP_AUTH_PASS")

    if not (whatsapp_api_url and whatsapp_chat_id and whatsapp_auth_user and whatsapp_auth_pass):
        logging.warning(f"[{account_config['USERNAME']}] {log_prefix} notification credentials missing for this account. Skipping notification.")
        return

    payload = {
        "chatId": whatsapp_chat_id,
        "reply_to": None,
        "text": message_text,
        "linkPreview": False,
        "linkPreviewHighQuality": False,
        "session": "default"
    }

    headers = {
        "Content-Type": "application/json",
    }

    try:
        response = await asyncio.to_thread(
            requests.post,
            whatsapp_api_url,
            headers=headers,
            json=payload,
            auth=(whatsapp_auth_user, whatsapp_auth_pass),
            verify=False # Set to True if you have a valid SSL certificate for your WhatsApp API
        )
        response.raise_for_status()
        logging.info(f"[{account_config['USERNAME']}] {log_prefix} notification sent successfully. Status: {response.status_code}")
    except requests.exceptions.RequestException as e:
        logging.error(f"[{account_config['USERNAME']}] Error sending {log_prefix} notification: {e}")
        if hasattr(e, 'response') and e.response is not None:
            logging.error(f"[{account_config['USERNAME']}] {log_prefix} API Status Code: {e.response.status_code}")
            logging.error(f"[{account_config['USERNAME']}] {log_prefix} API Response Body: {e.response.text}")
    except Exception as e:
        logging.error(f"[{account_config['USERNAME']}] An unexpected error occurred during {log_prefix} notification send: {e}")

# --- FUNCTION STAGE 1: GET WEBSOCKET CREDENTIALS (HTTP POST) ---
async def get_websocket_credentials(account_config):
    username = account_config["USERNAME"]
    cookie_string = account_config["YOUR_FIVERR_COOKIE_STRING"]
    x_csrf_token = account_config["YOUR_X_CSRF_TOKEN_VALUE"]

    logging.info(f"\n[{username}] Attempting to retrieve WebSocket credentials from: {WEBSOCKET_INIT_URL} (via POST)")

    headers = {
        "User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36",
        "Content-Type": "application/json",
        "Accept": "application/json",
        "X-Requested-With": "XMLHttpRequest",
        "Referer": "https://www.fiverr.com/",
        "Origin": "https://www.fiverr.com",
        "Cookie": cookie_string,
    }

    if x_csrf_token:
        headers["X-CSRF-Token"] = x_csrf_token

    payload = {}

    try:
        response = await asyncio.to_thread(requests.post, WEBSOCKET_INIT_URL, headers=headers, json=payload)
        response.raise_for_status()

        data = response.json()
        if data.get("success"):
            logging.info(f"[{username}] Successfully obtained WebSocket credentials.")
            return data.get("url"), data.get("token"), cookie_string, x_csrf_token
        else:
            logging.error(f"[{username}] Failed to obtain WebSocket credentials. Success response: {data.get('success')}. Message: {data.get('message', 'N/A')}")
            logging.error(f"[{username}] Full response data: {json.dumps(data, indent=2)}")

            whatsapp_msg = (
                f"🚨 *Fiverr Notification Error for {username}* 🚨\n"
                f"Waktu: {get_current_time_formatted()}\n"
                f"Gagal mendapatkan kredensial WebSocket. Mungkin karena Captcha, Cookie, atau CSRF Token tidak valid.\n"
                f"Pesan Fiverr: {data.get('message', 'Tidak ada pesan spesifik.')}\n"
                f"Silakan perbarui Cookie dan CSRF Token secara manual di konfigurasi."
            )
            await send_whatsapp_notification(account_config, whatsapp_msg)
            return None, None, None, None

    except requests.exceptions.RequestException as e:
        error_message = str(e)
        status_code_info = ""
        if hasattr(e, 'response') and e.response is not None:
            error_message = f"Status Code: {e.response.status_code}, Body: {e.response.text[:200]}..."
            status_code_info = f"Status Code: {e.response.status_code}\n"
            if e.response.status_code == 401 or e.response.status_code == 403:
                logging.error(f"[{username}] This is most likely an HTTP authentication issue. Ensure your headers/cookies are correct and active.")
                logging.error(f"[{username}] >>> PLEASE MANUALLY UPDATE 'YOUR_FIVERR_COOKIE_STRING' AND 'YOUR_X_CSRF_TOKEN_VALUE' FOR THIS ACCOUNT! <<<")
            else:
                logging.error(f"[{username}] Another error occurred with the HTTP POST request.")

        logging.error(f"[{username}] Error while fetching WebSocket credentials (HTTP POST): {error_message}")
        whatsapp_msg = (
            f"🚨 *Fiverr Notification Error for {username}* 🚨\n"
            f"Waktu: {get_current_time_formatted()}\n"
            f"Gagal mendapatkan kredensial WebSocket. Mungkin karena Captcha, Cookie, atau CSRF Token tidak valid.\n"
            f"{status_code_info}"
            f"Detail Error: {error_message}\n"
            f"Silakan perbarui Cookie dan CSRF Token secara manual di konfigurasi."
        )
        await send_whatsapp_notification(account_config, whatsapp_msg)
        return None, None, None, None

# --- NEW FUNCTION: GET SELLER GIGS INFORMATION ---
async def get_seller_gigs_info(account_config):
    username = account_config["USERNAME"]
    cookie_string = account_config["YOUR_FIVERR_COOKIE_STRING"]
    x_csrf_token = account_config["YOUR_X_CSRF_TOKEN_VALUE"]

    logging.info(f"\n[{username}] Attempting to retrieve seller gigs info from: {GIGS_API_URL}")

    headers = {
        "User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36",
        "Accept": "application/json",
        "X-Requested-With": "XMLHttpRequest",
        "Referer": "https://www.fiverr.com/inbox", # Or a more specific page if known
        "Origin": "https://www.fiverr.com",
        "Cookie": cookie_string,
    }

    if x_csrf_token:
        headers["X-CSRF-Token"] = x_csrf_token

    try:
        response = await asyncio.to_thread(requests.get, GIGS_API_URL, headers=headers, timeout=10)
        response.raise_for_status()

        data = response.json()
        logging.info(f"[{username}] Successfully fetched seller gigs info.")
        
        gigs_list = []
        if data and data.get('options'):
            for option in data['options']:
                gig_data = option.get('gig', {})
                if gig_data:
                    title = gig_data.get('localizedTitle', {}).get('original', 'N/A')
                    slug = gig_data.get('slug', 'N/A')
                    gig_url = f"https://www.fiverr.com/{username}/{slug}"
                    
                    packages_info = []
                    for pkg in gig_data.get('packages', []):
                        pkg_type = pkg.get('type', 'N/A')
                        pkg_title = pkg.get('localizedTitle', {}).get('original', 'N/A')
                        pkg_description = pkg.get('localizedDescription', {}).get('original', 'N/A')
                        pkg_price = pkg.get('price', {}).get('amountInUsd', 'N/A')
                        pkg_duration = pkg.get('duration', {}).get('inDays', 'N/A')
                        
                        packages_info.append({
                            "type": pkg_type,
                            "title": pkg_title,
                            "description": pkg_description,
                            "price": f"${pkg_price}",
                            "delivery_days": pkg_duration
                        })
                    
                    gigs_list.append({
                        "title": title,
                        "url": gig_url,
                        "packages": packages_info,
                        # Add more fields if available and needed for update payload,
                        # e.g., 'id', 'category_id', 'sub_category_id', 'service_type_id',
                        # 'gig_items_attributes', 'description', 'requirements'
                        # For now, we'll fetch these more comprehensively in get_full_gig_details
                        "gig_id": gig_data.get('id'), # Assuming 'id' is here for direct update
                        "slug": slug,
                        "category_id": gig_data.get('category', {}).get('id'),
                        "sub_category_id": gig_data.get('subCategory', {}).get('id'),
                        "service_type_id": gig_data.get('serviceType', {}).get('id'),
                        "description": gig_data.get('localizedDescription', {}).get('original', 'N/A'),
                        "requirements": gig_data.get('requirements'), # This might be simplified or need more extraction
                        "gig_items_attributes": gig_data.get('gigItems'), # This might need more specific parsing for each attribute
                    })
        return gigs_list

    except requests.exceptions.RequestException as e:
        error_message = str(e)
        status_code_info = ""
        if hasattr(e, 'response') and e.response is not None:
            error_message = f"Status Code: {e.response.status_code}, Body: {e.response.text[:200]}..."
            status_code_info = f"Status Code: {e.response.status_code}\n"
            if e.response.status_code == 401 or e.response.status_code == 403:
                logging.error(f"[{username}] HTTP authentication for gigs info failed. Ensure your cookie/CSRF-Token is still valid.")
            else:
                logging.error(f"[{username}] Another error occurred with the HTTP GET request.")

        logging.error(f"[{username}] Error while fetching seller gigs info (HTTP GET): {error_message}")
        whatsapp_msg = (
            f"🚨 *Fiverr Notification Error for {username}* 🚨\n"
            f"Waktu: {get_current_time_formatted()}\n"
            f"Gagal mendapatkan informasi gig seller. Mungkin karena Captcha, Cookie, atau CSRF Token tidak valid.\n"
            f"{status_code_info}"
            f"Detail Error: {error_message}\n"
            f"Silakan perbarui Cookie dan CSRF Token secara manually di konfigurasi."
        )
        await send_whatsapp_notification(account_config, whatsapp_msg)
        return []

# --- NEW FUNCTION: SEND INBOX MESSAGE (HTTP POST) ---
async def send_inbox_message(account_config, recipient_username, message_text, conversation_id=None):
    username = account_config["USERNAME"]
    cookie_string = account_config["YOUR_FIVERR_COOKIE_STRING"]
    x_csrf_token = account_config["YOUR_X_CSRF_TOKEN_VALUE"]

    logging.info(f"\n[{username}] Attempting to send message to {recipient_username} (via POST)")

    headers = {
        "User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36",
        "Content-Type": "application/json",
        "Accept": "application/json",
        "X-Requested-With": "XMLHttpRequest",
        "Referer": "https://www.fiverr.com/inbox", # Or a more specific page if known
        "Origin": "https://www.fiverr.com",
        "Cookie": cookie_string,
    }

    if x_csrf_token:
        headers["X-CSRF-Token"] = x_csrf_token

    payload = {
        "content_blocks": [{"type": "text", "plain_text": message_text}],
        "content_type": "text",
        "participants_usernames": [recipient_username],
        "channel_id": conversation_id,
        "pending_attachment_ids": []
    }

    try:
        response = await asyncio.to_thread(requests.post, MESSAGES_SEND_URL, headers=headers, json=payload)
        response.raise_for_status()

        data = response.json()
        logging.info(f"\n[{username}] --- Send Message Response Received ---")
        logging.info(json.dumps(data, indent=4))
        if data.get("success"):
            logging.info(f"[{username}] Message successfully sent to {recipient_username}.")
            # Store the sent message in history for this specific account
            if username not in conversation_history:
                conversation_history[username] = {}
            if conversation_id not in conversation_history[username]:
                conversation_history[username][conversation_id] = {"messages": [], "offer_status": "none", "forbidden_keyword_warned": False}
            conversation_history[username][conversation_id]["messages"].append({"role": "model", "text": message_text})
        else:
            fiverr_error_message = data.get('message', 'Unknown message.')
            logging.error(f"[{username}] Failed to send message: {fiverr_error_message}")
            whatsapp_msg = (
                f"⚠️ *Fiverr Auto-Reply Gagal untuk {username}* ⚠️\n"
                f"Waktu: {get_current_time_formatted()}\n"
                f"Gagal mengirim balasan otomatis ke {recipient_username}.\n"
                f"Pesan yang akan dikirim: '{message_text[:100]}...'\n"
                f"Pesan Error Fiverr: {fiverr_error_message}"
            )
            await send_whatsapp_notification(account_config, whatsapp_msg)
        return data

    except requests.exceptions.RequestException as e:
        error_message = str(e)
        status_code_info = ""
        if hasattr(e, 'response') and e.response is not None:
            error_message = f"Status Code: {e.response.status_code}, Body: {e.response.text[:200]}..."
            status_code_info = f"Status Code: {e.response.status_code}\n"
            if e.response.status_code == 401 or e.response.status_code == 403:
                logging.error(f"[{username}] HTTP authentication for sending messages failed. Ensure your cookie/CSRF-Token is still valid and no forbidden content is present.")
                logging.error(f"[{username}] >>> 403 (FORBIDDEN) ERROR MAY REQUIRE COOKIE/TOKEN RENEWAL FOR THIS ACCOUNT. <<<")
            else:
                logging.error(f"[{username}] Another error occurred with the HTTP POST request.")

        logging.error(f"[{username}] Error while sending message (HTTP POST): {error_message}")
        whatsapp_msg = (
            f"🚨 *Fiverr Notification Error for {username}* 🚨\n"
            f"Waktu: {get_current_time_formatted()}\n"
            f"Gagal mengirim pesan. Mungkin karena Captcha, Cookie, atau CSRF Token tidak valid.\n"
            f"{status_code_info}"
            f"Detail Error: {e}"
        )
        await send_whatsapp_notification(account_config, whatsapp_msg)
        return None

# --- NEW FUNCTION: GET INBOX CONTACTS (HTTP GET) ---
async def get_inbox_contacts(account_config):
    username = account_config["USERNAME"]
    cookie_string = account_config["YOUR_FIVERR_COOKIE_STRING"]
    x_csrf_token = account_config["YOUR_X_CSRF_TOKEN_VALUE"]

    logging.info(f"\n[{username}] Attempting to retrieve inbox contacts from: {INBOX_CONTACTS_URL} (via GET)")

    headers = {
        "User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36",
        "Accept": "application/json",
        "X-Requested-With": "XMLHttpRequest",
        "Referer": "https://www.fiverr.com/inbox", # Or a more specific page if known
        "Origin": "https://www.fiverr.com",
        "Cookie": cookie_string,
    }

    if x_csrf_token:
        headers["X-CSRF-Token"] = x_csrf_token

    try:
        response = await asyncio.to_thread(requests.get, INBOX_CONTACTS_URL, headers=headers)
        response.raise_for_status()

        data = response.json()
        logging.info(f"[{username}] --- Inbox Contacts Data Received ---")
        # logging.info(f"[{username}] {json.dumps(data, indent=4)}") # Disable for less verbosity if called frequently
        return data

    except requests.exceptions.RequestException as e:
        error_message = str(e)
        status_code_info = ""
        if hasattr(e, 'response') and e.response is not None:
            error_message = f"Status Code: {e.response.status_code}, Body: {e.response.text[:200]}..."
            status_code_info = f"Status Code: {e.response.status_code}\n"
            if e.response.status_code == 401 or e.response.status_code == 403:
                logging.error(f"[{username}] HTTP authentication for inbox failed. Ensure your cookie/CSRF-Token is still valid.")
                logging.error(f"[{username}] >>> PLEASE MANUALLY UPDATE 'YOUR_FIVERR_COOKIE_STRING' AND 'YOUR_X_CSRF_TOKEN_VALUE' FOR THIS ACCOUNT FROM YOUR BROWSER! <<<")
            else:
                logging.error(f"[{username}] Another error occurred with the HTTP GET request.")

        logging.error(f"[{username}] Error while fetching inbox contacts (HTTP GET): {error_message}")
        whatsapp_msg = (
            f"🚨 *Fiverr Notification Error for {username}* 🚨\n"
            f"Waktu: {get_current_time_formatted()}\n"
            f"Gagal mendapatkan kontak inbox. Mungkin karena Captcha, Cookie, atau CSRF Token tidak valid.\n"
            f"{status_code_info}"
            f"Detail Error: {error_message}\n"
            f"Silakan perbarui Cookie dan CSRF Token secara manual di konfigurasi."
        )
        await send_whatsapp_notification(account_config, whatsapp_msg)
        return None

# --- NEW FUNCTION: GET SPECIFIC CONVERSATION DETAILS (HTTP GET) dan KUMPULKAN PESAN ---
async def get_conversation_details(account_config, recipient_username):
    """
    Melakukan permintaan GET ke URL percakapan untuk mendapatkan detail percakapan.
    Ini adalah implementasi yang diminta oleh pengguna.
    Mengembalikan data JSON percakapan lengkap DAN string yang difilter untuk prompt.
    """
    username = account_config["USERNAME"] # Ini adalah nama pengguna seller (contoh: chyailya)
    cookie_string = account_config["YOUR_FIVERR_COOKIE_STRING"]
    x_csrf_token = account_config["YOUR_X_CSRF_TOKEN_VALUE"]
    user_id = account_config["USER_ID"]

    conversation_url = f"https://www.fiverr.com/inbox/contacts/{recipient_username}/conversation"
    logging.info(f"[{username}] Melakukan GET ke URL percakapan untuk {recipient_username}: {conversation_url}")

    headers = {
        "User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36",
        "Cookie": cookie_string,
        "X-CSRF-Token": x_csrf_token,
        "Accept": "application/json, text/plain, */*",
        "Referer": f"https://www.fiverr.com/inbox/{user_id}",
        "Accept-Language": "en-US,en;q=0.9",
        "Connection": "keep-alive",
        "Cache-Control": "no-cache",
        "Pragma": "no-cache",
        "Sec-Fetch-Dest": "empty",
        "Sec-Fetch-Mode": "cors",
        "Sec-Fetch-Site": "same-origin",
        "TE": "Trailers",
        "X-Requested-With": "XMLHttpRequest"
    }

    try:
        response = await asyncio.to_thread(requests.get, conversation_url, headers=headers, timeout=10)
        response.raise_for_status()
        conversation_data = response.json()
        logging.info(f"[{username}] Berhasil mendapatkan detail percakapan dari {recipient_username}.")

        filtered_messages_for_cmd = []
        if conversation_data and "messages" in conversation_data and conversation_data["messages"]:
            logging.info(f"[{username}] [CMD LOG] Detail Pesan dari Percakapan:")
            for message in conversation_data["messages"]:
                sender = message.get("sender", "N/A")
                recipient = message.get("recipient", "N/A")
                body = message.get("body", "N/A")
                log_line = f"  Pengirim: {sender}, Penerima: {recipient}, Pesan: \"{body}\""
                logging.info(f"[{username}] {log_line}") # Log to CMD
                filtered_messages_for_cmd.append(f"Dari {sender} ke {recipient}: \"{body}\"") # Collect for Gemini prompt

            # Tambahkan juga pesan terakhir sebagai string terpisah untuk digunakan oleh Gemini jika diperlukan
            last_message_body = conversation_data["messages"][-1].get("body", "")
            if last_message_body:
                logging.info(f"[{username}] Pesan terakhir (untuk Gemini prompt): \"{last_message_body}\"")
                # Gabungkan semua pesan yang difilter menjadi satu string untuk Gemini
                # Kita akan menggunakan ini sebagai prompt_text utama
                full_conversation_text_for_gemini = "\n".join(filtered_messages_for_cmd)
                return conversation_data, full_conversation_text_for_gemini
            else:
                return conversation_data, None # Tidak ada pesan terakhir untuk dijadikan prompt

        else:
            logging.info(f"[{username}] Tidak ada pesan ditemukan di percakapan dengan {recipient_username}.")
            return conversation_data, None # Kembalikan data kosong dan None untuk prompt

    except requests.exceptions.HTTPError as errh:
        logging.error(f"[{username}] HTTP Error untuk {recipient_username}: {errh}")
    except requests.exceptions.ConnectionError as errc:
        logging.error(f"[{username}] Error Koneksi untuk {recipient_username}: {errc}")
    except requests.exceptions.Timeout as errt:
        logging.error(f"[{username}] Timeout Error untuk {recipient_username}: {errt}")
    except requests.exceptions.RequestException as err:
        logging.error(f"[{username}] Terjadi kesalahan saat membuat permintaan GET untuk {recipient_username}: {err}")
    except json.JSONDecodeError:
        logging.error(f"[{username}] Gagal mendekode JSON dari respons untuk {recipient_username}. Respons: {response.text}")
    return None, None # Jika ada error, kembalikan None, None

# --- NEW FUNCTION: GET FULL CONVERSATION HISTORY (HTTP GET) ---
# Fungsi ini tetap untuk mendapatkan riwayat percakapan yang lengkap, untuk konteks Gemini.
# Nama fungsi ini akan membedakannya dari `get_conversation_details` yang khusus untuk permintaan GET pengguna dan log.
async def get_full_conversation_history(account_config, recipient_username, channel_id):
    username = account_config["USERNAME"]
    cookie_string = account_config["YOUR_FIVERR_COOKIE_STRING"]
    x_csrf_token = account_config["YOUR_X_CSRF_TOKEN_VALUE"]

    url = f"https://www.fiverr.com/inbox/contacts/{recipient_username}/conversation?id={channel_id}"
    logging.info(f"\n[{username}] Attempting to retrieve full conversation history from: {url} (via GET for Gemini context)")

    headers = {
        "User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36",
        "Accept": "application/json",
        "X-Requested-With": "XMLHttpRequest",
        "Referer": f"https://www.fiverr.com/inbox/conversation/{channel_id}",
        "Origin": "https://www.fiverr.com",
        "Cookie": cookie_string,
    }

    if x_csrf_token:
        headers["X-CSRF-Token"] = x_csrf_token

    try:
        response = await asyncio.to_thread(requests.get, url, headers=headers)
        response.raise_for_status()

        data = response.json()
        logging.info(f"[{username}] Successfully fetched full conversation history for channel {channel_id}.")

        formatted_messages = []
        messages = data.get('messages', [])
        for msg in messages:
            sender = msg.get('sender')
            body = msg.get('body')
            if sender and body:
                role = "user" if sender.lower() != username.lower() else "model"
                formatted_messages.append({"role": role, "text": body})

        return formatted_messages

    except requests.exceptions.RequestException as e:
        error_message = str(e)
        status_code_info = ""
        if hasattr(e, 'response') and e.response is not None:
            error_message = f"Status Code: {e.response.status_code}, Body: {e.response.text[:200]}..."
            status_code_info = f"Status Code: {e.response.status_code}\n"
            if e.response.status_code == 401 or e.response.status_code == 403:
                logging.error(f"[{username}] HTTP authentication for fetching full conversation history failed. Ensure your cookie/CSRF-Token is still valid.")
                logging.error(f"[{username}] >>> PLEASE MANUALLY UPDATE 'YOUR_FIVERR_COOKIE_STRING' AND 'YOUR_X_CSRF_TOKEN_VALUE' FOR THIS ACCOUNT FROM YOUR BROWSER! <<<")
            else:
                logging.error(f"[{username}] Another error occurred with the HTTP GET request.")

        logging.error(f"[{username}] Error while fetching full conversation history (HTTP GET): {error_message}")
        whatsapp_msg = (
            f"🚨 *Fiverr Notification Error for {username}* 🚨\n"
            f"Waktu: {get_current_time_formatted()}\n"
            f"Gagal mendapatkan riwayat percakapan lengkap untuk {recipient_username}. Mungkin karena Captcha, Cookie, atau CSRF Token tidak valid.\n"
            f"{status_code_info}"
            f"Detail Error: {error_message}\n"
            f"Silakan perbarui Cookie dan CSRF Token secara manual di konfigurasi."
        )
        await send_whatsapp_notification(account_config, whatsapp_msg)
        return [] # Return empty list on failure

# --- NEW FUNCTION: GENERATE RESPONSE WITH GEMINI (Enhanced Prompting and Context) ---
async def generate_gemini_response(prompt_text, account_username, recipient_username=None, conversation_id=None, forbidden_keyword_warned=False):
    # Check if the API key is properly set (not the placeholder)
    if not GEMINI_API_KEY or GEMINI_API_KEY == "YOUR_ACTUAL_GEMINI_API_KEY_HERE":
        logging.warning(f"[{account_username}] WARNING: GEMINI_API_KEY is not set correctly. Cannot contact Gemini API.")
        return "Sorry, I couldn't generate a relevant response."

    # For general prompts (like gig optimization), recipient_username and conversation_id might be None
    # We only need full conversation context for inbox replies.
    current_conversation_messages = []
    if recipient_username and conversation_id:
        lower_prompt_text = prompt_text.lower() # Only needed for inbox-specific forbidden keyword check
        fetched_history = await get_full_conversation_history(
            next(cfg for cfg in ACCOUNT_CONFIGS if cfg["USERNAME"] == account_username),
            recipient_username,
            conversation_id
        )
        current_conversation_messages = [
            {"role": msg["role"], "text": msg["text"]}
            for msg in fetched_history
        ]
        logging.info(f"[{account_username}] Fetched {len(current_conversation_messages)} messages for context.")

    # Build the conversation history for the prompt for this specific account
    history_for_gemini = []

    # Get current offer status for this conversation (only relevant for inbox)
    current_offer_status = conversation_history.get(account_username, {}).get(conversation_id, {}).get("offer_status", "none")

    # Initial system instruction setting persona, core rules, and seller context
    initial_system_instruction = (
        f"""You are a highly professional, friendly, and solution-oriented Fiverr seller named {account_username}.
        Your primary goal is to engage buyers, understand their needs, and guide them towards placing an order for your services on Fiverr.
        Always maintain a positive, helpful, and enthusiastic tone.
        Strictly adhere to Fiverr's Terms of Service: ALL communication and transactions MUST remain on Fiverr.
        Never ask for or provide external contact information, personal details, or links to external sites.
        If the buyer attempts to move communication off-platform, politely but firmly redirect them back to Fiverr.
        Your responses should be concise, clear, and easy to understand.
        Crucially, **always respond in the exact same language as the buyer's last message.**
        Do not mention you are an AI or a bot. Focus on the service you provide and how it benefits the buyer.

        Here is a summary of your available gigs and their packages, use this information to inform your responses and suggest relevant services:
        """
    )

    # Add gig information to the initial system instruction
    if account_username in SELLER_GIGS_INFO and SELLER_GIGS_INFO[account_username]:
        for gig in SELLER_GIGS_INFO[account_username]: # Use account_username for global SELLER_GIGS_INFO
            initial_system_instruction += f"\n- Gig Title: {gig['title']}"
            initial_system_instruction += f"\n  URL: {gig['url']}"
            for pkg in gig['packages']:
                initial_system_instruction += f"\n  Package Type: {pkg['type']}, Title: {pkg['title']}, Description: {pkg['description']}, Price: {pkg['price']}, Delivery: {pkg['delivery_days']} days"
    else:
        initial_system_instruction += "\n(No specific gig information available at this time. Focus on general services.)"

    history_for_gemini.append({"role": "user", "parts": [{"text": initial_system_instruction}]})
    history_for_gemini.append({"role": "model", "parts": [{"text": "Understood. I am ready to respond as a professional Fiverr seller, with knowledge of my gigs and services."}]})


    # Add relevant recent conversation history from the fetched data (only for inbox replies)
    if current_conversation_messages:
        for msg in current_conversation_messages[-10:]: # Pass last 10 messages for context
            history_for_gemini.append({"role": msg["role"], "parts": [{"text": msg["text"]}]})

    # Add the current input as the latest "user" input for Gemini to respond to
    history_for_gemini.append({"role": "user", "parts": [{"text": prompt_text}]})

    # The main change: only send the forbidden keyword prompt if it's an inbox message
    if recipient_username and conversation_id: # Only for inbox conversations
        current_message_contains_forbidden_keyword = False
        for keyword_pattern in FIVERR_FORBIDDEN_KEYWORDS:
            if re.search(keyword_pattern, lower_prompt_text):
                current_message_contains_forbidden_keyword = True
                break
        
        if current_message_contains_forbidden_keyword:
            logging.info(f"[{account_username}] --- FORBIDDEN KEYWORD DETECTED in current message. Generating rule-compliant response. ---")
            history_for_gemini.append({
                "role": "user",
                "parts": [{"text":
                    """The buyer's last message contains content that *attempts to move communication or transactions outside of Fiverr* or requests forbidden contact details.
                    This is a direct violation of Fiverr's Terms of Service.
                    Your task is to respond *politely, professionally, and very clearly* that all communication and transactions must remain strictly on Fiverr for their safety and the integrity of the platform.
                    **Do NOT explicitly repeat or acknowledge the forbidden keyword/request.**
                    Instead, gently but firmly redirect the conversation back to the project details on Fiverr.
                    Emphasize that this policy protects both buyer and seller.
                    Conclude with a call to action that encourages them to continue the discussion or place an order *on Fiverr*.
                    Maintain the buyer's original language. Keep the response concise. """
                }]
            })
        else:
            # If no forbidden keyword in the *current* message, provide the regular prompt.
            history_for_gemini.append({
                "role": "user",
                "parts": [{"text":
                    f"""The buyer has sent a message. Analyze their request and provide a **compelling, action-oriented response** designed to move them towards placing an order or clarifying details for a custom offer.
                    **Your response should:**
                    1.  Acknowledge their message and express enthusiasm.
                    2.  Briefly demonstrate understanding of their needs or question.
                    3.  Highlight how your specific service (or a custom offer) is the perfect solution for them. Focus on benefits and quick results.
                    4.  If they ask a question, answer it directly and succinctly.
                    5.  Include a very clear and direct **Call to Action (CTA)**:
                        * If their request is clear, encourage them to "place an order now" via your gig link or a custom offer button. Mention the most relevant gig title from your available gigs.
                        * **IMPORTANT CUSTOM OFFER LOGIC:**
                            * If the buyer's message clearly indicates a need for a custom solution, a project beyond standard gig packages, or they specifically ask for a quote for a custom task,
                            * AND if the current `offer_status` for this conversation is "none" (meaning no custom offer process has started yet),
                            * THEN you should propose a custom offer as an option.
                            **If you decide to propose a custom offer, you MUST start your response with the exact phrase: "CUSTOM_OFFER_PROPOSE:"** followed by a polite and friendly sentence offering a choice between your gigs and a custom offer.
                            **Example if proposing:** "CUSTOM_OFFER_PROPOSE: Tentu! Untuk kebutuhan spesifik Anda, apakah Anda ingin melihat gig saya yang relevan atau saya bisa membuat penawaran khusus untuk Anda? Mohon balas 'ya' jika Anda ingin penawaran khusus." (Or similar in English: "Sure! For your specific needs, would you like to check out my relevant gigs, or would you prefer I create a custom offer for you? Please reply 'yes' if you'd like a custom offer.")
                            **Do NOT include the custom offer template itself at this stage.**
                            * **If the `offer_status` is not "none" (i.e., it's "proposed" or "template_sent"), do NOT propose a custom offer again.** Instead, focus on answering their current question and guiding them to the next step of the ongoing custom offer process (e.g., filling the template). If they explicitly ask for the template again while `offer_status` is "template_sent" (e.g., "mana formatnya?", "resend template"), simply state that you've already sent it and are waiting for their details.
                    6.  Maintain a friendly, proactive, and confident tone.
                    7.  Ensure the response is in the **exact same language as the buyer's message** and is concise, ideally 2-4 sentences (excluding the special tag and custom offer choice sentence if present).
                    Consider if they are asking about pricing, delivery time, or project scope and address it while pushing for the next step (order)."""
                }]
            })


    headers = {
        'Content-Type': 'application/json',
    }
    params = {
        'key': GEMINI_API_KEY
    }

    json_data = {
        'contents': history_for_gemini
    }

    logging.info(f"\n[{account_username}] --- Full Gemini Prompt Content (for debugging) ---")
    logging.info(json.dumps(json_data, indent=2))
    logging.info(f"[{account_username}] --- End Full Gemini Prompt Content ---")

    try:
        response = await asyncio.to_thread(requests.post, GEMINI_API_URL, headers=headers, params=params, json=json_data)
        response.raise_for_status()

        result = response.json()
        if result.get("candidates") and result["candidates"][0].get("content") and \
           result["candidates"][0]["content"].get("parts") and result["candidates"][0]["content"]["parts"][0].get("text"):
            generated_text = result["candidates"][0]["content"]["parts"][0]["text"]

            # Robust cleaning of potential introductory phrases from Gemini
            clean_response = generated_text.strip()

            # A list of common intros Gemini might generate that need to be removed
            intros_to_remove = [
                "Balasan Anda sebagai Penjual Fiverr:",
                "Your reply as a Fiverr seller:",
                "Oke, ini balasan Anda:",
                "Okay, here's your reply:",
                "Mohon berikan balasan Anda:",
                "Please provide your reply:",
                "Tentu, berikut adalah balasan Anda sebagai penjual Fiverr profesional:",
                "Certainly, here's your reply as a professional Fiverr seller:",
                "Sebagai penjual Fiverr profesional, berikut balasan yang bisa Anda gunakan:",
                "As a professional Fiverr seller, here's a response you can use:",
                "Berikut adalah respons yang dapat Anda kirimkan:",
                "Here is a response you can send:",
                "Tentu, ini draf balasan untuk Anda:",
                "Certainly, here's a draft response for you:",
                "Baik, ini balasan profesional yang dapat Anda gunakan:",
                "Alright, here's a professional response you can use:",
                "Saya telah membuatkan balasan untuk Anda:",
                "I've drafted a response for you:",
                "Berikut adalah balasan yang disesuaikan untuk pembeli Anda:",
                "Here is a tailored response for your buyer:",
                "Ini draf balasan yang sesuai:",
                "Here's an appropriate draft response:",
                "Berikut adalah balasan singkat dan profesional:",
                "Here's a concise and professional reply:",
                "Tentu! Sebagai penjual Fiverr, berikut adalah balasan yang efektif dan sesuai dengan aturan platform:",
                "Okay! As a Fiverr seller, here's an effective and platform-compliant response:"
            ]

            for intro in intros_to_remove:
                if clean_response.lower().startswith(intro.lower()):
                    clean_response = clean_response[len(intro):].strip()
                    break # Only remove one matching intro

            # Final check to remove any leading quotes or dashes if Gemini somehow adds them
            clean_response = re.sub(r'^[“"\'\-—–]', '', clean_response).strip()

            # Ensure the response is not just an empty string after cleaning
            if not clean_response:
                return "Sorry, I couldn't generate a relevant response."

            return clean_response
        else:
            logging.error(f"[{account_username}] Failed to get text from Gemini response: {result}")
            # Log the full result to understand why text wasn't found
            logging.error(f"[{account_username}] Full Gemini response (no text found): {json.dumps(result, indent=2)}")
            return "Sorry, I couldn't generate a relevant response."

    except requests.exceptions.RequestException as e:
        logging.error(f"[{account_username}] Error while contacting Gemini API: {e}")
        if hasattr(e, 'response') and e.response is not None:
            logging.error(f"[{account_username}] Status Code: {e.response.status_code}")
            logging.error(f"[{account_username}] Response Body: {e.response.text}")
        return "Sorry, there was a problem processing your request."
    except Exception as e:
        logging.error(f"[{account_username}] An unexpected error occurred during generate_gemini_response: {e}")
        return "Sorry, an unexpected error occurred."

# --- NEW FUNCTION: SEND GENERIC ACTIVITY PING ---
async def send_activity_ping(account_config):
    username = account_config["USERNAME"]
    user_id = account_config["USER_ID"]
    cookie_string = account_config["YOUR_FIVERR_COOKIE_STRING"]
    x_csrf_token = account_config["YOUR_X_CSRF_TOKEN_VALUE"]

    logging.info(f"\n[{username}] --- Sending generic activity ping to Fiverr ---")

    headers = {
        "User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36",
        "Content-Type": "application/json",
        "Accept": "application/json",
        "X-Requested-With": "XMLHttpRequest",
        "Referer": f"https://www.fiverr.com/sellers/{username}/edit",
        "Origin": "https://www.fiverr.com",
        "Cookie": cookie_string,
    }

    if x_csrf_token:
        headers["X-CSRF-Token"] = x_csrf_token

    # The payload from your provided data for a general activity ping
    payload = [{
        "facility": "self_view_perseus",
        "user": {"id": user_id},
        "platform": "mobile_web",
        "url": f"https://www.fiverr.com/sellers/{username}/edit",
        "page": {
            "ctx_id": "d2820811870a4c86bafd3bf3fd7f594d",
            "name": "self_view_perseus"
        },
        "feature": {"name": "cpra"},
        "type": "cpra_footer_link_is_loaded",
        "group": "technical_events"
    }]

    try:
        response = await asyncio.to_thread(requests.post, ACTIVITY_URL, headers=headers, json=payload)
        response.raise_for_status()

        logging.info(f"[{username}] Generic activity ping sent successfully. Status: {response.status_code}")
    except requests.exceptions.RequestException as e:
        error_message = str(e)
        status_code_info = ""
        if hasattr(e, 'response') and e.response is not None:
            error_message = f"Status Code: {e.response.status_code}, Body: {e.response.text[:200]}..."
            status_code_info = f"Status Code: {e.response.status_code}\n"
            if e.response.status_code == 401 or e.response.status_code == 403:
                logging.error(f"[{username}] Generic activity ping authentication failed. Your cookie/CSRF-Token might be invalid or expired.")
                logging.error(f"[{username}] >>> PLEASE MANUALLY UPDATE 'YOUR_FIVERR_COOKIE_STRING' AND 'YOUR_X_CSRF_TOKEN_VALUE' FOR THIS ACCOUNT! <<<")
            else:
                logging.error(f"[{username}] Another error occurred with the HTTP POST request.")

        logging.error(f"[{username}] Error sending generic activity ping: {error_message}")
        whatsapp_msg = (
            f"🚨 *Fiverr Notification Error for {username}* 🚨\n"
            f"Waktu: {get_current_time_formatted()}\n"
            f"Gagal mengirim ping aktivitas generik. Mungkin karena Captcha, Cookie, atau CSRF Token tidak valid.\n"
            f"{status_code_info}"
            f"Detail Error: {error_message}\n"
            f"Silakan perbarui Cookie dan CSRF Token secara manual di konfigurasi."
        )
        await send_whatsapp_notification(account_config, whatsapp_msg)
    except Exception as e:
        logging.error(f"[{username}] An unexpected error occurred during generic activity ping: {e}")

# --- NEW FUNCTION: SEND NOTIFICATION SHOWN ACTIVITY ---
async def send_notification_shown_activity(account_config, notification_id, conversation_id=None):
    username = account_config["USERNAME"]
    user_id = account_config["USER_ID"]
    cookie_string = account_config["YOUR_FIVERR_COOKIE_STRING"]
    x_csrf_token = account_config["YOUR_X_CSRF_TOKEN_VALUE"]

    logging.info(f"\n[{username}] --- Sending 'notification_is_shown' activity for notification_id: {notification_id} ---")

    headers = {
        "User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36",
        "Content-Type": "application/json",
        "Accept": "application/json",
        "X-Requested-With": "XMLHttpRequest",
        "Referer": "https://www.fiverr.com/inbox/",
        "Origin": "https://www.fiverr.com",
        "Cookie": cookie_string,
    }

    if x_csrf_token:
        headers["X-CSRF-Token"] = x_csrf_token

    # Dynamic ctx_id generation based on the conversation ID if available, otherwise a default
    page_ctx_id = conversation_id if conversation_id else "25deb3dbd8404c3183c58b4df08101d2"

    payload = [{
        "facility": "inbox_perseus",
        "user": {"id": user_id},
        "platform": "mobile_web",
        "url": "https://www.fiverr.com/inbox/",
        "page": {
            "ctx_id": page_ctx_id,
            "name": "inbox_perseus"
        },
        "type": "notification_is_shown",
        "group": "notification_impressions",
        "entity": "client",
        "action": {"type": "imp"},
        "notification": {
            "id": notification_id,
            "type": "conversation",
            "source": "toast"
        }
    }]

    try:
        response = await asyncio.to_thread(requests.post, ACTIVITY_URL, headers=headers, json=payload)
        response.raise_for_status()

        logging.info(f"[{username}] 'notification_is_shown' activity sent successfully. Status: {response.status_code}")
    except requests.exceptions.RequestException as e:
        logging.error(f"[{username}] Error sending 'notification_is_shown' activity: {e}")
        if hasattr(e, 'response') and e.response is not None:
            logging.error(f"[{username}] Status Code: {e.response.status_code}")
            logging.error(f"[{username}] Response Body: {e.response.text}")
    except Exception as e:
        logging.error(f"[{username}] An unexpected error occurred during 'notification_is_shown' send: {e}")

# --- NEW FUNCTION: SEND REAL-TIME CONVERSION REQUEST ---
async def send_realtime_conversion(account_username, data_payload):
    """
    Sends a real-time conversion request to the specified URL.
    """
    logging.info(f"[{account_username}] --- Sending Real-Time Conversion Request ---")

    headers = {
        "Content-Type": "application/json",
        "Referer": "https://www.fiverr.com/inbox/",
    }

    # The outer 'data' key is part of the request structure you provided.
    final_payload = {"data": [data_payload]}

    try:
        response = await asyncio.to_thread(requests.post, CONVERSION_TRACK_URL, headers=headers, json=final_payload)
        response.raise_for_status()

        logging.info(f"[{account_username}] Real-Time Conversion request sent successfully. Status: {response.status_code}")
    except requests.exceptions.RequestException as e:
        logging.error(f"[{account_username}] Error sending Real-Time Conversion request: {e}")
        if hasattr(e, 'response') and e.response is not None:
            logging.error(f"[{account_username}] Status Code: {e.response.status_code}")
            logging.error(f"[{account_username}] Response Body: {e.response.text}")
    except Exception as e:
        logging.error(f"[{account_username}] An unexpected error occurred during real-time conversion send: {e}")


# --- FUNCTION TO PERIODICALLY SEND ACTIVITY PINGS ---
async def send_activity_periodically(account_config, interval_seconds):
    username = account_config["USERNAME"]
    logging.info(f"\n[{username}] --- Starting periodic general activity pings every {interval_seconds} seconds ---")
    while True:
        await send_activity_ping(account_config)
        await asyncio.sleep(interval_seconds)

# --- NEW FUNCTION: PERFORM PERIODIC GET REQUESTS FOR ORDER PAGES ---
async def get_order_pages_periodically(account_config, interval_seconds):
    username = account_config["USERNAME"]
    cookie_string = account_config["YOUR_FIVERR_COOKIE_STRING"]
    x_csrf_token = account_config["YOUR_X_CSRF_TOKEN_VALUE"]

    # Initialize previously_seen_orders for this account if not already
    if username not in previously_seen_orders:
        previously_seen_orders[username] = set()

    order_urls = [
        f"https://www.fiverr.com/users/{username}/manage_orders/type/priority",
        f"https://www.fiverr.com/users/{username}/manage_orders/type/active",
        f"https://www.fiverr.com/users/{username}/manage_orders/type/late_delivery",
        f"https://www.fiverr.com/users/{username}/manage_orders/type/delivered",
    ]

    logging.info(f"\n[{username}] --- Starting periodic GET requests for order pages every {interval_seconds} seconds ---")
    while True: # Loop utama untuk pengecekan berulang
        headers = {
            "User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36",
            "Accept": "application/json, text/javascript, */*; q=0.01", # Changed to accept JSON primarily
            "Accept-Encoding": "gzip, deflate, br",
            "Accept-Language": "en-US,en;q=0.9",
            "Connection": "keep-alive",
            "Cookie": cookie_string,
            "Host": "www.fiverr.com",
            "Sec-Fetch-Dest": "empty",
            "Sec-Fetch-Mode": "cors",
            "Sec-Fetch-Site": "same-origin",
            "X-Requested-With": "XMLHttpRequest",
        }

        if x_csrf_token:
            headers["X-CSRF-Token"] = x_csrf_token

        current_order_ids = set() # Untuk menyimpan ID pesanan yang diambil dalam siklus ini

        for url in order_urls:
            filter_type = url.split('/')[-1] # Ekstrak 'priority', 'active', dll.
            logging.info(f"[{username}] Fetching order page data from: {url}")
            try:
                response = await asyncio.to_thread(requests.get, url, headers=headers)
                response.raise_for_status()

                order_data = response.json() # Parse the JSON response
                logging.info(f"[{username}] Successfully fetched data from {url}. Status: {response.status_code}")
                # logging.info(f"[{username}] Order Data: {json.dumps(order_data, indent=2)}") # Uncomment to see full data

                if order_data.get('results'):
                    for order in order_data['results']:
                        order_id = order.get('id')
                        if order_id:
                            current_order_ids.add(order_id)
                            # Periksa pesanan baru
                            if order_id not in previously_seen_orders[username]:
                                order_status = order.get('status', 'N/A')
                                buyer_username = order.get('buyer', {}).get('username', 'N/A')
                                order_title = order.get('gig', {}).get('title', 'N/A')
                                order_price = order.get('price', 'N/A')

                                logging.info(f"[{username}] *PESANAN BARU TERDETEKSI!*")
                                logging.info(f"  - ID Pesanan: {order_id}")
                                logging.info(f"  - Status: {order_status}")
                                logging.info(f"  - Pembeli: {buyer_username}")
                                logging.info(f"  - Gig: {order_title}")
                                logging.info(f"  - Harga: ${order_price}")

                                whatsapp_msg = (
                                    f"🎉 *PESANAN BARU DI FIBERR UNTUK {username.upper()}!* 🎉\n"
                                    f"Waktu: {get_current_time_formatted()}\n"
                                    f"ID Pesanan: {order_id}\n"
                                    f"Status: {order_status}\n"
                                    f"Pembeli: {buyer_username}\n"
                                    f"Judul Gig: {order_title}\n"
                                    f"Harga: ${order_price}\n"
                                    f"Link: https://www.fiverr.com/users/{username}/manage_orders/type/active"
                                )
                                await send_whatsapp_notification(account_config, whatsapp_msg)
                                previously_seen_orders[username].add(order_id) # Tambahkan ke set yang sudah dilihat

                else:
                    logging.info(f"[{username}] Tidak ada pesanan ditemukan untuk filter '{order_data.get('current_filter')}' atau array hasil kosong.")

            except requests.exceptions.JSONDecodeError:
                logging.error(f"[{username}] Error: Respon dari {url} bukan JSON yang valid.")
                logging.error(f"[{username}] Konten Respon (500 karakter pertama): {response.text[:500]}...")
                whatsapp_msg = (
                    f"🚨 *Kesalahan Notifikasi Fiverr untuk {username}* 🚨\n"
                    f"Waktu: {get_current_time_formatted()}\n"
                    f"Gagal mengambil data pesanan dari {url}. Respon bukan JSON valid.\n"
                    f"Silakan cek koneksi atau status Fiverr."
                )
                await send_whatsapp_notification(account_config, whatsapp_msg)
            except requests.exceptions.RequestException as e:
                error_message = str(e)
                status_code_info = ""
                if hasattr(e, 'response') and e.response is not None:
                    error_message = f"Status Code: {e.response.status_code}, Body: {e.response.text[:200]}..."
                    status_code_info = f"Status Code: {e.response.status_code}\n"
                    if e.response.status_code == 401 or e.response.status_code == 403:
                        logging.error(f"[{username}] Otentikasi pengambilan halaman pesanan gagal. Cookie/CSRF-Token Anda mungkin tidak valid atau kedaluwarsa.")
                        logging.error(f"[{username}] >>> HARAP PERBARUI SECARA MANUAL 'YOUR_FIVERR_COOKIE_STRING' DAN 'YOUR_X_CSRF_TOKEN_VALUE' UNTUK AKUN INI! <<<")
                    else:
                        logging.error(f"[{username}] Terjadi kesalahan lain dengan permintaan HTTP GET.")

                logging.error(f"[{username}] Error mengambil {url}: {error_message}")
                whatsapp_msg = (
                    f"🚨 *Kesalahan Notifikasi Fiverr untuk {username}* 🚨\n"
                    f"Waktu: {get_current_time_formatted()}\n"
                    f"Gagal mengambil halaman pesanan dari {url}. Mungkin karena Captcha, Cookie, atau CSRF Token tidak valid.\n"
                    f"{status_code_info}"
            f"Silakan perbarui Cookie dan CSRF Token secara manual di konfigurasi."
                )
                await send_whatsapp_notification(account_config, whatsapp_msg)
            except Exception as e:
                logging.error(f"[{username}] Terjadi kesalahan tak terduga saat mengambil {url}: {e}")
                whatsapp_msg = (
                    f"🚨 *Kesalahan Notifikasi Fiverr untuk {username}* 🚨\n"
                    f"Waktu: {get_current_time_formatted()}\n"
                    f"Terjadi kesalahan tak terduga saat mengambil halaman pesanan dari {url}.\n"
                    f"Detail Error: {e}"
                )
                await send_whatsapp_notification(account_config, whatsapp_msg)

        # Perbarui set pesanan yang sudah dilihat untuk siklus berikutnya
        previously_seen_orders[username].update(current_order_ids)

        await asyncio.sleep(interval_seconds)

# --- NEW: Process and Reply to New Messages (Consolidated Logic) ---
async def process_new_message_and_reply(account_config, sender_username, initial_message_body, conversation_id, notification_id):
    username = account_config["USERNAME"]

    # Ensure conversation_history structure exists for this conversation
    if username not in conversation_history:
        conversation_history[username] = {}
    if conversation_id not in conversation_history[username]:
        conversation_history[username][conversation_id] = {"messages": [], "offer_status": "none", "forbidden_keyword_warned": False}
    
    current_offer_status = conversation_history[username][conversation_id]["offer_status"]
    current_forbidden_keyword_warned = conversation_history[username][conversation_id]["forbidden_keyword_warned"]
    
    # Trim initial_message_body for easier regex matching (remove leading/trailing whitespace)
    trimmed_message_body = initial_message_body.strip()

    # --- Scenario 1: Buyer has filled out the custom offer format ---
    if current_offer_status == "template_sent" and CUSTOM_OFFER_FILLED_REGEX.search(trimmed_message_body):
        logging.info(f"[{username}] Buyer has submitted custom offer details for {sender_username}! Sending to admin via WhatsApp.")
        whatsapp_admin_msg = (
            f"📥 *Custom Offer Details Received for {username.upper()}!* 📥\n"
            f"Waktu: {get_current_time_formatted()}\n"
            f"Dari Pembeli: {sender_username}\n"
            f"Pesan Pembeli (Detail Penawaran Khusus):\n"
            f"----------------------------------------\n"
            f"{initial_message_body}\n" # Send original, untrimmed message
            f"----------------------------------------\n"
            f"Link Konversasi: https://www.fiverr.com/inbox/conversation/{conversation_id}\n"
            f"Segera buat penawaran khusus di Fiverr!"
        )
        await send_whatsapp_notification(account_config, whatsapp_admin_msg, to_admin=True)

        wait_message = "Terima kasih banyak atas detailnya! Saya akan segera membuat penawaran khusus untuk Anda. Mohon tunggu sekitar 15-30 menit, saya akan segera kembali dengan penawaran terbaik."
        await send_inbox_message(account_config, sender_username, wait_message, conversation_id)
        
        conversation_history[username][conversation_id]["offer_status"] = "none" # Reset status
        logging.info(f"[{username}] Sent 'wait' message to {sender_username} and reset offer_status to 'none'.")
        return # Stop further processing for this message

    # --- Scenario 2: Buyer confirms custom offer after it was proposed ---
    elif current_offer_status == "proposed" and BUYER_AFFIRM_CUSTOM_OFFER_REGEX.search(trimmed_message_body):
        logging.info(f"[{username}] Buyer confirmed custom offer for {sender_username}. Sending template.")
        
        wait_message = "Baik! Mohon tunggu sebentar ya, saya akan siapkan format untuk detail penawaran khusus Anda. Saya akan segera kembali dalam 15-30 menit."
        await send_inbox_message(account_config, sender_username, wait_message, conversation_id)
        
        # Give a small delay before sending the template (e.g., 2 seconds)
        await asyncio.sleep(2) 

        # Send the custom offer request format
        await send_inbox_message(account_config, sender_username, CUSTOM_OFFER_REQUEST_FORMAT, conversation_id)
        logging.info(f"[{username}] Custom offer request format sent to {sender_username}.")
        
        conversation_history[username][conversation_id]["offer_status"] = "template_sent" # Update status
        logging.info(f"[{username}] offer_status set to 'template_sent' for {sender_username}.")
        return # Stop further processing for this message

    # --- Scenario 3: Buyer asks for template again after template sent (simple keywords) ---
    elif current_offer_status == "template_sent" and \
         ("mana formatnya" in trimmed_message_body.lower() or \
          "ulang template" in trimmed_message_body.lower() or \
          "kirim lagi formatnya" in trimmed_message_body.lower() or \
          "where is the format" in trimmed_message_body.lower() or \
          "resend template" in trimmed_message_body.lower() or \
          "send format again" in trimmed_message_body.lower()):
        logging.info(f"[{username}] Buyer asked to resend custom offer template for {sender_username}. Resending.")
        await send_inbox_message(account_config, sender_username, CUSTOM_OFFER_REQUEST_FORMAT, conversation_id)
        logging.info(f"[{username}] Custom offer request format resent to {sender_username}.")
        # The offer_status remains "template_sent" as we're still waiting for them to fill it.
        
        # After resending template, let Gemini still answer their *current* question
        # This will regenerate the full conversation context for Gemini to respond to
        conversation_details, full_conversation_text_for_gemini = await get_conversation_details(account_config, sender_username)
        if conversation_details and full_conversation_text_for_gemini:
            prompt_to_gemini = full_conversation_text_for_gemini
            # Call Gemini, ensuring it knows not to re-propose the offer
            gemini_response_text = await generate_gemini_response(
                prompt_to_gemini,
                username,
                sender_username,
                conversation_id,
                current_forbidden_keyword_warned
            )
            if gemini_response_text and not gemini_response_text.startswith("CUSTOM_OFFER_PROPOSE:"):
                # Filter out any lingering custom offer proposal text from Gemini if it accidentally included it
                clean_gemini_response = gemini_response_text.replace("CUSTOM_OFFER_PROPOSE:", "").strip()
                logging.info(f"[{username}] Generated follow-up reply for {sender_username}: '{clean_gemini_response[:50]}...'")
                await send_inbox_message(account_config, sender_username, clean_gemini_response, conversation_id)
            else:
                logging.info(f"[{username}] Gemini's follow-up response was empty or tried to propose offer again. Skipping immediate follow-up after resending template.")
        return # Stop further processing for this message


    # --- Scenario 4: Regular message / Initial custom offer proposal from Gemini ---
    # If none of the above specific custom offer flow scenarios match, proceed with regular Gemini response.
    conversation_details, full_conversation_text_for_gemini = await get_conversation_details(account_config, sender_username)
    
    if conversation_details and full_conversation_text_for_gemini:
        prompt_to_gemini = full_conversation_text_for_gemini
        logging.info(f"[{username}] Mengirim prompt ke Gemini:\n{prompt_to_gemini}")

        # Determine if the current message contains forbidden keywords
        current_message_contains_forbidden_keyword = False
        for keyword_pattern in FIVERR_FORBIDDEN_KEYWORDS:
            if re.search(keyword_pattern, trimmed_message_body.lower()):
                current_message_contains_forbidden_keyword = True
                break

        # Pass the current message body to identify if a warning is needed
        gemini_response_text = await generate_gemini_response(
            prompt_to_gemini,
            username,
            sender_username,
            conversation_id,
            current_forbidden_keyword_warned
        )
        
        if gemini_response_text:
            # Check if the generated response is a forbidden keyword warning
            is_a_forbidden_keyword_warning_response = "all communication and project details" in gemini_response_text.lower() and \
                                                    "must stay right here on fiverr" in gemini_response_text.lower()
            
            # --- Logic to update or reset the forbidden_keyword_warned flag ---
            if is_a_forbidden_keyword_warning_response and not current_forbidden_keyword_warned:
                # This is the first time we're sending this warning for this conversation
                conversation_history[username][conversation_id]["forbidden_keyword_warned"] = True
                logging.info(f"[{username}] Set 'forbidden_keyword_warned' to True for conversation {conversation_id} with {sender_username} (first warning).")
            elif not current_message_contains_forbidden_keyword and current_forbidden_keyword_warned:
                # Gemini generated a normal response (because the *current* message was clean),
                # AND there was a prior warning. Reset the flag.
                conversation_history[username][conversation_id]["forbidden_keyword_warned"] = False
                logging.info(f"[{username}] Reset 'forbidden_keyword_warned' to False for conversation {conversation_id} with {sender_username} (current message was clean).")
            # If the message contains forbidden keywords again AND a warning was already given,
            # the flag remains True, and Gemini will still give the warning due to `current_message_contains_forbidden_keyword` logic in generate_gemini_response.


            if gemini_response_text.startswith("CUSTOM_OFFER_PROPOSE:") and current_offer_status == "none":
                logging.info(f"[{username}] Gemini recommended proposing a custom offer choice.")
                # Extract the actual message after the tag
                message_to_send_to_buyer = gemini_response_text.replace("CUSTOM_OFFER_PROPOSE:", "").strip()
                
                await send_inbox_message(account_config, sender_username, message_to_send_to_buyer, conversation_id)
                logging.info(f"[{username}] Custom offer choice message sent to {sender_username}.")
                
                conversation_history[username][conversation_id]["offer_status"] = "proposed" # Update status
                logging.info(f"[{username}] offer_status set to 'proposed' for {sender_username}.")

            else:
                final_response_to_send = gemini_response_text.replace("CUSTOM_OFFER_PROPOSE:", "").strip()
                if final_response_to_send:
                    logging.info(f"[{username}] Generated auto-reply for {sender_username}: '{final_response_to_send[:50]}...'")
                    await send_inbox_message(account_config, sender_username, final_response_to_send, conversation_id)
                    logging.info(f"[{username}] Balasan Gemini berhasil dikirim ke Fiverr untuk {sender_username}.")
                else:
                    logging.warning(f"[{username}] Gemini generated empty response after removing special tag. Skipping sending.")

            conversion_payload = {"adv":"9di75e4","ref":"https://www.fiverr.com/inbox/","upv":"1.1.0","paapi":"1","pixel_ids":["9yhrkuj"]}
            await send_realtime_conversion(username, conversion_payload)
        else:
            logging.info(f"[{username}] Reply skipped due to Gemini failed to respond (e.g., empty response).")
    else:
        logging.warning(f"[{username}] Gagal mengambil detail percakapan atau tidak ada pesan untuk {sender_username}. Melewatkan auto-reply.")
        return

# --- NEW FUNCTION: UPDATE FIVERR GIG ---
async def update_fiverr_gig(account_config, gig_data, new_title, new_tags):
    username = account_config["USERNAME"]
    cookie_string = account_config["YOUR_FIVERR_COOKIE_STRING"]
    x_csrf_token = account_config["YOUR_X_CSRF_TOKEN_VALUE"]

    original_gig_title = gig_data.get('title')
    gig_slug = gig_data.get('slug')
    gig_id = gig_data.get('gig_id')

    if not gig_slug or not gig_id:
        logging.error(f"[{username}] Cannot update gig '{original_gig_title}': Missing gig slug or ID.")
        return False

    update_url = FIVERR_GIG_UPDATE_BASE_URL.format(username=username, gig_slug=gig_slug)
    logging.info(f"[{username}] Attempting to update gig '{original_gig_title}' at: {update_url}")

    # Helper function to recursively add nested dictionary/list items to payload
    def add_nested_payload(prefix, data, current_payload):
        if isinstance(data, dict):
            for k, v in data.items():
                new_prefix = f"{prefix}[{k}]"
                if isinstance(v, (dict, list)):
                    add_nested_payload(new_prefix, v, current_payload)
                else:
                    current_payload[new_prefix] = str(v)
        elif isinstance(data, list):
            for i, item in enumerate(data):
                new_prefix = f"{prefix}[{i}]"
                if isinstance(item, (dict, list)):
                    add_nested_payload(new_prefix, item, current_payload)
                else:
                    current_payload[new_prefix] = str(item)

    payload = {
        'gig[title]': new_title,
        'gig[category_id]': str(gig_data.get('category_id', '')), # Ensure string
        'gig[sub_category_id]': str(gig_data.get('sub_category_id', '')), # Ensure string
        'gig[service_type_id]': str(gig_data.get('service_type_id', '')), # Ensure string
        'gig[tag_list]': new_tags,
        'gig[active_packages_count]': str(len(gig_data.get('packages', []))), # Ensure string
        'gig[description]': gig_data.get('description', ''),
        '_method': 'patch',
        'authenticity_token': x_csrf_token,
        'current_tab': 'general',
        'current_step': 'general',
    }

    # Dynamically add packages
    for p_idx, pkg in enumerate(gig_data.get('packages', [])):
        base_pkg_key = f'gig[packages][{p_idx + 1}]' # Fiverr often uses 1-based indexing for packages
        payload[f'{base_pkg_key}[title]'] = pkg.get('title', '')
        payload[f'{base_pkg_key}[description]'] = pkg.get('description', '')
        payload[f'{base_pkg_key}[duration]'] = str(pkg.get('duration', '')) # Use 'duration' from parsed data
        payload[f'{base_pkg_key}[price]'] = str(pkg.get('price', '')).replace('$', '')

        # Recursively add package content (e.g., gig[packages][1][content][613][active])
        if 'content' in pkg and isinstance(pkg['content'], dict):
            for content_id, content_data in pkg['content'].items():
                if isinstance(content_data, dict):
                    add_nested_payload(f'{base_pkg_key}[content][{content_id}]', content_data, payload)

    # Dynamically add gig_items_attributes
    gig_items_attrs = gig_data.get('gig_items_attributes', {})
    # If gig_items_attributes is a list of dictionaries, convert it to a dictionary keyed by 'id'
    if isinstance(gig_items_attrs, list): 
        temp_dict = {}
        for item in gig_items_attrs:
            item_id = item.get('id')
            if item_id:
                temp_dict[str(item_id)] = item
        gig_items_attrs = temp_dict
    
    # Process gig_items_attributes
    if isinstance(gig_items_attrs, dict):
        for attr_key, attr_val in gig_items_attrs.items():
            add_nested_payload(f'gig[gig_items_attributes][{attr_key}]', attr_val, payload)

    # Dynamically add requirements (e.g., gig[requirements][0][r_id])
    if 'requirements' in gig_data and isinstance(gig_data['requirements'], list):
        for req_idx, req_item in enumerate(gig_data['requirements']):
            add_nested_payload(f'gig[requirements][{req_idx}]', req_item, payload)

    # Add other dynamic attributes if they were present in the initial fetch
    # These typically have keys that are hashes or special IDs,
    # and their values are lists or strings.
    # The `get_fiverr_gigs_data` needs to capture these into `gig_data` under a key like `dynamic_attributes`
    # For now, keeping the example hardcoded ones from your initial prompt
    # for the 'sql' gig, with a check for the title.
    if "sql" in original_gig_title.lower():
        payload['604dbc40608b270017462b11[0]'] = 'centralized_database'
        payload['604dbc40608b270017462b11[1]'] = 'distributed_database'
        payload['604dbc40608b270017462b11[2]'] = 'object_oriented_database'
        payload['604dbc40608b270017462b11[3]'] = 'relational_database'
        payload['604dbc9a761fec001836468d[0]'] = 'ms_sql'
        payload['604dbc9a761fec001836468d[1]'] = 'mysql'
        payload['604dbc9a761fec001836468d[2]'] = 'sqlite'
        payload['604dbc9a761fec001836468d[3]'] = 'sql_server'
        payload['604dbcfb394d0b0014826758[0]'] = 'sql'
        payload['604dbcfb394d0b0014826758[1]'] = 'ddl'
        payload['604dbcfb394d0b0014826758[2]'] = 'dml'
        payload['604dbcfb394d0b0014826758[3]'] = 'er_diagrams'
        
        # Static requirements from your example (if not already captured by dynamic `gig[requirements]`)
        # This part might duplicate if requirements are already in gig_data['requirements']
        # This should ideally be handled by the 'requirements' extraction in get_fiverr_gigs_data
        # but including it for compatibility with your provided example if that extraction fails.
        # It's better to avoid hardcoding if possible.
        if not gig_data.get('requirements'): # Only add if not already dynamically added
            payload['gig[requirements][][r_id]'] = '684c0582c7d3c99ec8ff715d'
            payload['gig[requirements][][buyable_id]'] = '434888963'
            payload['gig[requirements][][buyable_type]'] = 'Gig'
            payload['gig[requirements][][type]'] = 'free_text'
            payload['gig[requirements][][body]'] = 'can you help me with ...'
            payload['gig[requirements][][is_mandatory]'] = 'true'
            payload['gig[requirements][][sort_order]'] = '0'

    payload['gig[attachments_tag_updated]'] = 'false'

    headers = {
        "User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36",
        "Accept": "text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9",
        "Accept-Language": "en-US,en;q=0.9,id;q=0.8",
        "Referer": f"https://www.fiverr.com/users/{username}/manage_gigs/{gig_slug}/edit",
        "Origin": "https://www.fiverr.com",
        "Cookie": cookie_string,
        "X-CSRF-Token": x_csrf_token,
        "X-Requested-With": "XMLHttpRequest",
        "Content-Type": "application/x-www-form-urlencoded" # Important for form data
    }

    try:
        logging.info(f"[{username}] Sending gig update request for '{original_gig_title}'...")
        # requests.post with data=payload sends application/x-www-form-urlencoded
        response = await asyncio.to_thread(requests.post, update_url, headers=headers, data=payload, allow_redirects=False)
        response.raise_for_status()

        if response.status_code == 200: # Fiverr often returns 200 even for redirects after successful update
            logging.info(f"[{username}] Successfully updated gig '{original_gig_title}' to new title '{new_title}'.")
            return True
        else:
            logging.error(f"[{username}] Failed to update gig '{original_gig_title}'. Status: {response.status_code}. Response: {response.text[:500]}...")
            return False

    except requests.exceptions.RequestException as e:
        logging.error(f"[{username}] Error updating gig '{original_gig_title}': {e}")
        if hasattr(e, 'response') and e.response is not None:
            logging.error(f"[{username}] Update Gig API Status Code: {e.response.status_code}")
            logging.error(f"[{username}] Update Gig API Response Body: {e.response.text}")
        return False
    except Exception as e:
        logging.error(f"[{username}] An unexpected error occurred during gig update: {e}")
        return False

# --- NEW FUNCTION: DELETE WHATSAPP CHAT HISTORY ---
async def delete_whatsapp_chat(account_config, chat_id):
    whatsapp_api_url_base = account_config.get("WHATSAPP_API_URL").split("/sendText")[0] # Get base URL
    delete_url = f"{whatsapp_api_url_base}/chats/{chat_id}" # Construct delete URL
    whatsapp_auth_user = account_config.get("WHATSAPP_AUTH_USER")
    whatsapp_auth_pass = account_config.get("WHATSAPP_AUTH_PASS")

    if not (delete_url and whatsapp_auth_user and whatsapp_auth_pass):
        logging.warning(f"[{account_config['USERNAME']}] WhatsApp API credentials or base URL missing for deletion. Skipping.")
        return False

    headers = {
        "accept": "*/*"
    }

    try:
        logging.info(f"[{account_config['USERNAME']}] Attempting to delete WhatsApp chat history for {chat_id}...")
        response = await asyncio.to_thread(
            requests.delete,
            delete_url,
            headers=headers,
            auth=(whatsapp_auth_user, whatsapp_auth_pass),
            verify=False # Set to True if you have a valid SSL certificate for your WhatsApp API
        )
        response.raise_for_status()

        logging.info(f"[{account_config['USERNAME']}] WhatsApp chat history for {chat_id} deleted successfully. Status: {response.status_code}")
        return True
    except requests.exceptions.RequestException as e:
        logging.error(f"[{account_config['USERNAME']}] Error deleting WhatsApp chat history for {chat_id}: {e}")
        if hasattr(e, 'response') and e.response is not None:
            logging.error(f"[{account_config['USERNAME']}] WhatsApp API Status Code: {e.response.status_code}")
            logging.error(f"[{account_config['USERNAME']}] WhatsApp API Response Body: {e.response.text}")
        return False
    except Exception as e:
        logging.error(f"[{account_config['USERNAME']}] An unexpected error occurred during WhatsApp chat deletion: {e}")
        return False

# --- NEW FUNCTION: MONITOR WHATSAPP FOR 'UP' COMMAND ---
async def monitor_whatsapp_for_up_command(account_config, interval_seconds=10):
    username = account_config["USERNAME"]
    admin_chat_id = account_config.get("WHATSAPP_ADMIN_CHAT_ID")
    # Ensure this URL is generic enough or specifically for the admin chat
    whatsapp_api_url_base = "http://103.85.60.82:3001/api/default/chats/" 

    auth_user = account_config.get("WHATSAPP_AUTH_USER", "ridwan")
    auth_pass = account_config.get("WHATSAPP_AUTH_PASS", "m0h4mm4d")

    if not admin_chat_id:
        logging.warning(f"[{username}] WhatsApp Admin Chat ID not configured. Cannot monitor for 'Up' command.")
        return

    fetch_messages_url = f"{whatsapp_api_url_base}{admin_chat_id}/messages?downloadMedia=false&limit=1"
    logging.info(f"[{username}] Monitoring WhatsApp for 'Up' command in chat {admin_chat_id}...")

    while True:
        try:
            response = await asyncio.to_thread(
                requests.get,
                fetch_messages_url,
                auth=(auth_user, auth_pass),
                verify=False  # karena IP lokal, self-signed TLS
            )
            response.raise_for_status()
            messages = response.json()

            if messages and isinstance(messages, list) and len(messages) > 0:
                latest_message = messages[0]
                message_body = latest_message.get("body", "").strip()
                reply_to = latest_message.get("replyTo", {})

                if message_body.lower() == "up" and reply_to:
                    reply_body = reply_to.get("body", "")
                    if "Fiverr Gig Optimization Report" in reply_body:
                        logging.info(f"[{username}] 'Up' command detected in reply to optimization report")

                        gig_title_match = re.search(r"--- Gig: \*(.*?)\*", reply_body)
                        new_title_match = re.search(r"JUDUL_BARU_1:\s*(.*?)(?:\n|$)", reply_body) # Adjusted regex
                        new_tags_match = re.search(r"TAG_BARU:\s*(.*?)(?:\n|$)", reply_body) # Adjusted regex

                        if gig_title_match and new_title_match and new_tags_match:
                            original_title = gig_title_match.group(1).strip()
                            new_title = new_title_match.group(1).strip()
                            new_tags = new_tags_match.group(1).strip()

                            # Temukan Gig berdasarkan judul asli dari SELLER_GIGS_INFO
                            target_gig = next(
                                (gig for gig in SELLER_GIGS_INFO.get(username, []) if gig.get('title') == original_title),
                                None
                            )

                            if target_gig:
                                update_success = await update_fiverr_gig(
                                    account_config,
                                    target_gig, # Pass the full gig_data for dynamic payload
                                    new_title,
                                    new_tags
                                )

                                if update_success:
                                    # Notifikasi sukses
                                    success_msg = (
                                        f"✅ *Fiverr Gig Updated Successfully!* ✅\n"
                                        f"Waktu: {get_current_time_formatted()}\n"
                                        f"Akun: {username}\n"
                                        f"Gig Lama: '{original_title}'\n"
                                        f"Judul Baru: '{new_title}'\n"
                                        f"Tag Baru: {new_tags}\n"
                                        f"Perubahan telah diterapkan di Fiverr."
                                    )
                                    await send_whatsapp_notification(account_config, success_msg, to_admin=True)

                                    # DELETE chat history after successful update
                                    await delete_whatsapp_chat(account_config, admin_chat_id)
                                    logging.info(f"[{username}] Chat WhatsApp berhasil dihapus setelah pembaruan gig.")

                                else:
                                    logging.warning(f"[{username}] Gagal memperbarui gig '{original_title}'.")
                            else:
                                logging.warning(f"[{username}] Tidak ditemukan gig dengan judul '{original_title}' dalam SELLER_GIGS_INFO. Pastikan data sudah dimuat dengan benar.")
                        else:
                            logging.warning(f"[{username}] Gagal mengekstrak judul baru atau tag baru dari balasan WhatsApp. Format tidak sesuai.")
                else:
                    # If the latest message is not "up" in reply to a report, do nothing and continue looping.
                    pass 

        except Exception as e:
            logging.error(f"[{username}] Error in WhatsApp monitor loop: {e}")

        await asyncio.sleep(interval_seconds) # Keep looping and checking


# --- FUNCTION STAGE 2: RECEIVE WEBSOCKET NOTIFICATIONS ---
async def receive_fiverr_notifications(websocket_url, auth_token, account_config):
    username = account_config["USERNAME"]
    user_id = account_config["USER_ID"]

    logging.info(f"\n[{username}] Mencoba terhubung ke WebSocket: {websocket_url}")

    while True: # This loop will keep trying WebSocket connection
        try:
            async with websockets.connect(websocket_url, ssl=ssl_context) as websocket:
                logging.info(f"[{username}] Berhasil terhubung ke WebSocket Fiverr.")

                auth_message = {
                    "type": "auth",
                    "token": auth_token,
                    "username": username
                }
                await websocket.send(json.dumps(auth_message))
                logging.info(f"[{username}] Pesan autentikasi WebSocket dikirim: {json.dumps(auth_message)}")

                while True: # Main loop for receiving messages AS LONG AS connection is alive
                    try:
                        message = await websocket.recv()
                        logging.info(f"\n[{username}] --- Notifikasi Diterima ---")

                        try:
                            parsed_message = json.loads(message)

                            if "eventType" in parsed_message:
                                if parsed_message["eventType"] == "inbox_message_realtime_notification":
                                    logging.info(f"\n[{username}] >>>> REAL-TIME NOTIFICATION FROM: {parsed_message.get('display_name', 'Unknown')} ({parsed_message.get('username', 'N/A')})")
                                    logging.info(f"[{username}]     Message Preview: {parsed_message.get('preview_text', 'N/A')}")
                                    logging.info(f"[{username}]     Read Status: {'Read' if parsed_message.get('is_read') else 'Unread'}")
                                    logging.info(f"[{username}]     Conversation URL: https://www.fiverr.com{parsed_message.get('url', '')}")

                                    sender_username_ws = parsed_message.get('username', 'N/A')
                                    message_body_ws = parsed_message.get('preview_text', '')
                                    notification_id = parsed_message.get('id', f"inbox_{user_id}_{int(time.time() * 1000)}")
                                    conversation_id_ws = parsed_message.get('channel_id')

                                    if sender_username_ws.lower() != username.lower():
                                        logging.info(f"[{username}] Menerima pesan masuk dari pengguna lain melalui WebSocket.")

                                        whatsapp_msg = (
                                            f"💬 *PESAN BARU DI FIBERR UNTUK {username.upper()}!* 💬\n"
                                            f"Waktu: {get_current_time_formatted()}\n"
                                            f"Dari: {parsed_message.get('display_name', 'Unknown')}\n"
                                            f"Pesan: {message_body_ws}\n"
                                            f"Link Konversasi: https://www.fiverr.com{parsed_message.get('url', '')}"
                                        )
                                        await send_whatsapp_notification(account_config, whatsapp_msg)

                                        # Call the new processing function
                                        await process_new_message_and_reply(account_config, sender_username_ws, message_body_ws, conversation_id_ws, notification_id)
                                    else:
                                        logging.info(f"[{username}] Message received from self (via WebSocket). Not processing for auto-reply.")

                                elif parsed_message["eventType"] == "inbox_message_received":
                                    msg_data = parsed_message.get("message", {})
                                    sender_username = msg_data.get('sender', 'N/A')
                                    message_body = msg_data.get('body', '')
                                    conversation_id_inbox = msg_data.get('channelId')
                                    notification_id_inbox = f"inbox_{user_id}_{conversation_id_inbox}_{int(time.time() * 1000)}" if conversation_id_inbox else f"inbox_{user_id}_{int(time.time() * 1000)}"

                                    logging.info(f"\n[{username}] **** NEW MESSAGE RECEIVED (Detailed) ****")
                                    logging.info(f"[{username}]     Sender: {msg_data.get('senderDisplayName', sender_username)}")
                                    logging.info(f"[{username}]     Message Content: {message_body}")

                                    if sender_username.lower() != username.lower():
                                        logging.info(f"[{username}] Menerima pesan masuk dari pengguna lain.")

                                        whatsapp_msg = (
                                            f"💬 *PESAN BARU DI FIBERR UNTUK {username.upper()}!* 💬\n"
                                            f"Waktu: {get_current_time_formatted()}\n"
                                            f"Dari: {msg_data.get('senderDisplayName', sender_username)}\n"
                                            f"Pesan: {message_body}\n"
                                            f"Link Konversasi: https://www.fiverr.com/inbox/conversation/{conversation_id_inbox}" # More specific link
                                        )
                                        await send_whatsapp_notification(account_config, whatsapp_msg)

                                        # Call the new processing function
                                        await process_new_message_and_reply(account_config, sender_username, message_body, conversation_id_inbox, notification_id_inbox)
                                    else:
                                        logging.info(f"[{username}] Message received from self. Not processing for auto-reply.")
                                else:
                                    logging.info(f"[{username}] Unknown eventType notification: {parsed_message['eventType']}")
                            elif "type" in parsed_message and parsed_message["type"] == "ping":
                                await websocket.send(json.dumps({"type": "pong"}))
                            else:
                                pass

                        except json.JSONDecodeError:
                            logging.error(f"[{username}] Message is not valid JSON format. May be plain text or binary.")

                    except websockets.exceptions.ConnectionClosedOK:
                        logging.warning(f"[{username}] WebSocket connection closed normally. Will attempt to reconnect.")
                        break
                    except websockets.exceptions.ConnectionClosedError as e:
                        logging.error(f"[{username}] WebSocket connection closed with error: {e}. Will attempt to reconnect.")
                        break
                    except Exception as e:
                        logging.error(f"[{username}] An error occurred while receiving message: {e}. Will attempt to reconnect.")
                        break

        except websockets.exceptions.InvalidURI as e:
            logging.error(f"[{username}] Error: Invalid WebSocket URL: {e}. Will not retry for this account.")
            whatsapp_msg = (
                f"🚨 *Fiverr Notification Error for {username}* 🚨\n"
                f"Waktu: {get_current_time_formatted()}\n"
                f"URL WebSocket tidak valid: {e}. Tidak akan mencoba lagi untuk akun ini."
            )
            await send_whatsapp_notification(account_config, whatsapp_msg)
            return False
        except websockets.exceptions.InvalidStatusCode as e:
            logging.error(f"[{username}] Connection error: Invalid status code: {e.status_code}. Headers: {e.headers}. Retrying in 5 seconds...")
            whatsapp_msg = (
                f"🚨 *Fiverr Notification Error for {username}* 🚨\n"
                f"Waktu: {get_current_time_formatted()}\n"
                f"Kesalahan koneksi WebSocket: Status Code {e.status_code}.\n"
                f"Mungkin karena Captcha, Cookie, atau CSRF Token tidak valid.\n"
                f"Silakan perbarui Cookie dan CSRF Token secara manual di konfigurasi."
            )
            await send_whatsapp_notification(account_config, whatsapp_msg)
            await asyncio.sleep(5)
        except ConnectionRefusedError:
            logging.error(f"[{username}] Connection refused. Ensure WebSocket server is running and URL is correct. Retrying in 5 seconds...")
            whatsapp_msg = (
                f"🚨 *Fiverr Notification Error for {username}* 🚨\n"
                f"Waktu: {get_current_time_formatted()}\n"
                f"Koneksi WebSocket ditolak. Pastikan URL dan server benar. Mencoba lagi..."
            )
            await send_whatsapp_notification(account_config, whatsapp_msg)
            await asyncio.sleep(5)
        except Exception as e:
            logging.error(f"[{username}] A general error occurred while connecting to WebSocket: {e}. Retrying in 5 seconds...")
            whatsapp_msg = (
                f"🚨 *Fiverr Notification Error for {username}* 🚨\n"
                f"Waktu: {get_current_time_formatted()}\n"
                f"Terjadi kesalahan umum saat menghubungkan ke WebSocket: {e}.\n"
                f"Mencoba lagi dalam 5 detik."
            )
            await send_whatsapp_notification(account_config, whatsapp_msg)
            await asyncio.sleep(5)
    return True

# --- MODIFIED INBOX CHECKING FUNCTION ---
async def check_and_reply_inbox_periodically(account_config, interval_seconds):
    username = account_config["USERNAME"]
    # RUN IMMEDIATELY WITHOUT WAITING FOR THE FIRST DELAY
    logging.info(f"\n[{username}] --- Starting immediate inbox check ---")
    await check_inbox_and_reply(account_config)

    # Then run periodically
    while True:
        await asyncio.sleep(interval_seconds)
        await check_inbox_and_reply(account_config)

async def check_inbox_and_reply(account_config):
    username = account_config["USERNAME"]
    user_id = account_config["USER_ID"]

    logging.info(f"\n[{username}] --- Manually checking inbox ---")
    contacts_data = await get_inbox_contacts(account_config)
    if contacts_data:
        contacts_list = []
        if isinstance(contacts_data, list):
            contacts_list = contacts_data
        elif isinstance(contacts_data, dict) and 'contacts' in contacts_data:
            contacts_list = contacts_data['contacts']
        else:
            logging.error(f"[{username}] Unknown contact data format. Skipping processing.")
            whatsapp_msg = (
                f"⚠️ *Fiverr Data Error for {username}* ⚠️\n"
                f"Waktu: {get_current_time_formatted()}\n"
                f"Format data kontak inbox tidak dikenal. Melewatkan pemrosesan."
            )
            await send_whatsapp_notification(account_config, whatsapp_msg)
            return

        for contact in contacts_list:
            recent_sender = contact.get('recentMessageSender', '').lower()
            excerpt_text = contact.get('excerpt', '')
            recipient_username_for_history = contact.get('username') # The actual other user's username
            conversation_id = contact.get('id', contact.get('channelId'))
            notification_id = f"inbox_{user_id}_{conversation_id}_{int(time.time() * 1000)}" if conversation_id else f"inbox_{user_id}_{int(time.time() * 1000)}"

            # Check if the message is from another user, has content, and is unread
            if recent_sender != username.lower() and excerpt_text and not contact.get('isRead', False):
                logging.info(f"[{username}] Detecting message from '{contact.get('username', 'N/A')}' (via Inbox check)")
                logging.info(f"[{username}]     Preview: '{excerpt_text}'")

                whatsapp_msg = (
                    f"💬 *PESAN BARU DI FIBERR UNTUK {username.upper()}!* 💬\n"
                    f"Waktu: {get_current_time_formatted()}\n"
                    f"Dari: {contact.get('username', 'N/A')}\n"
                    f"Pesan: {excerpt_text}\n"
                    f"Link Konversasi: https://www.fiverr.com/inbox/conversation/{contact.get('id', '')}"
                )
                await send_whatsapp_notification(account_config, whatsapp_msg)

                # Call the new processing function
                await process_new_message_and_reply(account_config, recipient_username_for_history, excerpt_text, conversation_id, notification_id)
            else:
                pass # Message is from self or already read, or no content

        logging.info(f"[{username}] Selesai memeriksa inbox. Tidak ada pesan baru yang menunggu dibalas secara otomatis.")
    else:
        logging.error(f"[{username}] Failed to retrieve contact data.")
        whatsapp_msg = (
            f"🚨 *Fiverr Notification Error for {username}* 🚨\n"
            f"Waktu: {get_current_time_formatted()}\n"
            f"Gagal mendapatkan data kontak inbox. Mungkin karena Captcha, Cookie, atau CSRF Token tidak valid.\n"
            f"Silakan perbarui Cookie dan CSRF Token secara manual di konfigurasi."
        )
        await send_whatsapp_notification(account_config, whatsapp_msg)

# --- FUNCTIONS FROM COBA2.PY ---
async def get_fiverr_gigs_data(account_config):
    """
    Mengambil data gigs dari Fiverr dengan mengekstraknya dari HTML yang diterima.

    Args:
        account_config (dict): Konfigurasi akun yang berisi cookie_string dan csrf_token.

    Returns:
        list or None: Daftar data gigs dalam format JSON jika berhasil, None jika gagal.
    """
    username = account_config["USERNAME"]
    cookie_string = account_config["YOUR_FIVERR_COOKIE_STRING"]
    csrf_token = account_config["YOUR_X_CSRF_TOKEN_VALUE"]

    # This URL is dynamically constructed using the username from account_config.
    # So, it will correctly fetch gigs for each configured user.
    url = f"https://www.fiverr.com/users/{username}/manage_gigs?current_filter=active&days_for_stats=14"

    headers = {
        "User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/100.0.4896.127 Safari/537.36",
        "Accept": "text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9",
        "Accept-Language": "en-US,en;q=0.9,id;q=0.8",
        "Cookie": cookie_string.strip(),
        "X-CSRF-Token": csrf_token.strip(),
        "X-Requested-With": "XMLHttpRequest",
        "Referer": f"https://www.fiverr.com/users/{username}/manage_gigs?current_filter=active&days_for_stats=14"
    }

    logging.info(f"[{username}] Mencoba mengambil konten HTML gigs dari: {url}")
    gigs_data = None

    try:
        response = await asyncio.to_thread(requests.get, url, headers=headers)
        response.raise_for_status() # Raise HTTPError for bad responses (4xx or 5xx)
        html_content = response.text

        soup = BeautifulSoup(html_content, 'html.parser')
        
        target_script_content = None
        script_tags = soup.find_all('script', type='text/javascript')
        for script in script_tags:
            if script.string and 'document.viewData' in script.string and 'results":{' in script.string:
                target_script_content = script.string
                break
        
        if target_script_content:
            match = re.search(r'document\.viewData\s*=\s*(\{.*?\})\s*;', target_script_content, re.DOTALL)
            
            if match:
                extracted_json_string = match.group(1)
                
                json_string_cleaned = (
                    extracted_json_string.replace('\\u003c', '<')
                               .replace('\\u003e', '>',)
                               .replace('\\u0026', '&')
                               .replace('\\u0022', '"')
                               .replace('\\t', '')
                               .replace('\\n', '')
                               .replace('\\r', '')
                               .replace('\\/', '/')
                )
                
                try:
                    data = json.loads(json_string_cleaned)
                    gigs_data = data.get('results', {}).get('rows', [])
                    
                    # Extract full gig details if available in this response for later updates
                    full_gigs_details = []
                    for gig_item in gigs_data:
                        gig_info = gig_item.get('gig', {})
                        if gig_info:
                            gig_id = gig_info.get('id')
                            title = gig_info.get('title')
                            slug = gig_info.get('slug')
                            category_id = gig_info.get('category', {}).get('id')
                            sub_category_id = gig_info.get('subCategory', {}).get('id')
                            service_type_id = gig_info.get('serviceType', {}).get('id')
                            description = gig_info.get('description') 
                            
                            packages_data = []
                            for pkg in gig_info.get('packages', []):
                                pkg_content = pkg.get('content', {})
                                # Recursively process nested content for dynamic IDs
                                def process_nested_content(obj):
                                    if isinstance(obj, dict):
                                        return {k: process_nested_content(v) for k, v in obj.items()}
                                    elif isinstance(obj, list):
                                        return [process_nested_content(elem) for elem in obj]
                                    else:
                                        return obj

                                packages_data.append({
                                    "title": pkg.get('title'),
                                    "description": pkg.get('description'),
                                    "duration": pkg.get('duration', {}).get('inDays'),
                                    "price": pkg.get('price', {}).get('amountInUsd'),
                                    "content": process_nested_content(pkg_content) # Capture full content with dynamic IDs
                                })
                            
                            gig_items_attributes_data = {}
                            # Capture gigItems data (which maps to gig_items_attributes)
                            gig_items = gig_info.get('gigItems', [])
                            if isinstance(gig_items, list):
                                for item in gig_items:
                                    item_id = item.get('id')
                                    if item_id:
                                        gig_items_attributes_data[str(item_id)] = item
                            elif isinstance(gig_items, dict):
                                # If gigItems is already a dict (e.g., from a different API structure), use it directly
                                gig_items_attributes_data = gig_items 
                            
                            # Additional top-level dynamic attributes that might be needed in the payload
                            additional_dynamic_attributes = {}
                            # Example: If your JSON has a field like 'gig_attributes_x': { 'key': 'value' }
                            # You'd extract them here. For the 604dbc... example, these usually
                            # come directly from the HTML form values.
                            # We'll explicitly check for the hardcoded ones if needed, but it's better to dynamically find them.
                            # For the 'sql' gig in your example, these were specific form field names.
                            # If you need to include them dynamically, you'd need to extract them from the edit page HTML.

                            # Add the essential data for SELLER_GIGS_INFO for later update.
                            full_gigs_details.append({
                                "title": title,
                                "gig_id": gig_id,
                                "slug": slug,
                                "category_id": category_id,
                                "sub_category_id": sub_category_id,
                                "service_type_id": service_type_id,
                                "description": description,
                                "packages": packages_data,
                                "gig_items_attributes": gig_items_attributes_data, # Full dynamic gig items
                                "requirements": gig_info.get('requirements'), # Capture requirements
                                "dynamic_attributes": additional_dynamic_attributes # For other top-level dynamic keys
                            })

                    logging.info(f"[{username}] Berhasil mengekstrak {len(gigs_data)} gigs dari HTML.")
                    # Update global SELLER_GIGS_INFO with full details
                    global SELLER_GIGS_INFO
                    SELLER_GIGS_INFO[username] = full_gigs_details

                except json.JSONDecodeError as e:
                    logging.error(f"[{username}] Error saat memparsing JSON dari HTML: {e}")
            else:
                logging.warning(f"[{username}] Pola JSON 'document.viewData' tidak ditemukan di dalam blok script yang teridentifikasi.")
        else:
            logging.warning(f"[{username}] Blok script yang berisi 'document.viewData' dengan data gigs tidak ditemukan di HTML.")

    except requests.exceptions.RequestException as e:
        logging.error(f"[{username}] Error saat mengambil data gigs (Request): {e}")
        whatsapp_msg = (
            f"🚨 *Fiverr Notification Error for {username}* 🚨\n"
            f"Waktu: {get_current_time_formatted()}\n"
            f"Gagal mengambil data gigs. Mungkin karena Captcha, Cookie, atau CSRF Token tidak valid.\n"
            f"Detail Error: {e}"
        )
        await send_whatsapp_notification(account_config, whatsapp_msg)
    except Exception as e:
        logging.error(f"[{username}] Terjadi kesalahan tak terduga saat berinteraksi dengan database: {e}")
        whatsapp_msg = (
            f"🚨 *Fiverr Notification Error for {username}* 🚨\n"
            f"Waktu: {get_current_time_formatted()}\n"
            f"Terjadi kesalahan tak terduga saat berinteraksi dengan database untuk gigs: {e}."
        )
        await send_whatsapp_notification(account_config, whatsapp_msg)

    return gigs_data # This returns gig statistics, not necessarily the full update payload

async def insert_gigs_into_db(gigs_data, account_config):
    """
    Memasukkan data gigs ke dalam tabel 'gigs' di database MySQL.

    Args:
        gigs_data (list): Daftar dictionary yang berisi data gig.
        account_config (dict): Konfigurasi akun untuk logging dan mendapatkan username.
    """
    username = account_config["USERNAME"]
    if not gigs_data:
        logging.info(f"[{username}] Tidak ada data gigs untuk dimasukkan ke database.")
        return

    conn = None
    cursor = None
    try:
        # Koneksi ke database
        conn = await asyncio.to_thread(
            mysql.connector.connect,
            host=DB_CONFIG['hostname'],
            user=DB_CONFIG['username'],
            password=DB_CONFIG['password'],
            database=DB_CONFIG['database']
        )
        cursor = await asyncio.to_thread(conn.cursor)

        # Query INSERT
        # Menambahkan 'username' ke daftar kolom dan nilai
        insert_query = """
        INSERT INTO gigs (title, impressions, clicks, orders_count, cancellation_rate, username)
        VALUES (%s, %s, %s, %s, %s, %s)
        ON DUPLICATE KEY UPDATE
            impressions = VALUES(impressions),
            clicks = VALUES(clicks),
            orders_count = VALUES(orders_count),
            cancellation_rate = VALUES(cancellation_rate),
            last_updated = CURRENT_TIMESTAMP;
        """

        for gig in gigs_data:
            gig_info = gig.get('gig', {})
            stats = gig.get('gigsData', [])

            title = gig_info.get('title', None)
            impressions = next((int(item['value']) for item in stats if item['type'] == 'impressions'), 0)
            clicks = next((int(item['value']) for item in stats if item['type'] == 'clicks'), 0)
            orders = next((int(item['value']) for item in stats if item['type'] == 'orders'), 0)
            cancellation_rate_str = next((item['value'] for item in stats if item['type'] == 'cancellation_rate'), '0')
            try:
                cancellation_rate = float(cancellation_rate_str)
            except ValueError:
                cancellation_rate = 0.0 # Default to 0.0 if conversion fails

            if title: # Hanya masukkan jika ada judul
                try:
                    # Menambahkan username ke tuple nilai yang dilewatkan ke execute
                    await asyncio.to_thread(cursor.execute, insert_query, (title, impressions, clicks, orders, cancellation_rate, username))
                    logging.info(f"[{username}] Berhasil memasukkan/memperbarui gig: {title}")
                except mysql.connector.Error as err:
                    logging.error(f"[{username}] Gagal memasukkan/memperbarui gig {title}: {err}")
                    await asyncio.to_thread(conn.rollback) # Rollback jika ada kesalahan pada satu insert

        await asyncio.to_thread(conn.commit) # Commit semua perubahan
        logging.info(f"[{username}] Operasi database selesai.")

    except mysql.connector.Error as err:
        logging.error(f"[{username}] Error koneksi atau operasi database: {err}")
        whatsapp_msg = (
            f"🚨 *Fiverr Database Error for {username}* 🚨\n"
            f"Waktu: {get_current_time_formatted()}\n"
            f"Error koneksi atau operasi database saat memasukkan data gigs: {err}."
        )
        await send_whatsapp_notification(account_config, whatsapp_msg)
    except Exception as e:
        logging.error(f"[{username}] Terjadi kesalahan tak terduga saat berinteraksi dengan database: {e}")
        whatsapp_msg = (
            f"🚨 *Fiverr Database Error for {username}* 🚨\n"
            f"Waktu: {get_current_time_formatted()}\n"
            f"Terjadi kesalahan tak terduga saat berinteraksi dengan database untuk gigs: {e}."
        )
        await send_whatsapp_notification(account_config, whatsapp_msg)
    finally:
        if conn and conn.is_connected():
            await asyncio.to_thread(cursor.close)
            await asyncio.to_thread(conn.close)
            logging.info(f"[{username}] Koneksi database ditutup.")

# --- NEW FUNCTION: GET LOW-PERFORMING GIGS FROM DB ---
async def get_low_performing_gigs(username, impressions_threshold=500, clicks_threshold=10):
    """
    Mengambil gig dengan impressions atau clicks rendah dari database untuk username tertentu.
    """
    conn = None
    cursor = None
    low_performing_gigs = []
    try:
        conn = await asyncio.to_thread(
            mysql.connector.connect,
            host=DB_CONFIG['hostname'],
            user=DB_CONFIG['username'],
            password=DB_CONFIG['password'],
            database=DB_CONFIG['database']
        )
        cursor = await asyncio.to_thread(conn.cursor, dictionary=True) # return rows as dictionaries

        # Query untuk memilih gig berdasarkan username dan ambang batas
        query = """
        SELECT title, impressions, clicks, orders_count
        FROM gigs
        WHERE username = %s AND (impressions < %s OR clicks < %s);
        """
        await asyncio.to_thread(cursor.execute, query, (username, impressions_threshold, clicks_threshold))
        result = await asyncio.to_thread(cursor.fetchall)

        for row in result:
            low_performing_gigs.append(row)
        
        logging.info(f"[{username}] Ditemukan {len(low_performing_gigs)} gig berkinerja rendah.")

    except mysql.connector.Error as err:
        logging.error(f"[{username}] Error saat mengambil gig berkinerja rendah dari database: {err}")
    except Exception as e:
        logging.error(f"[{username}] Kesalahan tak terduga saat mengambil gig berkinerja rendah: {e}")
    finally:
        if conn and conn.is_connected():
            await asyncio.to_thread(cursor.close)
            await asyncio.to_thread(conn.close)
    return low_performing_gigs

# --- NEW FUNCTION: GENERATE GIG OPTIMIZATION SUGGESTIONS WITH GEMINI ---
async def generate_gig_optimization_suggestions(account_username, gigs_list):
    """
    Meminta Gemini untuk memberikan saran optimasi (judul dan tag) untuk daftar gig.
    """
    if not gigs_list:
        return None

    prompt_parts = []
    # Modified prompt for shorter titles and "I will" prefix
    prompt_parts.append(f"""Anda adalah seorang pakar SEO dan marketing di Fiverr. Anda perlu memberikan saran yang sangat spesifik dan efektif untuk mengoptimalkan GIGS berikut ini agar mendapatkan impressions dan clicks yang lebih tinggi.

Untuk setiap gig yang diberikan, berikan 5 ide Judul gig baru yang sangat menarik dan berpotensi tinggi untuk meningkatkan impressions dan clicks. Ingat, setiap judul gig di Fiverr dimulai dengan "I will ". Anda HANYA perlu memberikan teks yang datang *setelah* "I will ". Setiap judul (termasuk "I will ") tidak boleh lebih dari 66 karakter. Jadi, saran Anda harus sekitar 55-60 karakter. Setiap judul harus:
- Singkat dan jelas.
- Mengandung keyword relevan.
- Menjanjikan manfaat atau hasil yang spesifik.
- Memicu rasa penasaran atau urgensi (jika relevan).

Setelah itu, berikan 5 ide Tag gig baru yang relevan dan populer (masing-masing tag dipisahkan koma).

Pastikan format output untuk setiap gig adalah:
--- GIG: [Judul Asli Gig] ---
JUDUL_BARU_1: [Saran Judul 1]
JUDUL_BARU_2: [Saran Judul 2]
JUDUL_BARU_3: [Saran Judul 3]
JUDUL_BARU_4: [Saran Judul 4]
JUDUL_BARU_5: [Saran Judul 5]
TAG_BARU: [tag1, tag2, tag3, tag4, tag5]

Jangan tambahkan teks pengantar atau penutup umum, hanya saran untuk setiap gig.
Pisahkan setiap blok saran gig dengan baris kosong.

Berikut adalah daftar gigs yang perlu dioptimalkan:
""")

    for i, gig in enumerate(gigs_list):
        prompt_parts.append(f"""
Gig #{i+1}:
Judul: "{gig['title']}"
Impressions (30 hari terakhir): {gig['impressions']}
Klik (30 hari terakhir): {gig['clicks']}
Order (30 hari terakhir): {gig['orders_count']}
""")

    prompt_text = "\n".join(prompt_parts)

    logging.info(f"[{account_username}] Meminta saran optimasi untuk {len(gigs_list)} gig dari Gemini.")

    headers = {
        'Content-Type': 'application/json',
    }
    params = {
        'key': GEMINI_API_KEY
    }

    json_data = {
        'contents': [
            {"role": "user", "parts": [{"text": prompt_text}]}
        ]
    }

    try:
        response = await asyncio.to_thread(requests.post, GEMINI_API_URL, headers=headers, params=params, json=json_data)
        response.raise_for_status()

        result = response.json()
        if result.get("candidates") and result["candidates"][0].get("content") and \
           result["candidates"][0]["content"].get("parts") and result["candidates"][0]["content"]["parts"][0].get("text"):
            generated_text = result["candidates"][0]["content"]["parts"][0]["text"]
            logging.info(f"[{account_username}] Saran Gemini diterima untuk {len(gigs_list)} gig.")
            return generated_text.strip()
        else:
            logging.error(f"[{account_username}] Gagal mendapatkan teks dari respons Gemini untuk saran gig: {result}")
            return None

    except requests.exceptions.RequestException as e:
        logging.error(f"[{account_username}] Error saat menghubungi Gemini API untuk saran gig: {e}")
        if hasattr(e, 'response') and e.response is not None:
            logging.error(f"[{account_username}] Status Code: {e.response.status_code}")
            logging.error(f"[{account_username}] Response Body: {e.response.text}")
        return None
    except Exception as e:
        logging.error(f"[{account_username}] Kesalahan tak terduga saat membuat saran optimasi gig: {e}")
        return None

# --- NEW FUNCTION: PERIODIC GIG OPTIMIZATION CHECK AND SUGGESTION ---
# Global dictionary to store the last notification time for each account's gig optimization
last_gig_optimization_notification = {}

async def periodic_gig_optimization(account_config, interval_seconds, impressions_threshold=500, clicks_threshold=10):
    username = account_config["USERNAME"]
    logging.info(f"\n[{username}] --- Starting periodic gig optimization check. Notification sent daily at 11 AM WIB. ---")

    # Initialize last_gig_optimization_notification for this account if not present
    if username not in last_gig_optimization_notification:
        last_gig_optimization_notification[username] = None # Or load from a persistent store if needed

    while True:
        now = datetime.now()
        # Define target time for notification (11:00 AM WIB)
        target_time = now.replace(hour=11, minute=0, second=0, microsecond=0)

        # Check if notification was already sent today
        notification_already_sent_today = (
            last_gig_optimization_notification[username] is not None and
            last_gig_optimization_notification[username].date() == now.date()
        )

        if not notification_already_sent_today and now >= target_time:
            logging.info(f"[{username}] Melakukan evaluasi gig untuk optimasi pada 11:00 WIB...")
            
            low_performing_gigs = await get_low_performing_gigs(username, impressions_threshold, clicks_threshold)
            
            if low_performing_gigs:
                suggestions_text = await generate_gig_optimization_suggestions(username, low_performing_gigs)

                if suggestions_text:
                    # Split the overall suggestions into individual gig blocks
                    # re.split will give ['', 'Gig Title 1', 'Suggestions for Gig 1', 'Gig Title 2', 'Suggestions for Gig 2', ...]
                    # We need to skip the first empty string and then take pairs.
                    gig_suggestion_blocks = re.split(r'--- GIG: (.*?) ---', suggestions_text, flags=re.DOTALL)
                    
                    # Iterate through the split blocks, taking title and suggestions in pairs
                    # Start from index 1 and increment by 2
                    for i in range(1, len(gig_suggestion_blocks), 2):
                        original_gig_title = gig_suggestion_blocks[i].strip()
                        suggestions_for_this_gig = gig_suggestion_blocks[i+1].strip()
                        
                        # Construct a separate WhatsApp message for each gig
                        whatsapp_admin_notification_per_gig = (
                            f"📈 *Fiverr Gig Optimization Report for {username.upper()}* 📉\n"
                            f"Waktu: {get_current_time_formatted()}\n"
                            f"--- Gig: *{original_gig_title}* ---\n"
                        )
                        
                        # Find original gig metrics from the low_performing_gigs list
                        original_gig_metrics = next((g for g in low_performing_gigs if g['title'] == original_gig_title), None)
                        if original_gig_metrics:
                            whatsapp_admin_notification_per_gig += (
                                f"  Impressions: {original_gig_metrics['impressions']}, "
                                f"Clicks: {original_gig_metrics['clicks']}, "
                                f"Orders: {original_gig_metrics['orders_count']}\n"
                            )
                        
                        whatsapp_admin_notification_per_gig += (
                            f"  Saran Gemini:\n{suggestions_for_this_gig}\n\n"
                            f"Tinjau saran ini di WhatsApp Admin Anda dan pertimbangkan untuk menerapkan perubahan di Fiverr secara manual."
                        )

                        await send_whatsapp_notification(account_config, whatsapp_admin_notification_per_gig, to_admin=True)
                        logging.info(f"[{username}] Laporan optimasi untuk gig '{original_gig_title}' dikirim ke admin WhatsApp.")
                        await asyncio.sleep(2) # Add a small delay between messages to avoid potential rate limits

                    last_gig_optimization_notification[username] = now # Update last sent time after all gigs are processed
                else:
                    logging.warning(f"[{username}] Gagal mendapatkan saran optimasi dari Gemini untuk gig berkinerja rendah. Notifikasi tidak dikirim.")
            else:
                logging.info(f"[{username}] Tidak ada gig berkinerja rendah yang ditemukan untuk optimasi.")
        else:
            if notification_already_sent_today:
                logging.info(f"[{username}] Laporan optimasi gig sudah dikirim hari ini. Melewatkan pengecekan.")
            else:
                logging.info(f"[{username}] Bukan waktu 11:00 WIB atau laporan sudah dikirim. Menunggu hingga besok atau waktu yang tepat.")

        # Calculate sleep duration until the next 11:00 AM WIB
        now = datetime.now()
        next_run = now.replace(hour=11, minute=0, second=0, microsecond=0)
        if now >= next_run:
            # If it's past 11 AM today, schedule for 11 AM tomorrow
            next_run += timedelta(days=1)
        
        sleep_seconds = (next_run - now).total_seconds()
        logging.info(f"[{username}] Sleeping for {int(sleep_seconds)} seconds until next gig optimization check (next 11:00 AM WIB).")
        await asyncio.sleep(sleep_seconds)


# --- FUNCTION TO PERIODICALLY FETCH AND STORE GIG DATA ---
# Global dictionary to store the last database update time for each account
last_db_update_time = {}

async def fetch_and_store_gig_data_periodically(account_config, interval_seconds):
    username = account_config["USERNAME"]
    logging.info(f"\n[{username}] --- Starting periodic gig data fetch and database store. Data stored daily at 11 AM WIB. ---")

    # Initialize last_db_update_time for this account if not present
    if username not in last_db_update_time:
        last_db_update_time[username] = None # Or load from a persistent store if needed

    while True:
        now = datetime.now()
        # Define target time for daily database update (11:00 AM WIB)
        target_time = now.replace(hour=11, minute=0, second=0, microsecond=0)

        # Check if data was already stored today
        data_already_stored_today = (
            last_db_update_time[username] is not None and
            last_db_update_time[username].date() == now.date()
        )

        if not data_already_stored_today and now >= target_time:
            logging.info(f"[{username}] Executing daily gig data fetch and database store at 11:00 WIB.")
            gigs_data = await get_fiverr_gigs_data(account_config)
            if gigs_data:
                await insert_gigs_into_db(gigs_data, account_config)
                last_db_update_time[username] = now # Update last stored time
            else:
                logging.warning(f"[{username}] No gig data fetched for storage today.")
        else:
            if data_already_stored_today:
                logging.info(f"[{username}] Data gigs sudah disimpan ke database hari ini. Melewatkan penyimpanan.")
            else:
                logging.info(f"[{username}] Bukan waktu 11:00 WIB atau data sudah disimpan. Menunggu hingga besok atau waktu yang tepat.")

        # Calculate sleep duration until the next 11:00 AM WIB
        now = datetime.now()
        next_run = now.replace(hour=11, minute=0, second=0, microsecond=0)
        if now >= next_run:
            # If it's past 11 AM today, schedule for 11 AM tomorrow
            next_run += timedelta(days=1)
        
        sleep_seconds = (next_run - now).total_seconds()
        logging.info(f"[{username}] Sleeping for {int(sleep_seconds)} seconds until next gig data fetch (next 11:00 AM WIB).")
        await asyncio.sleep(sleep_seconds)


# --- MAIN FUNCTION TO RUN ALL ACCOUNTS CONCURRENTLY ---
async def main():
    logging.info("Starting Fiverr Notifier script for multiple accounts. Ensure your cookies and X-CSRF-Tokens are valid for all configured accounts.")

    global SELLER_GIGS_INFO # Declare that we will be modifying the global variable

    all_tasks = []
    initialization_tasks = []

    # Interval settings (in seconds)
    CHECK_INBOX_INTERVAL_SECONDS = 30 # For checking inbox for new messages (HTTP GET)
    ACTIVITY_PING_INTERVAL_SECONDS = 10 # For sending general activity pings (to mimic browser ping)
    ORDER_PAGE_CHECK_INTERVAL_SECONDS = 300 # New: Interval for checking order pages (e.g., every 5 minutes)
    
    # Define normal intervals
    # Changed to 1 day for database storage
    NORMAL_GIG_DATA_FETCH_INTERVAL_SECONDS = 86400 # Once a day (24 * 3600)
    # Changed to 7 days for optimization checks
    NORMAL_GIG_OPTIMIZATION_CHECK_INTERVAL_SECONDS = 604800 # Once a week (7 * 24 * 3600)

    # --- DEBUG/TEST MODE CONFIGURATION ---
    # Set to True to run the periodic tasks much faster for testing purposes.
    # Set to False for normal operation with long intervals.
    DEBUG_MODE = True # CHANGE THIS TO FALSE FOR PRODUCTION USE!

    if DEBUG_MODE:
        logging.warning("DEBUG_MODE is ENABLED: Intervals are drastically reduced for testing!")
        GIG_DATA_FETCH_INTERVAL_SECONDS = 60 # Fetch gig data every 1 minute in debug mode
        GIG_OPTIMIZATION_CHECK_INTERVAL_SECONDS = 120 # Check for optimization every 2 minutes in debug mode
        # PENTING: Sesuaikan ambang batas ini sesuai dengan data gig Anda yang sebenarnya
        IMPRESSIONS_THRESHOLD_DEBUG = 10 # Misalnya, pemicu jika impressions < 10
        CLICKS_THRESHOLD_DEBUG = 2     # Misalnya, pemicu jika clicks < 2
    else:
        GIG_DATA_FETCH_INTERVAL_SECONDS = NORMAL_GIG_DATA_FETCH_INTERVAL_SECONDS
        GIG_OPTIMIZATION_CHECK_INTERVAL_SECONDS = NORMAL_GIG_OPTIMIZATION_CHECK_INTERVAL_SECONDS
        IMPRESSIONS_THRESHOLD_DEBUG = 500 # Nilai default untuk mode non-debug
        CLICKS_THRESHOLD_DEBUG = 10     # Nilai default untuk mode non-debug


    for i, account_config in enumerate(ACCOUNT_CONFIGS):
        username = account_config["USERNAME"]
        logging.info(f"\n--- Initializing for Account {i+1}: {username} ---")

        # Get credentials for the current account
        websocket_url, websocket_token, \
        cookie_string, x_csrf_token = await get_websocket_credentials(account_config)

        if not (websocket_url and websocket_token and cookie_string):
            logging.error(f"[{username}] Failed to obtain credentials for this account. Skipping this account.")
            continue

        logging.info(f"[{username}] Credentials successfully obtained.")

        # NEW: Fetch seller's gig information at startup for each account (for Gemini)
        logging.info(f"[{username}] Fetching seller's gig information for Gemini context...")
        # We need to call get_fiverr_gigs_data which populates SELLER_GIGS_INFO with detailed data
        # for gig updates.
        await get_fiverr_gigs_data(account_config) 
        gigs_data_for_gemini = SELLER_GIGS_INFO.get(username, [])

        if gigs_data_for_gemini:
            logging.info(f"[{username}] Successfully loaded {len(gigs_data_for_gemini)} gigs for Gemini context.")
            for gig in gigs_data_for_gemini:
                logging.info(f"[{username}]   - Gig (for Gemini): '{gig['title']}' ({len(gig.get('packages', []))} packages)")
        else:
            logging.warning(f"[{username}] No gig information could be loaded for this seller for Gemini context.")

        # Add initial gig data to the database immediately upon successful initialization
        logging.info(f"[{username}] Performing initial gig data insertion into database.")
        initial_gigs_for_db = await get_fiverr_gigs_data(account_config) # Call again to get simpler data for DB
        initialization_tasks.append(asyncio.create_task(insert_gigs_into_db(initial_gigs_for_db, account_config)))
        last_db_update_time[username] = datetime.now() # Mark as updated immediately after initial insert


        # Create tasks for each account
        periodic_inbox_task = asyncio.create_task(
            check_and_reply_inbox_periodically(
                account_config,
                CHECK_INBOX_INTERVAL_SECONDS
            )
        )
        all_tasks.append(periodic_inbox_task)

        periodic_activity_task = asyncio.create_task(
            send_activity_periodically(
                account_config,
                ACTIVITY_PING_INTERVAL_SECONDS
            )
        )
        all_tasks.append(periodic_activity_task)

        websocket_task = asyncio.create_task(
            receive_fiverr_notifications(
                websocket_url,
                websocket_token,
                account_config
            )
        )
        all_tasks.append(websocket_task)

        # Tambahkan sedikit penundaan untuk memberi waktu WebSocket terhubung dan diautentikasi
        logging.info(f"[{username}] Memberi waktu sejenak agar koneksi WebSocket stabil sebelum memulai GET order pages dan gig data update...")
        await asyncio.sleep(5)

        # New: Add task for periodically checking order pages, after WebSocket is started
        periodic_order_check_task = asyncio.create_task(
            get_order_pages_periodically(
                account_config,
                ORDER_PAGE_CHECK_INTERVAL_SECONDS
            )
        )
        all_tasks.append(periodic_order_check_task)
        
        # New: Add task for periodically fetching and storing gig data (from coba2.py logic)
        # This will now be time-aware
        periodic_gig_data_task = asyncio.create_task(
            fetch_and_store_gig_data_periodically(
                account_config,
                GIG_DATA_FETCH_INTERVAL_SECONDS # Uses adjusted interval based on DEBUG_MODE
            )
        )
        all_tasks.append(periodic_gig_data_task)

        # NEW: Add task for periodic gig optimization check and suggestion
        # This will also be time-aware
        periodic_gig_optimization_task = asyncio.create_task(
            periodic_gig_optimization(
                account_config,
                GIG_OPTIMIZATION_CHECK_INTERVAL_SECONDS, # Uses adjusted interval based on DEBUG_MODE
                impressions_threshold=IMPRESSIONS_THRESHOLD_DEBUG, # Lower threshold for testing
                clicks_threshold=CLICKS_THRESHOLD_DEBUG # Lower threshold for testing
            )
        )
        all_tasks.append(periodic_gig_optimization_task)

        # NEW: Add task for monitoring WhatsApp for 'Up' command
        monitor_whatsapp_task = asyncio.create_task(
            monitor_whatsapp_for_up_command(
                account_config,
                interval_seconds=10 # Check WhatsApp every 10 seconds
            )
        )
        all_tasks.append(monitor_whatsapp_task)


    # Wait for all initial gig data insertions to complete
    if initialization_tasks:
        logging.info("Waiting for initial gig data insertions to complete for all accounts...")
        await asyncio.gather(*initialization_tasks)
        logging.info("All initial gig data insertions complete.")

    if not all_tasks:
        logging.info("No accounts could be initialized. Exiting.")
        return

    logging.info("\nAll configured accounts' real-time monitoring, inbox checking, activity pings, order page checks, and gig data updates have started.")
    logging.info("Press Ctrl+C to stop the script.")

    # Wait until one of the tasks completes (or all fail/cancel)
    done, pending = await asyncio.wait(
        all_tasks,
        return_when=asyncio.FIRST_COMPLETED
    )

    # Cancel any remaining running tasks gracefully
    for task in pending:
        task.cancel()

    logging.info("\nScript stopped.")

# --- 5. RUN THE SCRIPT ---
if __name__ == "__main__":
    try:
        asyncio.run(main())
    except KeyboardInterrupt:
        logging.info("\nScript stopped by user (Ctrl+C).")
    except Exception as e:
        logging.error(f"Unexpected error in main loop: {e}")