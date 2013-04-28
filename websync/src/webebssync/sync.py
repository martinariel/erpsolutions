#-------------------------------------------------------------------------------------------------------------
#-------------------------------------------------------------------------------------------------------------
#-------------------------------------------------------------------------------------------------------------
#-------------------------------------------------------------------------------------------------------------
#-------------------------------------------------------------------------------------------------------------

import pyodbc 
import MySQLdb

#-------------------------------------------------------------------------------------------------------------
# Configuracion
#-------------------------------------------------------------------------------------------------------------

# Conexion a plataforma
erp = pyodbc.connect  ( 'driver={SQL Server};server=192.168.0.11;database=dbDesarrollo;uid=PFUSER;pwd=USERPF' )

# Conexion a sistema web
web = MySQLdb.connect ( host = "erp-solutions.com.ar", user = "root", passwd = "1234", db = "EBS_WEB_INTERFACE" )

# Lista de precios por defecto
lista_precios_ofar = 1

# Clasificaciones de articulos a exlcuir
excluir_articulos = ['PETERS','PEXPO' ]

#Estados a procesar, origen de pedidos y destino
estado_pendiente  = -1
estado_finalizado = 4

#-------------------------------------------------------------------------------------------------------------
#-------------------------------------------------------------------------------------------------------------
#-------------------------------------------------------------------------------------------------------------
#-------------------------------------------------------------------------------------------------------------
#-------------------------------------------------------------------------------------------------------------
#-------------------------------------------------------------------------------------------------------------
#-------------------------------------------------------------------------------------------------------------
#-------------------------------------------------------------------------------------------------------------
#-------------------------------------------------------------------------------------------------------------

#----------------------------------------

def crear_id ( _str ) :

	id = 0
	for c in _str.strip() :
		id = id + 23 * ord ( c )
	return id

#----------------------------------------

def exportar_clientes () :

	print "------------------------------------------------------"
	print "Exportacion de clientes"
	print "------------------------------------------------------"

	datos_web = web.cursor();
	datos_web.execute ( "DELETE FROM ra_customers")
	web.commit()

	cursor = erp.cursor()


	cursor.execute ( """SELECT * 
						FROM CCOB_CLIE 
						inner JOIN CCOB_DCLI on ( CCOB_CLIE.CLIE_CLIENTE = CCOB_DCLI.DCLI_CLIENTE   )
						inner JOIN SIST_PCIA on ( DCLI_PROVINCIA         = SIST_PCIA.PCIA_PROVINCIA )

						WHERE CCOB_DCLI.DCLI_UTILIZABLE = 1 AND ( CLIE_FECHA_BAJA IS NULL or CLIE_FECHA_BAJA > GETDATE() ) """)

	clientes = cursor.fetchall()

	i = 1
	total = len ( clientes )

	for cliente in clientes :

		sql = """INSERT INTO ra_customers 
		         values ( %s , '%s' , '%s' , '%s' , '%s' , %s , '%s' , '%s' , 
		         	'%s' , '%s' , '%s' , '%s' , '%s' , '%s' , %s , %s , %s , %s , %s )""" % \
		      (
		      	str ( cliente.CLIE_CLIENTE      ) ,
		      	str ( cliente.CLIE_CLIENTE      ) , 
		      	str ( cliente.CLIE_NOMBRE       ) ,
		      	str ( cliente.CLIE_NOMBRE_LEGAL ) ,
		      	str ( cliente.CLIE_CUIT).strip()[0:11] , 
		      	str ( cliente.DCLI_RENGLON) ,
		      	str ( cliente.DCLI_DOMICILIO) ,
		      	'',
		      	str ( cliente.DCLI_COD_POSTAL) ,
		      	str ( cliente.PCIA_NOMBRE),
		      	str ( cliente.DCLI_LOCALIDAD) ,
		      	str ( cliente.DCLI_COD_POSTAL) ,
		      	str ( cliente.DCLI_TELEFONO) ,
		      	str ( cliente.CLIE_CONDICION_IVA), # CLIE_CONDICION_IVA
		      	str ( cliente.CLIE_VENDEDOR) , # Vendedor 
		      	str ( 0 ) , #str ( crear_id ( cliente.CLIE_COND_PAGO ) ) , # terminos de pago
		      	str ( lista_precios_ofar) , # Lista de Precios
		      	str ( cliente.DCLI_RENGLON) ,
		      	str ( 0) )

		datos_web.execute ( sql )

		print "Clientes: %d/%d" % ( i , total )

		i = i + 1

	print "------------------------------------------------------"
	print "Exportacion Relacion Cliente-Vendedor"
	print "------------------------------------------------------"

	# La relacion con los vendedores
	cursor.execute ( "SELECT VECL_CLIENTE , VECL_VENDEDOR FROM CCOB_VECL" )

	clientes_vendedor = cursor.fetchall()

	total = len ( clientes_vendedor )
	i = 1

	for cv in clientes_vendedor :

		sql = 'UPDATE ra_customers set SALESREP_ID = %s where CUSTOMER_ID = %s' % ( str(cv.VECL_CLIENTE) , str(cv.VECL_VENDEDOR) ) 

		datos_web.execute ( sql )

		print "Cliente-Vendedor: %d/%d" % ( i , total )

		i = i + 1

	web.commit()

	print "------------------------------------------------------"
	print "Exportacion Relacion Cliente-Lista de Precios"
	print "------------------------------------------------------"

	cursor.execute ( "SELECT CLIV_CLIENTE, CLIV_LISTA_PRECVTA from VENT_CLIV")

	cliente_precios = cursor.fetchall()

	total = len ( cliente_precios )
	i = 1

	for cp in cliente_precios :

		sql = "UPDATE ra_customers set price_list_id = %s WHERE customer_id = %s" % \
		(
			str ( cp.CLIV_LISTA_PRECVTA) ,
			str ( cp.CLIV_CLIENTE ),
		)

		datos_web.execute ( sql )

		print "Cliente-Lista de Precios: %d/%d" % ( i , total )
		i = i + 1

	web.commit()


#----------------------------------------

def exportar_vendedores () :

	print "------------------------------------------------------"
	print "Exportacion de Vendedores"
	print "------------------------------------------------------"

	datos_web = web.cursor();
	datos_web.execute ( "DELETE FROM salesreps")
	web.commit()

	cursor = erp.cursor()

	cursor.execute ( """SELECT VEND_VENDEDOR, VEND_NOMBRE FROM SIST_VEND where VEND_UTILIZABLE = 1""")

	vendedores = cursor.fetchall()

	total = len ( vendedores )
	i = 1

	for v in vendedores :

		sql = """INSERT INTO salesreps 
			(SALESREP_ID,RESOURCE_ID,LAST_UPDATE_DATE,LAST_UPDATED_BY,
				CREATION_DATE,CREATED_BY,NAME,SET_OF_BOOKS_ID,SALESREP_NUMBER,ORG_ID)
			 values 
			 ( %s , %s , '2001-06-12 00:00:00',0,'2001-06-12 00:00:00',0,'%s',0,%s,0 )""" % \
		(
			str ( v.VEND_VENDEDOR ),
			str ( v.VEND_VENDEDOR ),
			str ( v.VEND_NOMBRE   ),
			str ( v.VEND_VENDEDOR )
		)

		datos_web.execute ( sql )

		print "Vendedores: %d/%d" % ( i , total )

		i = i + 1

	web.commit()

#----------------------------------------

def exportar_articulos() :

	print "------------------------------------------------------"
	print "Exportacion de Articulos"
	print "------------------------------------------------------"

	datos_web = web.cursor()
	datos_web.execute ( "DELETE from mtl_system_items_b")
	web.commit()

	cursor = erp.cursor()

	categorias = ','.join ( [ "'%s'" % ( s ) for s in excluir_articulos ] )

	sql = """SELECT * 
		     FROM STOC_ARTS 
		     INNER JOIN STOC_ARVE on ( STOC_ARTS.ARTS_ARTICULO = STOC_ARVE.ARVE_ARTICULO )
		     WHERE STOC_ARTS.ARTS_SE_VENDE = 1 and STOC_ARTS.ARTS_CLASIF_2 NOT IN (%s) """ % \
		     ( categorias )

	cursor.execute ( sql )

	articulos = cursor.fetchall()

	i = 1
	total = len ( articulos )
	#segment1 es el codigo de producto
	for a in articulos :

		codigo = str ( a.ARTS_ARTICULO_EMP ).strip()

		codigo = "%s-%s-%s" % ( codigo[0:3] , codigo[3:5] , codigo[5:8])

		sql = """INSERT INTO mtl_system_items_b 
				(INVENTORY_ITEM_ID,ORGANIZATION_ID,LAST_UPDATE_DATE,LAST_UPDATED_BY,
					CREATION_DATE,CREATED_BY,DESCRIPTION,SEGMENT1,UNIT_OF_ISSUE,GLOBAL_ATTRIBUTE2)
				VALUES
				(%s,105,'2001-06-12 00:00:00', 0,'2001-06-12 00:00:00',0,'%s','%s','%s','%s')""" % \
				(
					str ( a.ARTS_ARTICULO ).strip() ,
					str ( a.ARTS_NOMBRE       ) ,
					codigo ,
					str ( a.ARTS_UNIMED_STOCK ) ,
					'T-EXENTA'
				)

		datos_web.execute ( sql )

		print "Articulos: %d/%d" % ( i , total )

		i = i + 1

	# Impuestos
	print "------------------------------------------------------"
	print "Exportacion de impuestos"
	print "------------------------------------------------------"

	sql = "select ARVI_ARTICULO from VENT_ARVI where ARVI_CATEGORIA_IMP = 'C21'"

	cursor.execute ( sql )

	impuestos = cursor.fetchall()

	total = len ( impuestos )
	i     = 1

	for impuesto in impuestos :

		sql = """UPDATE mtl_system_items_b 
				SET GLOBAL_ATTRIBUTE2='T-BIENES' 
				WHERE INVENTORY_ITEM_ID=%s""" % ( impuesto.ARVI_ARTICULO )

		datos_web.execute ( sql )

		print "Impuestos: %d/%d" % ( i , total )

		i = i + 1

	web.commit()

#----------------------------------------

def exportar_precios() :

	print "------------------------------------------------------"
	print "Exportacion de Listas de precios"
	print "------------------------------------------------------"
	
	datos_web = web.cursor()
	datos_web.execute ( "DELETE from list_price")
	web.commit()

	cursor = erp.cursor()

	sql = """SELECT LIPV_LISTA_PRECVTA,LIPV_NOMBRE, ARPV_ARTICULO, ARPV_PRECIO_VTA 
			 FROM VENT_LIPV
			 INNER JOIN VENT_ARPV ON ( VENT_LIPV.LIPV_LISTA_PRECVTA = VENT_ARPV.ARPV_LISTA_PRECVTA )
			 WHERE 
			 LIPV_FECHA_VIG_DES <= GETDATE() AND
			 GETDATE()<= LIPV_FECHA_VIG_HAS"""

	cursor.execute ( sql )

	precios = cursor.fetchall()
	total   = len(precios)
	i       = 1

	for precio in precios :

		operand = str(precio.ARPV_PRECIO_VTA).replace ( "," , ".")

		sql = """INSERT INTO list_price 
			     (LIST_HEADER_ID,CREATION_DATE,CREATED_BY,LAST_UPDATE_DATE,NAME,PRODUCT_ID,OPERAND,ATTRIBUTE1,ATTRIBUTE2,ATTRIBUTE3,FLEXFIELD1)
			     VALUES 
			     (%s,'2001-06-12 00:00:00',0,'2001-06-12 00:00:00','%s',%s,%s,0,0,0,'N')""" % \
			     (
			     	str ( precio.LIPV_LISTA_PRECVTA ),
			     	str ( precio.LIPV_NOMBRE        ),
			     	str ( precio.ARPV_ARTICULO      ),
			     	operand
			     )

		datos_web.execute ( sql )

		print "Precios: %d/%d" % ( i , total )

		i = i + 1

	web.commit()

#----------------------------------------

def exportar_condicion_pago() :

	print "------------------------------------------------------"
	print "Exportacion de Condiciones de Pago"
	print "------------------------------------------------------"

	datos_web = web.cursor()
	datos_web.execute ( "DELETE from flexfield_pay_terms")
	web.commit()

	cursor = erp.cursor()

	sql = """SELECT * FROM CCOB_CPV1 where CPV1_UTILIZABLE = 1"""

	cursor.execute ( sql )

	terminos = cursor.fetchall()

	total = len ( terminos )
	i = 1

	for t in terminos :

		sql = """INSERT INTO 
			    flexfield_pay_terms ( pay_term_id , pay_term_description , codigo )
			    VALUES
			    (%s, '%s' ,'%s') """ % \
			    (
			    	str ( crear_id ( t.CPV1_CLASIF_NPCV_1 )) ,
			    	str ( t.CPV1_NOMBRE ) ,
			    	str ( t.CPV1_CLASIF_NPCV_1 )
			    )

		datos_web.execute ( sql )

		print "Condicion de Pago: %d/%d" % ( i , total )

		i = i + 1

	web.commit()

#----------------------------------------

def exportar_terminos_pago () :

	print "------------------------------------------------------"
	print "Exportacion de Terminos de Pago"
	print "------------------------------------------------------"

	datos_web = web.cursor()
	datos_web.execute ( "DELETE from ra_terms_tl")
	web.commit()

	cursor = erp.cursor()

	sql = """SELECT * FROM CCOB_CPCL where cpcl_utilizable = 1"""

	cursor.execute ( sql )

	terminos = cursor.fetchall()

	total = len ( terminos )
	i = 1

	for t in terminos :

		sql = """INSERT INTO 
			    ra_terms_tl ( term_id , description , name, CREATION_DATE )
			    VALUES
			    (%s, '%s' ,'%s' , '2001-06-12 00:00:00') """ % \
			    (
			    	str ( crear_id ( t.CPCL_COND_PAGO ) ) ,
			    	str ( t.CPCL_NOMBRE ) ,
			    	str ( t.CPCL_COND_PAGO )
			    )

		datos_web.execute ( sql )

		print "Termino de Pago: %d/%d" % ( i , total )

		i = i + 1

	web.commit()


#----------------------------------------
#----------------------------------------
#----------------------------------------

def construir_insert ( tabla , datos ) :
	return "INSERT INTO %s (%s) VALUES (%s) " % \
		( tabla , 
		  ",".join ( [ d.strip()      for d in datos ]) , 
		  ",".join ( [ str(datos[d])  for d in datos ]) 
		)

#----------------------------------------

def importar_pedidos() :

	print "------------------------------------------------------"
	print "Importacion de Pedidos"
	print "------------------------------------------------------"
	
	datos_web = web.cursor(MySQLdb.cursors.DictCursor)
	datos_erp = erp.cursor()

	datos_web.execute ( "SELECT * FROM transactions where state_id = %s and numero_pedido > 0 " % ( str ( estado_pendiente )  ) )

	transacciones = datos_web.fetchall()

	# Nada que hacer, chau.
	if not transacciones : return

	a_ejecutar = [] #Sentencias SQL a ejecutar en plataforma

	#Primero busco el mayor numero de transaccion y reservo el lugar -----
	sql = "UPDATE IMAC_PGIM SET PGIM_ULT_NUMINT_CPBI = %s " % ( max ( [ t['TRANSACTION_ID'] for t in transacciones ] ) )
	datos_erp.execute ( sql )
	#---------------------------------------------------------------------

	for t in transacciones :

		header_id      = t['HEADER_ID']
		transaction_id = t['TRANSACTION_ID']

		sql = "SELECT * from oe_headers_iface_all where orig_sys_document_ref = %s" % ( header_id )
		datos_web.execute ( sql )
		header = datos_web.fetchall()[0]

		sql = "SELECT * from oe_lines_iface_all where orig_sys_document_ref = %s" %  ( header_id )
		datos_web.execute ( sql )
		lineas = datos_web.fetchall()

		IMAC_CPBI   = dict()
		IMAC_NPIA   = dict()
		l_IMAC_NPDI = list()
		l_IMAC_NDDI = list()

		#------------------------------------------------------------------------

		IMAC_CPBI['CPBI_NUMINT_CPBI'] = transaction_id
		IMAC_CPBI['CPBI_CLASE_CPBTE'] = '76'
		IMAC_CPBI['CPBI_FH_ING_REPOSIT'] = 'getdate()'
		IMAC_CPBI['CPBI_HABILITADO_IMPORT'] ='1' 
		IMAC_CPBI['CPBI_EN_PROCESO'] = '0'

		#------------------------------------------------------------------------

		IMAC_NPIA['NPIA_NUMINT_CPBI'] = transaction_id
		IMAC_NPIA['NPIA_APLICA_NUM_AUTO'] = '0'
		IMAC_NPIA['NPIA_CONTIENE_PRECIOS_DTOS'] = '1'
		IMAC_NPIA['NPIA_CALCULA_PROM_VOL'] = '0'
		IMAC_NPIA['NPIA_CALCULA_BANDEJAS'] = '0'
		IMAC_NPIA['NPIA_DIVISION_NPCA'] = '1'
		IMAC_NPIA['NPIA_TIPO_NPCA'] = '10'
		IMAC_NPIA['NPIA_NUMERO_NPCA'] = "'%s'" % ( t['numero_pedido'] )
		IMAC_NPIA['NPIA_FECHA_EMI'] = "'%s'" % ( t['CREATED'].strftime("%Y-%m-%d %H:%M:%S") )
		IMAC_NPIA['NPIA_CLIENTE'] = header['customer_id']
		IMAC_NPIA['NPIA_MONEDA'] = "'PS'"
		IMAC_NPIA['NPIA_OBSERVACION'] = "'%s'" % ( t['OBS'] )
		IMAC_NPIA['NPIA_COND_PAGO'] = "'%s'" % ( header['PAYMENT_TERM'] )
		IMAC_NPIA['NPIA_LISTA_PRECVTA'] = header ['price_list_id']
		IMAC_NPIA['NPIA_CANT_DTO_CAB'] = '0'
		IMAC_NPIA['NPIA_CANT_DTO_REN']  = '0' # TODO
		IMAC_NPIA['NPIA_DTONETBRU_CAB'] = '1' # TODO
		IMAC_NPIA['NPIA_DTONETBRU_REN'] = '1' # TODO
		IMAC_NPIA['NPIA_RENGLON_DIR'] = header['ship_to_org_id']
		IMAC_NPIA['NPIA_DEPOSITO_BASE'] = '1'
		IMAC_NPIA['NPIA_FEC_ENT_BASE'] = IMAC_NPIA['NPIA_FECHA_EMI']
		IMAC_NPIA['NPIA_CLASIF_NPCV_1'] = "'%s'" % ( header['attribute5'] )
		IMAC_NPIA['NPIA_CLASIF_NPCV_2'] = "'01'"  #TODO
		IMAC_NPIA['NPIA_CLASIF_NPCV_3'] = "'S'"   #TODO
		IMAC_NPIA['NPIA_CLASIF_NPCV_4'] = "'-'"
		IMAC_NPIA['NPIA_VENDEDOR_1']    = header['salesrep_id']
		IMAC_NPIA['NPIA_VOLUMEN_EMB']   = '0'
		IMAC_NPIA['NPIA_PESO_EMB']      = '0'


		#------------------------------------------------------------------------

		numerador = 1
		for linea in lineas :
			IMAC_NPDI = dict()
			
			IMAC_NPDI['NPDI_NUMINT_CPBI']     = transaction_id
			IMAC_NPDI['NPDI_RENGLON_NPDE']    = numerador 
			IMAC_NPDI['NPDI_ARTICULO']        = linea['inventory_item_id']
			IMAC_NPDI['NPDI_DEPOSITO']        = '1'
			IMAC_NPDI['NPDI_FECHA_ENTREGA']   = "'%s'" % ( t['CREATED'].strftime("%Y-%m-%d %H:%M:%S") )
			IMAC_NPDI['NPDI_UNIMED']          = "'%s'" % ( linea['order_quantity_uom'] )
			IMAC_NPDI['NPDI_CANTIDAD']        = linea['ordered_quantity']
			IMAC_NPDI['NPDI_PRECIO_UNITARIO'] = str(linea['unit_list_price']).replace("," , ".")
			IMAC_NPDI['NPDI_PRECIO_UNITARIO'] = str('35.44').replace("," , ".") # TODO FIXME
			IMAC_NPDI['NPDI_LISTA_PRECVTA']   = linea['price_list_id'] # TODO FIXME precio de la lista!!
			IMAC_NPDI['NPDI_UM_PRECIO_VTA']   = '1'
			IMAC_NPDI['NPDI_FACTOR_UMS']      = '1'
			IMAC_NPDI['NPDI_CLASIF_NPDE_1']   = '2'

                        
                        IMAC_NDDI = dict()
                        #TODO SOLO EN EL CASO QUE SEA CERO
                        if linea['unit_list_price'] == 0 : #FIXME
                                IMAC_NDDI['NDDI_NUMINT_CPBI'] = transaction_id
                                IMAC_NDDI['NDDI_RENGLON_NPDE'] = numerador
                                IMAC_NDDI['NDDI_ORDEN'] = "1"
                                IMAC_NDDI['NDDI_POR_DESCUENTO'] = "'100'"
                                l_IMAC_NDDI.append ( IMAC_NDDI )

			numerador = numerador + 1

			l_IMAC_NPDI.append ( IMAC_NPDI )

                if len ( l_IMAC_NDDI ) > 0 :
                        IMAC_NPIA['NPIA_CANT_DTO_REN'] = '1'

		sql = construir_insert ( "IMAC_CPBI" , IMAC_CPBI )
		datos_erp.execute ( sql )

		sql = construir_insert ( "IMAC_NPIA" , IMAC_NPIA )
		datos_erp.execute ( sql )

		for linea in l_IMAC_NPDI :
			sql = construir_insert ( "IMAC_NPDI" , linea )
			datos_erp.execute ( sql )

		for descuento in l_IMAC_NDDI :
                        sql = construir_insert ( "IMAC_NDDI" , descuento )
                        datos_erp.execute ( sql )


	# Por ultimo Cambio el estado de los pedidos en la web
	# TODO
	for t in transacciones :
		
		sql = "UPDATE transactions set state_id = %s where transaction_id = %s" % \
			( t['TRANSACTION_ID'] , estado_finalizado )
		datos_web.execute ( sql )

	print "------ Se han transferido %d pedidos." % ( len(transacciones))
	# Aplico todos los cambios

	erp.commit()
	web.commit()

#----------------------------------------
