import sys
import mysql.connector
import xlsxwriter
import pandas as pd
import sys
import mysql.connector
import numpy as np
import os
from dotenv import load_dotenv
load_dotenv()
#ESTE ARGUMENTO NO SE USA EN ESTE REPORTE, SERÁ 0 SIEMPRE UWU
id=0
#configurar la conexion a la base de datos
DB_USERNAME = os.getenv('DB_USERNAME')
DB_DATABASE = os.getenv('DB_DATABASE')
DB_PASSWORD = os.getenv('DB_PASSWORD')
DB_PORT = os.getenv('DB_PORT')
# Conectar a DB
cnx = mysql.connector.connect(user=DB_USERNAME,
                              password=DB_PASSWORD,
                              host='localhost',
                              port=DB_PORT,
                              database=DB_DATABASE,
                              use_pure=False)

a_color='#354F84'
b_color='#91959E'

writer = pd.ExcelWriter('storage/report/kpis2'+str(id)+'.xlsx', engine='xlsxwriter')

workbook = writer.book
##FORMATOS PARA EL TITULO------------------------------------------------------------------------------
rojo_l = workbook.add_format({
    'bold': 0,
    'border': 0,
    'align': 'center',
    'valign': 'vcenter',
    #'fg_color': 'yellow',
    'font_color': 'red',
    'font_size':16})
negro_s = workbook.add_format({
    'bold': 0,
    'border': 0,
    'align': 'center',
    'valign': 'vcenter',
    'font_color': 'black',
    'font_size':12})
negro_b = workbook.add_format({
    'bold': 2,
    'border': 0,
    'align': 'center',
    'valign': 'vcenter',
    'font_color': 'black',
    'font_size':13,
    
    'text_wrap': True,
    'num_format': 'dd/mm/yyyy'}) 
rojo_b = workbook.add_format({
    'bold': 2,
    'border': 0,
    'align': 'center',
    'valign': 'vcenter',
    'font_color': 'red',
    'font_size':13})      

#FORMATOS PARA CABECERAS DE TABLA --------------------------------
header_format = workbook.add_format({
    'bold': True,
    'text_wrap': True,
    'valign': 'center',
    'fg_color': 'yellow',
    'border': 1,})

blue_header_format = workbook.add_format({
    'bold': True,
    'bg_color': a_color,
    'text_wrap': True,
    'valign': 'top',
    'align': 'center',
    'border_color':'white',
    'font_color': 'white',
    'border': 1})
blue_header_format_bold = workbook.add_format({
    'bold': True,
    'bg_color': a_color,
    'text_wrap': True,
    'valign': 'top',
    'align': 'center',
    'border_color':'white',
    'font_color': 'white',
    'num_format': '[$$-409]#,##0.00',
    'border': 1,
    'font_size':13})
blue_footer_format_bold = workbook.add_format({
    'bold': True,
    'bg_color': a_color,
    'text_wrap': True,
    'valign': 'top',
    'align': 'center',
    'border_color':'white',
    'font_color': 'white',
    'border': 1,
    'num_format': '[$$-409]#,##0.00',
    'font_size':11})
red_header_format = workbook.add_format({
    'bold': True,
    'bg_color': b_color,
    'text_wrap': True,
    'valign': 'top',
    'align': 'center',
    'border_color':'white',
    'font_color': 'white',
    'border': 1})

red_header_format_bold = workbook.add_format({
    'bold': True,
    'bg_color': b_color,
    'text_wrap': True,
    'valign': 'top',
    'align': 'center',
    'border_color':'white',
    'font_color': 'white',
    'border': 1,
    'font_size':13})


#FORMATOS PARA TABLAS PER CE------------------------------------

blue_content = workbook.add_format({
    'border': 1,
    'align': 'center',
    'valign': 'vcenter',
    'font_color': 'black',
    
    'border_color':a_color,
    'font_size':10,
    'num_format': '[$$-409]#,##0.00'})
blue_content_unit = workbook.add_format({
    'border': 1,
    'align': 'center',
    'valign': 'vcenter',
    'font_color': 'black',
    
    'border_color':a_color,
    'font_size':10,
    'num_format': '0.00'})
blue_content_bold = workbook.add_format({
    'bold': True,
    'border': 1,
    'align': 'center',
    'valign': 'vcenter',
    'font_color': 'black',
    'font_size':11,
    'border_color':a_color,
    'num_format': '[$$-409]#,##0.00'})

blue_content_bold_dll = workbook.add_format({
    'bold': True,
    'border': 1,
    'align': 'center',
    'valign': 'vcenter',
    'font_color': 'black',
    'font_size':11,
    'bg_color': '#b4e3b1',
    'border_color':a_color,
    'num_format': '[$$-409]#,##0.00'})
blue_content_date = workbook.add_format({
    'border': 1,
    'align': 'center',
    'valign': 'vcenter',
    'font_color': 'black',
    'font_size':9,
    'border_color':a_color,
    'num_format': 'dd/mm/yyyy'})
#FOOTER FORMATS---------------------------------------------------------
observaciones_format = workbook.add_format({
    'bold': True,
    'text_wrap': True,
    'valign': 'top',
    'fg_color':'#BDD7EE',
    'border': 1})

blue_content_dll = workbook.add_format({
    'border': 1,
    'align': 'center',
    'valign': 'vcenter',
    'font_color': 'black',
    'bg_color': '#b4e3b1',
    'border_color':a_color,
    'font_size':10,
    'num_format': '[$$-409]#,##0.00'})

total_cereza_format = workbook.add_format({
    'bold': True,
    'text_wrap': True,
    'valign': 'top',
    'fg_color':'#F4B084',
    'border': 1})
df=pd.DataFrame()
df[0:1].to_excel(writer, sheet_name='Sheet1', startrow=7,startcol=6, header=False, index=False)

worksheet = writer.sheets['Sheet1']
#Encabezado del documento--------------

import datetime

currentDateTime = datetime.datetime.now()
date = currentDateTime.date()
year = date.strftime("%Y")

cotizaciones=pd.read_sql('select quotations.*, customers.customer,users.name from (quotations inner join customers on quotations.customer_id = customers.id) inner join users on users.id = quotations.user_id ',cnx)

price_cols=['price','total_price','import','unit_price']
aceros=pd.read_sql('select * from steels ',cnx)
aceros.loc[aceros['caliber']=='EST 3 IN','caliber']='EST3'
products=pd.DataFrame()
import tablas_dict
for i in tablas_dict.tablas:
    # print(i)
    #buscar en la base de datos todos los productos de esta tabla
    #pertenecientes a la cotizacion pedida por el usuario.
    p=pd.read_sql('select * from '+i,cnx)
    p=p.assign(tabla=i)
    if(('cost' not in p.columns)&(len(p)>0)):
        if('caliber' not in p.columns):
            #esto es en especifico por un caso en que todas kas piezas son cal 14
            p=p.assign(caliber='14')
        try:
            p['caliber']=p['caliber'].str.replace('-','')
        except:
            print(' ')
        # print(str(p['caliber'].values[0]))
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
        # print(i)
    products=products.append(p,ignore_index=True)
cols_to_fill_str=['description','protector','model','sku']
products[cols_to_fill_str]=products[cols_to_fill_str].fillna('')
cols_kg=['weight','total_kg','total_weight','weight_kg']
cols_m2=['m2','total_m2']
price_cols=['price','total_price','import','unit_price']
products[cols_kg+cols_m2]=products[cols_kg+cols_m2].fillna(0)

kilos=products[cols_kg].sum(axis=1, numeric_only=True).sum()
#trayendo informacion de materiales
materials=pd.read_sql('select * from (materials left join price_list_screws on materials.price_list_screw_id= price_list_screws.id)left join price_lists on price_lists.id=materials.price_list_id',cnx)
materials['type']=materials['type'].fillna('')
#Diccionario de meses 
worksheet = writer.sheets['Sheet1']
#Encabezado del documento--------------
worksheet.merge_range('B2:F2', 'REPORTE POR COTIZACION ', negro_b)
worksheet.merge_range('B3:F3', 'ADMINISTRATIVO', negro_s)
worksheet.merge_range('B4:F4', 'COSTOS ', negro_b)
worksheet.write('H2', 'AÑO', negro_b)

worksheet.write('I2', year, negro_b)
worksheet.merge_range('J2:K3', """FECHA DEL REPORTE
DD/MM/AAAA""", negro_b)

worksheet.write('L2', date, negro_b)
worksheet.insert_image("A1", "img/logo/logo.png",{"x_scale": 0.6, "y_scale": 0.6})

#Cabecera de fechas
espacios=' '
for i in range(80):
    espacios=espacios+' '
worksheet.merge_range('B6:H6', "FECHA DEL REPORTE:"+espacios+str(date), blue_header_format)
worksheet.merge_range('B7:E7', "PERIODO REPORTADO:", blue_header_format)


worksheet.write('F7', "Mensual", blue_header_format)
worksheet.write('G7', """DESDE (AAAA-MM-DD)
                """+str(cotizaciones.sort_values(by='created_at')['created_at'].values[0])[0:10], blue_header_format)
worksheet.write('H7', """Hasta (AAAA-MM-DD)
                """+str(cotizaciones.sort_values(by='created_at')['created_at'].values[len(cotizaciones)-1])[0:10], blue_header_format)


#Cabezera pricipal
worksheet.merge_range('B10:B11', 'PDA', blue_header_format)
worksheet.merge_range('C10:C11', 'KPI', blue_header_format)
worksheet.merge_range('D10:D11', 'DETALLE', blue_header_format)
worksheet.merge_range('E10:E11', "VALOR", blue_header_format)
worksheet.merge_range('F10:F11', "UNIDAD", blue_header_format)
worksheet.merge_range('G10:G11', "INDICADOR", blue_header_format)
worksheet.merge_range('H10:H11', "RESULTADO", blue_header_format)
def cociente(a,b):
    if(b>0):
        return a/b
    else: 
        return 0
row=12
#Indicadores globales
worksheet.write('B'+str(row),str(row-11),blue_content)
worksheet.write('C'+str(row),'TOTAL COTIZADO EQUIVALENTE EN MONEDA NACIONAL',blue_content)
worksheet.write('D'+str(row),'GLOBAL',blue_content)
worksheet.write('E'+str(row),products[price_cols].sum(axis=1, numeric_only=True).sum(),blue_content)
worksheet.write('F'+str(row),'MN',blue_content)
worksheet.write('G'+str(row), '100%',blue_content)
worksheet.write('H'+str(row),'',blue_content)
row=row+1
worksheet.write('B'+str(row),str(row-11),blue_content)
worksheet.write('C'+str(row),'NUMERO TOTAL DE COTIZAICONES REALIZADAS',blue_content)
worksheet.write('D'+str(row),'GLOBAL',blue_content)
worksheet.write('E'+str(row),str(len(cotizaciones)),blue_content)
worksheet.write('F'+str(row),'COTIZACIONES',blue_content)
worksheet.write('G'+str(row), '100%',blue_content)
worksheet.write('H'+str(row),'',blue_content)
row=row+1
worksheet.write('B'+str(row),str(row-11),blue_content)
worksheet.write('C'+str(row),'COTIZACION PROMEDIO EN M.N. (TAMAÑO DE PROYECTO)',blue_content)
worksheet.write('D'+str(row),'GLOBAL',blue_content)
worksheet.write('E'+str(row),cociente(products[price_cols].sum(axis=1, numeric_only=True).sum(),len(cotizaciones)),blue_content)
worksheet.write('F'+str(row),'MN',blue_content)
worksheet.write('G'+str(row), '100%',blue_content)
worksheet.write('H'+str(row),'',blue_content)
row=row+1
worksheet.write('B'+str(row),str(row-11),blue_content)
worksheet.write('C'+str(row),'NUMERO DE CLIENTES A LOS QUE SE LES COTIZA EN EL PERIODO',blue_content)
worksheet.write('D'+str(row),'GLOBAL',blue_content)
worksheet.write('E'+str(row),str(len(cotizaciones['name'].unique())),blue_content)
worksheet.write('F'+str(row),'CLIENTES',blue_content)
worksheet.write('G'+str(row), '100%',blue_content)
worksheet.write('H'+str(row),'',blue_content)
row=row+1
worksheet.write('B'+str(row),str(row-11),blue_content)
worksheet.write('C'+str(row),'PROMEDIO COTIZADO POR CLIENTE',blue_content)
worksheet.write('D'+str(row),'GLOBAL',blue_content)
worksheet.write('E'+str(row),cociente(products[price_cols].sum(axis=1, numeric_only=True).sum(),len(cotizaciones['name'].unique())),blue_content)
worksheet.write('F'+str(row),'MN',blue_content)
worksheet.write('G'+str(row), '100%',blue_content)
worksheet.write('H'+str(row),'',blue_content)
row=row+1
worksheet.write('B'+str(row),str(row-11),blue_content)
worksheet.write('C'+str(row),'NUMERO DE COTIZADORES EMPLEADOS',blue_content)
worksheet.write('D'+str(row),'GLOBAL',blue_content)
worksheet.write('E'+str(row),str(len(cotizaciones['user_id'].unique())),blue_content)
worksheet.write('F'+str(row),'COTIZADORES',blue_content)
worksheet.write('G'+str(row), '100%',blue_content)
worksheet.write('H'+str(row),'',blue_content)
row=row+1
worksheet.write('B'+str(row),str(row-11),blue_content)
worksheet.write('C'+str(row),'PROMEDIO COTIZADO POR COTIZADOR',blue_content)
worksheet.write('D'+str(row),'GLOBAL',blue_content)
worksheet.write('E'+str(row),cociente(products[price_cols].sum(axis=1, numeric_only=True).sum(),len(cotizaciones['user_id'].unique())),blue_content)
worksheet.write('F'+str(row),'MN',blue_content)
worksheet.write('G'+str(row), '100%',blue_content)
worksheet.write('H'+str(row),'',blue_content)
row=row+1
#CICLO PARA LOS TIPOS
for i in cotizaciones['system'].unique():
    estas_cotizaciones=cotizaciones.loc[cotizaciones['system']==i]
    estos_productos=products.loc[products['quotation_id'].isin(estas_cotizaciones['id'].unique())]
    worksheet.write('B'+str(row),str(row-11),blue_content)
    worksheet.write('C'+str(row),'TOTAL COTIZADO EQUIVALENTE EN MONEDA NACIONAL',blue_content)
    worksheet.write('D'+str(row),i,blue_content)
    worksheet.write('E'+str(row),estos_productos[price_cols].sum(axis=1, numeric_only=True).sum(),blue_content)
    worksheet.write('F'+str(row),'MN',blue_content)
    worksheet.write('G'+str(row), '100%',blue_content)
    worksheet.write('H'+str(row),'',blue_content)
    row=row+1
    worksheet.write('B'+str(row),str(row-11),blue_content)
    worksheet.write('C'+str(row),'NUMERO TOTAL DE COTIZAICONES REALIZADAS',blue_content)
    worksheet.write('D'+str(row),i,blue_content)
    worksheet.write('E'+str(row),str(len(estas_cotizaciones)),blue_content)
    worksheet.write('F'+str(row),'COTIZACIONES',blue_content)
    worksheet.write('G'+str(row), '100%',blue_content)
    worksheet.write('H'+str(row),'',blue_content)
    row=row+1
    worksheet.write('B'+str(row),str(row-11),blue_content)
    worksheet.write('C'+str(row),'COTIZACION PROMEDIO EN M.N. (TAMAÑO DE PROYECTO)',blue_content)
    worksheet.write('D'+str(row),i,blue_content)
    worksheet.write('E'+str(row),cociente(estos_productos[price_cols].sum(axis=1, numeric_only=True).sum(),len(estas_cotizaciones)),blue_content)
    worksheet.write('F'+str(row),'MN',blue_content)
    worksheet.write('G'+str(row), '100%',blue_content)
    worksheet.write('H'+str(row),'',blue_content)
    row=row+1
    worksheet.write('B'+str(row),str(row-11),blue_content)
    worksheet.write('C'+str(row),'NUMERO DE CLIENTES A LOS QUE SE LES COTIZA EN EL PERIODO',blue_content)
    worksheet.write('D'+str(row),i,blue_content)
    worksheet.write('E'+str(row),str(len(estas_cotizaciones['name'].unique())),blue_content)
    worksheet.write('F'+str(row),'CLIENTES',blue_content)
    worksheet.write('G'+str(row), '100%',blue_content)
    worksheet.write('H'+str(row),'',blue_content)
    row=row+1
    worksheet.write('B'+str(row),str(row-11),blue_content)
    worksheet.write('C'+str(row),'PROMEDIO COTIZADO POR CLIENTE',blue_content)
    worksheet.write('D'+str(row),i,blue_content)
    worksheet.write('E'+str(row),cociente(estos_productos[price_cols].sum(axis=1, numeric_only=True).sum(),len(estas_cotizaciones['name'].unique())),blue_content)
    worksheet.write('F'+str(row),'MN',blue_content)
    worksheet.write('G'+str(row), '100%',blue_content)
    worksheet.write('H'+str(row),'',blue_content)
    row=row+1
    worksheet.write('B'+str(row),str(row-11),blue_content)
    worksheet.write('C'+str(row),'NUMERO DE COTIZADORES EMPLEADOS',blue_content)
    worksheet.write('D'+str(row),i,blue_content)
    worksheet.write('E'+str(row),str(len(estas_cotizaciones['user_id'].unique())),blue_content)
    worksheet.write('F'+str(row),'COTIZADORES',blue_content)
    worksheet.write('G'+str(row), '100%',blue_content)
    worksheet.write('H'+str(row),'',blue_content)
    row=row+1
    worksheet.write('B'+str(row),str(row-11),blue_content)
    worksheet.write('C'+str(row),'PROMEDIO COTIZADO POR COTIZADOR',blue_content)
    worksheet.write('D'+str(row),i,blue_content)
    worksheet.write('E'+str(row),cociente(estos_productos[price_cols].sum(axis=1, numeric_only=True).sum(),len(estas_cotizaciones['user_id'].unique())),blue_content)
    worksheet.write('F'+str(row),'MN',blue_content)
    worksheet.write('G'+str(row), '100%',blue_content)
    worksheet.write('H'+str(row),'',blue_content)
    row=row+1


#ajustar columnas
worksheet.set_column('A:A',15)
worksheet.set_column('C:C',38)
worksheet.set_column('D:D',12)
worksheet.set_column('E:E',25)
worksheet.set_column('J:J',25)
worksheet.set_column('L:L',15)
worksheet.set_column('G:H',25)
worksheet.set_column('L:L',15)
worksheet.set_column('M:M',90)
worksheet.set_column('N:N',90)
worksheet.set_column('I:N',15)
worksheet.set_column('F:F',20)

#worksheet.set_landscape()
worksheet.set_paper(9)
worksheet.fit_to_pages(1, 1)  
workbook.close()
