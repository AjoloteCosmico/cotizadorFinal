import pandas as pd
import numpy as np
import pandas as pd
from docxtpl import DocxTemplate,  InlineImage
from docx.shared import Mm
import sys
import mysql.connector
import os
from dotenv import load_dotenv
from datetime import date
id=sys.argv[1]
# id=208
today = date.today()
load_dotenv()

#configurar la conexion a la base de datos
DB_USERNAME = os.getenv('DB_USERNAME')
DB_DATABASE = os.getenv('DB_DATABASE')
DB_PASSWORD = os.getenv('DB_PASSWORD')
DB_PORT = os.getenv('DB_PORT')
DB_HOST=os.getenv('DB_HOST')

# Conectar a DB
cnx = mysql.connector.connect(user=DB_USERNAME,
                              password=DB_PASSWORD,
                              host=DB_HOST,
                              port=DB_PORT,
                              database=DB_DATABASE,
                              use_pure=False)
cotizacion=pd.read_sql("select * from quotations where id ="+str(id),cnx)
cart_products=pd.read_sql("select * from cart_products where quotation_id ="+str(id),cnx)

cliente=pd.read_sql("""select * from customers where customers.id="""+str(cotizacion['customer_id'].values[0]),cnx)
user=pd.read_sql("""select * from users where users.id="""+str(cotizacion['user_id'].values[0]),cnx)
productos=pd.read_sql("""select * from cart_products where cart_products.quotation_id="""+str(cotizacion['id'].values[0]),cnx)
questionario=pd.read_sql("""select * from questionaries where questionaries.quotation_id="""+str(cotizacion['id'].values[0]),cnx)
if(len(questionario)==0):
    questionario=pd.read_sql("""select * from questionaries limit 1""",cnx)

import datetime

currentDateTime = datetime.datetime.now()
date = currentDateTime.date()
year = date.strftime("%Y")

from tablas_dict import tablas
from tablas_dict import redact
from tablas_dict import extras
from tablas_dict import ref
aceros=pd.read_sql('select * from steels ',cnx)
aceros.loc[aceros['caliber']=='EST 3 IN','caliber']='EST3'

print('producots en carrito ',len(cart_products))
products=pd.DataFrame()
#iterar sobre tablas
for i in tablas:
    
    #buscar en la base de datos todos los productos de esta tabla
    #pertenecientes a la cotizacion pedida por el usuario.
    p=pd.read_sql('select * from '+i+' where quotation_id = '+str(id),cnx)
    p=p.assign(tabla=i)
    cart_reference=[]
    if(len(p)>0):
        cart_reference=cart_products.loc[cart_products['id']==p.cart_id.values[0]]
    if(('cost' not in p.columns)&(len(p)>0)&(len(cart_reference)>0)):
        
        if('caliber' not in p.columns):
             #esto es en especifico por un caso en que todas kas piezas son cal 14
             p=p.assign(caliber='14')
        try:
            p['caliber']=p['caliber'].str.replace('-','')
        except:
            print(' ')
        
    if(len(p)>0):
        if(len(cart_reference)>0):
            print('ha sido encontrada una instancia de ')
            p=p.assign(cost_unit=cart_reference['unit_price'].values[0])
            p=p.assign(cost_total=cart_reference['total_price'].values[0])
            p=p.assign(cantidad=cart_reference['amount'].values[0])
            p=p.assign(sku=cart_reference['sku'].values[0])
        else:
            # p=p.assign(cost_unit=cart_reference['unit_price'].values[0])
            # p=p.assign(cost_total=cart_reference['total_price'].values[0])
            p=p.assign(cantidad=p['amount'].values[0])
    products=pd.concat([products,p],ignore_index=True)
# print(products)
cols_to_fill_str=['description','protector','model','sku']
products[cols_to_fill_str]=products[cols_to_fill_str].fillna('')
cols_kg=['weight','total_kg','total_weight','weight_kg']
cols_m2=['m2','total_m2']
largo_cols=['long','length','length_meters','frame_background',
       'length_dimension']
ancho_cols=['uncut_front',  'uncut_background',
       'depth']
products[cols_kg+cols_m2+largo_cols+ancho_cols]=products[cols_kg+cols_m2+largo_cols+ancho_cols].fillna(0)
price_cols=['price','total_price','import','unit_price']
products[price_cols] =products[price_cols].fillna(0)    
pricelist_protectors=pd.read_sql('select * from price_list_protectors',cnx)
quotation_protectors=pd.read_sql('select quotation_protectors.*, protectors.sku from quotation_protectors  inner join protectors on protectors.protector=quotation_protectors.protector where quotation_id ='+str(id),cnx)
quotation_shlf=pd.read_sql('select * from selective_heavy_load_frames where quotation_id ='+str(id),cnx)
materials=pd.read_sql('select * from (materials left join price_list_screws on materials.price_list_screw_id= price_list_screws.id)left join price_lists on price_lists.id=materials.price_list_id',cnx)
materials['type']=materials['type'].fillna('')

if(cotizacion['type'].values[0]=='DOBLE PROFUNDIDAD'):
    doc= DocxTemplate("plantilla_d.docx")
else:
    if(cotizacion['type'].values[0]=='DRIVE IN'):
        doc= DocxTemplate("plantilla_drivein.docx")
    else:
        doc = DocxTemplate("plantilla.docx")



instalacion_tables=['quotation_installs','quotation_uninstalls']
productos=[]
print(products.columns)
for col in largo_cols:
    products[col]=products[col].astype(float)

for i in range(len(products)):
    this_color=' '
    seccion=None
    carga=None
    altura=0
    ancho=0
   
    if(products['tabla'].values[i] not in instalacion_tables+['quotation_travel_assignments','packagings']):
        print(products['tabla'].values[i],products['cantidad'].values[i],products['cost_unit'].values[i],p)
        if('frames' in products['tabla'].values[i]):
            this_color='Color:  Galvanizado. (otros colores opcionales).'
            seccion=questionario['section'].values[0]
        if('joist' in products['tabla'].values[i]):
            this_color='Anaranjado'
        if(('brazo' in products['tabla'].values[i])|('arrios' in products['tabla'].values[i])):
            this_color='Anaranjado'
            carga='{0:.2f}'.format(products['weight_kg'].values[i])
        
        if(products['cantidad'].values[i]>0):
            print('        SI----')
            #Gerenerar los atributos escogidos para cada talba
            #atributos por defecto
            product_dict={'nombre':redact[products['tabla'].values[i]],
                        'extra':extras[products['tabla'].values[i]],                          
                        'ref':ref[products['tabla'].values[i]],
                        'precio':products[price_cols].sum(axis=1).values[i],
                        'cantidad':int(products['cantidad'].values[i]),
                        'color': this_color,
                        'largo': round(products[largo_cols].sum(axis=1).values[i],2),
                        'carga': carga,
                        'altura': round(products[largo_cols].sum(axis=1).values[i],2),
                        'ancho': round(products[ancho_cols].sum(axis=1).values[i],2),
                        'depth': products['depth'].values[i],
                        'model': products['model'].values[i],
                        'seccion':seccion,
                        'peralte':None,
                        'sku':products['sku'].values[i]}
            #marcos
            if('frames' in products['tabla'].values[i]):
                section="84mm por 38mm"
                if(products['model'].values[i]=='TC-2'):
                    section="84mm por 52mm"
                if(products['model'].values[i]=='TC-3'):
                    section="84mm por 72mm"
                product_dict={'nombre':redact[products['tabla'].values[i]],
                        'extra':extras[products['tabla'].values[i]],                          
                        'ref':products['model'].values[i],
                        'precio':products[price_cols].sum(axis=1).values[i],
                        'cantidad':int(products['cantidad'].values[i]),
                        'color': 'Galvanizado (otros colores disponibles)',
                        'largo': 0,
                        'carga': 0,
                        'altura': products[largo_cols].sum(axis=1).values[i],
                        'ancho':0,
                        'depth': products['depth'].values[i],
                        'model': products['model'].values[i],
                        'seccion':section,
                        'peralte':None,
                        'sku':products['sku'].values[i]}
            if('structural_frames' in products['tabla'].values[i]):
                product_dict={'nombre':redact[products['tabla'].values[i]],
                        'extra':extras[products['tabla'].values[i]],                          
                        'ref':'ME-'+str(products['model'].values[i][0]),
                        'precio':products[price_cols].sum(axis=1).values[i],
                        'cantidad':int(products['cantidad'].values[i]),
                        'color': 'Azul TYRSA (otros colores disponibles)',
                        'largo': 0,
                        'carga': 0,
                        'altura': products[largo_cols].sum(axis=1).values[i],
                        'ancho': 0,
                        'depth': products['depth'].values[i],
                        'model': products['model'].values[i],
                        'peralte':None,
                        'seccion':products['model'].values[i],
                        'sku':products['sku'].values[i]}
            #vigas
            if('joist' in products['tabla'].values[i]):
                product_dict={'nombre':redact[products['tabla'].values[i]],
                        'extra':extras[products['tabla'].values[i]],                          
                        'ref':ref[products['tabla'].values[i]],
                        'precio':products[price_cols].sum(axis=1).values[i],
                        'cantidad':int(products['cantidad'].values[i]),
                        'color': this_color,
                        'largo': products[largo_cols].sum(axis=1).values[i],
                        'carga': 'loading_capacity',
                        'altura': products[largo_cols].sum(axis=1).values[i],
                        'ancho': products[ancho_cols].sum(axis=1).values[i],
                        'depth': products['depth'].values[i],
                        'model': products['model'].values[i],
                        'seccion':None,
                        'peralte':str(round(products['camber'].values[i],1))+'"(in)',
                        'sku':products['sku'].values[i]}
            #brazos
            if(('brazo' in products['tabla'].values[i])|('arrios' in products['tabla'].values[i])):
                product_dict={'nombre':redact[products['tabla'].values[i]],
                        'extra':extras[products['tabla'].values[i]],                          
                        'ref':ref[products['tabla'].values[i]],
                        'precio':products[price_cols].sum(axis=1).values[i],
                        'cantidad':int(products['cantidad'].values[i]),
                        'color': this_color,
                        'largo': products[largo_cols].sum(axis=1).values[i],
                        'carga': carga,
                        'altura': products[largo_cols].sum(axis=1).values[i],
                        'ancho': products[ancho_cols].sum(axis=1).values[i],
                        'depth': products['depth'].values[i],
                        'model': products['model'].values[i],
                        'seccion':None,
                        'peralte':None,
                        'sku':products['sku'].values[i]}
            
            productos.append(product_dict)

print('X-X-X-X-X-X-X calculado el total',products[price_cols]) 

precio_total=cart_products['total_price'].sum()
print(precio_total)
kilos_totales=products[cols_kg].sum(axis=1).sum()
fletes_tables=['packagings']
instalacion_tables=['quotation_installs','quotation_uninstalls']
precios=products[price_cols+['tabla','print']]
costo_flete=precios.loc[precios['tabla'].isin(fletes_tables)].sum(axis=1,numeric_only=True).sum()
costo_instalacion=precios.loc[(precios['tabla'].isin(instalacion_tables))&(precios['print']=='Sí')].sum(axis=1,numeric_only=True).sum()
costo_instalacion_incluida=precios.loc[(precios['tabla'].isin(instalacion_tables))&(precios['print']=='In')].sum(axis=1,numeric_only=True).sum()

if(costo_instalacion>0):
    print('la instalacion se desglosa')
    print(precios.loc[(precios['tabla'].isin(instalacion_tables))&(precios['print']=='Sí')])
    des_inst=1
else:
    des_inst=0
text=str(questionario['ndib'].values[0])+','
dibujos=[]
while(',' in text):
    print('entra iteracion')
    myindex=text.index(',')
    print(myindex)
    dibujos.append(text[0:myindex])
    text=text[myindex+1:]

if(len(dibujos)>0):
    primer_dibujo=dibujos[0]
else:
    primer_dibujo=' '
# try:
#     photo=InlineImage(doc,'storage/img/'+str(id).zfill(8)+'img0.JPG',width=Mm(50))
# except:
#     photo=''
photos = []
for i in range(10):
    path = f"storage/img/{str(id).zfill(8)}img{i}.jpg"
    if os.path.exists(path):  
        photos.append(InlineImage(doc, path, width=Mm(50)))
    else:
        photos.append("")  # No existe la imagen

context={
    'cliente':cliente['customer'].values[0],
    'direccion':cliente['address'].values[0]+' '+cliente['outdoor'].values[0]+', '+cliente['city'].values[0]+' '+cliente['suburb'].values[0]+' '+cliente['state'].values[0]+', cp: '+str(cliente['zip_code'].values[0]),
    'folio': cotizacion['invoice'].values[0],
    'fecha': today,
    'asesor': user['name'].values[0],
    'mayus_type':cotizacion['type'].values[0],
    'type':cotizacion['type'].values[0].capitalize(),
    'productos': productos,
    'precio_total': '{:2,.2f}'.format(precio_total),
    'kilos_totales': '{0:.2f}'.format(kilos_totales),
    'costo_flete':'{0:.2f}'.format(costo_flete),
    'costo_instalacion':'{:2,.2f}'.format(costo_instalacion),
    'costo_instalacion_incluida':'{:2,.2f}'.format(costo_instalacion_incluida),
    'costo_selectivo':'{:2,.2f}'.format(precio_total - costo_flete -costo_instalacion),
    'estado': cliente['state'].values[0],
    'a5': questionario['a5'].values[0], #que productos e almacena
   #reativos de nivel
    'a8': questionario['a8'].values[0],#frente
    'a9': questionario['a9'].values[0],#fondo
    'a10': questionario['a10'].values[0],#alto
    'a11': questionario['a11'].values[0],#peso
# dimensiones de la tarima
    'a18': questionario['a18'].values[0],#frente
    'a19': questionario['a19'].values[0],#fondo
    'a20': questionario['a20'].values[0],#alto
    'a21': questionario['a21'].values[0],#peso
#ambiente
    'a25': questionario['a25'].values[0], #temperatura
    'a26': questionario['a26'].values[0], #inflamable
    'a27': questionario['a27'].values[0], #explosivo
    'a28': questionario['a28'].values[0], #corrosivo
#Reactivos extra
#     'ndib':  str(questionario['ndib'].values[0]).replace(',',"""
# """), #numero de dibujos
'ndib':  str(questionario['ndib'].values[0]).replace(',','O'),
'primer_dibujo':primer_dibujo ,
'photos':photos,
    'npos':  questionario['npos'].values[0], #numero de posiciones
    'vigas':  questionario['vigas'].values[0], #vigas
    'tiempo':  questionario['tiempo'].values[0], #tiempo de entrega
    'dibujos': dibujos,
    'des_inst': des_inst
    }
doc.render(context) 
doc.save("storage/Cotizacion"+str(id)+".docx")
