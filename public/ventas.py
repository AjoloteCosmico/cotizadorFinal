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

a_color='#354F84'
b_color='#91959E'
a_lite='#b4c7ed'
# Conectar a DB
cnx = mysql.connector.connect(user=DB_USERNAME,
                              password=DB_PASSWORD,
                              host='localhost',
                              port=DB_PORT,
                              database=DB_DATABASE,
                              use_pure=False)
#Seccion para traer informacion de la base
# join para cobros
# cobros=pd.read_sql('Select cobros.* ,customers.customer,internal_orders.invoice, users.name from ((cobros inner join internal_orders on internal_orders.id = cobros.order_id) inner join customers on customers.id = internal_orders.customer_id )inner join users on cobros.capturo=users.id',cnx)
quotation=pd.read_sql("select * from quotations where id=" +str(id),cnx)

factores=pd.read_sql('select * from price_lists',cnx)
#traer datos de los pedidos
# pedidos=pd.read_sql("""Select internal_orders.* ,customers.clave,customers.alias,
# coins.exchange_sell, coins.coin, coins.symbol,coins.code
# from ((
#     internal_orders
#     inner join customers on customers.id = internal_orders.customer_id )
#     inner join coins on internal_orders.coin_id = coins.id)
#      """,cnx)
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
        try: 
            factor=factores.loc[factores['caliber']==p['caliber'].values[0],'f_total'].values[0]
        except:
            print('falló al buscar factor cal',p['caliber'].values[0])
        
            factor=4.15
        p=p.assign(factor=factor)
    products=products.append(p,ignore_index=True)
products=products.loc[products['amount']>0].reset_index(drop=True)
cols_to_fill_str=['description','protector','model','sku']
products[cols_to_fill_str]=products[cols_to_fill_str].fillna('')
cols_kg=['weight','total_kg','total_weight','weight_kg']
cols_m2=['m2','total_m2']
price_cols=['price','total_price','import','unit_price']
products[cols_kg+cols_m2+price_cols+['amount']]=products[cols_kg+cols_m2+price_cols+['amount']].fillna(0)

pricelist_protectors=pd.read_sql('select * from price_list_protectors',cnx)
quotation_protectors=pd.read_sql('select quotation_protectors.*, protectors.sku from quotation_protectors  inner join protectors on protectors.protector=quotation_protectors.protector where quotation_id ='+str(id),cnx)
quotation_shlf=pd.read_sql('select * from selective_heavy_load_frames where quotation_id ='+str(id),cnx)
df[0:1].to_excel(writer, sheet_name='Sheet1', startrow=7,startcol=6, header=False, index=False)
materials=pd.read_sql('select * from (materials left join price_list_screws on materials.price_list_screw_id= price_list_screws.id)left join price_lists on price_lists.id=materials.price_list_id',cnx)
materials['type']=materials['type'].fillna('')
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
worksheet.merge_range('A6:A8', 'PDA', blue_header_format)
worksheet.merge_range('B6:B8', 'SKU	', blue_header_format)
worksheet.merge_range('C6:C8', 'CANT', blue_header_format)	
worksheet.merge_range('D6:D8', 'DESCRIPCION', blue_header_format)	
worksheet.merge_range('E6:E8', 'PRECIO VNT UNIT', blue_header_format)	
worksheet.merge_range('F6:F8', 'PRECIO VNT TOTAL', blue_header_format)	
worksheet.merge_range('G6:G8', 'CALIBRE	', blue_header_format)
worksheet.merge_range('H6:H8', 'KG UNIT	', blue_header_format)
worksheet.merge_range('I6:I8', 'KG TOTAL', blue_header_format)	

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
#iterar sobre los productos

for i in range(0,len(products)):
    if(i%2==1):
        formato=blue_content
        formato_unit=blue_content_unit
    if(i%2==0):
        formato=blue_content_lite
        formato_unit=blue_content_unit_lite
    
    def my_func(row, table_name):
        return row in table_name
    piezas=materials.loc[materials['product'].apply(my_func,table_name=products['tabla'].values[i])]
    costo_product=products['cost'].values[i]
    n=len(piezas)
    piezas['type']=piezas['type'].fillna('')
    print(n,products['tabla'].values[i],row_count,products['cost'].values[i])
    #pda
    worksheet.write('A'+str(row_count), str(i*n+1), formato)
    #sku
    worksheet.write('B'+str(row_count), products['sku'].values[i], formato)
    worksheet.write('C'+str(row_count), str(products['amount'].values[i]), formato)
    #descripcion
    worksheet.write('D'+str(row_count), tablas[products['tabla'].values[i]]+products['protector'].values[i]+' '+products['model'].values[i], formato)
    #costos
    print(costo_product)
    worksheet.write('E'+str(row_count), products[price_cols].sum(axis=1, numeric_only=True)[i]/products['factor'].values[i], formato)
    worksheet.write('F'+str(row_count), products['amount'].values[i]*products[price_cols].sum(axis=1, numeric_only=True)[i]/products['factor'].values[i], formato)
    #calibre
    worksheet.write('G'+str(row_count), str(ret_na(products['caliber'].values[i])), formato)
    print('a punto de krakear',products['amount'].values[i],' ------------------')
    #pesos
    worksheet.write('H'+str(row_count),(num(products['total_weight'].values[i])+num(products['total_kg'].values[i])+products['weight'].values[i]+products['weight_kg'].values[i])/products['amount'].values[i], formato_unit)
    worksheet.write('I'+str(row_count),(num(products['total_weight'].values[i])+num(products['total_kg'].values[i])+products['weight'].values[i]+products['weight_kg'].values[i]), formato_unit)
    row_count=row_count+1
    #PIEZAS PIEZAS PIEZAS CICLO DE PIEZAS
    for j in range(0,n):
        
        print('entre al ciclo')
        print(piezas['cost'].fillna(0).values[j],piezas['amount'])
        costo= piezas['cost'].fillna(0).values[j].sum()
        cant= piezas['amount'].fillna(0).values[j].sum()
        worksheet.write('A'+str(row_count), str(i*n+2+j), formato)
        #sku
        worksheet.write('B'+str(row_count), ''.join(materials['sku'].fillna('').values[0]), formato)
        worksheet.write('C'+str(row_count), str(piezas['amount'].values[j]), formato)
        worksheet.write('D'+str(row_count), str(piezas['description'].fillna('').values[j][0])+str(piezas['piece'].fillna('').values[j]), formato)
        #costos
        worksheet.write('E'+str(row_count),costo, formato)
        worksheet.write('F'+str(row_count), cant*costo, formato)
        #calibre
        worksheet.write('G'+str(row_count), piezas['type'].values[j][0]+piezas['type'].values[j][1], formato_unit)
        #pesos
        worksheet.write('H'+str(row_count),str(0.0), formato_unit)
        worksheet.write('I'+str(row_count),str(0.0), formato_unit)
        row_count=row_count+1
trow=row_count


#TOTALES
worksheet.merge_range('C'+str(trow+1)+':E'+str(trow), 'TOTAL (EQV M.N)', blue_header_format_bold)
worksheet.write_formula('F'+str(trow),'{=SUM(F9:F'+str(trow-1)+')}',blue_footer_format_bold)
worksheet.write_formula('I'+str(trow),'{=SUM(I9:I'+str(trow-1)+')}',blue_footer_format_bold_kg)

#subtabla
worksheet.write('F'+str(trow+5), 'PRECIO DE VENTA POR POSICION', blue_header_format_bold)
worksheet.write_formula('E'+str(trow+5), '{=(F'+str(trow)+'* '+str(row_count-9)+')}', blue_content)
worksheet.write_formula('I'+str(trow+5), '{=(H'+str(trow)+'/ I'+str(trow)+')}', blue_content)




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