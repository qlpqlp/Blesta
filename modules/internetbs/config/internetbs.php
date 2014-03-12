<?php
// All available TLDs
Configure::set("Internetbs.tlds", array(
	// Second-Level
	".br.com",
	".cn.com",
	".de.com",
	".eu.com",
	".gr.com",
	".hu.com",
	".no.com",
	".qc.com",
	".ru.com",
	".se.com",
	".uk.com",
	".us.com",
	".us.org",
	".uy.com",
	".za.com",
	".uk.net",
	".se.net",
	".co.uk",
	// Generic
	".biz",
	".com",
	".info",
	".name",
	".net",
	".org",
	// Speciality
	".asia",
	".co",
	".me",
	".mobi",
	".pw",
	".tel",
	".tv",
	// Country Code
	".cc",
	".de",
	".eu",
	".fr",
	".in",
	".it",
	".la",
	".nl",
	".us",
));

// Transfer fields
Configure::set("Internetbs.transfer_fields", array(
	'domain' => array(
		'label' => Language::_("Internetbs.transfer.domain", true),
		'type' => "text"
	),
	'transfer_key' => array(
		'label' => Language::_("Internetbs.transfer.transfer_key", true),
		'type' => "text"
	)
));

// Domain fields
Configure::set("Internetbs.domain_fields", array(
	'domain' => array(
		'label' => Language::_("Internetbs.domain.domain", true),
		'type' => "text"
	),
));

// Nameserver fields
Configure::set("Internetbs.nameserver_fields", array(
	'ns1' => array(
		'label' => Language::_("Internetbs.nameserver.ns1", true),
		'type' => "text"
	),
	'ns2' => array(
		'label' => Language::_("Internetbs.nameserver.ns2", true),
		'type' => "text"
	),
	'ns3' => array(
		'label' => Language::_("Internetbs.nameserver.ns3", true),
		'type' => "text"
	),
	'ns4' => array(
		'label' => Language::_("Internetbs.nameserver.ns4", true),
		'type' => "text"
	),
	'ns5' => array(
		'label' => Language::_("Internetbs.nameserver.ns5", true),
		'type' => "text"
	)
));

// Whois sections
Configure::set("Internetbs.whois_sections", array(
	"registrant",
    "technical",
	"billing",
	"admin"
));

// Whois fields

Configure::set("Internetbs.whois_fields", array(
	'Registrant_firstname' => array(
		'label' => Language::_("Internetbs.whois.RegistrantFirstName", true),
		'type' => "text"
	),
	'Registrant_lastname' => array(
		'label' => Language::_("Internetbs.whois.RegistrantLastName", true),
		'type' => "text"
	),
	'Registrant_email' => array(
		'label' => Language::_("Internetbs.whois.RegistrantEmailAddress", true),
		'type' => "text"
	),
	'Registrant_phonenumber' => array(
		'label' => Language::_("Internetbs.whois.RegistrantPhone", true),
		'type' => "text"
	),
	'Registrant_organization' => array(
		'label' => Language::_("Internetbs.whois.RegistrantOrganization", true),
		'type' => "text"
	),
	'Registrant_city' => array(
		'label' => Language::_("Internetbs.whois.RegistrantCity", true),
		'type' => "text"
	),
	'Registrant_street' => array(
		'label' => Language::_("Internetbs.whois.RegistrantAddress1", true),
		'type' => "text"
	),
	'Registrant_street2' => array(
		'label' => Language::_("Internetbs.whois.RegistrantAddress2", true),
		'type' => "text"
	),
	'Registrant_street3' => array(
		'label' => Language::_("Internetbs.whois.RegistrantAddress3", true),
		'type' => "text"
	),
	'Registrant_postalcode' => array(
		'label' => Language::_("Internetbs.whois.RegistrantPostalCode", true),
		'type' => "text"
	),
	'Registrant_countrycode' => array(
		'label' => Language::_("Internetbs.whois.RegistrantCountryCode", true),
		'type' => "text"
	),
	'Registrant_country' => array(
		'label' => Language::_("Internetbs.whois.RegistrantCountry", true),
		'type' => "text"
	),

//tecnical

	'Technical_firstname' => array(
		'label' => Language::_("Internetbs.whois.TechFirstName", true),
		'type' => "text"
	),
	'Technical_lastname' => array(
		'label' => Language::_("Internetbs.whois.TechLastName", true),
		'type' => "text"
	),
	'Technical_email' => array(
		'label' => Language::_("Internetbs.whois.TechEmailAddress", true),
		'type' => "text"
	),
	'Technical_phonenumber' => array(
		'label' => Language::_("Internetbs.whois.TechPhone", true),
		'type' => "text"
	),
	'Technical_organization' => array(
		'label' => Language::_("Internetbs.whois.TechOrganization", true),
		'type' => "text"
	),
	'Technical_city' => array(
		'label' => Language::_("Internetbs.whois.TechCity", true),
		'type' => "text"
	),
	'Technical_street' => array(
		'label' => Language::_("Internetbs.whois.TechAddress1", true),
		'type' => "text"
	),
	'Technical_street2' => array(
		'label' => Language::_("Internetbs.whois.TechAddress3", true),
		'type' => "text"
	),
	'Technical_street3' => array(
		'label' => Language::_("Internetbs.whois.TechAddress2", true),
		'type' => "text"
	),
	'Technical_postalcode' => array(
		'label' => Language::_("Internetbs.whois.TechPostalCode", true),
		'type' => "text"
	),
	'Technical_countrycode' => array(
		'label' => Language::_("Internetbs.whois.TechCountryCode", true),
		'type' => "text"
	),
	'Technical_country' => array(
		'label' => Language::_("Internetbs.whois.TechCountry", true),
		'type' => "text"
	),
//billing
	'Billing_firstname' => array(
		'label' => Language::_("Internetbs.whois.AuxBillingFirstName", true),
		'type' => "text"
	),
	'Billing_lastname' => array(
		'label' => Language::_("Internetbs.whois.AuxBillingLastName", true),
		'type' => "text"
	),
	'Billing_email' => array(
		'label' => Language::_("Internetbs.whois.AuxBillingEmailAddress", true),
		'type' => "text"
	),
	'Billing_phonenumber' => array(
		'label' => Language::_("Internetbs.whois.AuxBillingPhone", true),
		'type' => "text"
	),
	'Billing_organization' => array(
		'label' => Language::_("Internetbs.whois.AuxBillingOrganization", true),
		'type' => "text"
	),
	'Billing_city' => array(
		'label' => Language::_("Internetbs.whois.AuxBillingCity", true),
		'type' => "text"
	),
	'Billing_street' => array(
		'label' => Language::_("Internetbs.whois.AuxBillingAddress1", true),
		'type' => "text"
	),
	'Billing_street2' => array(
		'label' => Language::_("Internetbs.whois.AuxBillingAddress2", true),
		'type' => "text"
	),
	'Billing_street3' => array(
		'label' => Language::_("Internetbs.whois.AuxBillingAddress3", true),
		'type' => "text"
	),
	'Billing_postalcode' => array(
		'label' => Language::_("Internetbs.whois.AuxBillingPostalCode", true),
		'type' => "text"
	),
	'Billing_countrycode' => array(
		'label' => Language::_("Internetbs.whois.AuxBillingCountryCode", true),
		'type' => "text"
	),
	'Billing_country' => array(
		'label' => Language::_("Internetbs.whois.AuxBillingCountry", true),
		'type' => "text"
	),

// admin
	'Admin_firstname' => array(
		'label' => Language::_("Internetbs.whois.AdminFirstName", true),
		'type' => "text"
	),
	'Admin_lastname' => array(
		'label' => Language::_("Internetbs.whois.AdminLastName", true),
		'type' => "text"
	),
	'Admin_email' => array(
		'label' => Language::_("Internetbs.whois.AdminEmailAddress", true),
		'type' => "text"
	),
	'Admin_phonenumber' => array(
		'label' => Language::_("Internetbs.whois.AdminPhone", true),
		'type' => "text"
	),
	'Admin_organization' => array(
		'label' => Language::_("Internetbs.whois.AdminOrganization", true),
		'type' => "text"
	),
	'Admin_city' => array(
		'label' => Language::_("Internetbs.whois.AdminCity", true),
		'type' => "text"
	),
	'Admin_street' => array(
		'label' => Language::_("Internetbs.whois.AdminAddress1", true),
		'type' => "text"
	),
	'Admin_street2' => array(
		'label' => Language::_("Internetbs.whois.AdminAddress2", true),
		'type' => "text"
	),
	'Admin_street3' => array(
		'label' => Language::_("Internetbs.whois.AdminAddress2", true),
		'type' => "text"
	),
	'Admin_postalcode' => array(
		'label' => Language::_("Internetbs.whois.AdminPostalCode", true),
		'type' => "text"
	),
	'Admin_countrycode' => array(
		'label' => Language::_("Internetbs.whois.AdminCountryCode", true),
		'type' => "text"
	),
	'Admin_country' => array(
		'label' => Language::_("Internetbs.whois.AdminCountry", true),
		'type' => "text"
	)
));

// .US
Configure::set("Internetbs.domain_fields.us", array(
	'us_nexus' => array(
		'label' => Language::_("Internetbs.domain.RegistrantNexus", true),
		'type' => "select",
		'options' => array(
			'C11' => Language::_("Internetbs.domain.RegistrantNexus.c11", true),
			'C12' => Language::_("Internetbs.domain.RegistrantNexus.c12", true),
			'C21' => Language::_("Internetbs.domain.RegistrantNexus.c21", true),
			'C31' => Language::_("Internetbs.domain.RegistrantNexus.c31", true),
			'C32' => Language::_("Internetbs.domain.RegistrantNexus.c32", true)
		)
	),
	'us_purpose' => array(
		'label' => Language::_("Internetbs.domain.RegistrantPurpose", true),
		'type' => "select",
		'options' => array(
			'P1' => Language::_("Internetbs.domain.RegistrantPurpose.p1", true),
			'P2' => Language::_("Internetbs.domain.RegistrantPurpose.p2", true),
			'P3' => Language::_("Internetbs.domain.RegistrantPurpose.p3", true),
			'P4' => Language::_("Internetbs.domain.RegistrantPurpose.p4", true),
			'P5' => Language::_("Internetbs.domain.RegistrantPurpose.p5", true)
		)
	)
));

// .EU
Configure::set("Internetbs.domain_fields.eu", array(
	'eu_whoispolicy' => array(
		'label' => Language::_("Internetbs.domain.EUAgreeWhoisPolicy", true),
		'type' => "checkbox",
		'options' => array(
			'I AGREE' => Language::_("Internetbs.domain.EUAgreeWhoisPolicy.yes", true)
		)
	)
));

// .CA
Configure::set("Internetbs.domain_fields.ca", array(
	'cira_legal_type' => array(
		'label' => Language::_("Internetbs.domain.CIRALegalType", true),
		'type' => "select",
		'options' => array(
			'CCO' => Language::_("Internetbs.domain.RegistrantPurpose.cco", true),
			'CCT' => Language::_("Internetbs.domain.RegistrantPurpose.cct", true),
			'RES' => Language::_("Internetbs.domain.RegistrantPurpose.res", true),
			'GOV' => Language::_("Internetbs.domain.RegistrantPurpose.gov", true),
			'EDU' => Language::_("Internetbs.domain.RegistrantPurpose.edu", true),
			'ASS' => Language::_("Internetbs.domain.RegistrantPurpose.ass", true),
			'HOP' => Language::_("Internetbs.domain.RegistrantPurpose.hop", true),
			'PRT' => Language::_("Internetbs.domain.RegistrantPurpose.prt", true),
			'TDM' => Language::_("Internetbs.domain.RegistrantPurpose.tdm", true),
			'TRD' => Language::_("Internetbs.domain.RegistrantPurpose.trd", true),
			'PLT' => Language::_("Internetbs.domain.RegistrantPurpose.plt", true),
			'LAM' => Language::_("Internetbs.domain.RegistrantPurpose.lam", true),
			'TRS' => Language::_("Internetbs.domain.RegistrantPurpose.trs", true),
			'ABO' => Language::_("Internetbs.domain.RegistrantPurpose.abo", true),
			'INB' => Language::_("Internetbs.domain.RegistrantPurpose.inb", true),
			'LGR' => Language::_("Internetbs.domain.RegistrantPurpose.lgr", true),
			'OMK' => Language::_("Internetbs.domain.RegistrantPurpose.omk", true),
			'MAJ' => Language::_("Internetbs.domain.RegistrantPurpose.maj", true)
		)
	),
	'cira_whois_display' => array(
		'label' => Language::_("Internetbs.domain.CIRAWhoisDisplay", true),
		'type' => "select",
		'options' => array(
			'FULL' => Language::_("Internetbs.domain.CIRAWhoisDisplay.full", true),
			'PRIVATE' => Language::_("Internetbs.domain.CIRAWhoisDisplay.private", true),
		)
	),
	'cira_language' => array(
		'label' => Language::_("Internetbs.domain.CIRALanguage", true),
		'type' => "select",
		'options' => array(
			'en' => Language::_("Internetbs.domain.CIRALanguage.en", true),
			'fr' => Language::_("Internetbs.domain.CIRALanguage.fr", true),
		)
	),
	'cira_agreement_version' => array(
		'type' => "hidden",
		'options' => "2.0"
	),
	'cira_agreement_value' => array(
		'type' => "hidden",
		'options' => "Y"
	)
));

// .UK
Configure::set("Internetbs.domain_fields.uk", array(
	'uk_legal_type' => array(
		'label' => Language::_("Internetbs.domain.UKLegalType", true),
		'type' => "select",
		'options' => array(
			'IND' => Language::_("Internetbs.domain.UKLegalType.ind", true),
			'FIND' => Language::_("Internetbs.domain.UKLegalType.find", true),
			'LTD' => Language::_("Internetbs.domain.UKLegalType.ltd", true),
			'PLC' => Language::_("Internetbs.domain.UKLegalType.plc", true),
			'PTNR' => Language::_("Internetbs.domain.UKLegalType.ptnr", true),
			'LLP' => Language::_("Internetbs.domain.UKLegalType.llp", true),
			'IP' => Language::_("Internetbs.domain.UKLegalType.ip", true),
			'STRA' => Language::_("Internetbs.domain.UKLegalType.stra", true),
			'SCH' => Language::_("Internetbs.domain.UKLegalType.sch", true),
			'RCHAR' => Language::_("Internetbs.domain.UKLegalType.rchar", true),
			'GOV' => Language::_("Internetbs.domain.UKLegalType.gov", true),
			'OTHER' => Language::_("Internetbs.domain.UKLegalType.other", true),
			'CRC' => Language::_("Internetbs.domain.UKLegalType.crc", true),
			'FCORP' => Language::_("Internetbs.domain.UKLegalType.fcorp", true),
			'STAT' => Language::_("Internetbs.domain.UKLegalType.stat", true),
			'FOTHER' => Language::_("Internetbs.domain.UKLegalType.fother", true)
		)
	),
	'uk_reg_co_no' => array(
		'label' => Language::_("Internetbs.domain.UKCompanyID", true),
		'type' => "text"
	),
	'registered_for' => array(
		'label' => Language::_("Internetbs.domain.UKRegisteredfor", true),
		'type' => "text"
	)
));

// .ASIA
Configure::set("Internetbs.domain_fields.asia", array(
	'asia_cclocality' => array(
		'type' => "hidden",
		'options' => null
	),
	'asia_legalentitytype' => array(
		'label' => Language::_("Internetbs.domain.ASIALegalEntityType", true),
		'type' => "select",
		'options' => array(
			'corporation' => Language::_("Internetbs.domain.ASIALegalEntityType.corporation", true),
			'cooperative' => Language::_("Internetbs.domain.ASIALegalEntityType.cooperative", true),
			'partnership' => Language::_("Internetbs.domain.ASIALegalEntityType.partnership", true),
			'government' => Language::_("Internetbs.domain.ASIALegalEntityType.government", true),
			'politicalParty' => Language::_("Internetbs.domain.ASIALegalEntityType.politicalParty", true),
			'society' => Language::_("Internetbs.domain.ASIALegalEntityType.society", true),
			'institution' => Language::_("Internetbs.domain.ASIALegalEntityType.institution", true),
			'naturalPerson' => Language::_("Internetbs.domain.ASIALegalEntityType.naturalPerson", true)
		)
	),
	'asia_identform' => array(
		'label' => Language::_("Internetbs.domain.ASIAIdentForm", true),
		'type' => "select",
		'options' => array(
			'certificate' => Language::_("Internetbs.domain.ASIAIdentForm.certificate", true),
			'legislation' => Language::_("Internetbs.domain.ASIAIdentForm.legislation", true),
			'societyRegistry' => Language::_("Internetbs.domain.ASIAIdentForm.societyRegistry", true),
			'politicalPartyRegistry' => Language::_("Internetbs.domain.ASIAIdentForm.politicalPartyRegistry", true),
			'passport' => Language::_("Internetbs.domain.ASIAIdentForm.passport", true)
		)
	),
	'asia_ident:number' => array(
		'label' => Language::_("Internetbs.domain.ASIAIdentNumber", true),
		'type' => "text"
	)
));
?>