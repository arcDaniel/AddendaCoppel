<?php
	if($_REQUEST['generar'] == 'true'){
		
		$fecha = $_REQUEST['fecha']; //yyyymmdd
		$fecha_pedido = $_REQUEST['fecha_pedido']; //yyyy-mm-dd
		$fecha_entrega = $_REQUEST['fecha_entrega']; //yyyy-mm-dd
		$fecha_xml = $_REQUEST['fecha_xml']; //yyyy-mm-ddThh:mm:ss
		
		$no_factura = $_REQUEST['no_factura'];
		$no_pedido = $_REQUEST['no_pedido'];
		$no_proveedor = $_REQUEST['no_proveedor'];
		
		$ciudad = $_REQUEST['ciudad'];
		$bodega_destino = $_REQUEST['bodega_destino'];
		$bodega_receptora = $_REQUEST['bodega_receptora'];
		$region = $_REQUEST['region'];
		
		$fletera = $_REQUEST['fletera'];
		
		//COD-CANT$PRECIO[DESCRIPCION]
		$articulos = $_REQUEST['articulos'];
		
		$articulos_xml = "";
		$articulos_footer = "";
		$contador = 0;
		$total_antes_iva = 0;
		$tmp = array();
		$tmp = preg_split("/[;]+/",$articulos); //COD-CANT$PRECIO[nombre]
		foreach($tmp as $key=>$value){
			$contador++;
			$codigo = substr($value,0,strpos($value,'-'));
			$cantidad = substr($value,strpos($value,'-')+1,strpos($value,'$')-strlen($value));
			$precio = substr($value,strpos($value,'$')+1,strpos($value,'[')-strlen($value));
			$descripcion = substr($value,strpos($value,'[')+1,strpos($value,']')-strlen($value));
			$total = $precio*$cantidad;
			$total_antes_iva += $total;
			$articulos_xml .= "<lineItem type=\"SimpleInvoiceLineItemType\" number=\"".$contador."\"><tradeItemIdentification><gtin>".$codigo."</gtin></tradeItemIdentification><alternateTradeItemIdentification type=\"BUYER_ASSIGNED\">".$codigo."</alternateTradeItemIdentification><tradeItemDescriptionInformation language=\"ES\"><longText>".$descripcion."</longText></tradeItemDescriptionInformation><invoicedQuantity unitOfMeasure=\"PCE\">".$cantidad."</invoicedQuantity><grossPrice><Amount>".$precio."</Amount></grossPrice><netPrice><Amount>".$precio."</Amount>
</netPrice><modeloInformation><longText>".$descripcion."</longText></modeloInformation><extendedAttributes><lotNumber/></extendedAttributes><allowanceCharge allowanceChargeType=\"CHARGE_GLOBAL\" settlementType=\"OFF_INVOICE\"><specialServicesType>PAD</specialServicesType><monetaryAmountOrPercentage><percentagePerUnit/><ratePerUnit><amountPerUnit/></ratePerUnit></monetaryAmountOrPercentage></allowanceCharge><totalLineAmount><grossAmount><Amount>".$total."</Amount></grossAmount><netAmount><Amount>".$total."</Amount></netAmount></totalLineAmount><DetCaractsFisicas><Composicion><Material/><GrmRelleno/></Composicion><Detjoyeria><Kilataje/></Detjoyeria><Peso Udmedida=\"GRM\"/></DetCaractsFisicas></lineItem>";
			//CANT|PIEZA|COD|DESC|PRECIO|TOTAL
			$articulos_footer .= $cant."|PIEZA|".$codigo."|".$descripcion."|".$precio."|".$total; 
		}
		$total_iva = $total_antes_iva*0.16;
		$total_despues_iva = $total_antes_iva*1.16;
		
		$output = "<cfdi:Addenda><requestForPayment type=\"SimpleInvoiceType\" contentVersion=\"1.0\" documentStructureVersion=\"CPLM1.0\" documentStatus=\"ORIGINAL\" DeliveryDate=\"".$fecha."\"><requestForPaymentIdentification><entityType>INVOICE</entityType><uniqueCreatorIdentification>".$no_factura."</uniqueCreatorIdentification></requestForPaymentIdentification><orderIdentification><referenceIdentification type=\"ON\">".$no_pedido."</referenceIdentification><ReferenceDate>".$fecha_pedido."</ReferenceDate><FechaPromesaEnt>".$fecha_entrega."</FechaPromesaEnt></orderIdentification><seller><gln>0</gln><alternatePartyIdentification type=\"SELLER_ASSIGNED_IDENTIFIER_FOR_A_PARTY\">".$no_proveedor."</alternatePartyIdentification><IndentificaTipoProv>1</IndentificaTipoProv></seller><shipTo><gln>0</gln><nameAndAddress><name/><streetAddressOne/><city>".$ciudad."</city><postalCode/></nameAndAddress><BodegaDestino>".$bodega_destino."</BodegaDestino><BodegaReceptora>".$bodega_receptora."</BodegaReceptora></shipTo><RegionCel><Region>".$region."</Region></RegionCel><Customs><alternatePartyIdentification type=\"TN\"/></Customs><currency currencyISOCode=\"MXN\"><currencyFunction>BILLING_CURRENCY</currencyFunction><rateOfChange/></currency><FleteCaja type=\"SELLER_PROVIDED\">".$fletera."</FleteCaja><allowanceCharge allowanceChargeType=\"CHARGE_GLOBAL\" settlementType=\"BILL_BACK\"><specialServicesType>AA</specialServicesType><monetaryAmountOrPercentage><rate base=\"INVOICE_VALUE\"><percentage/></rate></monetaryAmountOrPercentage></allowanceCharge>".$articulos_xml."<totalCaractsFisicas><Peso Udmedida=\"GRM\"/></totalCaractsFisicas><totalAmount><Amount>".$total_antes_iva."</Amount></totalAmount><TotalAllowanceCharge allowanceOrChargeType=\"CHARGE\"><specialServicesType>AA</specialServicesType><Amount>0.00</Amount></TotalAllowanceCharge><baseAmount><Amount>".$total_antes_iva."</Amount></baseAmount><tax type=\"VAT\"><taxPercentage>16.00</taxPercentage><taxAmount>".$total_iva."</taxAmount><taxCategory>TRANSFERIDO</taxCategory></tax><payableAmount><Amount>".$total_despues_iva."</Amount></payableAmount><cadenaOriginal><cadena>||3.2|".$fecha_xml."|ingreso|Pago en una sola exhibición|47260.32|MXN|54821.97|OIG100330DPA|OCEANUS INTERNATIONAL GROUP MEXICO SA DE CV|RAMAL LA TIJERA|1850 B|EL CAMPANARIO|Zapopan|Zapopan|Jalisco|México|45234|COP920428Q20|COPPEL S.A DE C.V|Calle Republica|2855 Pte.|Recursos Hidraulicos|.|Sinaloa|México|80105".$articulos_footer."|IVA|16.000000|".$total_iva."|".$total_iva."||</cadena></cadenaOriginal></requestForPayment></cfdi:Addenda>";
		
		echo($output);
	}else{
		echo("0");
	}
?>