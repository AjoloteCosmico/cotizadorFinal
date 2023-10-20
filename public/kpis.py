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

id=str(sys.argv[1])
#configurar la conexion a la base de datos
DB_USERNAME = os.getenv('DB_USERNAME')
DB_DATABASE = os.getenv('DB_DATABASE')
DB_PASSWORD = os.getenv('DB_PASSWORD')
DB_PORT = os.getenv('DB_PORT')
DB_USERNAME = os.getenv('DB_USERNAME')
DB_DATABASE = os.getenv('DB_DATABASE')
DB_PASSWORD = os.getenv('DB_PASSWORD')
DB_PORT = os.getenv('DB_PORT')

a_color='#354F84'
a_lite='#b4c7ed'
b_color='#91959E'
# Conectar a DB
cnx = mysql.connector.connect(user=DB_USERNAME,
                              password=DB_PASSWORD,
                              host='localhost',
                              port=DB_PORT,
                              database=DB_DATABASE,
                              use_pure=False)

writer = pd.ExcelWriter('storage/report/kpis'+str(id)+'.xlsx', engine='xlsxwriter')

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

blue_transparent_bold = workbook.add_format({
    'bold': 0,
    'border': 1,
    'align': 'center',
    'valign': 'vcenter',
    'font_color': 'black',

    'font_size':11,
    'border_color':a_color,})

blue_transparent_bold_result = workbook.add_format({
    'bold': True,
    'bg_color': a_color,
    'border': 1,
    'align': 'center',
    'valign': 'vcenter',
    'border_color':'white',
    'font_color': 'white',
    'font_size':11,
    'border_color':a_color,})

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



#traer datos
cotizaciones=pd.read_sql('select quotations.*, customers.customer,users.name from (quotations inner join customers on quotations.customer_id = customers.id) inner join users on users.id = quotations.user_id ',cnx)
cotizaciones['created_at']=pd.to_datetime(cotizaciones['created_at'])
cotizaciones=cotizaciones.assign(mes=cotizaciones.created_at.astype(str).str[5:7])

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
Meses = {'01': 'Enero', '02':'Febrero', '03':'Marzo', '04':'Abril', '05':'Mayo','06': 'Junio',
                   '07': 'Julio', '08':'Agosto', '09':'Septiembre', '10':'Octubre', '11':'Noviembre', '12':'Diciembre'}
        

worksheet = writer.sheets['Sheet1']
#Encabezado del documento--------------
worksheet.merge_range('B2:F2', 'REPORTE POR COTIZACION ', negro_b)
worksheet.merge_range('B3:F3', "KPI's", negro_s)
worksheet.merge_range('B4:F4', 'MONTOS TOTALES ', negro_b)
worksheet.write('H2', 'AÑO', negro_b)

worksheet.write('I2', year, negro_b)
worksheet.merge_range('J2:K2', """FECHA DEL REPORTE """, negro_b)

worksheet.merge_range('J3:K3', date, negro_b)
worksheet.insert_image("A1", "img/logo/logo.png",{"x_scale": 0.6, "y_scale": 0.6})

#Cabecera 1 Meses
worksheet.merge_range('B6:I6', 'CONCENTRADO DE COTIZACIONES MENSUALES (NO INCLUYE ACTUALIZACIONES)', blue_header_format)
worksheet.merge_range('B7:B9', 'MES', blue_header_format)
worksheet.merge_range('C7:C9', 'USD', blue_header_format)
worksheet.merge_range('D7:D9', 'T.C', blue_header_format)
worksheet.merge_range('E7:E9', "$20.00", blue_content_bold_dll)
worksheet.merge_range('F7:F9', 'PESOS', blue_header_format)
worksheet.merge_range('G7:G9', 'SUMA', blue_header_format)
worksheet.merge_range('H7:H9', 'NO. COT', blue_header_format)
worksheet.merge_range('I7:I9', "NO. CLIEN", blue_header_format)
row=10

for i in Meses:
    cotizaciones_mes=cotizaciones.loc[cotizaciones['mes']==i]
    worksheet.write('B'+str(row),Meses[i],blue_content)
    worksheet.write('C'+str(row),0,blue_content_dll)
    worksheet.merge_range('D'+str(row)+':E'+str(row),0,blue_content)
    worksheet.write('F'+str(row),products.loc[products['quotation_id'].isin(cotizaciones_mes['id'].unique()),price_cols].sum(axis=1, numeric_only=True).sum(),blue_content)
    worksheet.write('G'+str(row),products.loc[products['quotation_id'].isin(cotizaciones_mes['id'].unique()),price_cols].sum(axis=1, numeric_only=True).sum(),blue_content)
    worksheet.write('H'+str(row),str(len(cotizaciones_mes)),blue_content_unit)
    worksheet.write('I'+str(row),str(len(cotizaciones_mes['customer'].unique())),blue_content_unit)
    row=row+1
<<<<<<< HEAD

#Sumas 
numero_De_Filas = 12
total=0
renglones=0
for i in range(numero_De_Filas):
    worksheet.write(9 + i, 2, i + 1, blue_content)  
    total=total+(i+1)
    renglones=i
worksheet.write(renglones+10, 2, total, blue_footer_format_bold)

total=0
arreglo2 = [None]*13
for i in range(numero_De_Filas):
    worksheet.write(9 + i, 3, ((i + 1)*20), blue_content)  
    total=total+(i+1)
    arreglo2[i]=((i+1)*20)
    renglones=i
worksheet.merge_range('D22:E22', total, blue_footer_format_bold)

total=0
arreglo1 = [None]*13
for i in range(numero_De_Filas):
    worksheet.write(9 + i, 5, i + 501, blue_content)  
    total=total+(i+501)
    arreglo1[i]=(i+501)
    renglones=i
worksheet.write(renglones+10, 5, total, blue_footer_format_bold)

total=0
arreglo3 = [None]*13
for i in range(numero_De_Filas):
    arreglo3[i] = arreglo1[i] + arreglo2[i]
    worksheet.write(9 + i, 6,arreglo3[i], blue_content) 
    total=total+(arreglo3[i])
    renglones=i
worksheet.write(renglones+10, 6, total, blue_footer_format_bold)

total=0
for i in range(numero_De_Filas):
    worksheet.write(9 + i, 7, i, blue_transparent_bold )  
    total=total+(i+1)
    renglones=i
worksheet.write(renglones+10, 7, total, blue_transparent_bold_result )

total=0
for i in range(numero_De_Filas):
    worksheet.write(9 + i, 8, i, blue_transparent_bold  )  
    total=total+(i+1)
    renglones=i
worksheet.write(renglones+10, 8, total, blue_transparent_bold_result )

#Cabecera 2 vendedores
worksheet.merge_range('M6:O6', 'ENERO', blue_header_format)
worksheet.merge_range('M7:M8', 'VENDEDOR', blue_header_format)
worksheet.merge_range('N7:O7', 'COTIZACIONES', blue_header_format)
worksheet.write('N8', 'NO.', blue_header_format)
worksheet.write('O8', '$', blue_header_format)

=======
#FILA DE TOTALES AQUI, EJ
worksheet.write('F'+str(row),products[price_cols].sum(axis=1, numeric_only=True).sum(),blue_footer_format_bold)
    
>>>>>>> 29703cb79cab0ee0caf2fc9d81b814d1872b00df
#iterando sobre vendedores
row=9
suma_N = 0
suma_O = 0
for i in cotizaciones['user_id'].unique():
    cotizaciones_vendedor=cotizaciones.loc[cotizaciones['user_id']==i]
    worksheet.write('M'+str(row),cotizaciones.loc[cotizaciones['user_id']==i,'name'].values[0],blue_content)
    worksheet.write('N'+str(row),str(len(cotizaciones_vendedor)),blue_content_unit)
    
    suma_N += len(cotizaciones_vendedor)

    total_O = products.loc[products['quotation_id'].isin(cotizaciones_vendedor['id'].unique()), price_cols].sum(axis=1, numeric_only=True).sum()
    worksheet.write('O' + str(row), total_O, blue_content)
    suma_O += total_O

    row=row+1
worksheet.write(row-1, 13 , suma_N, blue_transparent_bold_result )
worksheet.write(row-1, 14 , suma_O, blue_transparent_bold_result )


#Cabecera 3 ingenieros
worksheet.merge_range('D26:G26', 'ENERO', blue_header_format)
worksheet.merge_range('D27:D28', 'Ingeniero', blue_header_format)
worksheet.merge_range('E27:G27', 'ACTIVIDADES', blue_header_format)
worksheet.write('E28', "NO", blue_header_format)
worksheet.write('F28', "INGENIERIA (HORAS)", blue_header_format)
worksheet.write('G28', "OBRAS", blue_header_format)

#Ingenieros
worksheet.write('D29', "OSCAR", blue_content)
worksheet.write('D30', "ALDO", blue_content)
worksheet.write('D31', "GERMAN", blue_content)
worksheet.write('D32', "MIGUEL", blue_content)

#Sumas Ingenieros
worksheet.write('E33', "SUMA", blue_transparent_bold_result)
worksheet.write('F33', "SUMA", blue_transparent_bold_result)


#ajustar columnas
worksheet.set_column('A:A',15)
worksheet.set_column('B:B',15)
worksheet.set_column('D:D',15)
worksheet.set_column('E:F',25)
worksheet.set_column('D:E',9)
#worksheet.set_column('F:F',20)
worksheet.set_column('J:J',25)
worksheet.set_column('L:L',15)
worksheet.set_column('G:G',25)
worksheet.set_column('H:H',15)
worksheet.set_column('L:L',15)
<<<<<<< HEAD
worksheet.set_column('L:M',90)
worksheet.set_column('N:O',30)
=======
worksheet.set_column('M:M',15)
worksheet.set_column('N:N',15)
>>>>>>> 29703cb79cab0ee0caf2fc9d81b814d1872b00df
worksheet.set_column('I:N',15)
worksheet.set_column('P:T',15)

#worksheet.set_landscape()
worksheet.set_paper(9)
worksheet.fit_to_pages(1,1)  
workbook.close()
