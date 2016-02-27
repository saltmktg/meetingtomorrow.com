<?php
	$fedExURL = 'https://wsbeta.fedex.com:443/web-services';
	$fedExPostData = '<?xml version="1.0" encoding="utf-8"?>
		<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns="http://fedex.com/ws/rate/v13">
			<soapenv:Header/>
			<soapenv:Body>
				<RateRequest>
					<WebAuthenticationDetail>
						<UserCredential>
							<Key>BPrD80cOo8UKZwdV</Key>
							<Password>G3uIqbcaaSKiNVQ02DxcUvOyy</Password>
						</UserCredential>
					</WebAuthenticationDetail>
					<ClientDetail>
						<AccountNumber>510087461</AccountNumber>
						<MeterNumber>118554636</MeterNumber>
					</ClientDetail>
					<Version>
						<ServiceId>crs</ServiceId>
						<Major>13</Major>
						<Intermediate>0</Intermediate>
						<Minor>0</Minor>
					</Version>
					<ReturnTransitAndCommit>1</ReturnTransitAndCommit>
					<RequestedShipment>
						<ShipTimestamp>2016-01-21T20:00:00-00:00</ShipTimestamp>
						<DropoffType>REGULAR_PICKUP</DropoffType>
						<PackagingType>YOUR_PACKAGING</PackagingType>
						<Shipper>
							<AccountNumber>510087461</AccountNumber>
							<Contact>
								<PersonName>Shipping Department</PersonName>
								<CompanyName>Meeting Tomorrow</CompanyName>
								<PhoneNumber>(773) 754-3878</PhoneNumber>
							</Contact>
							<Address>
								<StreetLines>1802 W Berteau Ave</StreetLines>
								<StreetLines>Ste 105</StreetLines>
								<City>Chicago</City>
								<StateOrProvinceCode>IL</StateOrProvinceCode>
								<PostalCode>60613</PostalCode>
								<UrbanizationCode>IL</UrbanizationCode>
								<CountryCode>US</CountryCode>
								<Residential>0</Residential>
							</Address>
						</Shipper>
						<Recipient>
							<Contact>
								<PersonName>Venue Contact / Delivery Contact</PersonName>
								<CompanyName>Meeting Tomorrow</CompanyName>
								<PhoneNumber>(773) 754-3878</PhoneNumber>
								<EMailAddress></EMailAddress>
							</Contact>
							<Address>
								<StreetLines>1802 W Berteau Ave</StreetLines>
								<StreetLines>Ste 105</StreetLines>
								<City>Chicago</City>
								<StateOrProvinceCode>IL</StateOrProvinceCode>
								<PostalCode>60613</PostalCode>
								<UrbanizationCode>IL</UrbanizationCode>
								<CountryCode>US</CountryCode>
								<Residential>0</Residential>
							</Address>
						</Recipient>
						<Origin>
							<Contact>
								<PersonName>Shipping Department</PersonName>
								<CompanyName>Meeting Tomorrow</CompanyName>
								<PhoneNumber>(773) 754-3878</PhoneNumber>
							</Contact>
							<Address>
								<StreetLines>1802 W Berteau Ave</StreetLines>
								<StreetLines>Door A</StreetLines>
								<City>Chicago</City>
								<StateOrProvinceCode>IL</StateOrProvinceCode>
								<PostalCode>60613</PostalCode>
								<UrbanizationCode>IL</UrbanizationCode>
								<CountryCode>US</CountryCode>
								<Residential>0</Residential>
							</Address>
						</Origin>
						<ShippingChargesPayment>
							<PaymentType>SENDER</PaymentType>
							<Payor>
								<ResponsibleParty>
									<AccountNumber>510087461</AccountNumber>
									<Contact/>
								</ResponsibleParty>
							</Payor>
						</ShippingChargesPayment>
						<RateRequestTypes>LIST</RateRequestTypes>
						<PackageCount>1</PackageCount>
						<RequestedPackageLineItems>
							<SequenceNumber>1</SequenceNumber>
							<GroupPackageCount>1</GroupPackageCount>
							<Weight>
								<Units>LB</Units>
								<Value>10</Value>
							</Weight>
							<PhysicalPackaging>CASE</PhysicalPackaging>
							<CustomerReferences>
								<CustomerReferenceType>CUSTOMER_REFERENCE</CustomerReferenceType>
								<Value>S10000</Value>
							</CustomerReferences>
							<CustomerReferences>
								<CustomerReferenceType>P_O_NUMBER</CustomerReferenceType>
								<Value></Value>
							</CustomerReferences>
							<CustomerReferences>
								<CustomerReferenceType>DEPARTMENT_NUMBER</CustomerReferenceType>
								<Value></Value>
							</CustomerReferences>
							<SpecialServicesRequested>
								<SpecialServiceTypes>SIGNATURE_OPTION</SpecialServiceTypes>
								<SignatureOptionDetail>
									<OptionType>NO_SIGNATURE_REQUIRED</OptionType>
								</SignatureOptionDetail>
							</SpecialServicesRequested>
						</RequestedPackageLineItems>
					</RequestedShipment>
				</RateRequest>
			</soapenv:Body>
		</soapenv:Envelope>';
	
	$curl = curl_init();
	curl_setopt($curl,CURLOPT_URL,$fedExURL);
	curl_setopt($curl,CURLOPT_HEADER,false);
	curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
	curl_setopt($curl,CURLOPT_CONNECTTIMEOUT,30);
	curl_setopt($curl,CURLOPT_TIMEOUT,30);
	curl_setopt($curl,CURLOPT_POST,true);
	curl_setopt($curl,CURLOPT_POSTFIELDS,$fedExPostData);
	$fedExResponse = curl_exec($curl);
	
	if ( $fedExResponse != false )
	{
		echo('FedEx Response:<br><br>' . $fedExResponse);
		echo('<br><br><br><br><br>Response var_dump:<br><br>');
		var_dump($fedExResponse);
	}
	else { echo('Error:<br><br>' . curl_error($curl)); }
	
	curl_close($curl);
?>