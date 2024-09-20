import sys
import mysql.connector
import xlsxwriter
import pandas as pd
import sys
import mysql.connector
import numpy as np
import os
import formatos
import tablas_dict
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
a_lite='#b4c7ed'
b_color='#91959E'
# Conectar a DB
cnx = mysql.connector.connect(user=DB_USERNAME,
                              password=DB_PASSWORD,
                              host='localhost',
                              port=DB_PORT,
                              database=DB_DATABASE,
                              use_pure=False)

quotation=pd.read_sql("select * from quotations where id=" +str(id),cnx)

writer = pd.ExcelWriter('storage/report/margen'+str(id)+'.xlsx', engine='xlsxwriter')
workbook = writer.book
df=quotation[['id','user_id']]

df[0:1].to_excel(writer, sheet_name='Sheet1', startrow=7,startcol=3, header=False, index=False)
worksheet = writer.sheets['Sheet1']

#agregarle los formatos
# workbook=formatos.add_formats(workbook)
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
#Encabezado del documento--------------
worksheet.merge_range('B2:F2', 'REPORTE POR COTIZACION ', negro_b)
worksheet.merge_range('B3:F3', 'ADMINISTRATIVO', negro_s)
worksheet.merge_range('B4:F4', 'COSTOS ', negro_b)
worksheet.write('H2', 'AÑO', negro_b)
import datetime

currentDateTime = datetime.datetime.now()
date = currentDateTime.date()
year = date.strftime("%Y")
aceros=pd.read_sql('select * from steels ',cnx)
aceros.loc[aceros['caliber']=='EST 3 IN','caliber']='EST3'

def cociente(a,b):
    if(b>0):
        return a/b
    else: 
        return 0

products=pd.DataFrame()
for i in tablas_dict.tablas:
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

products=products.loc[products['amount']>0].reset_index(drop=True)
cols_to_fill_str=['description','protector','model','sku']
products[cols_to_fill_str]=products[cols_to_fill_str].fillna('')
cols_kg=['weight','total_kg','total_weight','weight_kg']
cols_m2=['m2','total_m2']
price_cols=['price','total_price','import','unit_price']
products[cols_kg+cols_m2]=products[cols_kg+cols_m2].fillna(0)


#trayendo informacion de materiales
materials=pd.read_sql('select * from (materials left join price_list_screws on materials.price_list_screw_id= price_list_screws.id)left join price_lists on price_lists.id=materials.price_list_id',cnx)
materials['type']=materials['type'].fillna('')
materials=materials.rename(columns={'product':'tabla'})
#calculando costos
used_materials=products[['tabla','quotation_id']].merge(materials,how='inner',on='tabla')

precio_pintura=pd.read_sql("select cost from price_lists where description like 'PINTURA'",cnx).values[0]
costo_instalacion=products.loc[products['tabla'].isin(['quotation_uninstalls','quotation_installs',]),'cost'].sum()

costo_fletes=products.loc[products['tabla'].isin(['freights','packagings']),'cost'].sum()

costo_acero=products.loc[products['caliber'].isna(),'cost'].sum()

costo_otros=products['cost'].sum()-costo_acero-costo_instalacion-costo_fletes
costo_pintura=products.loc[products['caliber'].notna(),'cost'].sum()
costo_total=costo_pintura+products['cost'].sum()+used_materials['cost'].fillna(0).sum(axis=1, numeric_only=True).sum()
precio_venta=products[price_cols].sum(axis=1,numeric_only=True).sum()
#Llenado del archivo
#Cabeceras -------
worksheet.write('I2', year, negro_b)
worksheet.merge_range('J2:K3', """FECHA DEL REPORTE
DD/MM/AAAA""", negro_b)

worksheet.write('L2', date, negro_b)
worksheet.insert_image("A1", "img/logo/logo.png",{"x_scale": 0.6, "y_scale": 0.6})

worksheet.merge_range('B6:C6', 'COTIZACION', blue_header_format)
worksheet.merge_range('B7:C7', 'COSTO DE KG PINTURA	', blue_header_format)
worksheet.merge_range('B8:C8', 'POSICIONES', blue_header_format)	
worksheet.merge_range('B9:C9', 'COTIZACION', blue_header_format)	
worksheet.merge_range('B10:C10', 'SISTEMA', blue_header_format)	
worksheet.merge_range('B11:C11', 'ACERO', blue_header_format)	
worksheet.merge_range('B12:C12', 'TORNILLERIA 	', blue_header_format)
worksheet.merge_range('B13:C13', 'OTROS	', blue_header_format)
worksheet.merge_range('B14:C14', 'INSTALACION', blue_header_format)	
worksheet.merge_range('B15:C15', 'FLETE', blue_header_format)
worksheet.merge_range('B16:C16', 'PINTURA	', blue_header_format)
worksheet.merge_range('B17:C17', 'C.C.V.', blue_header_format)	
worksheet.merge_range('B18:C18', 'SUMA DE COSTOS', blue_header_format)	

worksheet.merge_range('B20:C20', 'PRECIO VENTA', blue_header_format)	
worksheet.merge_range('B21:C21', 'COSTO (-)', blue_header_format)
worksheet.merge_range('B22:C22', 'RESTA', blue_header_format)
worksheet.merge_range('B23:C23', '%', blue_header_format)
worksheet.merge_range('B24:C24', 'PRECIO POR POS', blue_header_format)
worksheet.merge_range('B25:C25', 'DIAS DE OUPACION', blue_header_format)
worksheet.merge_range('B26:C26', 'OPERARIOS', blue_header_format)	
worksheet.merge_range('B27:C27', 'KILOS PROYECTO', blue_header_format)	
worksheet.merge_range('B28:C28', 'COSTO POR KILO', blue_header_format)	
worksheet.merge_range('B29:C29', 'PRECIO PROPUESTO KG', blue_header_format)	

#Fila 2
worksheet.merge_range('D6:E6', quotation['invoice'].values[0], blue_content)
worksheet.merge_range('D7:E7', len(used_materials)+len(products), blue_content)
worksheet.merge_range('D8:E8',int(precio_pintura), blue_content)	
worksheet.merge_range('D9:E9', 'COT-'+ quotation['invoice'].values[0], blue_content)	
worksheet.merge_range('D10:E10', quotation['type'].values[0], blue_content)	
worksheet.merge_range('D11:E11',products.loc[products['caliber'].notna(),'cost'].sum(), blue_content)	
worksheet.merge_range('D12:E12', used_materials.loc[used_materials['price_list_screw_id'].notna(),'cost'].fillna(0).sum(axis=1, numeric_only=True).sum(), blue_content)
worksheet.merge_range('D13:E13', costo_otros, blue_content)
worksheet.merge_range('D14:E14', costo_instalacion, blue_content)	
worksheet.merge_range('D15:E15', costo_fletes, blue_content)
worksheet.merge_range('D16:E16', products[cols_m2].sum(axis=1, numeric_only=True).sum()*int(precio_pintura), blue_content)
worksheet.merge_range('D17:E17', costo_acero+used_materials.loc[used_materials['price_list_screw_id'].notna(),'cost'].fillna(0).sum(axis=1, numeric_only=True).sum(), blue_content)	
worksheet.merge_range('D18:E18', costo_total, blue_content)	
	
worksheet.merge_range('D20:E20', precio_venta, blue_content)	
worksheet.merge_range('D21:E21', costo_total, blue_content)
worksheet.merge_range('D22:E22', precio_venta-costo_total, blue_content)
worksheet.merge_range('D23:E23', '{:.2f}'.format(cociente((precio_venta-costo_total)*100,precio_venta))+'%', blue_content)
worksheet.merge_range('D24:E24', cociente(costo_total,(len(used_materials)+len(products))), blue_content)

worksheet.merge_range('D27:E27',products[cols_kg].sum(axis=1,numeric_only=True).sum(), blue_content_unit)	
worksheet.merge_range('D28:E28', cociente(costo_total,products[cols_kg].sum(axis=1,numeric_only=True).sum()), blue_content)	
worksheet.merge_range('D29:E29', cociente(precio_venta,products[cols_kg].sum(axis=1,numeric_only=True).sum()), blue_content)


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