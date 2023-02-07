<?php defined('BASEPATH') OR exit('No direct script access allowed');
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Client;

class Einvoice {
	
	private static $_auth_url;
	private static $_ci;
	private static $_einv_url;
	private static $_get;
	private static $_client;
	private static $_credential;
	private static $_crd = [];
	private static $_count;
	private static $_company;
	
	public function __construct() 
	{
		self::$_ci    =& get_instance();

		self::$_client = new Client( array( 
			'curl'   => array( CURLOPT_SSL_VERIFYPEER => false ),
			'verify' => false,
			'headers' => [ 'Content-Type' => 'application/json' ],
		));

		self::$_count = 0;
		if(self::$_ci->session->credential)
			self::$_company = self::$_ci->session->credential['company_id'];
		self::$_credential = self::$_ci->settings->getEinvCredential();

		foreach (self::$_credential as $key => $value) {
			$name = str_replace('_test', '', $value['name']);
			self::$_crd[$name] = $value['value'];
		}

		self::$_auth_url = self::$_crd['einv_auth_url'];
		self::$_einv_url = self::$_crd['einv_url'];
	}
	/**
	* API To Generate EwayBill Token
	*/
	public static function generateToken($crd = null) {
		
		ini_set('memory_limit', '-1');
		
		if($crd){
			self::$_crd = $crd;
			self::$_auth_url = $crd['einv_auth_url'];

		}
		
		self::$_get['aspid'] = self::$_crd['einv_asp_id'];
		self::$_get['password'] = self::$_crd['einv_password'];
		self::$_get['Gstin'] = self::$_crd['einv_gstin'];
		self::$_get['user_name'] = self::$_crd['einv_username'];
		self::$_get['eInvPwd'] = self::$_crd['einv_einvpwd'];

		//$gstCall = self::$_ci->kaabar->getField('companies', self::$_company, 'id', 'gst_call');

		try {

			$response = self::$_client->get(self::$_auth_url.http_build_query(self::$_get));
			/// Update GST CALL
			//self::$_ci->kaabar->save('companies', ['gst_call' => ($gstCall-1)], ['id' => self::$_company]);
			
			$result = $response->getBody()->read(1024);
			$result = json_decode($result, true);
			self::$_ci->session->set_userdata('authtoken', $result['Data']['AuthToken']);

			return $result['Data']['AuthToken']; 

		}catch (GuzzleHttp\Exception\BadResponseException $e) {

			/// Update GST CALL
			//self::$_ci->kaabar->save('companies', ['gst_call' => ($gstCall-1)], ['id' => self::$_company]);

			if ($e->hasResponse()){
		    	$response = $e->getResponse();
		    	$errors = json_decode($response->getBody())->error->error_cd;
		    	$errorsCodes = preg_replace("/[^0-9]/", "", explode(', ', $errors));
		    	$result = ['code' => $response->getStatusCode(), 'errors' => $errorsCodes];
		    	return $result;
		    }
		}
	}

	/**
	* API To Fetch EwayBill Details Using EWB NO
	*/
	public static function generate($data = null, $param = null) {
		ini_set('memory_limit', '-1');

		$token = self::$_ci->session->userdata('authtoken');

		//if(! isset($token)){
			$token = self::generateToken();
		//}

		self::$_get['aspid'] = self::$_crd['einv_asp_id'];
		self::$_get['password'] = self::$_crd['einv_password'];
		self::$_get['Gstin'] = self::$_crd['einv_gstin'];
		self::$_get['user_name'] = self::$_crd['einv_username'];
		self::$_get['eInvPwd'] = self::$_crd['einv_einvpwd'];
		self::$_get['authtoken'] = $token;
		self::$_get['QrCodeSize'] = 250;
		//self::$_get['ParseIrnResp'] = 0;
		
		self::$_einv_url = self::$_einv_url.$param;

		$gstCall = self::$_ci->kaabar->getField('companies', self::$_company, 'id', 'gst_call');

		try {

			$response = self::$_client->post(self::$_einv_url.http_build_query(self::$_get),
				['body' => json_encode($data)],
			);

			/// Update GST CALL
			self::$_ci->kaabar->save('companies', ['gst_call' => ($gstCall-1)], ['id' => self::$_company]);

            $value = json_decode($response->getBody()->read(1024*1024*2), true);

            $result = ['code' => $response->getStatusCode(), 'result' => $value];
			return $result;

		}catch (GuzzleHttp\Exception\BadResponseException $e) {

			/// Update GST CALL
			self::$_ci->kaabar->save('companies', ['gst_call' => ($gstCall-1)], ['id' => self::$_company]);
            	
            if ($e->hasResponse()){
		    	$response = $e->getResponse();
		    	$errors = json_decode($response->getBody());//->error->error_cd;
		    	$result = ['code' => $response->getStatusCode(), 'errors' => $errors];
		    	return $result;
		    }
		}
	}

}





/*
{
	"$schema": "http://json-schema.org/draft-07/schema#",
	"Title": "GST-India Invoice Document ",
	"Description": "GST Invoice format for IRN Generation in INDIA",
	"Version": {
		"type": "string",
		"minLength": 1,
		"maxLength": 6,
		"description": "Version of the schema"
	},
	"Irn": {
		"type": "string",
		"minLength": 64,
		"maxLength": 64,
		"description": "Invoice Reference Number"
	},
	"TranDtls": {
		"type": "object",
		"properties": {
			"TaxSch": {
				"type": "string",
				"minLength": 3,
				"maxLength": 10,
				"enum": [
					"GST"
				],
				"pattern": "^(GST)$",
				"description": "GST- Goods and Services Tax Scheme"
			},
			"SupTyp": {
				"type": "string",
				"minLength": 3,
				"maxLength": 10,
				"enum": [
					"B2B",
					"SEZWP",
					"SEZWOP",
					"EXPWP",
					"EXPWOP",
					"DEXP"
				],
				"pattern": "(?i)^((B2B)|(SEZWP)|(SEZWOP)|(EXPWP)|(EXPWOP)|(DEXP))$",
				"description": "Type of Supply: B2B-Business to Business, SEZWP - SEZ with payment, SEZWOP - SEZ without payment, EXPWP - Export with Payment, EXPWOP - Export without payment,DEXP - Deemed Export"
			},
			"RegRev": {
				"type": "string",
				"minLength": 1,
				"maxLength": 1,
				"enum": [
					"Y",
					"N"
				],
				"pattern": "^([Y|N]{1})$",
				"description": "Y- whether the tax liability is payable under reverse charge"
			},
			"EcmGstin": {
				"type": "string",
				"minLength": 15,
				"maxLength": 15,
				"pattern": "^([0-9]{2}[0-9A-Z]{13})$",
				"description": "GSTIN of e-Commerce operator"
			},
			"IgstOnIntra": {
				"type": "string",
				"minLength": 1,
				"maxLength": 1,
				"enum": [
					"Y",
					"N"
				],
				"pattern": "^([Y|N]{1})$",
				"description": "Y- indicates the supply is intra state but chargeable to IGST"
			}
		},
		"required": [
			"TaxSch",
			"SupTyp"
		]
	},
	"DocDtls": {
		"type": "object",
		"properties": {
			"Typ": {
				"type": "string",
				"minLength": 3,
				"maxLength": 3,
				"enum": [
					"INV",
					"CRN",
					"DBN"
				],
				"pattern": "(?i)^((INV)|(CRN)|(DBN))$",
				"description": "Document Type: INVOICE, CREDIT NOTE, DEBIT NOTE"
			},
			"No": {
				"type": "string",
				"minLength": 1,
				"maxLength": 16,
				"pattern": "^([a-zA-Z1-9]{1}[a-zA-Z0-9\/-]{0,15})$",
				"description": "Document Number"
			},
			"Dt": {
				"type": "string",
				"minLength": 10,
				"maxLength": 10,
				"pattern": "^[0-3][0-9]\/[0-1][0-9]\/[2][0][1-2][0-9]$",
				"description": "Document Date"
			}
		},
		"required": [
			"Typ",
			"No",
			"Dt"
		]
	},
	"SellerDtls": {
		"type": "object",
		"properties": {
			"Gstin": {
				"type": "string",
				"minLength": 15,
				"maxLength": 15,
				"pattern": "([0-9]{2}[0-9A-Z]{13})",
				"description": "GSTIN of supplier"
			},
			"LglNm": {
				"type": "string",
				"minLength": 3,
				"maxLength": 100,
				"pattern": "^([^\\\"])*$",
				"description": "Legal Name"
			},
			"TrdNm": {
				"type": "string",
				"minLength": 3,
				"maxLength": 100,
				"pattern": "^([^\\\"])*$",
				"description": "Tradename"
			},
			"Addr1": {
				"type": "string",
				"minLength": 1,
				"maxLength": 100,
				"pattern": "^([^\\\"])*$",
				"description": "Building/Flat no, Road/Street"
			},
			"Addr2": {
				"type": "string",
				"minLength": 3,
				"maxLength": 100,
				"pattern": "^([^\\\"])*$",
				"description": "Address 2 of the supplier (Floor no., Name of the premises/building)"
			},
			"Loc": {
				"type": "string",
				"minLength": 3,
				"maxLength": 50,
				"pattern": "^([^\\\"])*$",
				"description": "Location"
			},
			"Pin": {
				"type": "number",
				"minimum": 100000,
				"maximum": 999999,
				"description": "Pincode"
			},
			"Stcd": {
				"type": "string",
				"minLength": 1,
				"maxLength": 2,
				"pattern": "^(?!0+$)([0-9]{1,2})$",
				"description": "State Code of the supplier. Refer the master"
			},
			"Ph": {
				"type": "String",
				"minLength": 6,
				"maxLength": 12,
				"pattern": "^([0-9]{6,12})$",
				"description": "Phone or Mobile No."
			},
			"Em": {
				"type": "string",
				"minLength": 6,
				"maxLength": 100,
				"pattern": "^[a-zA-Z0-9+_.-]+@[a-zA-Z0-9.-]+$",
				"description": "Email-Id"
			}
		},
		"required": [
			"Gstin",
			"LglNm",
			"Addr1",
			"Loc",
			"Pin",
			"Stcd"
		]
	},
	"BuyerDtls": {
		"type": "object",
		"properties": {
			"Gstin": {
				"type": "string",
				"minLength": 3,
				"maxLength": 15,
				"pattern": "^(([0-9]{2}[0-9A-Z]{13})|URP)$",
				"description": "GSTIN of buyer , URP if exporting"
			},
			"LglNm": {
				"type": "string",
				"minLength": 3,
				"maxLength": 100,
				"pattern": "^([^\\\"])*$",
				"description": "Legal Name"
			},
			"TrdNm": {
				"type": "string",
				"minLength": 3,
				"maxLength": 100,
				"pattern": "^([^\\\"])*$",
				"description": "Trade Name"
			},
			"Pos": {
				"type": "string",
				"minLength": 1,
				"maxLength": 2,
				"pattern": "^(?!0+$)([0-9]{1,2})$",
				"description": "State code of Place of supply. If POS lies outside the country, the code shall be 96."
			},
			"Addr1": {
				"type": "string",
				"minLength": 1,
				"maxLength": 100,
				"pattern": "^([^\\\"])*$",
				"description": "Address 1 of the buyer. (Building/Flat no., Road/Street etc.)"
			},
			"Addr2": {
				"type": "string",
				"minLength": 3,
				"maxLength": 100,
				"pattern": "^([^\\\"])*$",
				"description": "Address 2 of the buyer. (Floor no., Name of the premises/ building)"
			},
			"Loc": {
				"type": "string",
				"minLength": 3,
				"maxLength": 100,
				"pattern": "^([^\\\"])*$",
				"description": "Location"
			},
			"Pin": {
				"type": " number",
				"minimum": 100000,
				"maximum": 999999,
				"description": "Pincode"
			},
			"Stcd": {
				"type": "string",
				"minLength": 1,
				"maxLength": 2,
				"pattern": "^(?!0+$)([0-9]{1,2})$",
				"description": "State Code of the buyer. Refer the master"
			},
			"Ph": {
				"type": "String",
				"minLength": 6,
				"maxLength": 12,
				"pattern": "^([0-9]{6,12})$",
				"description": "Phone or Mobile No."
			},
			"Em": {
				"type": "string",
				"minLength": 6,
				"maxLength": 100,
				"pattern": "^[a-zA-Z0-9+_.-]+@[a-zA-Z0-9.-]+$",
				"description": "Email-Id"
			}
		},
		"required": [
			"Gstin",
			"LglNm",
			"Pos",
			"Addr1",
			"Loc",
			"Stcd"
		]
	},
	"DispDtls": {
		"type": "object",
		"properties": {
			"Nm": {
				"type": "string",
				"minLength": 3,
				"maxLength": 100,
				"pattern": "^([^\\\"])*$",
				"description": "Name of the company from which the goods are dispatched"
			},
			"Addr1": {
				"type": "string",
				"minLength": 1,
				"maxLength": 100,
				"pattern": "^([^\\\"])*$",
				"description": "Address 1 of the entity from which goods are dispatched. (Building/Flat no.Road/Street etc.)"
			},
			"Addr2": {
				"type": "string",
				"minLength": 3,
				"maxLength": 100,
				"pattern": "^([^\\\"])*$",
				"description": "Address 2 of the entity from which goods are dispatched. (Floor no., Name of the premises/building)"
			},
			"Loc": {
				"type": "string",
				"minLength": 3,
				"maxLength": 100,
				"pattern": "^([^\\\"])*$",
				"description": "Location"
			},
			"Pin": {
				"type": "number",
				"minimum": 100000,
				"maximum": 999999,
				"description": "Pincode"
			},
			"Stcd": {
				"type": "string",
				"minLength": 1,
				"maxLength": 2,
				"pattern": "^(?!0+$)([0-9]{1,2})$",
				"description": "State Code. Refer the master"
			}
		},
		"required": [
			"Nm",
			"Addr1",
			"Loc",
			"Pin",
			"Stcd"
		]
	},
	"ShipDtls": {
		"type": "object",
		"properties": {
			"Gstin": {
				"type": "string",
				"maxLength": 15,
				"minLength": 3,
				"pattern": "^(([0-9]{2}[0-9A-Z]{13})|URP)$",
				"description": "GSTIN of entity to whom goods are shipped"
			},
			"LglNm": {
				"type": "string",
				"minLength": 3,
				"maxLength": 100,
				"pattern": "^([^\\\"])*$",
				"description": "Legal Name"
			},
			"TrdNm": {
				"type": "string",
				"minLength": 3,
				"maxLength": 100,
				"pattern": "^([^\\\"])*$",
				"description": "Trade Name"
			},
			"Addr1": {
				"type": "string",
				"minLength": 1,
				"maxLength": 100,
				"pattern": "^([^\\\"])*$",
				"description": "Address1 of the entity to whom the supplies are shipped to. (Building/Flat no., Road/Street etc.)"
			},
			"Addr2": {
				"type": "string",
				"minLength": 3,
				"maxLength": 100,
				"pattern": "^([^\\\"])*$",
				"description": "Address 2 of the entity to whom the supplies are shipped to. (Floor no., Name of the premises/building)."
			},
			"Loc": {
				"type": "string",
				"minLength": 3,
				"maxLength": 100,
				"pattern": "^([^\\\"])*$",
				"description": "Place (City,Town,Village) entity to whom the supplies are shipped to."
			},
			"Pin": {
				"type": "number",
				"minimum": 100000,
				"maximum": 999999,
				"description": "Pincode"
			},
			"Stcd": {
				"type": "string",
				"minLength": 1,
				"maxLength": 2,
				"pattern": "^(?!0+$)([0-9]{1,2})$",
				"description": "State Code to which supplies are shipped to. Refer the master"
			}
		},
		"required": [
			"LglNm",
			"Addr1",
			"Loc",
			"Pin",
			"Stcd"
		]
	},
	"ItemList": [{
		"type": "object",
		"properties": {
			"SlNo": {
				"type": "string",
				"minLength": 1,
				"maxLength": 6,
				"pattern": "^([0-9]{1,6})$",
				"description": "Serial No. of Item"
			},
			"PrdDesc": {
				"type": "string",
				"minLength": 3,
				"maxLength": 300,
				"pattern": "^([^\\\"])*$",
				"description": "Product Description"
			},
			"IsServc": {
				"type": "string",
				"minLength": 1,
				"maxLength": 1,
				"enum": [
					"Y",
					"N"
				],
				"pattern": "^([Y|N]{1})$",
				"description": "Specify whether the supply is service or not. Specify Y-for Service"
			},
			"HsnCd": {
				"type": "string",
				"minLength": 4,
				"maxLength": 8,
				"pattern": "^(?!0+$)([0-9]{4}|[0-9]{6}|[0-9]{8})$",
				"description": "HSN Code. Refer Master"
			},
			"Barcde": {
				"type": "string",
				"minLength": 3,
				"maxLength": 30,
				"pattern": "^([^\\\"])*$",
				"description": "Bar Code"
			},
			"Qty": {
				"type": "number",
				"minimum": 0,
				"maximum": 9999999999.999,
				"description": "Quantity"
			},
			"FreeQty": {
				"type": "number",
				"minimum": 0,
				"maximum": 9999999999.999,
				"description": "Free Quantity"
			},
			"Unit": {
				"type": "string",
				"minLength": 3,
				"maxLength": 8,
				"pattern": "^([A-Z|a-z]{3,8})$",
				"description": "Unit. Refer the master"
			},
			"UnitPrice": {
				"type": "number",
				"minimum": 0,
				"maximum": 999999999999.999,
				"description": "Unit Price - Rate"
			},
			"TotAmt": {
				"type": "number",
				"minimum": 0,
				"maximum": 999999999999.99,
				"description": "Gross Amount (Unit Price * Quantity)"
			},
			"Discount": {
				"type": "number",
				"minimum": 0,
				"maximum": 999999999999.99,
				"description": "Discount"
			},
			"PreTaxVal": {
				"type": "number",
				"minimum": 0,
				"maximum": 999999999999.99,
				"description": "Pre tax value"
			},
			"AssAmt": {
				"type": "number",
				"minimum": 0,
				"maximum": 999999999999.99,
				"description": "Taxable Value (Total Amount -Discount)"
			},
			"GstRt": {
				"type": "number",
				"minimum": 0,
				"maximum": 999.999,
				"description": "The GST rate, represented as percentage that applies to the invoiced item. It will IGST rate only."
			},
			"IgstAmt": {
				"type": "number",
				"minimum": 0,
				"maximum": 999999999999.99,
				"description": " Amount of IGST payable."
			},
			"CgstAmt": {
				"type": "number",
				"minimum": 0,
				"maximum": 999999999999.99,
				"description": " Amount of CGST payable."
			},
			"SgstAmt": {
				"type": "number",
				"minimum": 0,
				"maximum": 999999999999.99,
				"description": " Amount of SGST payable."
			},
			"CesRt": {
				"type": "number",
				"minimum": 0,
				"maximum": 999.999,
				"description": "Cess Rate"
			},
			"CesAmt": {
				"type": "number",
				"minimum": 0,
				"maximum": 999999999999.99,
				"description": "Cess Amount(Advalorem) on basis of rate and quantity of item"
			},
			"CesNonAdvlAmt": {
				"type": "number",
				"minimum": 0,
				"maximum": 999999999999.99,
				"description": "Cess Non-Advol Amount"
			},
			"StateCesRt": {
				"type": "number",
				"minimum": 0,
				"maximum": 999.999,
				"description": "State CESS Rate"
			},
			"StateCesAmt": {
				"type": "number",
				"minimum": 0,
				"maximum": 999999999999.99,
				"description": "State CESS Amount"
			},
			"StateCesNonAdvlAmt": {
				"type": "number",
				"minimum": 0,
				"maximum": 999999999999.99,
				"description": "State CESS Non Adval Amount"
			},
			"OthChrg": {
				"type": "number",
				"minimum": 0,
				"maximum": 999999999999.99,
				"description": "Other Charges"
			},
			"TotItemVal": {
				"type": "number",
				"minimum": 0,
				"maximum": 999999999999.99,
				"description": "Total Item Value = Assessable Amount + CGST Amt + SGST Amt + Cess Amt + CesNonAdvlAmt + StateCesAmt + StateCesNonAdvlAmt+Otherchrg"
			},
			"OrdLineRef": {
				"type": "string",
				"minLength": 1,
				"maxLength": 50,
				"pattern": "^([^\\\"])*$",
				"description": "Order line reference"
			},
			"OrgCntry": {
				"type": "string",
				"minLength": 2,
				"maxLength": 2,
				"pattern": "^([A-Z|a-z]{2})$",
				"description": "Origin Country. Refer Master"
			},
			"PrdSlNo": {
				"type": "string",
				"minLength": 1,
				"maxLength": 20,
				"pattern": "^([^\\\"])*$",
				"description": "Serial number in case of each item having a unique number."
			},
			"BchDtls": {
				"type": "object",
				"properties": {
					"Nm": {
						"type": "string",
						"minLength": 3,
						"maxLength": 20,
						"pattern": "^([^\\\"])*$",
						"description": "Batch number"
					},
					"ExpDt": {
						"type": "string",
						"maxLength": 10,
						"minLength": 10,
						"pattern": "^[0-3][0-9]\/[0-1][0-9]\/[2][0][1-2][0-9]$",
						"description": "Batch Expiry Date"
					},
					"WrDt": {
						"type": "string",
						"maxLength": 10,
						"minLength": 10,
						"pattern": "^[0-3][0-9]\/[0-1][0-9]\/[2][0][1-2][0-9]$",
						"description": "Warranty Date"
					}
				},
				"required": [
					"Nm"
				]
			},
			"AttribDtls": [{
				"type": "object",
				"properties": {
					"Nm": {
						"type": "string",
						"minLength": 1,
						"maxLength": 100,
						"pattern": "^([^\\\"])*$",
						"description": "Attribute name of the item"
					},
					"Val": {
						"type": "string",
						"minLength": 1,
						"maxLength": 100,
						"pattern": "^([^\\\"])*$",
						"description": "Attribute value of the item"
					}
				}
			}]
		},
		"required": [
			"SlNo",
			"IsServc",
			"HsnCd",
			"UnitPrice",
			"TotAmt",
			"AssAmt",
			"GstRt",
			"TotItemVal"
		]
	}],
	"ValDtls": {
		"type": "object",
		"properties": {
			"AssVal": {
				"type": "number",
				"minimum": 0,
				"maximum": 99999999999999.99,
				"description": "Total Assessable value of all items"
			},
			"CgstVal": {
				"type": "number",
				"maximum": 99999999999999.99,
				"minimum": 0,
				"description": "Total CGST value of all items"
			},
			"SgstVal": {
				"type": "number",
				"minimum": 0,
				"maximum": 99999999999999.99,
				"description": "Total SGST value of all items"
			},
			"IgstVal": {
				"type": "number",
				"minimum": 0,
				"maximum": 99999999999999.99,
				"description": "Total IGST value of all items"
			},
			"CesVal": {
				"type": "number",
				"minimum": 0,
				"maximum": 99999999999999.99,
				"description": "Total CESS value of all items"
			},
			"StCesVal": {
				"type": "number",
				"minimum": 0,
				"maximum": 99999999999999.99,
				"description": "Total State CESS value of all items"
			},
			"Discount": {
				"type": "number",
				"minimum": 0,
				"maximum": 99999999999999.99,
				"description": "Discount"
			},
			"OthChrg": {
				"type": "number",
				"minimum": 0,
				"maximum": 99999999999999.99,
				"description": "Other Charges"
			},
			"RndOffAmt": {
				"type": "number",
				"minimum": -99.99,
				"maximum": 99.99,
				"description": "Rounded off amount"
			},
			"TotInvVal": {
				"type": "number",
				"minimum": 0,
				"maximum": 99999999999999.99,
				"description": "Final Invoice value "
			},
			"TotInvValFc": {
				"type": "number",
				"minimum": 0,
				"maximum": 99999999999999.99,
				"description": "Final Invoice value in Additional Currency"
			}
		},
		"required": [
			"AssVal",
			"TotInvVal"
		]
	},
	"PayDtls": {
		"type": "object",
		"properties": {
			"Nm": {
				"type": "string",
				"minLength": 1,
				"maxLength": 100,
				"pattern": "^([^\\\"])*$",
				"description": "Payee Name"
			},
			"AccDet": {
				"type": "string",
				"minLength": 1,
				"maxLength": 18,
				"pattern": "^([^\\\"])*$",
				"description": "Bank account number of payee"
			},
			"Mode": {
				"type": "string",
				"minLength": 1,
				"maxLength": 18,
				"pattern": "^([^\\\"])*$",
				"description": "Mode of Payment: Cash, Credit, Direct Transfer"
			},
			"FinInsBr": {
				"type": "string",
				"minLength": 1,
				"maxLength": 11,
				"pattern": "^([^\\\"])*$",
				"description": "Branch or IFSC code"
			},
			"PayTerm": {
				"type": "string",
				"minLength": 1,
				"maxLength": 100,
				"pattern": "^([^\\\"])*$",
				"description": "Terms of Payment"
			},
			"PayInstr": {
				"type": "string",
				"minLength": 1,
				"maxLength": 100,
				"pattern": "^([^\\\"])*$",
				"description": "Payment Instruction"
			},
			"CrTrn": {
				"type": "string",
				"minLength": 1,
				"maxLength": 100,
				"pattern": "^([^\\\"])*$",
				"description": "Credit Transfer"
			},
			"DirDr": {
				"type": "string",
				"minLength": 1,
				"maxLength": 100,
				"pattern": "^([^\\\"])*$",
				"description": "Direct Debit"
			},
			"CrDay": {
				"type": "number",
				"minimum": 0,
				"maximum": 9999,
				"description": "Credit Days"
			},
			"PaidAmt": {
				"type": "number",
				"minimum": 0,
				"maximum": 99999999999999.99,
				"description": "The sum of amount which have been paid in advance."
			},
			"PaymtDue": {
				"type": "number",
				"minimum": 0,
				"maximum": 99999999999999.99,
				"description": "Outstanding amount that is required to be paid."
			}
		}
	},
	"RefDtls": {
		"type": "object",
		"properties": {
			"InvRm": {
				"type": "string",
				"maxLength": 100,
				"minLength": 3,
				"pattern": "^([^\\\"])*$",
				"description": "Remarks/Note"
			},
			"DocPerdDtls": {
				"type": "object",
				"properties": {
					"InvStDt": {
						"type": "string",
						"maxLength": 10,
						"minLength": 10,
						"pattern": "^[0-3][0-9]\/[0-1][0-9]\/[2][0][1-2][0-9]$",
						"description": "Invoice Period Start Date"
					},
					"InvEndDt": {
						"type": "string",
						"maxLength": 10,
						"minLength": 10,
						"pattern": "^[0-3][0-9]\/[0-1][0-9]\/[2][0][1-2][0-9]$",
						"description": "Invoice Period End Date"
					}
				},
				"required": [
					" InvStDt ",
					" InvEndDt "
				]
			},
			"PrecDocDtls": [{
				"type": "object",
				"properties": {
					"InvNo": {
						"type": "string",
						"minLength": 1,
						"maxLength": 16,
						"pattern": "^[1-9a-zA-Z]{1}[0-9a-zA-Z\/-]{1,15}$",
						"description": "Reference of original invoice, if any."
					},
					"InvDt": {
						"type": "string",
						"maxLength": 10,
						"minLength": 10,
						"pattern": "^[0-3][0-9]\/[0-1][0-9]\/[2][0][1-2][0-9]$",
						"description": "Date of preceding invoice"
					},
					"OthRefNo": {
						"type": "string",
						"minLength": 1,
						"maxLength": 20,
						"pattern": "^([^\\\"])*$",
						"description": "Other Reference"
					}
				}
			}],
			"required": [
				"InvNo",
				"InvDt"
			],
			"ContrDtls": [{
				"type": "object",
				"properties": {
					"RecAdvRefr": {
						"type": "string",
						"minLength": 1,
						"maxLength": 20,
						"pattern": "^([^\\\"])*$",
						"description": "Receipt Advice No."
					},
					"RecAdvDt": {
						"type": "string",
						"minLength": 10,
						"maxLength": 10,
						"pattern": "^[0-3][0-9]\/[0-1][0-9]\/[2][0][1-2][0-9]$",
						"description": "Date of receipt advice"
					},
					"TendRefr": {
						"type": "string",
						"minLength": 1,
						"maxLength": 20,
						"pattern": "^([^\\\"])*$",
						"description": "Lot/Batch Reference No."
					},
					"ContrRefr": {
						"type": "string",
						"minLength": 1,
						"maxLength": 20,
						"pattern": "^([^\\\"])*$",
						"description": "Contract Reference Number"
					},
					"ExtRefr": {
						"type": "string",
						"minLength": 1,
						"maxLength": 20,
						"pattern": "^([^\\\"])*$",
						"description": "Any other reference"
					},
					"ProjRefr": {
						"type": "string",
						"minLength": 1,
						"maxLength": 20,
						"pattern": "^([^\\\"])*$",
						"description": "Project Reference Number"
					},
					"PORefr": {
						"type": "string",
						"minLength": 1,
						"maxLength": 16,
						"pattern": "^([^\\\"])*$",
						"description": " PO Reference Number"
					},
					"PORefDt": {
						"type": "string",
						"minLength": 10,
						"maxLength": 10,
						"pattern": "^[0-3][0-9]\/[0-1][0-9]\/[2][0][1-2][0-9]$",
						"description ": "POReferencedate "
					}
				}
			}]
		}
	},
	"AddlDocDtls ": [{
		"type ": "object ",
		"properties ": {
			"Url ": {
				"type ": "string ",
				"minLength ": 3,
				"maxLength ": 100,
				"pattern ": "^([^\\\"])*$",
				"description ": "SupportingdocumentURL "
			},
			"Docs ": {
				"type ": "string ",
				"minLength ": 3,
				"maxLength ": 1000,
				"pattern ": "^([^\\\"])*$",
				"description": "Supporting document in Base64 Format"
			},
			"Info": {
				"type": "string",
				"minLength": 3,
				"maxLength": 1000,
				"pattern": "^([^\\\"])*$",
				"description": "Any additional information"
			}
		}
	}],
	"ExpDtls": {
		"type": "object",
		"properties": {
			"ShipBNo": {
				"type": "string",
				"minLength": 1,
				"maxLength": 20,
				"pattern": "^([^\\\"])*$",
				"description": "Shipping Bill No."
			},
			"ShipBDt": {
				"type": "string",
				"minLength": 10,
				"maxLength": 10,
				"pattern": "^[0-3][0-9]\/[0-1][0-9]\/[2][0][1-2][0-9]$",
				"description": "Shipping Bill Date"
			},
			"Port": {
				"type": "string",
				"minLength": 2,
				"maxLength": 10,
				"pattern": "^[0-9|A-Z|a-z]{2,10}$",
				"description": "Port Code. Refer the master"
			},
			"RefClm": {
				"type": "string",
				"minLength": 1,
				"maxLength": 1,
				"pattern": "^([Y|N]{1})$",
				"description": "Claiming Refund. Y/N"
			},
			"ForCur": {
				"type": "string",
				"minLength": 3,
				"maxLength": 16,
				"pattern": "^[A-Z|a-z]{3,16}$",
				"description": "Additional Currency Code. Refer the master"
			},
			"CntCode": {
				"type": "string",
				"minLength": 2,
				"maxLength": 2,
				"pattern": "^([A-Z]{2})$",
				"description": "Country Code. Refer the master"
			},
			"ExpDuty": {
				"type": "number",
				"minimum": 0,
				"maximum": 999999999999.99,
				"description": "Export Duty"
			}
		}
	},
	"EwbDtls": {
		"type": "object",
		"properties": {
			"TransId": {
				"type": "string",
				"minLength": 15,
				"maxLength": 15,
				"pattern": "^([0-9]{2}[0-9A-Z]{13})$",
				"description": "Transin/GSTIN"
			},
			"TransName": {
				"type": "string",
				"minLength": 3,
				"maxLength": 100,
				"pattern": "^([^\\\"])*$",
				"description": "Name of the transporter"
			},
			"TransMode": {
				"type": "string",
				"maxLength": 1,
				"minLength": 1,
				"enum": [
					"1",
					"2",
					"3",
					"4"
				],
				"pattern": "^([1-4]{1})?$",
				"description": "Mode of transport (Road-1, Rail-2, Air-3, Ship-4)"
			},
			"Distance": {
				"type": "number",
				"minimum": 0,
				"maximum": 4000,
				"description": " Distance between source and destination PIN codes"
			},
			"TransDocNo": {
				"type": "string",
				"minLength": 1,
				"maxLength": 15,
				"pattern": "^([a-zA-Z0-9\/-]{1,15})$",
				"description": "Tranport Document Number"
			},
			"TransDocDt": {
				"type": "string",
				"minLength": 10,
				"maxLength": 10,
				"pattern": "^[a-zA-Z0-9]{1}[a-zA-Z0-9-/]*$",
				"description": "Transport Document Date"
			},
			"VehNo": {
				"type": "string",
				"minLength": 4,
				"maxLength": 20,
				"pattern": "^([A-Z|a-z|0-9]{4,20})$",
				"description": "Vehicle Number"
			},
			"VehType": {
				"type": "string",
				"minLength": 1,
				"maxLength": 1,
				"enum": [
					"O",
					"R"
				],
				"pattern": "^([O|R]{1})$",
				"description": "Whether O-ODC or R-Regular "
			}
		},
		"required": [
			"Distance"
		]
	},
	"required": [
		"Version",
		"TranDtls",
		"DocDtls",
		"SellerDtls",
		"BuyerDtls",
		"ItemList",
		"ValDtls"
	]
}
*/