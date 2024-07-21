import pandas as pd
import numpy as np
import pandas as pd
from docxtpl import DocxTemplate,  InlineImage
from docx.shared import Mm
import sys
import mysql.connector
import os
from dotenv import load_dotenv
from datetime import date
load_dotenv()

#configurar la conexion a la base de datos
DB_USERNAME = os.getenv('DB_USERNAME')
DB_DATABASE = os.getenv('DB_DATABASE')
DB_PASSWORD = os.getenv('DB_PASSWORD')
DB_PORT = os.getenv('DB_PORT')
DB_HOST=os.getenv('DB_HOST')

# Conectar a DB
cnx = mysql.connector.connect(user=DB_USERNAME,
                              password=DB_PASSWORD,
                              host=DB_HOST,
                              port=DB_PORT,
                              database=DB_DATABASE,
                              use_pure=False)
cursor=cnx.cursor(buffered=True)

from tablas_dict import tablas
for i in tablas:
    query="""ALTER TABLE `"""+i+"""` CHANGE `id` `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT, add PRIMARY KEY (`id`);"""
    cursor.execute(query)