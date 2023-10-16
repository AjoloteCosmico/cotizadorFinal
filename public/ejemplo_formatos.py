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
#ESTE ARGUMENTO NO SE USA EN ESTE REPORTE, SERÁ 0 SIEMPRE UWU tabla 1
id=str(sys.argv[1])

a_color='#354F84'
b_color='#91959E'

writer = pd.ExcelWriter('storage/report/ejemplo_formato'+str(id)+'.xlsx', engine='xlsxwriter')

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


worksheet = writer.sheets['Sheet1']
#Encabezado del documento--------------
worksheet.merge_range('B2:F2', 'REPORTE POR COTIZACION ', negro_b)
worksheet.merge_range('B3:F3', 'ADMINISTRATIVO', negro_s)
worksheet.merge_range('B4:F4', 'COSTOS ', negro_b)
worksheet.write('H2', 'AÑO', negro_b)

worksheet.write('I2', year, negro_b)
worksheet.merge_range('J2:K3', """FECHA DEL REPORTE
DD/MM/AAAA""", negro_b)


#Fila 1
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
worksheet.merge_range('D6:E6', '', blue_content)
worksheet.merge_range('D7:E7', '150', blue_content)
worksheet.merge_range('D8:E8', '1,000', blue_content)	
worksheet.merge_range('D9:E9', 'COT-12179', blue_content)	
worksheet.merge_range('D10:E10', 'SELECTIVO', blue_content)	
worksheet.merge_range('D11:E11', '1,153,200', blue_content)	
worksheet.merge_range('D12:E12', '6,880 	', blue_content)
worksheet.merge_range('D13:E13', '0	', blue_content)
worksheet.merge_range('D14:E14', '60,000', blue_content)	
worksheet.merge_range('D15:E15', '5,000', blue_content)
worksheet.merge_range('D16:E16', '41,635	', blue_content)
worksheet.merge_range('D17:E17', '106,072', blue_content)	
worksheet.merge_range('D18:E18', '1,372,787', blue_content)	
	
worksheet.merge_range('D20:E20', '2,651,796', blue_content)	
worksheet.merge_range('D21:E21', '1,372,787', blue_content)
worksheet.merge_range('D22:E22', '1,279,009', blue_content)
worksheet.merge_range('D23:E23', '0.48', blue_content)
worksheet.merge_range('D24:E24', '2,651.80', blue_content)

worksheet.merge_range('D27:E27', '17,577', blue_content)	
worksheet.merge_range('D28:E28', '78.10', blue_content)	
worksheet.merge_range('D29:E29', '150.87', blue_content)


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