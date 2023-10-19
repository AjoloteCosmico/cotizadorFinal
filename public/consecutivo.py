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
id=str(sys.argv[1])

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

writer = pd.ExcelWriter('storage/report/consecutivo'+str(id)+'.xlsx', engine='xlsxwriter')

workbook = writer.book
##FORMATOS PARA EL TITULO------------------------------------------------------------------------------

a_color='#354F84'
a_lite='#b4c7ed'
b_color='#91959E'
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

#Conectarse a la base
#traer datos


cotizaciones=pd.read_sql('select quotations.*, customers.customer,users.name from (quotations inner join customers on quotations.customer_id = customers.id) inner join users on users.id = quotations.user_id ',cnx)


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


worksheet.merge_range('B6:B8', 'NO', blue_header_format)
worksheet.merge_range('C6:C8', 'DIAS', blue_header_format)
worksheet.merge_range('D6:D8', '# COT', blue_header_format)
worksheet.merge_range('E6:E8', "F. ENTREGA", blue_header_format)
worksheet.merge_range('F6:F8', 'VENDEDOR', blue_header_format)
worksheet.merge_range('G6:G8', 'CLIENTE', blue_header_format)
worksheet.merge_range('H6:H8', 'PRIO', blue_header_format)
worksheet.merge_range('I6:I8', "MONTO", blue_header_format)
worksheet.merge_range('J6:J8', 'MONTO MN', blue_header_format)
worksheet.merge_range('K6:K8', 'DESCRIPCION', blue_header_format)
worksheet.merge_range('L6:L8', 'KILOS', blue_header_format)
worksheet.merge_range('M6:M8', "ENCARGADO", blue_header_format)
worksheet.merge_range('N6:N8', 'OBSERVACIONES', blue_header_format)

price_cols=['price','total_price','import','unit_price']
aceros=pd.read_sql('select * from steels ',cnx)
aceros.loc[aceros['caliber']=='EST 3 IN','caliber']='EST3'

def cociente(a,b):
    if(b>0):
        return a/b
    else: 
        return 0
import tablas_dict
for j in range(len(cotizaciones)):
    products=pd.DataFrame()
    for i in tablas_dict.tablas:
        # print(i)
        #buscar en la base de datos todos los productos de esta tabla
        #pertenecientes a la cotizacion pedida por el usuario.
        p=pd.read_sql('select * from '+i+' where quotation_id = '+str(cotizaciones['id'].values[0]),cnx)
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
    materials=materials.rename(columns={'product':'tabla'})
    #calculando costos
    used_materials=products[['tabla','quotation_id']].merge(materials,how='inner',on='tabla')

    precio_pintura=pd.read_sql("select cost from price_lists where description like 'PINTURA'",cnx).values[0]
    costo_pintura=products.loc[products['caliber'].notna(),'cost'].sum()
    costo_total=costo_pintura+products['cost'].sum()+used_materials['cost'].fillna(0).sum(axis=1, numeric_only=True).sum()
    precio_venta=products[price_cols].sum(axis=1,numeric_only=True).sum()
    # print(used_materials)
    worksheet.write('B'+str(j+9),str(j+1),blue_content)
    worksheet.write('C'+str(j+9),' ',blue_content)
    worksheet.write('D'+str(j+9),cotizaciones['invoice'].values[j],blue_content)
    worksheet.write('E'+str(j+9),' ',blue_content)
    worksheet.write('F'+str(j+9),cotizaciones['customer'].values[j],blue_content)
    worksheet.write('G'+str(j+9),cotizaciones['name'].values[j],blue_content)
    worksheet.write('H'+str(j+9),'',blue_content)
    worksheet.write('I'+str(j+9),precio_venta*20,blue_content)
    worksheet.write('J'+str(j+9),precio_venta,blue_content)
    worksheet.write('K'+str(j+9),cotizaciones['system'].values[j],blue_content)
    worksheet.write('L'+str(j+9),kilos,blue_content_unit)
    worksheet.write('M'+str(j+9),'',blue_content)
    worksheet.write('N'+str(j+9),'',blue_content)
#ajustar columnas
worksheet.set_column('A:A',15)
worksheet.set_column('D:D',20)
worksheet.set_column('F:F',25)
worksheet.set_column('L:L',15)
worksheet.set_column('G:G',15)
worksheet.set_column('H:H',15)
worksheet.set_column('I:N',15)
worksheet.set_column('P:T',15)

#worksheet.set_landscape()
worksheet.set_paper(9)
worksheet.fit_to_pages(1, 1)  
workbook.close()
