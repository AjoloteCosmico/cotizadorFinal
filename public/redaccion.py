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
print(sys.argv[1])
today = date.today()
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
cotizacion=pd.read_sql("select * from quotations where id ="+str(sys.argv[1]),cnx)
cliente=pd.read_sql("""select * from customers where customers.id="""+str(cotizacion['customer_id'].values[0]),cnx)
user=pd.read_sql("""select * from users where users.id="""+str(cotizacion['user_id'].values[0]),cnx)
productos=pd.read_sql("""select * from cart_products where cart_products.quotation_id="""+str(cotizacion['id'].values[0]),cnx)
import datetime

currentDateTime = datetime.datetime.now()
date = currentDateTime.date()
year = date.strftime("%Y")

from tablas_dict import tablas
aceros=pd.read_sql('select * from steels ',cnx)
aceros.loc[aceros['caliber']=='EST 3 IN','caliber']='EST3'

products=pd.DataFrame()
#iterar sobre tablas
for i in tablas:
    print(i)
    #buscar en la base de datos todos los productos de esta tabla
    #pertenecientes a la cotizacion pedida por el usuario.
    p=pd.read_sql('select * from '+i+' where quotation_id = '+str(id),cnx)
    p=p.assign(tabla=i)
    if(('cost' not in p.columns)&(len(p)>0)):
        if('caliber' not in p.columns):
             #esto es en especifico por un caso en que todas kas piezas son cal 14
             p=p.assign(caliber='14')
        try:
            p['caliber']=p['caliber'].str.replace('-','')
        except:
            print(' ')
        print(str(p['caliber'].values[0]))
        costo=aceros.loc[aceros['caliber']==str(p['caliber'].values[0]),'cost'].values[0]
        if('total_kg' in p.columns):
            p=p.assign(cost=costo*p.total_kg)
        if('total_weight' in p.columns):
           
            p=p.assign(cost=costo*p.total_weight)
        if('weight_kg' in p.columns):
         
            p=p.assign(cost=costo*p.weight_kg)
        if('weight' in p.columns):
          
            p=p.assign(cost=costo*p.weight)
        if('long' in p.columns):
           
            p=p.assign(cost=costo*p.long)
        print(i)
    products=products.append(p,ignore_index=True)
cols_to_fill_str=['description','protector','model','sku']
products[cols_to_fill_str]=products[cols_to_fill_str].fillna('')
cols_kg=['weight','total_kg','total_weight','weight_kg']
cols_m2=['m2','total_m2']
products[cols_kg+cols_m2]=products[cols_kg+cols_m2].fillna(0)

pricelist_protectors=pd.read_sql('select * from price_list_protectors',cnx)
quotation_protectors=pd.read_sql('select quotation_protectors.*, protectors.sku from quotation_protectors  inner join protectors on protectors.protector=quotation_protectors.protector where quotation_id ='+str(id),cnx)
quotation_shlf=pd.read_sql('select * from selective_heavy_load_frames where quotation_id ='+str(id),cnx)
materials=pd.read_sql('select * from (materials left join price_list_screws on materials.price_list_screw_id= price_list_screws.id)left join price_lists on price_lists.id=materials.price_list_id',cnx)
materials['type']=materials['type'].fillna('')

doc = DocxTemplate("plantilla.docx")

context={
    'cliente':cliente['customer'].values[0],
    'direccion':cliente['address'].values[0]+' '+cliente['outdoor'].values[0]+', '+cliente['city'].values[0]+' '+cliente['suburb'].values[0]+' '+cliente['state'].values[0]+', cp: '+str(cliente['zip_code'].values[0]),
    'folio': cotizacion['invoice'].values[0],
    'fecha':today,
    'asesor':user['name'].values[0],
} 
print(context['direccion'])
doc.render(context) 
doc.save("storage/Cotizacion"+str(sys.argv[1])+".docx")
      