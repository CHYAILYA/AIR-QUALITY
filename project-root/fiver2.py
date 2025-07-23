import requests
import json
import re
import time
import mysql.connector
from bs4 import BeautifulSoup

def get_fiverr_gigs_data(cookie_string, csrf_token):
    """
    Mengambil data gigs dari Fiverr dengan mengekstraknya dari HTML yang diterima.

    Args:
        cookie_string (str): String cookie dari sesi Fiverr yang login.
        csrf_token (str): Nilai X-CSRF-Token dari sesi Fiverr yang login.

    Returns:
        list or None: Daftar data gigs dalam format JSON jika berhasil, None jika gagal.
    """
    url = "https://www.fiverr.com/users/ahmadfikri820/manage_gigs?current_filter=active&days_for_stats=14"

    headers = {
        "User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/100.0.4896.127 Safari/537.36",
        "Accept": "text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9",
        "Accept-Language": "en-US,en;q=0.9,id;q=0.8",
        "Cookie": cookie_string.strip(),
        "X-CSRF-Token": csrf_token.strip(),
        "X-Requested-With": "XMLHttpRequest",
        "Referer": "https://www.fiverr.com/users/ahmadfikri820/manage_gigs?current_filter=active&days_for_stats=14"
    }

    print(f"Mencoba mengambil konten HTML dari: {url}")
    gigs_data = None

    try:
        response = requests.get(url, headers=headers)
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
                               .replace('\\u003e', '>')
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
                except json.JSONDecodeError as e:
                    print(f"Error saat memparsing JSON dari HTML: {e}")
            # else: # Dinonaktifkan
                # print("Pola JSON 'document.viewData' tidak ditemukan di dalam blok script yang teridentifikasi.")
        # else: # Dinonaktifkan
            # print("Blok script yang berisi 'document.viewData' dengan data gigs tidak ditemukan di HTML.")

    except requests.exceptions.RequestException as e:
        print(f"Error saat mengambil data (Request): {e}")
        # print(f"Status Code: {e.response.status_code if e.response else 'N/A'}") # Dinonaktifkan
        # print(f"Response Body (partial): {e.response.text[:500] if e.response else 'N/A'}") # Dinonaktifkan
    except Exception as e:
        print(f"Terjadi kesalahan tak terduga: {e}")

    return gigs_data

def insert_gigs_into_db(gigs_data, db_config):
    """
    Memasukkan data gigs ke dalam tabel 'gigs' di database MySQL.

    Args:
        gigs_data (list): Daftar dictionary yang berisi data gig.
        db_config (dict): Konfigurasi koneksi database.
    """
    if not gigs_data:
        print("Tidak ada data gigs untuk dimasukkan ke database.")
        return

    try:
        # Koneksi ke database
        conn = mysql.connector.connect(
            host=db_config['hostname'],
            user=db_config['username'],
            password=db_config['password'],
            database=db_config['database']
        )
        cursor = conn.cursor()

        # Query INSERT
        # Menggunakan ON DUPLICATE KEY UPDATE untuk menghindari duplikasi jika gig_id ada
        # atau untuk memperbarui data jika gig dengan judul yang sama sudah ada
        # Jika Anda ingin selalu memasukkan baris baru, hapus ON DUPLICATE KEY UPDATE.
        # Penting: Saat ini, gig_id tidak diekstrak. Jadi, kita akan mendasarkan UPDATE pada 'title'.
        # Jika Anda ingin menggunakan ID Fiverr untuk UPDATE, Anda perlu mengekstraknya dari JSON.
        insert_query = """
        INSERT INTO gigs (title, impressions, clicks, orders_count, cancellation_rate)
        VALUES (%s, %s, %s, %s, %s)
        ON DUPLICATE KEY UPDATE
            impressions = VALUES(impressions),
            clicks = VALUES(clicks),
            orders_count = VALUES(orders_count),
            cancellation_rate = VALUES(cancellation_rate),
            last_updated = CURRENT_TIMESTAMP;
        """

        # Jika Anda membuat 'title' sebagai UNIQUE KEY di tabel Anda,
        # maka ON DUPLICATE KEY UPDATE akan bekerja dengan baik.
        # Kalau tidak, setiap kali dijalankan, itu akan menambahkan baris baru.
        # Pastikan kolom 'title' memiliki UNIQUE constraint di SQL jika ingin ON DUPLICATE KEY UPDATE berfungsi berdasarkan judul.
        # ALTER TABLE gigs ADD UNIQUE (title);

        for gig in gigs_data:
            gig_info = gig.get('gig', {})
            stats = gig.get('gigsData', [])

            title = gig_info.get('title', None)
            impressions = next((int(item['value']) for item in stats if item['type'] == 'impressions'), 0)
            clicks = next((int(item['value']) for item in stats if item['type'] == 'clicks'), 0)
            orders = next((int(item['value']) for item in stats if item['type'] == 'orders'), 0)
            cancellation_rate_str = next((item['value'] for item in stats if item['type'] == 'cancellation_rate'), '0')
            cancellation_rate = float(cancellation_rate_str) # Pastikan ini float

            if title: # Hanya masukkan jika ada judul
                try:
                    cursor.execute(insert_query, (title, impressions, clicks, orders, cancellation_rate))
                    print(f"Berhasil memasukkan/memperbarui gig: {title}")
                except mysql.connector.Error as err:
                    print(f"Gagal memasukkan/memperbarui gig {title}: {err}")
                    conn.rollback() # Rollback jika ada kesalahan pada satu insert

        conn.commit() # Commit semua perubahan
        print("Operasi database selesai.")

    except mysql.connector.Error as err:
        print(f"Error koneksi atau operasi database: {err}")
    except Exception as e:
        print(f"Terjadi kesalahan tak terduga saat berinteraksi dengan database: {e}")
    finally:
        if 'conn' in locals() and conn.is_connected():
            cursor.close()
            conn.close()
            print("Koneksi database ditutup.")

if __name__ == "__main__":
    # --- GANTI NILAI INI DENGAN COOKIE DAN CSRF TOKEN TERBARU ANDA ---
    # Penting: Cookie dan CSRF token memiliki masa berlaku dan harus diperbarui secara berkala.
    # Jika Anda mendapatkan error 403 Forbidden, artinya cookie/token Anda sudah tidak valid.
    YOUR_FIVERR_COOKIE_STRING = "u_guid=1749101553000-23ae52f3a9ee91ce9e38f3d962c5ccdb297763ae; logged_out_currency=USD; cpra_opt_out_status_external=false; pxcts=7b6b04e4-41ce-11f0-8dd6-d04b37962a62; _pxvid=7b6af7f7-41ce-11f0-8dd4-b95205ee21be; __pxvid=7c775803-41ce-11f0-9370-f6aa54fbac45; _fbp=fb.1.1749101556502.310666059334163989; __pdst=d8f7230dc92142c2a7de27722dc5814e; _tt_enable_cookie=1; _ttp=01JWZ97TAGX6YBTZT8G3BBT3G0_.tt.1; hubspotutk=afb3e6ed0df10bd2bbb9d1913d18bd43; __hssrc=1; session_locale=en-US; logged_in_currency_v2=USD; was_logged_in=1%3Bahmadfikri820; _fiverr_session_key=94c7c52dec07e581581b5fdfb72a910f; new_guid=1749345009322-4f1eba57-3621-4c25-9369-fa1c23fb0891; page_views=25; hodor_creds=eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJmaXZlcnIvaG9kb3JfcGVnYXN1cyIsInVpZCI6MTI2NjQzMjk2LCJzaWQiOiIzNzk2YTAzZmViNTcwMzExNzU4Nzg4ZTQwOGI5NGQ2NSIsImlhdCI6MTc0OTM0NTA0MSwiZXhwIjoxNzgwOTAyNjQxfQ.14grIYozg_ntEdb1eXwrXcY-H2qrzac7_XxL6hf9QMo; cpra_opt_out_permanent_user=false; session_referer=https%3A%2F%2Ffiverr.formtitan.com%2Fftproject%2Fpro_application_form; _ga_XHGGCMRHEC=GS2.1.s1749355142$o3$g0$t1749355142$j60$l0$h0; _ga_XBEB8JPBY8=GS2.2.s1749487347$o1$g0$t1749487356$j60$l0$h0; _ga=GA1.1.103881883.1749101570; OptanonConsent=isGpcEnabled=0&datestamp=Mon+Jun+09+2025+23%3A42%3A51+GMT%2B0700+(Indochina+Time)&version=202211.1.0&isIABGlobal=false&hosts=&consentId=a64aa9a4-6cc3-42b0-a9c6-5aa1667fdf30&interactionCount=1&landingPath=https%3A%2F%2Fhelp.fiverr.com%2Fhc%2Fen-us%2Farticles%2F21099496893585-Fiverr-agencies&groups=C0001%3A1%2CC0002%3A1%2CC0004%3A1; _ga_311202432=GS2.1.s1749487347$o1$g1$t1749487381$j26$l0$h0; _pxhd=rEH-M5UX5UngdwgP9bp6vNcs5uoxhPf/DGk-vjxn7PucaJwFvn9pF0pW4gUwzTQUgbZDNOrx9nWWJf78hm4KQ==:r5JnKZUQ8wFW19BerSp30iYicbgAqYXCPwe2ppqbXwwZQ3qI9vZpJPYBzlZ5o0Tsttq/fgIvR6zHXcJCeCGSUAGPtDdFY5SMU1ePB8fvkZ/k=; _clck=1xverqg%7C2%7Cfwq%7C0%7C1982; hp_view=seller; last_content_pages_=gigs%7C%7C%7Cshow%7C%7C%7C422208574%3Bgigs%7C%7C%7Cshow%7C%7C%7C422205889; splash_screen_flag=true; splash_screen_hide=true; _gcl_au=1.1.1650952914.1749101554.985139045.1749789159.1749789158; redirect_url=%2Fusers%2Fahmadfikri820%2Fmanage_gigs; ftr_blst_1h=1749798782758; __hstc=156287140.afb3e6ed0df10bd2bbb9d1913d18bd43.1749101570728.1749786557852.1749798786502.78; visited_fiverr=true; t-ip=1; go_back_to_fiverr_url=%2Finbox; QSI_HistorySession=https%3A%2F%2Fwww.fiverr.com%2Finbox~1749789267416%7Chttps%3A%2F%2Fwww.fiverr.com%2Fusers%2Fahmadfikri820%2Fmanage_gigs~1749795111587%7Chttps%3A%2F%2Fwww.fiverr.com%2Fahmadfikri820%2Fbuying%3Fsource%3Davatar_menu_profile~1749801014342%7Chttps%3A%2F%2Fwww.fiverr.com%2Fusers%2Fahmadfikri820%2Fmanage_gigs~1749802033397%7Chttps%3A%2F%2Fwww.fiverr.com%2Fahmadfikri820%2Fbuying%3Fsource%3Davatar_menu_profile~1749802051781%7Chttps%3A%2F%2Fwww.fiverr.com%2Finbox~1749802056199; _px3=1db36a15a3d293b4f4d1cb8c2516d62efc14d25dc6700ab5b6555938523e2bcc:ZG8nSGWUpMziNOY0HxCn5p+NxRFouB8hm1sBnHLysVCvKGqucwNhn/trZtHqb5WlkMNJy0feyuch1kiPoAXyjA==:1000:vpGTEezOd9tqqtxEQ86jIFyBVcMjwOYGk00a6yEFyDJngXbb8FmhtYlg2UZYJburNofLMukdhY5DNbTDKSbO+jHM97UjmAhDWlF1n6I2AXCTnVW1QD5PoVakJlb9AKm1DqlLTSmr5tV6AHALx0beWTjZ9sCE6ovmu0CRpeiDgYsZEyFhr5ImY2t8e1861ZZZlPBdn6XQtja4Lk2muxP545TxAokGWBgGC1xjK9XW3j4=; _cfuvid=LkBpg.DVXQHnoqU_2LofFULSLDHimzQ39djHrg4i9LY-1749802075345-0.0.1.1-604800000; access_token=eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1aWQiOjEyNjY0MzI5Niwic2lkIjoiMzc5NmEwM2ZlYjU3MDMxMTc1ODc4OGU0MDhiOTRkNjUiLCJwcnYubnVsbCwiZXhwIjoxNzQ5ODAyMTQ3fQ.UWiH13BUCNi1MM5mORVBwfMMj1UCChOqbtC85ocoVI; _tq_id.TV-18900945-1.8951=c90e45ba9c563226.1715000829.0.1749802096..; _rdt_uuid=1749101569995.ad0380ce-2738-492d-9c20-5f9eba4f1d41; tatari-cookie-test=48917875; tatari-user-cookie=71f9b5f0692c47d24723a054aacf15239ce57f55e577c3e2e9d2c3ce4a837f0a; tatari-session-cookie=21e8a66c-4de2-db74-6750-eb8b1f41c976; ttcsid=1749798785666::EYQx4J3xNFXCHVn8JQeJ.84.1749802096176; _uetsid=855b6f0041ce11f08c3ce7b0a88b96fb; _uetvid=855b88c041ce11f0a6636386cf3eede3; ttcsid_C2IG4QAQV140ORDJ7BR0=1749798785665::d_sUrBGLg3WfA0pXNBUj.85.1749802096441; ttcsid_C6IPOEIO6DGUDE0A23MG=1749798785755::otoGL7NZchkJl1dRhx3V.85.1749802096441; ttcsid_CAFNM6RC77U9UO5AT1FG=1749798785671::JfghWeqUvGsWsV8s0hn0.85.1749802096442; ttcsid_C3VBNIPU9OSLU1GC4A70=1749798785668::zQbY0VWqn2sWDGg1bVBZ.85.1749802096442; __hssc=156287140.8.1749798786502; _clsk=t3z5ze%7C1749802110006%7C55%7C0%7Ca.clarity.ms%2Fcollect; _ga_GK1NB7HX40=GS2.1.s1749798782$o82$g1$t1749802122$j33$l0$h0; forterToken=92fc06594bc54bb2bba0551ef4a5183e_1749802123482__UDF4_17ck; _pxde=272ddd487b85f1328b96aaae13d3b106d1174f26b9bfda3833e8342889ef15d3:eyJ0aW1lc3RhbXAiOjE3NDk4MDIxMjM3MjgsImZfa2IiOjAsImlwY19pZCI6W119"
    YOUR_X_CSRF_TOKEN_VALUE = "1751239288.FImw4Lwccc77QnMKJofkFzIO1BUcrU+u1Ysfl1yFEn4="
       
    # Panggil fungsi untuk mendapatkan data gig aktif
    gigs_data = get_fiverr_gigs_data(YOUR_FIVERR_COOKIE_STRING, YOUR_X_CSRF_TOKEN_VALUE)

    if gigs_data:
        print("\n--- Data Gigs Anda (dari Fiverr.com/users/ahmadfikri820/manage_gigs) ---")
        for gig in gigs_data:
            gig_info = gig.get('gig', {})
            stats = gig.get('gigsData', [])

            title = gig_info.get('title', 'Tidak ada judul')
            impressions = next((item['value'] for item in stats if item['type'] == 'impressions'), 'N/A')
            clicks = next((item['value'] for item in stats if item['type'] == 'clicks'), 'N/A')
            orders = next((item['value'] for item in stats if item['type'] == 'orders'), 'N/A')
            cancellations = next((item['value'] for item in stats if item['type'] == 'cancellation_rate'), 'N/A')

            print(f"Judul Gig: {title}")
            print(f"  Impressions (terbaru): {impressions}")
            print(f"  Clicks (terbaru): {clicks}")
            print(f"  Orders (terbaru): {orders}")
            print(f"  Cancellation Rate (terbaru): {cancellations}%")
            print("-" * 30)
    else:
        print("Gagal mengambil atau mengekstrak data gigs. Periksa log di atas untuk detail.")

    # Konfigurasi Database
    db_config = {
        'hostname': 'localhost',
        'username': 'udara',
        'password': '@UdaraUnis2024',
        'database': 'udara'
    }

    # Memasukkan data ke database
    if gigs_data:
        print("\n--- Memasukkan data gigs ke database ---")
        insert_gigs_into_db(gigs_data, db_config)
    else:
        print("\nTidak ada data gigs yang berhasil diekstrak untuk dimasukkan ke database.")