import mysql.connector

def get_db_connection():
    return mysql.connector.connect(
        host='localhost',
        user='udara',
        password='@UdaraUnis2024',
        database='udara'
    )