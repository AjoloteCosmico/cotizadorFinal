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
# id=208
id=str(sys.argv[1])
#configurar la conexion a la base de datos
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
#Seccion para traer informacion de la base
# join para cobros
quotation=pd.read_sql("select * from quotations where id=" +str(id),cnx)

cart_products=pd.read_sql("select * from cart_products where quotation_id ="+str(id),cnx)
writer = pd.ExcelWriter('storage/report/ventas'+str(id)+'.xlsx', engine='xlsxwriter')

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
blue_footer_format_bold_kg = workbook.add_format({
    'bold': True,
    'bg_color': a_color,
    'text_wrap': True,
    'valign': 'top',
    'align': 'center',
    'border_color':'white',
    'font_color': 'white',
    'border': 1,
    'num_format': '#,##0.00',
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
blue_content_lite = workbook.add_format({
    'border': 1,
    'align': 'center',
    'valign': 'vcenter',
    'font_color': 'black',
    'bg_color':a_lite,
    'border_color':a_color,
    'font_size':10,
    'num_format': '[$$-409]#,##0.00'})
blue_content_unit_lite = workbook.add_format({
    'border': 1,
    'align': 'center',
    'valign': 'vcenter',
    'font_color': 'black',
    'bg_color':a_lite,
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
df=quotation[['id','user_id']]

df[0:1].to_excel(writer, sheet_name='Sheet1', startrow=7,startcol=6, header=False, index=False)
worksheet = writer.sheets['Sheet1']
#Encabezado del documento--------------

import datetime

currentDateTime = datetime.datetime.now()
date = currentDateTime.date()
year = date.strftime("%Y")

from tablas_dict import tablas
aceros=pd.read_sql('select * from steels ',cnx)
aceros.loc[aceros['caliber']=='EST 3 IN','caliber']='EST3'

pricelist_protectors=pd.read_sql('select * from price_list_protectors',cnx)
quotation_protectors=pd.read_sql('select quotation_protectors.*, protectors.sku from quotation_protectors  inner join protectors on protectors.protector=quotation_protectors.protector where quotation_id ='+str(id),cnx)
quotation_shlf=pd.read_sql('select * from selective_heavy_load_frames where quotation_id ='+str(id),cnx)
df[0:1].to_excel(writer, sheet_name='Sheet1', startrow=7,startcol=6, header=False, index=False)
# materials=pd.read_sql('select * from (materials left join price_list_screws on materials.price_list_screw_id= price_list_screws.id)left join price_lists on price_lists.id=materials.price_list_id',cnx)
# materials['type']=materials['type'].fillna('')
materials=pd.read_sql('select * from costos where quotation_id ='+str(id),cnx)
worksheet = writer.sheets['Sheet1']
#Encabezado del documento--------------
worksheet.merge_range('B2:F2', 'REPORTE POR COTIZACION ', negro_b)
worksheet.merge_range('B3:F3', 'VENTAS', negro_s)
worksheet.merge_range('B4:F4', 'PRECIOS ', negro_b)
worksheet.write('H2', 'AÑO', negro_b)

worksheet.write('I2', year, negro_b)
worksheet.merge_range('J2:K3', """FECHA DEL REPORTE
DD/MM/AAAA""", negro_b)

worksheet.write('L2', date, negro_b)
worksheet.insert_image("A1", "img/logo/logo.png",{"x_scale": 0.6, "y_scale": 0.6})
worksheet.merge_range('A6:A8', 'PDA', blue_header_format)
worksheet.merge_range('B6:B8', 'SKU	', blue_header_format)
worksheet.merge_range('C6:C8', 'CANT', blue_header_format)	
worksheet.merge_range('D6:D8', 'DESCRIPCION', blue_header_format)	
worksheet.merge_range('E6:E8', 'PRECIO UNIT', blue_header_format)	
worksheet.merge_range('F6:F8', 'PRECIO TOTAL', blue_header_format)	
worksheet.merge_range('G6:G8', 'CALIBRE	', blue_header_format)
worksheet.merge_range('H6:H8', 'KG UNIT	', blue_header_format)
worksheet.merge_range('I6:I8', 'KG TOTAL', blue_header_format)	
worksheet.merge_range('J6:J8', 'CTO X KG', blue_header_format)	
worksheet.merge_range('K6:K8', 'M2 UNIT	', blue_header_format)
worksheet.merge_range('L6:L8', 'M2 TOT', blue_header_format)

row_count=9
def ret_na(value):
    try:
        x=float(value)
    except:
        x='NA'
    try:
        if(np.isnan(x)):
            x='NA'
        if(np.isinf(x)):
            x='NA'
    except:
        x='NA'
    return x
def num(value):
    try:
        x=float(value)
    except:
        x=0
    if((np.isnan(x))|(np.isinf(x))):
        x=0
    return x
def secure_div(p,q):
    if(q==0):
        return 0.0
    else:
        return p/q
#iterar sobre los productos
cart_products_sin_transportes=cart_products.loc[~cart_products['type'].isin(['SINS','SUINS','SVIAT','SFLETE'])]
pda=1
for i in range(0,len(cart_products_sin_transportes)):
    
    def my_func(row, table_name):
        return row in table_name
    piezas=materials.loc[materials['type']==cart_products_sin_transportes['type'].values[i]]
    n=len(piezas)
    if(i%2==1):
        formato=blue_content
        formato_unit=blue_content_unit
    if(i%2==0):
        formato=blue_content_lite
        formato_unit=blue_content_unit_lite
    #PIEZAS PIEZAS PIEZAS CICLO DE PIEZAS
    for j in range(0,n):
        # print('entre al ciclo')
        # print(piezas['cost'].fillna(0).values[j],piezas['amount'])
        # costo= piezas['cost'].fillna(0).values[j].sum()
        # cant= piezas['amount'].fillna(0).values[j].sum()*products['amount'].values[i]
        worksheet.write('A'+str(row_count), str(pda), formato)
        #sku
        worksheet.write('B'+str(row_count), piezas['sku'].fillna('').values[j], formato)
        worksheet.write('C'+str(row_count), str(piezas['cant'].values[j]), formato)
        worksheet.write('D'+str(row_count), str(piezas['description'].values[j]), formato)
        #costos
        worksheet.write('E'+str(row_count),piezas['precio_unit'].values[j], formato)
        worksheet.write('F'+str(row_count),piezas['precio_total'].values[j], formato)
        #calibre
        worksheet.write('G'+str(row_count), piezas['calibre'].values[j], formato_unit)
        #pesos
        worksheet.write('H'+str(row_count),piezas['kg_unit'].values[j], formato_unit)
        worksheet.write('I'+str(row_count),piezas['kg_unit'].values[j]*piezas['cant'].values[j], formato_unit)
        worksheet.write('J'+str(row_count),secure_div(piezas['precio_unit'].values[j], piezas['kg_unit'].values[j]), formato)
        #medidas
        worksheet.write('K'+str(row_count), piezas['m2_unit'].values[j], formato_unit)
        worksheet.write('L'+str(row_count), piezas['m2_unit'].values[j]*piezas['cant'].values[j], formato_unit)
        row_count=row_count+1
        pda=pda+1
trow=row_count


#TOTALES
worksheet.merge_range('A'+str(trow+1)+':C'+str(trow), 'TOTAL (EQV M.N)', blue_header_format_bold)
worksheet.write_formula('F'+str(trow),'{=SUM(F9:F'+str(trow-1)+')}',blue_footer_format_bold)
worksheet.write_formula('I'+str(trow),'{=SUM(I9:I'+str(trow-1)+')}',blue_footer_format_bold_kg)
worksheet.write_formula('L'+str(trow),'{=SUM(L9:L'+str(trow-1)+')}',blue_footer_format_bold_kg)


#RESUMEN
worksheet.merge_range('B'+str(trow+3)+':C'+str(trow+4),'RESUMEN DE KILOS',blue_header_format_bold)
#subtabla 1, kilos
materials['kg_total']=materials['kg_unit']*materials['cant']
worksheet.write('B'+str(trow+5),'KILOS',blue_header_format)
worksheet.write('C'+str(trow+5),'CALIBRE',blue_header_format)
suma_peso=0
art_i=0
for i in materials.loc[materials['kg_unit']>0,'calibre'].astype(str).unique():
        print(i)
        p=materials.loc[materials['calibre']==i]
        sum_kg=p['kg_total'].fillna(0).sum()
        suma_peso=suma_peso+sum_kg
        worksheet.write('B'+str(trow+6+art_i),sum_kg,blue_content)
        worksheet.write('C'+str(trow+6+art_i),i,blue_content)
        art_i=art_i+1

worksheet.write('B'+str(trow+5+len(materials['calibre'].unique())),suma_peso,blue_footer_format_bold_kg)
#subtabla2 costos F-G-H-I-J-K-L-M-N
#                     F-G-H-I-J-K-L-M-N
worksheet.merge_range('F'+str(trow+4)+':I'+str(trow+4),'RESUMEN DE COSTOS',blue_header_format_bold)
worksheet.write('J'+str(trow+4),'POSICION',blue_header_format)
worksheet.write('K'+str(trow+4),'KILOS',blue_header_format)
worksheet.write('L'+str(trow+4),'PORCENTAJE',blue_header_format)

worksheet.merge_range('F'+str(trow+5)+':I'+str(trow+5),'CONTRATO UNITARIO SOLO MATERIALES',blue_header_format)
worksheet.merge_range('F'+str(trow+6)+':I'+str(trow+6),'CONTRATO UNITARIO SOLO ARMADO',blue_header_format)
worksheet.merge_range('F'+str(trow+7)+':I'+str(trow+7),'CONTRATO UNITARIO SOLO TRASLADO',blue_header_format)
worksheet.merge_range('F'+str(trow+8)+':I'+str(trow+8),'CONTRATO UNITARIO COMBINADO',blue_header_format)

precio_total=materials['precio_total'].sum()
solo_materiales=materials.loc[~materials['type'].isin(['SINS','SUINS','SVIAT','SFLETE'])]
solo_armado=materials.loc[materials['type'].isin(['SINS','SUINS'])]
solo_traslado=materials.loc[materials['type'].isin(['SVIAT','SFLETE'])]

worksheet.write('J'+str(trow+5),solo_materiales['precio_total'].sum(),blue_content)
worksheet.write('K'+str(trow+5),solo_materiales['kg_total'].sum(),blue_content_unit)
worksheet.write('L'+str(trow+5),"{:.2f}".format(solo_materiales['precio_total'].sum()/precio_total*100)+'%',blue_content_unit)

worksheet.write('J'+str(trow+6),solo_armado['precio_total'].sum(),blue_content)
worksheet.write('K'+str(trow+6),solo_armado['kg_total'].sum(),blue_content_unit)
worksheet.write('L'+str(trow+6),"{:.2f}".format(solo_armado['precio_total'].sum()/precio_total*100)+'%',blue_content_unit)

worksheet.write('J'+str(trow+7),solo_traslado['precio_total'].sum(),blue_content)
worksheet.write('K'+str(trow+7),solo_traslado['kg_total'].sum(),blue_content_unit)
worksheet.write('L'+str(trow+7),"{:.2f}".format(solo_traslado['precio_total'].sum()/precio_total*100)+'%',blue_content_unit)

worksheet.write('J'+str(trow+8),materials['precio_total'].sum(),blue_content)
worksheet.write('K'+str(trow+8),materials['kg_total'].sum(),blue_content_unit)
worksheet.write('L'+str(trow+8),'100%',blue_content)
#TODO: calcular bien esto, to6al menos iva


worksheet.set_column('B:B',20)
worksheet.set_column('D:D',30)
worksheet.set_column('J:J',15)
worksheet.set_column('E:E',15)
worksheet.set_column('F:F',15)
worksheet.set_column('G:L',15)
worksheet.set_column('N:R',15)
#worksheet.set_landscape()
worksheet.set_paper(9)
worksheet.fit_to_pages(1, 1)  
workbook.close()