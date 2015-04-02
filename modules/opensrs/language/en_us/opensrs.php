<?php
// Basics
$lang['Opensrs.name'] = "Opensrs";
$lang['Opensrs.module_row'] = "Account";
$lang['Opensrs.module_row_plural'] = "Accounts";

// Module management
$lang['Opensrs.add_module_row'] = "Add Account";
$lang['Opensrs.add_more_row'] = "Add More";
$lang['Opensrs.remove_row'] = "Remove";
$lang['Opensrs.manage.module_rows_title'] = "Accounts";
$lang['Opensrs.manage.module_rows_heading.user'] = "API Username";
$lang['Opensrs.manage.module_rows_heading.key'] = "Key";
$lang['Opensrs.manage.module_rows_heading.hashkey'] = "HashKey";
$lang['Opensrs.manage.module_rows_heading.sandbox'] = "Sandbox";
$lang['Opensrs.manage.module_rows_heading.options'] = "Options";
$lang['Opensrs.manage.module_rows.edit'] = "Edit";
$lang['Opensrs.manage.module_rows.delete'] = "Delete";
$lang['Opensrs.manage.module_rows.confirm_delete'] = "Are you sure you want to delete this account?";
$lang['Opensrs.manage.module_rows_no_results'] = "There are no accounts.";

// Row Meta
$lang['Opensrs.row_meta.user'] = "API Username";
$lang['Opensrs.row_meta.key'] = "Key";
$lang['Opensrs.row_meta.hashkey'] = "HashKey";
$lang['Opensrs.row_meta.sandbox'] = "Sandbox";
$lang['Opensrs.row_meta.sandbox_true'] = "Yes";
$lang['Opensrs.row_meta.sandbox_false'] = "No";

// Add row
$lang['Opensrs.add_row.box_title'] = "Add Opensrs Account";
$lang['Opensrs.add_row.basic_title'] = "Basic Settings";
$lang['Opensrs.add_row.add_btn'] = "Add Account";

// Edit row
$lang['Opensrs.edit_row.box_title'] = "Edit Opensrs Account";
$lang['Opensrs.edit_row.basic_title'] = "Basic Settings";
$lang['Opensrs.edit_row.add_btn'] = "Update Account";

// Package fields
$lang['Opensrs.package_fields.type'] = "Type";
$lang['Opensrs.package_fields.type_domain'] = "Domain Registration";
$lang['Opensrs.package_fields.type_domaintransfer'] = "Domain Transfer";
$lang['Opensrs.package_fields.type_domainrenew'] = "Domain Renew";
$lang['Opensrs.package_fields.type_ssl'] = "SSL Certificate";
$lang['Opensrs.package_fields.tld_options'] = "TLDs";
$lang['Opensrs.package_fields.ns1'] = "Name Server 1";
$lang['Opensrs.package_fields.ns2'] = "Name Server 2";
$lang['Opensrs.package_fields.ns3'] = "Name Server 3";
$lang['Opensrs.package_fields.ns4'] = "Name Server 4";
$lang['Opensrs.package_fields.ns5'] = "Name Server 5";

// Service management
$lang['Opensrs.tab_commands.title'] = "Commands";
$lang['Opensrs.tab_whois.title'] = "Whois";
$lang['Opensrs.tab_whois.section_Registrant'] = "Registrant";
$lang['Opensrs.tab_whois.section_Admin'] = "Administrative";
$lang['Opensrs.tab_whois.section_Technical'] = "Technical";
$lang['Opensrs.tab_whois.section_Billing'] = "Billing";
$lang['Opensrs.tab_whois.field_submit'] = "Update Whois";

$lang['Opensrs.tab_nameservers.title'] = "Name Servers";
$lang['Opensrs.tab_nameserver.field_ns'] = "Name Server %1\$s"; // %1$s is the name server number
$lang['Opensrs.tab_nameservers.field_submit'] = "Update Name Servers";

$lang['Opensrs.tab_dns.title'] = "DNS Records";

$lang['Opensrs.tab_emailforwarding.title'] = "Email Forwarding";
$lang['Opensrs.tab_emailforwardingver.field_source'] = "Email Prefix";
$lang['Opensrs.tab_emailforwardingver.field_destination'] = "Email to Foward";
$lang['Opensrs.tab_emailforwarding.field_submit'] = "Update Email Fowards";

$lang['Opensrs.tab_settings.title'] = "Settings";
$lang['Opensrs.tab_settings.field_registrar_lock'] = "Registrar Lock";
$lang['Opensrs.tab_settings.field_registrar_lock_yes'] = "Set the registrar lock. Recommended to prevent unauthorized transfer.";
$lang['Opensrs.tab_settings.field_registrar_lock_no'] = "Release the registrar lock so the domain can be transferred.";
$lang['Opensrs.tab_settings.field_request_epp'] = "EPP Code/Transfer Key";
$lang['Opensrs.tab_settings.field_submit'] = "Update Settings";

// Errors
$lang['Opensrs.!error.user.valid'] = "Please enter a API Username";
$lang['Opensrs.!error.key.valid'] = "Please enter the API Key";
$lang['Opensrs.!error.key.valid'] = "Please enter a random Hashkey for Salt";
$lang['Opensrs.!error.key.valid_connection'] = "The API Username and Key combination appear to be invalid, or your Opensrs account may not be configured to allow API access.";


// Domain Transfer Fields
$lang['Opensrs.transfer.domain'] = "Domain Name";
$lang['Opensrs.transfer.transfer_key'] = "EPP Code";

// Domain Fields
$lang['Opensrs.domain.domain'] = "Domain Name";
$lang['Opensrs.domain.NumYears'] = "Years";

// Nameserver Fields
$lang['Opensrs.nameserver.ns1'] = "Name Server 1";
$lang['Opensrs.nameserver.ns2'] = "Name Server 2";
$lang['Opensrs.nameserver.ns3'] = "Name Server 3";
$lang['Opensrs.nameserver.ns4'] = "Name Server 4";
$lang['Opensrs.nameserver.ns5'] = "Name Server 5";

// Whois Fields
$lang['Opensrs.whois.RegistrantFirstName'] = "First Name";
$lang['Opensrs.whois.RegistrantLastName'] = "Last Name";
$lang['Opensrs.whois.RegistrantAddress1'] = "Address 1";
$lang['Opensrs.whois.RegistrantAddress2'] = "Address 2";
$lang['Opensrs.whois.RegistrantAddress3'] = "Address 3";
$lang['Opensrs.whois.RegistrantCity'] = "City";
$lang['Opensrs.whois.RegistrantPostalCode'] = "Postal Code";
$lang['Opensrs.whois.RegistrantCountry'] = "Country";
$lang['Opensrs.whois.RegistrantCountryCode'] = "Country Code";
$lang['Opensrs.whois.RegistrantPhone'] = "Phone";
$lang['Opensrs.whois.RegistrantOrganization'] = "Organization";
$lang['Opensrs.whois.RegistrantEmailAddress'] = "Email";

$lang['Opensrs.whois.TechFirstName'] = "First Name";
$lang['Opensrs.whois.TechLastName'] = "Last Name";
$lang['Opensrs.whois.TechAddress1'] = "Address 1";
$lang['Opensrs.whois.TechAddress2'] = "Address 2";
$lang['Opensrs.whois.TechAddress3'] = "Address 3";
$lang['Opensrs.whois.TechCity'] = "City";
$lang['Opensrs.whois.TechPostalCode'] = "Postal Code";
$lang['Opensrs.whois.TechCountry'] = "Country";
$lang['Opensrs.whois.TechCountryCode'] = "Country Code";
$lang['Opensrs.whois.TechPhone'] = "Phone";
$lang['Opensrs.whois.TechOrganization'] = "Organization";
$lang['Opensrs.whois.TechEmailAddress'] = "Email";

$lang['Opensrs.whois.AdminFirstName'] = "First Name";
$lang['Opensrs.whois.AdminLastName'] = "Last Name";
$lang['Opensrs.whois.AdminAddress1'] = "Address 1";
$lang['Opensrs.whois.AdminAddress2'] = "Address 2";
$lang['Opensrs.whois.AdminAddress2'] = "Address 3";
$lang['Opensrs.whois.AdminCity'] = "City";
$lang['Opensrs.whois.AdminPostalCode'] = "Postal Code";
$lang['Opensrs.whois.AdminCountry'] = "Country";
$lang['Opensrs.whois.AdminCountryCode'] = "Country Code";
$lang['Opensrs.whois.AdminPhone'] = "Phone";
$lang['Opensrs.whois.AdminOrganization'] = "Organization";
$lang['Opensrs.whois.AdminEmailAddress'] = "Email";

$lang['Opensrs.whois.AuxBillingFirstName'] = "First Name";
$lang['Opensrs.whois.AuxBillingLastName'] = "Last Name";
$lang['Opensrs.whois.AuxBillingAddress1'] = "Address 1";
$lang['Opensrs.whois.AuxBillingAddress2'] = "Address 2";
$lang['Opensrs.whois.AuxBillingCity'] = "City";
$lang['Opensrs.whois.AuxBillingPostalCode'] = "Postal Code";
$lang['Opensrs.whois.AuxBillingCountry'] = "Country";
$lang['Opensrs.whois.AuxBillingCountryCode'] = "Country Code";
$lang['Opensrs.whois.AuxBillingPhone'] = "Phone";
$lang['Opensrs.whois.AuxBillingOrganization'] = "Organization";
$lang['Opensrs.whois.AuxBillingEmailAddress'] = "Email";

// .IT domain fields
$lang['Opensrs.domain.dotitEntityType'] = "Legal Entity Type";
$lang['Opensrs.domain.dotitEntityType.1'] = "Italian and foreign natural persons";
$lang['Opensrs.domain.dotitEntityType.2'] = "Companies/one man companies";
$lang['Opensrs.domain.dotitEntityType.3'] = "Freelance workers/professionals";
$lang['Opensrs.domain.dotitEntityType.4'] = "Non-profit organizations";
$lang['Opensrs.domain.dotitEntityType.5'] = "Public organizations";
$lang['Opensrs.domain.dotitEntityType.6'] = "Other subjects";
$lang['Opensrs.domain.dotitEntityType.7'] = "Foreigners who match 2-6";

$lang['Opensrs.domain.dotitNationality'] = "Nationality";
$lang['Opensrs.domain.dotitRegCode'] = "VAT/TAX/Passport/ID Number";
$lang['Opensrs.domain.dotitHideWhois'] = "Hide data in public WHOIS";
$lang['Opensrs.domain.dotitHideWhois.no'] = "No";
$lang['Opensrs.domain.dotitHideWhois.yes'] = "Yes";

$lang['Opensrs.domain.dotitterms'] = "Accept Nic.it registry:
Declarations and assumptions of liability - The Registrant of the domain name in question, declares under their own responsibility that they are:
            a) in possession of the citizenship or resident in a country belonging to the European Union (in the case of registration for natural persons);
			b) established in a country belonging to the European Union (in the case of registration for other organizations);
			c) aware and accept that the registration and management of a domain name is subject to the Rules of assignment and management of domain names in ccTLD. it and Regulations for the resolution of disputes in the ccTLD.it and their subsequent amendments;
			d) entitled to the use and/or legal availability of the domain name applied for, and that they do not prejudice, with the request for registration, the rights of others;
			e) aware that for the inclusion of personal data in the Database of assigned domain names, and their possible dissemination and accessibility via the Internet, consent must be given explicitly by ticking the appropriate boxes in the information below. See The policy of the .it Registry in the Whois Database on the website of the Registry (http://www.nic.it);
			f) aware and agree that in the case of erroneous or false declarations in this request, the Registry shall immediately revoke the domain name, or proceed with other legal actions. In such case the revocation shall not in any way give rise to claims against the Registry;
			g) release the Registry from any liability resulting from the assignment and use of the domain name by the natural person that has made the request;
			h) accept Italian jurisdiction and laws of the Italian State.

Information and acquisition of consent for the processing of data for the registration of the domain name and for visibility on the Internet
Disclosure regarding the protection of personal data (D. Lgs. 30 June 2003, No. 196, Art. 13) To carry out activities to which the present disclosure relates:

			a) the holder of the treatment of data is the Italian National Research Council, through the Institute of Informatics and Telematics of the CNR,.it Registry (http://www.nic.it);
			b) the Registrar is responsible for processing the data and manages contractual relations with the Registrant, the identification data are contained in the contract between the said Registrar and the Registrant, and therefore known by the party concerned. A list of those responsible for processing data is available on the website of the Registry (http://www.nic.it). The Registrar is the holder of the treatment of data with regard to contractual relations directly with the Registrant, not included in this disclosure;
			c) the mandatory information is that information that is essential in order for the service requested to be provided.


		The Registrant's personal data are collected by the Registrar who manages contractual relations with the Registrant using this form, in order to register and manage the domain name in the Data Base of Assigned Names at the Institute of Information and Telematics of the CNR, .it Registry.
		done electronically, the IP address from which the connection originates on the Internet relating to filling in the online form by the Registrant will also be detected and stored along with the log of the relative transaction. This is done in order to identify the Registrant in connection with the personal information and statements given by the Registrant. Personal data, the above-mentioned IP address and the log of the transaction are mandatory information.
		The mandatory information collected will be treated for purposes of administrative and accounting management, protection of rights and other objectives and activities related to registration, management, dispute, transfer and cancellation of the domain name, and for compliance with requirements of the law, regulations or EU legislation and disclosed to third parties for ancillary or necessary activities to ensure the accomplishment of those objectives. The data will not be used nor disclosed to third parties for any marketing purposes. With the exception of the IP address, the data will also be communicated to third parties who purport to want to act to protect individual rights in relation to the Registrant in connection with the registration or use of the domain name. In accordance with the standards of the Internet Engineering Task Force - IETF (http://www.ietf.org) to ensure the accessibility of the domain name on the Internet, in order to maintain the balance for the system, and in consideration of the policy of the Registry of the ccTLD .it in order to avoid situations of anonymity and to allow the tracing of assignees, in the case of registration the following information will in any case will be visible on the Internet, through a Whois query: domain name, name and surname of the Registrant or the corporate name, status of the domain, Registrar and technical data (contactID, date of registration, expiry date, date  of last update and nameserver).
		For the purposes of this disclosure, the consent to treatment for the purposes of registration refers to all these activities taken as a whole. Giving consent for the purposes of registration is optional, but if no consent is given, it will not be possible to finalize the registration, assignment and management of the domain name.
		Upon a separate agreement, via a Whois query the following will also be visible via the Internet: domain name, address of residence or head office of the Registrant along with their telephone number, fax number and e-mail address.
		For the purposes of this disclosure, the consent for access and dissemination via the Internet is only for the latter activities and types of data.

		Giving consent for accessibility and dissemination via the Internet is optional. Not giving consent does not preclude the registration but only public visibility via Whois queries of these data, within the limits defined above.
		More information on how to query the Registry Database is available on the website of the Registry: www.nic.it.
		The interested party can exercise their rights under Art. 7 of the Code for the protection of personal data which include the right of access, rectification and deletion of data.
		These rights may be exercised by request to the Registrar who manages the contractual relationship with the Registrant and subordinate to the Institute of Informatics and Telematics of CNR, Via Giuseppe Moruzzi, 1, I-56124 Pisa, Italy.


Consent to the processing of personal data for registration
The interested party, after reading the above disclosure, gives consent to the processing of information required for registration, as defined in the above disclosure.

Giving consent is optional, but if no consent is given, it will not be possible to finalize the registration, assignment and management of the domain name.


Consent to the processing of personal data for diffusion and accessibility via the Internet
The interested party, after reading the above disclosure, gives consent to the dissemination and accessibility via the Internet, as defined in the disclosure above.
Giving consent is optional, but absence of consent does not allow the dissemination and accessibility of Internet data.


Explicit Acceptance of the following points
For explicit acceptance, the interested party declares that they:

			c) are aware and agree that the registration and management of a domain name is subject to the Rules of assignment and management of domain names in ccTLD.it and Regulations for the resolution of disputes in the ccTLD.it and their subsequent amendments;
			f) are aware and agree that in the case of erroneous or false declarations in this request, the Registry shall immediately revoke the domain name, or proceed with other legal actions. In such case the revocation shall not in any way give rise to claims against the Registry;
			g) release the Registry from any liability resulting from the assignment and use of the domain name by the natural person that has made the request;
			h) accept the Italian jurisdiction and laws of the Italian State.
";
$lang['Opensrs.domain.dotitterms.yes'] = "Yes, I Accept";


// .US domain fields
$lang['Opensrs.domain.RegistrantNexus'] = "Registrant Type";
$lang['Opensrs.domain.RegistrantNexus.c11'] = "US citizen";
$lang['Opensrs.domain.RegistrantNexus.c12'] = "Permanent resident of the US";
$lang['Opensrs.domain.RegistrantNexus.c21'] = "US entity or organization";
$lang['Opensrs.domain.RegistrantNexus.c31'] = "Foreign organization";
$lang['Opensrs.domain.RegistrantNexus.c32'] = "Foreign organization with an office in the US";
$lang['Opensrs.domain.RegistrantPurpose'] = "Purpose";
$lang['Opensrs.domain.RegistrantPurpose.p1'] = "Business";
$lang['Opensrs.domain.RegistrantPurpose.p2'] = "Non-profit";
$lang['Opensrs.domain.RegistrantPurpose.p3'] = "Personal";
$lang['Opensrs.domain.RegistrantPurpose.p4'] = "Educational";
$lang['Opensrs.domain.RegistrantPurpose.p5'] = "Governmental";

// .EU domain fields
$lang['Opensrs.domain.EUAgreeWhoisPolicy'] = "Whois Policy";
$lang['Opensrs.domain.EUAgreeWhoisPolicy.yes'] = "I hereby agree that the Registry is entitled to transfer the data contained in this application to third parties(i) if ordered to do so by a public authority, carrying out its legitimate tasks; and (ii) upon demand of an ADR Provider as mentioned in section 16 of the Terms and Conditions which are published at www.eurid.eu; and (iii) as provided in Section 2 (WHOIS look-up facility) of the .eu Domain Name WHOIS Policy which is published at www.eurid.eu.";
$lang['Opensrs.domain.EUAgreeDeletePolicy'] = "Deleteion Rules";
$lang['Opensrs.domain.EUAgreeDeletePolicy.yes'] = "I agree and acknowledge to the special renewal and expiration terms set forth below for this domain name, including those terms set forth in the Registration Agreement. I understand that unless I have set this domain for autorenewal, this domain name must be explicitly renewed by the expiration date or the 20th of the month of expiration, whichever is sooner. (e.g. If the name expires on Sept 4th, 2008, then a manual renewal must be received by Sept 4th, 2008. If name expires on Sep 27th, 2008, the renewal request must be received prior to Sep 20th, 2008). If the name is not manually renewed or previously set to autorenew, a delete request will be issued by Opensrs. When a delete request is issued, the name will remain fully functional in my account until expiration, but will no longer be renewable nor will I be able to make any modifications to the name. These terms are subject to change.";

// .CA domain fields
$lang['Opensrs.domain.CIRALegalType'] = "Legal Type";
$lang['Opensrs.domain.RegistrantPurpose.cco'] = "Corporation";
$lang['Opensrs.domain.RegistrantPurpose.cct'] = "Canadian citizen";
$lang['Opensrs.domain.RegistrantPurpose.res'] = "Canadian resident";
$lang['Opensrs.domain.RegistrantPurpose.gov'] = "Government entity";
$lang['Opensrs.domain.RegistrantPurpose.edu'] = "Educational";
$lang['Opensrs.domain.RegistrantPurpose.ass'] = "Unincorporated Association";
$lang['Opensrs.domain.RegistrantPurpose.hop'] = "Hospital";
$lang['Opensrs.domain.RegistrantPurpose.prt'] = "Partnership";
$lang['Opensrs.domain.RegistrantPurpose.tdm'] = "Trade-mark";
$lang['Opensrs.domain.RegistrantPurpose.trd'] = "Trade Union";
$lang['Opensrs.domain.RegistrantPurpose.plt'] = "Political Party";
$lang['Opensrs.domain.RegistrantPurpose.lam'] = "Libraries, Archives and Museums";
$lang['Opensrs.domain.RegistrantPurpose.trs'] = "Trust";
$lang['Opensrs.domain.RegistrantPurpose.abo'] = "Aboriginal Peoples";
$lang['Opensrs.domain.RegistrantPurpose.inb'] = "Indian Band";
$lang['Opensrs.domain.RegistrantPurpose.lgr'] = "Legal Representative";
$lang['Opensrs.domain.RegistrantPurpose.omk'] = "Official Mark";
$lang['Opensrs.domain.RegistrantPurpose.maj'] = "The Queen";
$lang['Opensrs.domain.CIRAWhoisDisplay'] = "Whois";
$lang['Opensrs.domain.CIRAWhoisDisplay.full'] = "Make Public";
$lang['Opensrs.domain.CIRAWhoisDisplay.private'] = "Keep Private";
$lang['Opensrs.domain.CIRALanguage'] = "Preferred language for communication";
$lang['Opensrs.domain.CIRALanguage.en'] = "English";
$lang['Opensrs.domain.CIRALanguage.fr'] = "French";

// .UK domain fields
$lang['Opensrs.domain.UKLegalType'] = "Legal Type";
$lang['Opensrs.domain.UKLegalType.ind'] = "UK individual";
$lang['Opensrs.domain.UKLegalType.find'] = "Non-UK individual";
$lang['Opensrs.domain.UKLegalType.ltd'] = "UK Limited Company";
$lang['Opensrs.domain.UKLegalType.plc'] = "UK Public Limited Company";
$lang['Opensrs.domain.UKLegalType.ptnr'] = "UK Partnership";
$lang['Opensrs.domain.UKLegalType.llp'] = "UK Limited Liability Partnership";
$lang['Opensrs.domain.UKLegalType.ip'] = "UK Industrial/Provident Registered Company";
$lang['Opensrs.domain.UKLegalType.stra'] = "UK Sole Trader";
$lang['Opensrs.domain.UKLegalType.sch'] = "UK School";
$lang['Opensrs.domain.UKLegalType.rchar'] = "UK Registered Charity";
$lang['Opensrs.domain.UKLegalType.gov'] = "UK Government Body";
$lang['Opensrs.domain.UKLegalType.other'] = "UK Entity (other)";
$lang['Opensrs.domain.UKLegalType.crc'] = "UK Corporation by Royal Charter";
$lang['Opensrs.domain.UKLegalType.fcorp'] = "Foreign Organization";
$lang['Opensrs.domain.UKLegalType.stat'] = "UK Statutory Body FIND";
$lang['Opensrs.domain.UKLegalType.fother'] = "Other Foreign Organizations";
$lang['Opensrs.domain.UKCompanyID'] = "Company ID Number";
$lang['Opensrs.domain.UKRegisteredfor'] = "Registrant Name";

// .ASIA domain fields
$lang['Opensrs.domain.ASIALegalEntityType'] = "Legal Type";
$lang['Opensrs.domain.ASIALegalEntityType.corporation'] = "Corporations or Companies";
$lang['Opensrs.domain.ASIALegalEntityType.cooperative'] = "Cooperatives";
$lang['Opensrs.domain.ASIALegalEntityType.partnership'] = "Partnerships or Collectives";
$lang['Opensrs.domain.ASIALegalEntityType.government'] = "Government Bodies";
$lang['Opensrs.domain.ASIALegalEntityType.politicalParty'] = "Political parties or Trade Unions";
$lang['Opensrs.domain.ASIALegalEntityType.society'] = "Trusts, Estates, Associations or Societies";
$lang['Opensrs.domain.ASIALegalEntityType.institution'] = "Institutions";
$lang['Opensrs.domain.ASIALegalEntityType.naturalPerson'] = "Natural Persons";
$lang['Opensrs.domain.ASIAIdentForm'] = "Form of Identity";
$lang['Opensrs.domain.ASIAIdentForm.certificate'] = "Certificate of Incorporation";
$lang['Opensrs.domain.ASIAIdentForm.legislation'] = "Charter";
$lang['Opensrs.domain.ASIAIdentForm.societyRegistry'] = "Societies Registry";
$lang['Opensrs.domain.ASIAIdentForm.politicalPartyRegistry'] = "Political Party Registry";
$lang['Opensrs.domain.ASIAIdentForm.passport'] = "Passport/ Citizenship ID";
$lang['Opensrs.domain.ASIAIdentNumber'] = "Identity Number";
?>