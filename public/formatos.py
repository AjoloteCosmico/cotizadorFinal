import xlsxwriter
def add_formats(workbook):
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
    return workbook