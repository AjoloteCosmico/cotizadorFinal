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
# cobros=pd.read_sql('Select cobros.* ,customers.customer,internal_orders.invoice, users.name from ((cobros inner join internal_orders on internal_orders.id = cobros.order_id) inner join customers on customers.id = internal_orders.customer_id )inner join users on cobros.capturo=users.id',cnx)
quotation=pd.read_sql("select * from quotations where id=" +str(id),cnx)
#traer datos de los pedidos
# pedidos=pd.read_sql("""Select internal_orders.* ,customers.clave,customers.alias,
# coins.exchange_sell, coins.coin, coins.symbol,coins.code
# from ((
#     internal_orders
#     inner join customers on customers.id = internal_orders.customer_id )
#     inner join coins on internal_orders.coin_id = coins.id)
#      """,cnx)
writer = pd.ExcelWriter('storage/report/administrativo'+str(id)+'.xlsx', engine='xlsxwriter')

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
    products=products.append(p,ignore_index=True)

products=products.loc[products['amount']>0].reset_index(drop=True)
cols_to_fill_str=['description','protector','model','sku']
products[cols_to_fill_str]=products[cols_to_fill_str].fillna('')
cols_kg=['weight','total_kg','total_weight','weight_kg']
cols_m2=['m2','total_m2']
products[cols_kg+cols_m2]=products[cols_kg+cols_m2].fillna(0)

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
worksheet.merge_range('C6:C8', 'PDA', blue_header_format)
worksheet.merge_range('D6:D8', 'SKU	', blue_header_format)
worksheet.merge_range('E6:E8', 'CANT', blue_header_format)	
worksheet.merge_range('F6:F8', 'DESCRIPCION', blue_header_format)	
worksheet.merge_range('G6:G8', 'CTO UNIT', blue_header_format)	
worksheet.merge_range('H6:H8', 'CTO TOTAL', blue_header_format)	
worksheet.merge_range('I6:I8', 'CALIBRE	', blue_header_format)
worksheet.merge_range('J6:J8', 'KG UNIT	', blue_header_format)
worksheet.merge_range('K6:K8', 'KG TOTAL', blue_header_format)	
worksheet.merge_range('L6:L8', 'CTO X KG', blue_header_format)	
worksheet.merge_range('M6:M8', 'M2 UNIT	', blue_header_format)
worksheet.merge_range('N6:N8', 'M2 TOT', blue_header_format)

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
    
    def my_func(row, table_name):
        return row in table_name
    piezas=materials.loc[materials['product'].apply(my_func,table_name=products['tabla'].values[i])]
    costo_product=products['cost'].values[i]
    n=len(piezas)
    
    print(n,products['tabla'].values[i],row_count,products['cost'].values[i])
    
    if(i%2==1):
        formato=blue_content
        formato_unit=blue_content_unit
    if(i%2==0):
        formato=blue_content_lite
        formato_unit=blue_content_unit_lite
    
    #pda
    worksheet.write('C'+str(row_count), str(i*n+1), formato)
    #sku
    worksheet.write('D'+str(row_count), products['sku'].values[i], formato)
    worksheet.write('E'+str(row_count), str(products['amount'].values[i]), formato_unit)
    #descripcion
    worksheet.write('F'+str(row_count), tablas[products['tabla'].values[i]]+products['protector'].values[i]+' '+products['model'].values[i], formato)
    #costos
    print(costo_product)
    worksheet.write('G'+str(row_count), products[price_cols].sum(axis=1, numeric_only=True)[i], formato)
    worksheet.write('H'+str(row_count), products['amount'].values[i]*products[price_cols].sum(axis=1, numeric_only=True)[i], formato)
    #calibre
    worksheet.write('I'+str(row_count), ret_na(products['caliber'].values[i]), formato)
    #pesos
    worksheet.write('J'+str(row_count),(num(products['total_weight'].values[i])+num(products['total_kg'].values[i])+products['weight'].values[i]+products['weight_kg'].values[i])/products['amount'].values[i], formato_unit)
    worksheet.write('K'+str(row_count),(num(products['total_weight'].values[i])+num(products['total_kg'].values[i])+products['weight'].values[i]+products['weight_kg'].values[i]), formato_unit)
    try: 
        worksheet.write('L'+str(row_count),(num(products['amount'].values[i]*products['cost'].values[i])/(num(products['total_weight'].values[i])+num(products['total_kg'].values[i]))), formato)
    except:
        worksheet.write('L'+str(row_count),0, formato)
    #medidas
    worksheet.write('M'+str(row_count),products['m2'].values[i]+products['total_m2'].values[0], formato_unit)
    worksheet.write('N'+str(row_count),(products['m2'].values[i]+products['total_m2'].values[0])*products['amount'].values[i], formato)
    row_count=row_count+1
    #PIEZAS PIEZAS PIEZAS CICLO DE PIEZAS
    for j in range(0,n):
        
        print('entre al ciclo')
        print(piezas['cost'].fillna(0).values[j],piezas['amount'])
        costo= piezas['cost'].fillna(0).values[j].sum()
        cant= piezas['amount'].fillna(0).values[j].sum()
        worksheet.write('C'+str(row_count), str(i*n+2+j), formato)
        #sku
        worksheet.write('D'+str(row_count), ''.join(materials['sku'].fillna('').values[0]), formato)
        worksheet.write('E'+str(row_count), str(piezas['amount'].values[j]), formato)
        worksheet.write('F'+str(row_count), str(piezas['description'].fillna('').values[j][0])+str(piezas['piece'].fillna('').values[j]), formato)
        #costos
        worksheet.write('G'+str(row_count),costo, formato)
        worksheet.write('H'+str(row_count), cant*costo, formato)
        #calibre
        worksheet.write('I'+str(row_count), piezas['type'].values[j][0]+piezas['type'].values[j][1], formato_unit)
        #pesos
        worksheet.write('J'+str(row_count),str(0.0), formato_unit)
        worksheet.write('K'+str(row_count),str(0.0), formato_unit)
        worksheet.write('L'+str(row_count), 0.0, formato)
        #medidas
        worksheet.write('M'+str(row_count), 0.0, formato_unit)
        worksheet.write('N'+str(row_count), 0.0, formato_unit)
        row_count=row_count+1
trow=row_count


#TOTALES
worksheet.merge_range('C'+str(trow+1)+':E'+str(trow), 'TOTAL (EQV M.N)', blue_header_format_bold)
worksheet.write_formula('H'+str(trow),'{=SUM(H9:H'+str(trow-1)+')}',blue_footer_format_bold)
worksheet.write_formula('K'+str(trow),'{=SUM(K9:K'+str(trow-1)+')}',blue_footer_format_bold)
worksheet.write_formula('N'+str(trow),'{=SUM(N9:N'+str(trow-1)+')}',blue_footer_format_bold)




# worksheet.write('K'+str(trow), str(cobros['amount'].sum()), blue_content)
# worksheet.write('L'+str(trow), str(cobros['exchange_sell'].values[0]*cobros['amount'].sum()), blue_content_bold)


#RESUMEN
worksheet.merge_range('D'+str(trow+3)+':E'+str(trow+4),'RESUMEN DE KILOS',blue_header_format_bold)
#subtabla 1, kilos
worksheet.write('D'+str(trow+5),'KILOS',blue_header_format)
worksheet.write('E'+str(trow+5),'CALIBRE',blue_header_format)
suma_peso=0
art_i=0
for i in products['caliber'].fillna('NA').astype(str).unique():
    if(i!='NA'):
        print(i)
        p=products.loc[products['caliber']==i]
        sum_kg=p['weight'].fillna(0).sum()+p['total_weight'].fillna(0).sum()+p['weight_kg'].fillna(0).sum()+p['total_kg'].fillna(0).sum()
        suma_peso=suma_peso+sum_kg
        worksheet.write('D'+str(trow+6+art_i),sum_kg,blue_content)
        worksheet.write('E'+str(trow+6+art_i),i,blue_content)
        art_i=art_i+1

worksheet.write('D'+str(trow+5+len(products['caliber'].unique())),suma_peso,blue_footer_format_bold)
#subtabla2 costos
worksheet.merge_range('H'+str(trow+4)+':K'+str(trow+4),'RESUMEN DE COSTOS',blue_header_format_bold)
worksheet.write('L'+str(trow+4),'POSICION',blue_header_format)
worksheet.write('M'+str(trow+4),'KILOS',blue_header_format)
worksheet.write('N'+str(trow+4),'PORCENTAJE',blue_header_format)

worksheet.merge_range('H'+str(trow+5)+':K'+str(trow+5),'CONTRATO UNITARIO SOLO MATERIALES',blue_header_format)
worksheet.merge_range('H'+str(trow+6)+':K'+str(trow+6),'CONTRATO UNITARIO SOLO ARMADO',blue_header_format)
worksheet.merge_range('H'+str(trow+7)+':K'+str(trow+7),'CONTRATO UNITARIO SOLO TRASLADO',blue_header_format)
worksheet.merge_range('H'+str(trow+8)+':K'+str(trow+8),'CONTRATO UNITARIO COMBINADO',blue_header_format)


costo_total=products['cost'].sum()+materials['cost'].fillna(0).sum(axis=1, numeric_only=True).sum()

worksheet.write('L'+str(trow+5),materials['cost'].fillna(0).sum(axis=1, numeric_only=True).sum(),blue_content)
worksheet.write('M'+str(trow+5),0,blue_content)
worksheet.write('N'+str(trow+5),materials['cost'].fillna(0).sum(axis=1, numeric_only=True).sum()/costo_total*100,blue_content)

worksheet.write('L'+str(trow+6),products.loc[products['tabla'].isin(['quotation_installs','quotation_uninstalls']),'cost'].sum(),blue_content)
worksheet.write('M'+str(trow+6),products.loc[products['tabla'].isin(['quotation_installs','quotation_uninstalls']),'cost'].sum()/costo_total*100,blue_content)
worksheet.write('N'+str(trow+6),0,blue_content)

worksheet.write('L'+str(trow+7),products.loc[(products['tabla'].isin(['quotation_travel_assignments','packagings'])),'cost'].sum(),blue_content)
worksheet.write('M'+str(trow+7),0,blue_content)
worksheet.write('N'+str(trow+7),products.loc[(products['tabla'].isin(['quotation_travel_assignments','packagings'])),'cost'].sum()/costo_total*100,blue_content)

worksheet.write('L'+str(trow+8),products['cost'].sum(),blue_content)
worksheet.write('M'+str(trow+8),products[cols_kg].sum(axis=1, numeric_only=True).sum(),blue_content)
worksheet.write('N'+str(trow+8),'100%',blue_content)
#TODO: calcular bien esto, to6al menos iva

worksheet.set_column('A:A',15)
worksheet.set_column('D:D',20)
worksheet.set_column('F:F',30)
worksheet.set_column('L:L',15)
worksheet.set_column('G:G',15)
worksheet.set_column('H:H',15)
worksheet.set_column('I:N',15)
worksheet.set_column('P:T',15)

#worksheet.set_landscape()
worksheet.set_paper(9)
worksheet.fit_to_pages(1, 1)  
workbook.close()