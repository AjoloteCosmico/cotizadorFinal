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

a_color='#354F84'
b_color='#91959E'

writer = pd.ExcelWriter('storage/report/tabla3'+str(id)+'.xlsx', engine='xlsxwriter')

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

worksheet.write('L2', date, negro_b)
worksheet.insert_image("A1", "img/logo/logo.png",{"x_scale": 0.6, "y_scale": 0.6})

#Cabecera 1
worksheet.merge_range('B6:I6', 'CONCENTRADO DE COTIZACIONES MENSUALES (NO INCLUYE ACTUALIZACIONES)', blue_header_format)
worksheet.merge_range('B7:B9', 'MES', blue_header_format)
worksheet.merge_range('C7:C9', 'USD', blue_header_format)
worksheet.merge_range('D7:D9', 'T.C', blue_header_format)
worksheet.merge_range('E7:E9', "20", blue_header_format)
worksheet.merge_range('F7:F9', 'PESOS', blue_header_format)
worksheet.merge_range('G7:G9', 'SUMA', blue_header_format)
worksheet.merge_range('H7:H9', 'NO. COT', blue_header_format)
worksheet.merge_range('I7:I9', "NO. CLIEN", blue_header_format)
worksheet.merge_range('D10:E10', " ", blue_content)
worksheet.merge_range('D11:E11', " ", blue_content)
worksheet.merge_range('D12:E12', " ", blue_content)
worksheet.merge_range('D13:E13', " ", blue_content)
worksheet.merge_range('D14:E14', " ", blue_content)
worksheet.merge_range('D15:E15', " ", blue_content)
worksheet.merge_range('D16:E16', " ", blue_content)
worksheet.merge_range('D17:E17', " ", blue_content)
worksheet.merge_range('D18:E18', " ", blue_content)
worksheet.merge_range('D19:E19', " ", blue_content)
worksheet.merge_range('D20:E20', " ", blue_content)
worksheet.merge_range('D21:E21', " ", blue_content)

#Meses
worksheet.write('B10', "ENERO", blue_content)
worksheet.write('B11', "FEBERO", blue_content)
worksheet.write('B12', "MARZO", blue_content)
worksheet.write('B13', "ABRIL", blue_content)
worksheet.write('B14', "MAYO", blue_content)
worksheet.write('B15', "JUNIO", blue_content)
worksheet.write('B16', "JULIO", blue_content)
worksheet.write('B17', "AGOSTO", blue_content)
worksheet.write('B18', "SEPTIEMBRE", blue_content)
worksheet.write('B19', "OCTUBRE", blue_content)
worksheet.write('B20', "NOVIEMBRE", blue_content)
worksheet.write('B21', "DICIEMBRE", blue_content)

#Sumas
worksheet.write('C22', "SUMA", blue_content)
worksheet.write('D22', "SUMA", blue_content)
worksheet.write('E22', "SUMA", blue_content)
worksheet.write('F22', "SUMA", blue_content)
worksheet.write('G22', "SUMA", blue_content)
worksheet.write('H22', "SUMA", blue_content)
worksheet.write('I22', "SUMA", blue_content)

#Cabezera Enero
worksheet.merge_range('M6:O6', 'ENERO', blue_header_format)
worksheet.merge_range('M7:M8', 'VENDEDOR', blue_header_format)
worksheet.merge_range('N7:O7', 'COTIZACIONES', blue_header_format)
worksheet.write('N8', 'NO.', blue_header_format)
worksheet.write('O8', '$', blue_header_format)

#Vendedores
worksheet.write('M9', "V4 (SOLIS)", blue_content)
worksheet.write('M10', "V7 (GUSTAVO)", blue_content)
worksheet.write('M11', "V7 (GUSTAVO)", blue_content)
worksheet.write('M12', "V11 (FERNANDO)", blue_content)
worksheet.write('M13', "V12 (GUILLERMO)", blue_content)
worksheet.write('M14', "V13 (OSCAR)", blue_content)
worksheet.write('M15', "V14 (DORIAN)", blue_content)
worksheet.write('M13', "V16 (OLGUIN))", blue_content)
worksheet.write('M14', "V18 (IVAN)", blue_content)
worksheet.write('M15', "V19 (ALEJANDRA)", blue_content)

#Sumas vendedores
worksheet.write('N16', "SUMA", blue_content)
worksheet.write('O16', "SUMA", blue_content)

#Cabecera ingenieros
worksheet.merge_range('C26:F26', 'ENERO', blue_header_format)
worksheet.merge_range('C27:C28', 'Ingeniero', blue_header_format)
worksheet.merge_range('D27:F27', 'ACTIVIDADES', blue_header_format)
worksheet.write('D28', "NO", blue_header_format)
worksheet.write('E28', "INGENIERIA (HORAS)", blue_header_format)
worksheet.write('F28', "OBRAS", blue_header_format)



#Ingenieros
worksheet.write('C29', "OSCAR", blue_content)
worksheet.write('C30', "ALDO", blue_content)
worksheet.write('C31', "GERMAN", blue_content)
worksheet.write('C32', "MIGUEL", blue_content)

#Sumas Ingenieros
worksheet.write('D33', "SUMA", blue_content)
worksheet.write('E33', "SUMA", blue_content)

#ajustar columnas
worksheet.set_column('A:A',15)
worksheet.set_column('B:B',15)
worksheet.set_column('C:C',20)
worksheet.set_column('D:D',20)
worksheet.set_column('E:E',20)
worksheet.set_column('F:F',25)
worksheet.set_column('L:L',15)
worksheet.set_column('G:G',15)
worksheet.set_column('H:H',15)
worksheet.set_column('M:M',25)
worksheet.set_column('I:N',15)
worksheet.set_column('P:T',15)

#worksheet.set_landscape()
worksheet.set_paper(9)
worksheet.fit_to_pages(1, 1)  
workbook.close()