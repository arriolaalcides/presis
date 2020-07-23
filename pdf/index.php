<pdf>
        <dynamic-page font-type="DejaVuSans" margin="15px">
            <div width="100%" height="100%" border.color="black">
                <div id="contenedorCabecera" width="100%" height="110px" border.color="black">
                    <div id="logo" width="50%" float="left" border.color="black" height="110px">
                        <div id="imagen" height="66px" margin-left="50px">
                            <img src="{{ pdf_image('PresisRetiroBundle:logoFastTrack.jpg') }}" width="250px" height="66px" />
                        </div>
                        <div id="data" height="34px" font-size="8px" text-align="center">
                            CUIT: 30-70826062-9 | IIBB: 901-211904-0 | INIC DE ACT.: 14/03/2003 | R.N.P.S.P. N� 813
                        </div>
                    </div>
                    <div id="barras" width="50%" float="left" height="110px">
                        <div id="fecha" text-align="center" margin-top="10px" margin-bottom="10px" font-size="16px">
                            <b>GUIA: {{ retiro.id }}</b>
                        </div>
                        <div id="c_barras">
                            <barcode type="code39" code="{{ retiro.id }}" float="right" bar-thin-width="4px" bar-thick-width="10px" margin-right="15px" />
                        </div>
                        <div extends="fecha" text-align="center" margin-top="10px" font-size="8px">
                            Utilice este n�mero para hacer consultas
                        </div>
                    </div>
                </div><!--contenedorCabecera-->
                <div id="fila100" width="100%" border.color="black" font-size="10px" height="20px" padding-left="5px" padding-top="5px">
                    <div id="campo" width="25%" float="left"><b>Fecha:</b> {{ retiro.fechHora|date("d/m/Y") }}</div>
                    <div extends="campo"><b>Hora:</b> {{ retiro.fechHora|date("H:m") }}</div>
                    <div extends="campo"><b>Nro. Cta.:</b> {{ retiro.datosenvios.cliente ? retiro.datosenvios.cliente.nroCta }}</div>
                    <div extends="campo"><b>Cliente:</b> {{ retiro.datosenvios.cliente ? retiro.datosenvios.cliente.empresa }}</div>
                </div>
                <div id="fila1003" width="100%" border.color="black" font-size="10px" height="20px">
                    <div id="fila50" width="50%" height="100%" float="left" border.color="black" padding-top="5px">
                        <div padding-left="5px"><b>GUIA DEL AGENTE:</b> {{ retiro.datosenvios.guiaAgente }}</div>
                    </div>
                    <div extends="fila50">
                        <div padding-left="5px"><b>GUIA MANUAL:</b> {{ retiro.remito }}</div>
                    </div>
                </div>
                <!-- remitente / destinatario -->
                <div id="colIzquierda" width="50%" border.color="black" float="left">
                    <div id="tituloFila" border.color="black" padding="5px" font-size="10px" text-align="center">
                        <b>REMITENTE</b>
                        {% set haySender = retiro.sender ? true : false %}
                    </div>
                    <div id="fila" border.color="black" padding="5px" font-size="10px">
                        <b>Remite:</b> {{ haySender ? retiro.sender.remitente }}
                    </div>
                    <div extends="fila">
                        <b>Empresa:</b> {{ haySender ? retiro.sender.empresa }}
                    </div>
                    {% set direccion = haySender ?
                    retiro.sender.calle ~ ' ' ~
                    retiro.sender.altura ~ ' ' ~
                    retiro.sender.piso ~ ' ' ~
                    retiro.sender.dpto
                    : ''
                    %}
                    <div extends="fila">
                        <b>Direcci�n:</b> {{ direccion }}
                    </div>
                    <div extends="fila">
                        <b>Tel�fono:</b> {{ haySender ? retiro.sender.celular }}
                    </div>
                    <div id="fila3" font-size="10px" width="100%" border.color="black">
                        <div id="fila33" float="left" width="31%" border.color="black" height="25px" padding-top="5px" padding-left="5px" font-size="8px">Localidad:  {{ haySender ? retiro.sender.localidad }}</div>
                        <div extends="fila33">Provincia: {{ haySender ? retiro.sender.provincia }}</div>
                        <div id="fila33Fin" float="left" width="31%" height="25px" padding-top="5px" padding-left="5px" font-size="10px"><b>C.P.:</b>  {{ haySender ? retiro.sender.cp }}</div>
                    </div>
                </div>
                <div id="colDerecha" width="50%" border.color="black" float="left">
                    <div extends="tituloFila">
                        <b>DESTINATARIO</b>
                        {% set hayComprador = retiro.comprador ? true : false %}
                    </div>
                    <div extends="fila">
                        <b>Recibe:</b> {{ hayComprador ? retiro.comprador.apenom }}
                    </div>
                    <div extends="fila">
                        <b>Empresa:</b> {{ hayComprador ? retiro.comprador.empresa }}
                    </div>
                    {% set direccion = hayComprador ?
                    retiro.comprador.calle ~ ' ' ~
                    retiro.comprador.altura ~ ' ' ~
                    retiro.comprador.piso ~ ' ' ~
                    retiro.comprador.dpto
                    : ''
                    %}
                    <div extends="fila">
                        <b>Direcci�n:</b> {{ direccion }}
                    </div>
                    <div extends="fila">
                        <b>Tel�fono:</b> {{ hayComprador ? retiro.comprador.celular }}
                    </div>
                    <div extends="fila3" font-size="10px" width="100%" border.color="black">
                        <div extends="fila33" float="left" width="31%" border.color="black" height="25px" padding-top="5px" padding-left="5px">Localidad:  {{ hayComprador ? retiro.comprador.localidad }}</div>
                        <div extends="fila33">Provincia: {{ hayComprador ? retiro.comprador.provincia }}</div>
                        <div extends="fila33Fin" float="left" width="31%" height="25px" padding-top="5px" padding-left="5px" font-size="10px"><b>C.P.:</b> {{ hayComprador ? retiro.comprador.cp }}</div>
                    </div>
                </div>
                <!-- remitente / destinatario -->
                <div extends="fila1003" width="100%" border.color="black" font-size="10px" height="20px">
                    <div extends="fila50" width="50%" height="100%" float="left" border.color="black" padding-top="5px">
                        <div padding-left="5px" font-size="8px"><b>Observaciones:</b> {{ haySender ? retiro.sender.otherInfo }}</div>
                    </div>
                    <div extends="fila50">
                        <div padding-left="5px" font-size="8px"><b>Observaciones:</b> {{ hayComprador ? retiro.comprador.otherInfo }}</div>
                    </div>
                </div>
                <div margin-top="15px"></div>
                <!-- peso / volumen -->
                <div id="fila502" width="50%" float="left">
                    <div width="100%" height="20px">
                        <div width="29%" float="left" border.color="black" font-size="10px" height="100%" padding-top="5px">
                            <div padding-left="5px"><b>CANTIDAD:</b> {{ retiro.datosenvios.bultos }}</div>
                        </div>
                        <div width="30%" float="left" border.color="black" font-size="10px" height="100%" padding-top="5px">
                            <div padding-left="5px"><b>PESO (KG):</b> {{ retiro.datosenvios.peso|round(2, 'floor') }}</div>
                        </div>
                        <div width="41%" float="left" border.color="black" height="100%" font-size="10px" padding-top="5px">
                            <div padding-left="5px">Volumen (CM3): {{ retiro.datosenvios.volumen }}</div>
                        </div>
                    </div>
                    <div width="100%" height="20px">
                        <div width="29%" float="left" border.color="black" font-size="10px" height="100%" padding-top="5px">
                            <div padding-left="5px">Alto(CM): {{ retiro.datosenvios.alto }}</div>
                        </div>
                        <div width="30%" float="left" border.color="black" font-size="10px" height="100%" padding-top="5px">
                            <div padding-left="5px">Ancho (CM): {{ retiro.datosenvios.ancho }}</div>
                        </div>
                        <div width="41%" float="left" border.color="black" height="100%" font-size="10px" padding-top="5px">
                            <div padding-left="5px">Largo (CM): {{ retiro.datosenvios.largo }}</div>
                        </div>
                    </div>
                </div>
                <div extends="fila502">
                    <div width="50%" float="left" text-align="center" border.color="black" height="20px">
                        <div font-size="8px" padding-top="5px" height="100%">ZONA RUTEO</div>
                        <div font-size="12px" padding-top="5px" height="100%">{{ zona }}</div>
                    </div>
                    <div width="50%" float="left" border.color="black" height="20px">
                        <div width="100%" height="100%" padding-top="5px" border.color="black" font-size="8px">
                            <div padding="5px">Valor Declarado ($): {{ retiro.datosenvios.valorDeclarado }}</div>
                        </div>
                        <div width="100%" height="100%" padding-top="5px" font-size="8px">
                            <!-- cambio campo costoPorContrareembolso por contrareembolso 12-07 -->
                            <div padding="5px">Contrareembolso ($): {{ retiro.datosenvios.contrareembolso }}</div>
                        </div>
                    </div>
                </div>
                <!-- peso / volumen -->
                <div margin-top="15px"></div>
                <div margin-top="15px"></div>
                <!-- FILAS VALORES -->
                <div width="70%" height="100px" border.color="black" float="left">
                    <div width="100%" height="80px" border.color="black" float="left" font-size="8px">
                        <b>Observaciones:</b> {{ retiro.datosenvios.observaciones }}
                    </div>
                    <div width="100%" height="20px" border.color="black" float="left" font-size="8px">
                        <div width="50%" height="20px" border.color="black" float="left" font-size="8px">
                            <div padding-left="5px">Tipo operaci�n: {{ retiro.datosenvios.tipoOp == 1 ? 'Env�o' : 'Retiro' }}</div>
                        </div>
                        <div width="50%" height="20px" border.color="black" float="left" font-size="8px">
                            <div padding-left="5px">Tipo servicio: {{ retiro.datosenvios.ts == 1 ? 'Express' : 'Cargas' }}</div>
                        </div>
                    </div>
                </div>
                <div width="30%" height="100px" border.color="black" float="left" font-size="8px">
                    <div width="100%" height="30px" padding-top="10px">
                        <div width="35%" float="left">
                            <div padding-left="5px">Flete a cobrar</div>
                        </div>
                        {% set formaDePago = retiro.datosenvios.cliente ? retiro.datosenvios.cliente.FormaPago : '' %}
                        <div width="10%" height="15px" float="left" border.color="black">
                            {% if formaDePago matches '*Flete*' %}
                                <img src="{{ pdf_image('PresisRetiroBundle:ok.png') }}" width="16px" height="15px" />
                            {% endif %}
                        </div>
                        <div width="35%" float="left">
                            <div padding-left="5px">Cuenta corriente</div>
                        </div>
                        <div width="10%" height="15px" float="left" border.color="black">
                            {% if formaDePago matches '*corriente*' %}
                                <img src="{{ pdf_image('PresisRetiroBundle:ok.png') }}" width="16px" height="15px" />
                            {% endif %}
                        </div>
                    </div>
                    <div width="100%" height="30px">
                        <div width="35%" float="left">
                            <div padding-left="5px">Contrareembolso</div>
                        </div>
                        <div width="10%" height="15px" float="left" border.color="black">
                            {% if retiro.datosenvios.contrareembolso %}
                                <img src="{{ pdf_image('PresisRetiroBundle:ok.png') }}" width="16px" height="15px" />
                            {% endif %}
                        </div>
                        <div width="35%" float="left">
                            <div padding-left="5px">Contado</div>
                        </div>
                        <div width="10%" height="15px" float="left" border.color="black">
                            {% if formaDePago matches '*Contado*' %}
                                <img src="{{ pdf_image('PresisRetiroBundle:ok.png') }}" width="16px" height="15px" />
                            {% endif %}
                        </div>
                    </div>
                    <div width="100%" height="30px">
                        <div width="35%" float="left">
                            <div padding-left="5px">Remito conforme</div>
                        </div>
                        <div width="10%" height="15px" float="left" border.color="black">
                            {% if retiro.datosenvios.src %}
                                <img src="{{ pdf_image('PresisRetiroBundle:ok.png') }}" width="16px" height="15px" />
                            {% endif %}
                        </div>
                        <div width="35%" float="left">
                            <div padding-left="5px">CSR</div>
                        </div>
                        <div width="10%" height="15px" float="left" border.color="black">
                            {% if retiro.datosenvios.csr %}
                                <img src="{{ pdf_image('PresisRetiroBundle:ok.png') }}" width="16px" height="15px" />
                            {% endif %}
                        </div>
                    </div>
                </div>
                <div margin-top="15px"></div>
                <div extends="colIzquierda" width="50%" border.color="black" float="left" text-align="center">
                    <div extends="colIzquierda" width="100%" border.color="black" float="left" text-align="center">
                        <b>Autorizaci�n y Firma del Remitente</b>
                    </div>
                    <div width="100%" float="left" text-align="left" font-size="8px" height="40px">
                        Yo/Nosotros acepto/aceptamos las condiciones de despacho abajo
                        detalladas
                    </div>
                    <div width="100%" float="left" text-align="left" font-size="8px" height="80px">
                        Firma:
                    </div>
                    <div padding-top="5px" border.color="black" width="100%" float="left" text-align="left" font-size="8px" height="30px">
                        Aclaraci�n:
                    </div>
                </div>
                <div extends="colDerecha" width="50%" border.color="black" float="left" text-align="center">
                    <div extends="colDerecha" width="100%" border.color="black" float="left" text-align="center">
                        <b>Datos y firma de quien recibe / retira</b>
                    </div>
                    <div width="100%" float="left" text-align="left" font-size="8px" height="120px">
                        <div border.color="black" width="60%" float="left" text-align="left" font-size="8px" height="120px">
                            Firma:
                        </div>
                        <div width="40%" float="left" text-align="left" font-size="8px" height="120px">
                            <div width="100%" float="left" text-align="left" font-size="8px" height="60px">
                                Doc. de Identidad:
                            </div>
                            <div border.color="black" width="100%" float="left" text-align="left" font-size="8px" height="60px">
                                Fecha:
                            </div>
                        </div>
                    </div>
                    <div padding-top="5px" border.color="black" width="60%" float="left" text-align="left" font-size="8px" height="30px">
                        Aclaraci�n:
                    </div>
                    <div padding-top="5px" border.color="black" width="40%" float="left" text-align="left" font-size="8px" height="30px">
                        Hora:
                    </div>
                </div>
                <div id="condiciones" font-size="6px" text-align="justify" padding="5px">
                    CONDICIONES DE DEPACHO:
                    Sujeto a las disposiciones de la Ley N� 12.346 art. 117 y el C�digo Aeron�utico Argentino, el servicio contratado por Usted se regir� por normas y condiciones expresadas en el presente resumen que el cargador conoce y acepta.
                    1 Nuestro contrato con Usted: La presente Gu�a es el Titulo legal del contrato y su prueba, anulando cualquier otro documento. Estos T�rminos y Condiciones detallados a continuaci�n constituyen los t�rminos del contrato suscripto entre Usted,
                    remitente del env�o, nosotros, Servicios Log�sticos Integrados S.R.L Cuando Usted nos entrega su env�o, acepta nuestros t�rminos y condiciones tambi�n protegen a nuestros agentes, representantes y a cualquier persona que contratemos para
                    recolectar, transportar o entregar su env�o. Ning�n empleado de Servicios Log�sticos Integrados S.R.L o persona alguna est�n autorizados para modificar nuestros t�rminos y condiciones o hacer ofrecimientos en nuestro nombre. 2 �Qu� significa
                    �Env�o�? �Env�o� significa todos los paquetes, documentos, bolsines, etc., que viajan amparados bajo gu�a de transporte. 3 Env�os que no aceptamos/Declaraci�n Jurada de Contenidos: El remitente certifica con car�cter de Declaraci�n Jurada que los
                    detalles del env�o que realiza son completos exactos, y que acepta el Valor Declarado y que el Contenido de la carga es la real y no es de los despachos prohibidos por la ley, como dinero, documentos negociables, etc., ni tampoco incluye productos
                    prohibidos o restringidos por IATA ( International Air Transport Association) o por ICAO ( International Civil Aviation Organization), tales como productos inflamables, explosivos, corrosivos, t�xicos, radioactivos o que entra�en peligro efectivo o potencial
                    para la integridad del transporte, su tripulaci�n y/o carga conducida en el mismo, aclarando que la descripci�n de los elementos antes se�alados es de car�cter enunciativo y pueden comprender otras sustancias cuyo transporte sea peligroso y
                    prohibido. Por decreto nacional N� 1297/75, queda prohibido transportar frutas y hortalizas que puedan contener la plaga Moscas de los Frutos. En tales condiciones el remitente acepta la responsabilidad por el contenido de la carga, conociendo las
                    prescripciones del Art. 190 del C�digo Penal. La violaci�n del presente contrato y su Declaraci�n Jurada del Contenido autorizara a Servicios Log�sticos Integrados S.R.L a formular las acciones civiles y/o criminales que correspondan. 4 Inspecci�n,
                    Cobros y Estad�as: Usted conviene en que nosotros podemos inspeccionar su env�o por cualquier motivo y en todo momento. Nuestro precio se fija en base al peso real o volum�trico, lo que resulte superior. 5 Si usted nos solicita facturar al destinatario
                    o a un tercero: Usted acepta expresamente, que abandonar� el flete y los gastos ocasionados por este contrato de transporte instrumentando en la presente gu�a, si los mismos no fuesen pagados por el destinatario, debi�ndose retener la carga hasta
                    que la suma sea abonada por una de la dos partes. 6 Reclamos: Si desea hacer un reclamo por un env�o extraviado presentar: -Reclamo escrito �la denuncia policial correspondiente. � La copia de la gu�a- DNI � Pasados los 30 d�as de despachado el
                    env�o no se acepta reclamo por ning�n concepto. 7 Alcance de nuestra responsabilidad: (Sujetos a las condiciones 11 y 12) En lo que respecta cualquier env�o, nuestra responsabilidad por extrav�o se limitara a lo siguiente: *El valor declarado en este
                    contrato de transporte. * La cobertura ser� desde la recepci�n por Servicios Log�sticos Integrados S.R.L de los bultos contenidos en la presente gu�a de transporte hasta su destino final. * Si usted no declara Valor m�nimo Servicios Log�sticos Integrados
                    S.R.L se responsabilizara hasta un m�ximo de: $50. 8 Que esperamos decir con Valor Declarado: Es el valor por el cual Usted declara en el contenido de su env�o, y por el cual nos hacemos responsables. 9 Seguro de env�o: si su mercader�a supera
                    los valores m�nimos, le recomendamos que usted adquiera un seguro por el env�o. Para env�os nacionales podemos proporcionarles un seguro en el cual establece una prima del 1.5% (mas I.V.A). (Ejemplo: Para un valor declarado de $1000 la prima
                    ser� igual a $15 mas I.V.A). 10 Responsabilidad legal: Para cualquier siniestro, no especificado en los puntos 7,8,9, la responsabilidad de nuestra empresa se limita a lo establecido por la Ley N� 12.376., art. 117 y el C�digo Aeron�utico Argentino. 11
                    Env�os demorados: Haremos todo lo posible para entregar su env�o en los horarios establecidos de reparto, pero los mismos nos son garantizados y no forman parte de este contrato. 12 Circunstancias fuera de nuestro control: No somos responsables
                    si se extrav�a, da�a o entrega err�neamente un env�o debido a circunstancia fuera de nuestro control, lo que incluye: *Desastres naturales, como por ejemplo, terremotos, cicl�n, hurac�n o inundaci�n. *Fuerza Mayor, como por ejemplo guerra o
                    accidente de tr�nsito. *Cualquier acci�n u omisi�n de alguna persona ajena a Servicios Log�sticos Integrados S.R.L, como por ejemplo del remitente del env�o. *Del destinatario. *De una tercera parte interesada. * De la C.N.C., Aduana, Gendarmer�a u
                    otros entes o funcionarios del Gobierno.
                </div>
            </div><!--termina el div contenedor-->
        </dynamic-page>
    </pdf>