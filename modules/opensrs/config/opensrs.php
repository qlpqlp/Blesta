<?php
// All available TLDs
Configure::set("Opensrs.tlds", array(
".academy",
".accountants",
".actor",
".aero",
".agency",
".asia",
".associates",
".audio",
".bargains",
".berlin",
".bike",
".biz",
".blackfriday",
".boutique",
".builders",
".business",
".cab",
".camera",
".camp",
".capital",
".cards",
".care",
".careers",
".cash",
".catering",
".center",
".cheap",
".christmas",
".church",
".city",
".claims",
".cleaning",
".click",
".clinic",
".clothing",
".codes",
".coffee",
".cologne",
".com",
".community",
".company",
".computer",
".condos",
".construction",
".consulting",
".contractors",
".cool",
".coop",
".credit",
".creditcard",
".cruises",
".dance",
".dating",
".deals",
".democrat",
".dental",
".desi",
".diamonds",
".diet",
".digital",
".direct",
".directory",
".discount",
".domains",
".education",
".email",
".engineering",
".enterprises",
".equipment",
".estate",
".events",
".example",
".exchange",
".expert",
".exposed",
".fail",
".farm",
".finance",
".financial",
".fish",
".fitness",
".flights",
".florist",
".foundation",
".fund",
".furniture",
".futbol",
".gallery",
".gift",
".gifts",
".glass",
".graphics",
".gratis",
".gripe",
".guide",
".guitars",
".guru",
".hamburg",
".haus",
".healthcare",
".help",
".hiphop",
".holdings",
".holiday",
".host",
".hosting",
".house",
".immo",
".immobilien",
".industries",
".info",
".institute",
".insure",
".international",
".investments",
".jobs",
".juegos",
".kaufen",
".kitchen",
".koeln",
".land",
".lease",
".life",
".lighting",
".limited",
".limo",
".link",
".loans",
".maison",
".management",
".marketing",
".media",
".mobi",
".moda",
".nagoya",
".name",
".net",
".network",
".ninja",
".nyc",
".org",
".partners",
".parts",
".photo",
".photography",
".photos",
".pics",
".pictures",
".pizza",
".place",
".plumbing",
".press",
".pro",
".productions",
".properties",
".property",
".pub",
".quebec",
".recipes",
".reisen",
".rentals",
".repair",
".report",
".republican",
".restaurant",
".reviews",
".rocks",
".ruhr",
".sarl",
".schule",
".scot",
".services",
".sexy",
".shoes",
".singles",
".social",
".solar",
".solutions",
".supplies",
".supply",
".support",
".surgery",
".systems",
".tattoo",
".tax",
".technology",
".tel",
".tienda",
".tips",
".today",
".tokyo",
".tools",
".town",
".toys",
".training",
".travel",
".university",
".vacations",
".ventures",
".vet",
".viajes",
".villas",
".vision",
".voyage",
".watch",
".website",
".wien",
".works",
".wtf",
".xxx",
".yokohama",
".zone",
// Country TLD's
".ac",
".ae",
".af",
".ag",
".am",
".as",
".asn.au",
".at",
".be",
".biz.ki",
".biz.pl",
".bz",
".ca",
".cc",
".cd",
".ch",
".cl",
".cm",
".cn",
".co",
".co.ag",
".co.cm",
".co.gg",
".co.gl",
".co.gy",
".co.hu",
".co.il",
".co.im",
".co.in",
".co.je",
".co.kr",
".co.lc",
".co.ma",
".co.nz",
".co.uk",
".co.ve",
".co.za",
".com.af",
".com.ag",
".com.ai",
".com.ar",
".com.au",
".com.br",
".com.cm",
".com.cn",
".com.co",
".com.ec",
".com.es",
".com.gl",
".com.gr",
".com.gy",
".com.hn",
".com.hr",
".com.ht",
".com.im",
".com.ki",
".com.lc",
".com.lv",
".com.ly",
".com.mx",
".com.my",
".com.pe",
".com.pl",
".com.pr",
".com.pt",
".com.ro",
".com.ru",
".com.sb",
".com.sc",
".com.so",
".com.tw",
".com.ua",
".com.uy",
".com.vc",
".com.ve",
".cx",
".cz",
".de",
".dk",
".ec",
".edu.gr",
".edu.pl",
".es",
".eu",
".fi",
".fin.ec",
".firm.in",
".fm",
".fr",
".gd",
".gen.in",
".gg",
".gl",
".gr",
".gs",
".gy",
".hk",
".hm",
".hn",
".ht",
".hu",
".id.au",
".idv.tw",
".im",
".in",
".ind.in",
".info.ec",
".info.ht",
".info.ki",
".info.pl",
".io",
".is",
".it",
".je",
".jp",
".kg",
".kr",
".l.lc",
".la",
".lc",
".li",
".lt",
".ltd.uk",
".lu",
".lv",
".ly",
".ma",
".md",
".me",
".me.uk",
".med.ec",
".mn",
".mobi.ki",
".ms",
".mu",
".mx",
".my",
".net.af",
".net.ag",
".net.ai",
".net.au",
".net.cm",
".net.cn",
".net.co",
".net.ec",
".net.gg",
".net.gl",
".net.gr",
".net.gy",
".net.hn",
".net.ht",
".net.im",
".net.in",
".net.ki",
".net.lc",
".net.my",
".net.nz",
".net.pe",
".net.pl",
".net.sb",
".net.sc",
".net.so",
".net.uk",
".net.vc",
".nl",
".no",
".nom.ag",
".nom.co",
".nom.es",
".nom.pl",
".nu",
".off.ai",
".org.af",
".org.ag",
".org.ai",
".org.au",
".org.cn",
".org.es",
".org.gg",
".org.gl",
".org.gr",
".org.hn",
".org.ht",
".org.im",
".org.in",
".org.lc",
".org.my",
".org.nz",
".org.pl",
".org.sb",
".org.sc",
".org.so",
".org.tw",
".org.uk",
".org.vc",
".p.lc",
".pe",
".pl",
".plc.uk",
".pm",
".pro.ec",
".pt",
".pw",
".qa",
".re",
".ro",
".ru",
".sc",
".se",
".sg",
".sh",
".shop.pl",
".si",
".so",
".sr",
".st",
".sx",
".tc",
".tel.ki",
".tf",
".tk",
".tl",
".tm",
".to",
".tv",
".tw",
".uk",
".us",
".vc",
".vg",
".waw.pl",
".wf",
".ws",
".yt",
));

// Transfer fields
Configure::set("Opensrs.transfer_fields", array(
	'domain' => array(
		'label' => Language::_("Opensrs.transfer.domain", true),
		'type' => "text"
	),
	'transfer_key' => array(
		'label' => Language::_("Opensrs.transfer.transfer_key", true),
		'type' => "text"
	)
));

// Domain fields
Configure::set("Opensrs.domain_fields", array(
	'domain' => array(
		'label' => Language::_("Opensrs.domain.domain", true),
		'type' => "text"
	),
));

// Nameserver fields
Configure::set("Opensrs.nameserver_fields", array(
	'ns1' => array(
		'label' => Language::_("Opensrs.nameserver.ns1", true),
		'type' => "text"
	),
	'ns2' => array(
		'label' => Language::_("Opensrs.nameserver.ns2", true),
		'type' => "text"
	),
	'ns3' => array(
		'label' => Language::_("Opensrs.nameserver.ns3", true),
		'type' => "text"
	),
	'ns4' => array(
		'label' => Language::_("Opensrs.nameserver.ns4", true),
		'type' => "text"
	),
	'ns5' => array(
		'label' => Language::_("Opensrs.nameserver.ns5", true),
		'type' => "text"
	)
));

// Whois sections
Configure::set("Opensrs.whois_sections", array(
	"registrant",
    "technical",
	"billing",
	"admin"
));

// Whois fields

Configure::set("Opensrs.whois_fields", array(
	'Registrant_firstname' => array(
		'label' => Language::_("Opensrs.whois.RegistrantFirstName", true),
		'type' => "text"
	),
	'Registrant_lastname' => array(
		'label' => Language::_("Opensrs.whois.RegistrantLastName", true),
		'type' => "text"
	),
	'Registrant_email' => array(
		'label' => Language::_("Opensrs.whois.RegistrantEmailAddress", true),
		'type' => "text"
	),
	'Registrant_phonenumber' => array(
		'label' => Language::_("Opensrs.whois.RegistrantPhone", true),
		'type' => "text"
	),
	'Registrant_organization' => array(
		'label' => Language::_("Opensrs.whois.RegistrantOrganization", true),
		'type' => "text"
	),
	'Registrant_city' => array(
		'label' => Language::_("Opensrs.whois.RegistrantCity", true),
		'type' => "text"
	),
	'Registrant_street' => array(
		'label' => Language::_("Opensrs.whois.RegistrantAddress1", true),
		'type' => "text"
	),
	'Registrant_street2' => array(
		'label' => Language::_("Opensrs.whois.RegistrantAddress2", true),
		'type' => "text"
	),
	'Registrant_street3' => array(
		'label' => Language::_("Opensrs.whois.RegistrantAddress3", true),
		'type' => "text"
	),
	'Registrant_postalcode' => array(
		'label' => Language::_("Opensrs.whois.RegistrantPostalCode", true),
		'type' => "text"
	),
	'Registrant_countrycode' => array(
		'label' => Language::_("Opensrs.whois.RegistrantCountryCode", true),
		'type' => "text"
	),
	'Registrant_country' => array(
		'label' => Language::_("Opensrs.whois.RegistrantCountry", true),
		'type' => "text"
	),

//tecnical

	'Technical_firstname' => array(
		'label' => Language::_("Opensrs.whois.TechFirstName", true),
		'type' => "text"
	),
	'Technical_lastname' => array(
		'label' => Language::_("Opensrs.whois.TechLastName", true),
		'type' => "text"
	),
	'Technical_email' => array(
		'label' => Language::_("Opensrs.whois.TechEmailAddress", true),
		'type' => "text"
	),
	'Technical_phonenumber' => array(
		'label' => Language::_("Opensrs.whois.TechPhone", true),
		'type' => "text"
	),
	'Technical_organization' => array(
		'label' => Language::_("Opensrs.whois.TechOrganization", true),
		'type' => "text"
	),
	'Technical_city' => array(
		'label' => Language::_("Opensrs.whois.TechCity", true),
		'type' => "text"
	),
	'Technical_street' => array(
		'label' => Language::_("Opensrs.whois.TechAddress1", true),
		'type' => "text"
	),
	'Technical_street2' => array(
		'label' => Language::_("Opensrs.whois.TechAddress3", true),
		'type' => "text"
	),
	'Technical_street3' => array(
		'label' => Language::_("Opensrs.whois.TechAddress2", true),
		'type' => "text"
	),
	'Technical_postalcode' => array(
		'label' => Language::_("Opensrs.whois.TechPostalCode", true),
		'type' => "text"
	),
	'Technical_countrycode' => array(
		'label' => Language::_("Opensrs.whois.TechCountryCode", true),
		'type' => "text"
	),
	'Technical_country' => array(
		'label' => Language::_("Opensrs.whois.TechCountry", true),
		'type' => "text"
	),
//billing
	'Billing_firstname' => array(
		'label' => Language::_("Opensrs.whois.AuxBillingFirstName", true),
		'type' => "text"
	),
	'Billing_lastname' => array(
		'label' => Language::_("Opensrs.whois.AuxBillingLastName", true),
		'type' => "text"
	),
	'Billing_email' => array(
		'label' => Language::_("Opensrs.whois.AuxBillingEmailAddress", true),
		'type' => "text"
	),
	'Billing_phonenumber' => array(
		'label' => Language::_("Opensrs.whois.AuxBillingPhone", true),
		'type' => "text"
	),
	'Billing_organization' => array(
		'label' => Language::_("Opensrs.whois.AuxBillingOrganization", true),
		'type' => "text"
	),
	'Billing_city' => array(
		'label' => Language::_("Opensrs.whois.AuxBillingCity", true),
		'type' => "text"
	),
	'Billing_street' => array(
		'label' => Language::_("Opensrs.whois.AuxBillingAddress1", true),
		'type' => "text"
	),
	'Billing_street2' => array(
		'label' => Language::_("Opensrs.whois.AuxBillingAddress2", true),
		'type' => "text"
	),
	'Billing_street3' => array(
		'label' => Language::_("Opensrs.whois.AuxBillingAddress3", true),
		'type' => "text"
	),
	'Billing_postalcode' => array(
		'label' => Language::_("Opensrs.whois.AuxBillingPostalCode", true),
		'type' => "text"
	),
	'Billing_countrycode' => array(
		'label' => Language::_("Opensrs.whois.AuxBillingCountryCode", true),
		'type' => "text"
	),
	'Billing_country' => array(
		'label' => Language::_("Opensrs.whois.AuxBillingCountry", true),
		'type' => "text"
	),

// admin
	'Admin_firstname' => array(
		'label' => Language::_("Opensrs.whois.AdminFirstName", true),
		'type' => "text"
	),
	'Admin_lastname' => array(
		'label' => Language::_("Opensrs.whois.AdminLastName", true),
		'type' => "text"
	),
	'Admin_email' => array(
		'label' => Language::_("Opensrs.whois.AdminEmailAddress", true),
		'type' => "text"
	),
	'Admin_phonenumber' => array(
		'label' => Language::_("Opensrs.whois.AdminPhone", true),
		'type' => "text"
	),
	'Admin_organization' => array(
		'label' => Language::_("Opensrs.whois.AdminOrganization", true),
		'type' => "text"
	),
	'Admin_city' => array(
		'label' => Language::_("Opensrs.whois.AdminCity", true),
		'type' => "text"
	),
	'Admin_street' => array(
		'label' => Language::_("Opensrs.whois.AdminAddress1", true),
		'type' => "text"
	),
	'Admin_street2' => array(
		'label' => Language::_("Opensrs.whois.AdminAddress2", true),
		'type' => "text"
	),
	'Admin_street3' => array(
		'label' => Language::_("Opensrs.whois.AdminAddress2", true),
		'type' => "text"
	),
	'Admin_postalcode' => array(
		'label' => Language::_("Opensrs.whois.AdminPostalCode", true),
		'type' => "text"
	),
	'Admin_countrycode' => array(
		'label' => Language::_("Opensrs.whois.AdminCountryCode", true),
		'type' => "text"
	),
	'Admin_country' => array(
		'label' => Language::_("Opensrs.whois.AdminCountry", true),
		'type' => "text"
	)
));

// .IT
Configure::set("Opensrs.domain_fields.it", array(
	'dotitEntityType' => array(
		'label' => Language::_("Opensrs.domain.dotitEntityType", true),
		'type' => "select",
		'options' => array(
			'1' => Language::_("Opensrs.domain.dotitEntityType.1", true),
			'2' => Language::_("Opensrs.domain.dotitEntityType.2", true),
			'3' => Language::_("Opensrs.domain.dotitEntityType.3", true),
			'4' => Language::_("Opensrs.domain.dotitEntityType.4", true),
			'5' => Language::_("Opensrs.domain.dotitEntityType.5", true),
			'6' => Language::_("Opensrs.domain.dotitEntityType.6", true),
			'7' => Language::_("Opensrs.domain.dotitEntityType.7", true)
		)
	),
	'dotitNationality' => array(
		'label' => Language::_("Opensrs.domain.dotitNationality", true),
		'type' => "select",
		'options' => array(
'ITALY'=>'ITALY',        
'AFGHANISTAN'=>'AFGHANISTAN',
'ALAND ISLANDS'=>'ALAND ISLANDS',
'ALBANIA'=>'ALBANIA',
'ALGERIA'=>'ALGERIA',
'AMERICAN SAMOA'=>'AMERICAN SAMOA',
'ANDORRA'=>'ANDORRA',
'ANGOLA'=>'ANGOLA',
'ANGUILLA'=>'ANGUILLA',
'ANTARCTICA'=>'ANTARCTICA',
'ANTIGUA AND BARBUDA'=>'ANTIGUA AND BARBUDA',
'ARGENTINA'=>'ARGENTINA',
'ARMENIA'=>'ARMENIA',
'ARUBA'=>'ARUBA',
'AUSTRALIA'=>'AUSTRALIA',
'AUSTRIA'=>'AUSTRIA',
'AZERBAIJAN'=>'AZERBAIJAN',
'BAHAMAS'=>'BAHAMAS',
'BAHRAIN'=>'BAHRAIN',
'BANGLADESH'=>'BANGLADESH',
'BARBADOS'=>'BARBADOS',
'BELARUS'=>'BELARUS',
'BELGIUM'=>'BELGIUM',
'BELIZE'=>'BELIZE',
'BENIN'=>'BENIN',
'BERMUDA'=>'BERMUDA',
'BHUTAN'=>'BHUTAN',
'BOLIVIA'=>'BOLIVIA',
'BOSNIA AND HERZEGOVINA'=>'BOSNIA AND HERZEGOVINA',
'BOTSWANA'=>'BOTSWANA',
'BOUVET ISLAND'=>'BOUVET ISLAND',
'BRAZIL'=>'BRAZIL',
'BRITISH INDIAN OCEAN TERRITORY'=>'BRITISH INDIAN OCEAN TERRITORY',
'BRITISH VIRGIN ISLANDS'=>'BRITISH VIRGIN ISLANDS',
'BRUNEI'=>'BRUNEI',
'BULGARIA'=>'BULGARIA',
'BURKINA FASO'=>'BURKINA FASO',
'BURUNDI'=>'BURUNDI',
'CAMBODIA'=>'CAMBODIA',
'CAMEROON'=>'CAMEROON',
'CANADA'=>'CANADA',
'CAPE VERDE'=>'CAPE VERDE',
'CAYMAN ISLANDS'=>'CAYMAN ISLANDS',
'CENTRAL AFRICAN REPUBLIC'=>'CENTRAL AFRICAN REPUBLIC',
'CHAD'=>'CHAD',
'CHILE'=>'CHILE',
'CHINA'=>'CHINA',
'CHRISTMAS ISLAND'=>'CHRISTMAS ISLAND',
'COCOS (KEELING) ISLANDS'=>'COCOS (KEELING) ISLANDS',
'COLOMBIA'=>'COLOMBIA',
'COMOROS'=>'COMOROS',
'CONGO'=>'CONGO',
'COOK ISLANDS'=>'COOK ISLANDS',
'COSTA RICA'=>'COSTA RICA',
'CROATIA'=>'CROATIA',
'CUBA'=>'CUBA',
'CYPRUS'=>'CYPRUS',
'CZECH REPUBLIC'=>'CZECH REPUBLIC',
'DEMOCRATIC REPUBLIC OF CONGO'=>'DEMOCRATIC REPUBLIC OF CONGO',
'DENMARK'=>'DENMARK',
'DISPUTED TERRITORY'=>'DISPUTED TERRITORY',
'DJIBOUTI'=>'DJIBOUTI',
'DOMINICA'=>'DOMINICA',
'DOMINICAN REPUBLIC'=>'DOMINICAN REPUBLIC',
'EAST TIMOR'=>'EAST TIMOR',
'ECUADOR'=>'ECUADOR',
'EGYPT'=>'EGYPT',
'EL SALVADOR'=>'EL SALVADOR',
'EQUATORIAL GUINEA'=>'EQUATORIAL GUINEA',
'ERITREA'=>'ERITREA',
'ESTONIA'=>'ESTONIA',
'ETHIOPIA'=>'ETHIOPIA',
'FALKLAND ISLANDS'=>'FALKLAND ISLANDS',
'FAROE ISLANDS'=>'FAROE ISLANDS',
'FEDERATED STATES OF MICRONESIA'=>'FEDERATED STATES OF MICRONESIA',
'FIJI'=>'FIJI',
'FINLAND'=>'FINLAND',
'FRANCE'=>'FRANCE',
'FRENCH GUYANA'=>'FRENCH GUYANA',
'FRENCH POLYNESIA'=>'FRENCH POLYNESIA',
'FRENCH SOUTHERN TERRITORIES'=>'FRENCH SOUTHERN TERRITORIES',
'GABON'=>'GABON',
'GAMBIA'=>'GAMBIA',
'GEORGIA'=>'GEORGIA',
'GERMANY'=>'GERMANY',
'GHANA'=>'GHANA',
'GIBRALTAR'=>'GIBRALTAR',
'GREECE'=>'GREECE',
'GREENLAND'=>'GREENLAND',
'GRENADA'=>'GRENADA',
'GUADELOUPE'=>'GUADELOUPE',
'GUAM'=>'GUAM',
'GUATEMALA'=>'GUATEMALA',
'GUERNSEY'=>'GUERNSEY',
'GUINEA'=>'GUINEA',
'GUINEA-BISSAU'=>'GUINEA-BISSAU',
'GUYANA'=>'GUYANA',
'HAITI'=>'HAITI',
'HEARD ISLAND AND MCDONALD ISLANDS'=>'HEARD ISLAND AND MCDONALD ISLANDS',
'HONDURAS'=>'HONDURAS',
'HONG KONG'=>'HONG KONG',
'HUNGARY'=>'HUNGARY',
'ICELAND'=>'ICELAND',
'INDIA'=>'INDIA',
'INDONESIA'=>'INDONESIA',
'IRAN'=>'IRAN',
'IRAQ'=>'IRAQ',
'IRAQ-SAUDI ARABIA NEUTRAL ZONE'=>'IRAQ-SAUDI ARABIA NEUTRAL ZONE',
'IRELAND'=>'IRELAND',
'ISRAEL'=>'ISRAEL',
'ISLE OF MAN'=>'ISLE OF MAN',
'ITALY'=>'ITALY',
'IVORY COAST'=>'IVORY COAST',
'JAMAICA'=>'JAMAICA',
'JAPAN'=>'JAPAN',
'JERSEY'=>'JERSEY',
'JORDAN'=>'JORDAN',
'KAZAKHSTAN'=>'KAZAKHSTAN',
'KENYA'=>'KENYA',
'KIRIBATI'=>'KIRIBATI',
'KUWAIT'=>'KUWAIT',
'KYRGYZSTAN'=>'KYRGYZSTAN',
'LAOS'=>'LAOS',
'LATVIA'=>'LATVIA',
'LEBANON'=>'LEBANON',
'LESOTHO'=>'LESOTHO',
'LIBERIA'=>'LIBERIA',
'LIBYA'=>'LIBYA',
'LIECHTENSTEIN'=>'LIECHTENSTEIN',
'LITHUANIA'=>'LITHUANIA',
'LUXEMBOURG'=>'LUXEMBOURG',
'MACAU'=>'MACAU',
'MACEDONIA'=>'MACEDONIA',
'MADAGASCAR'=>'MADAGASCAR',
'MALAWI'=>'MALAWI',
'MALAYSIA'=>'MALAYSIA',
'MALDIVES'=>'MALDIVES',
'MALI'=>'MALI',
'MALTA'=>'MALTA',
'MARSHALL ISLANDS'=>'MARSHALL ISLANDS',
'MARTINIQUE'=>'MARTINIQUE',
'MAURITANIA'=>'MAURITANIA',
'MAURITIUS'=>'MAURITIUS',
'MAYOTTE'=>'MAYOTTE',
'MEXICO'=>'MEXICO',
'MOLDOVA'=>'MOLDOVA',
'MONACO'=>'MONACO',
'MONGOLIA'=>'MONGOLIA',
'MONTSERRAT'=>'MONTSERRAT',
'MOROCCO'=>'MOROCCO',
'MOZAMBIQUE'=>'MOZAMBIQUE',
'MYANMAR'=>'MYANMAR',
'NAMIBIA'=>'NAMIBIA',
'NAURU'=>'NAURU',
'NEPAL'=>'NEPAL',
'NETHERLANDS'=>'NETHERLANDS',
'NETHERLANDS ANTILLES'=>'NETHERLANDS ANTILLES',
'NEW CALEDONIA'=>'NEW CALEDONIA',
'NEW ZEALAND'=>'NEW ZEALAND',
'NICARAGUA'=>'NICARAGUA',
'NIGER'=>'NIGER',
'NIGERIA'=>'NIGERIA',
'NIUE'=>'NIUE',
'NORFOLK ISLAND'=>'NORFOLK ISLAND',
'NORTH KOREA'=>'NORTH KOREA',
'NORTHERN MARIANA ISLANDS'=>'NORTHERN MARIANA ISLANDS',
'NORWAY'=>'NORWAY',
'OMAN'=>'OMAN',
'PAKISTAN'=>'PAKISTAN',
'PALAU'=>'PALAU',
'PALESTINIAN OCCUPIED TERRITORIES'=>'PALESTINIAN OCCUPIED TERRITORIES',
'PANAMA'=>'PANAMA',
'PAPUA NEW GUINEA'=>'PAPUA NEW GUINEA',
'PARAGUAY'=>'PARAGUAY',
'PERU'=>'PERU',
'PHILIPPINES'=>'PHILIPPINES',
'PITCAIRN ISLANDS'=>'PITCAIRN ISLANDS',
'POLAND'=>'POLAND',
'PORTUGAL'=>'PORTUGAL',
'PUERTO RICO'=>'PUERTO RICO',
'QATAR'=>'QATAR',
'REUNION'=>'REUNION',
'ROMANIA'=>'ROMANIA',
'RUSSIA'=>'RUSSIA',
'RWANDA'=>'RWANDA',
'SAINT HELENA AND DEPENDENCIES'=>'SAINT HELENA AND DEPENDENCIES',
'SAINT KITTS AND NEVIS'=>'SAINT KITTS AND NEVIS',
'SAINT LUCIA'=>'SAINT LUCIA',
'SAINT PIERRE AND MIQUELON'=>'SAINT PIERRE AND MIQUELON',
'SAINT VINCENT AND THE GRENADINES'=>'SAINT VINCENT AND THE GRENADINES',
'SAMOA'=>'SAMOA',
'SAN MARINO'=>'SAN MARINO',
'SAO TOME AND PRINCIPE'=>'SAO TOME AND PRINCIPE',
'SAUDI ARABIA'=>'SAUDI ARABIA',
'SENEGAL'=>'SENEGAL',
'SEYCHELLES'=>'SEYCHELLES',
'SIERRA LEONE'=>'SIERRA LEONE',
'SINGAPORE'=>'SINGAPORE',
'SLOVAKIA'=>'SLOVAKIA',
'SLOVENIA'=>'SLOVENIA',
'SOLOMON ISLANDS'=>'SOLOMON ISLANDS',
'SOMALIA'=>'SOMALIA',
'SOUTH AFRICA'=>'SOUTH AFRICA',
'SOUTH GEORGIA AND SOUTH SANDWICH ISLANDS'=>'SOUTH GEORGIA AND SOUTH SANDWICH ISLANDS',
'SOUTH KOREA'=>'SOUTH KOREA',
'SPAIN'=>'SPAIN',
'SPRATLY ISLANDS'=>'SPRATLY ISLANDS',
'SRI LANKA'=>'SRI LANKA',
'SUDAN'=>'SUDAN',
'SURINAME'=>'SURINAME',
'SVALBARD AND JAN MAYEN'=>'SVALBARD AND JAN MAYEN',
'SWAZILAND'=>'SWAZILAND',
'SWEDEN'=>'SWEDEN',
'SWITZERLAND'=>'SWITZERLAND',
'SYRIA'=>'SYRIA',
'TAIWAN'=>'TAIWAN',
'TAJIKISTAN'=>'TAJIKISTAN',
'TANZANIA'=>'TANZANIA',
'THAILAND'=>'THAILAND',
'TOGO'=>'TOGO',
'TOKELAU'=>'TOKELAU',
'TONGA'=>'TONGA',
'TRINIDAD AND TOBAGO'=>'TRINIDAD AND TOBAGO',
'TUNISIA'=>'TUNISIA',
'TURKEY'=>'TURKEY',
'TURKMENISTAN'=>'TURKMENISTAN',
'TURKS AND CAICOS ISLANDS'=>'TURKS AND CAICOS ISLANDS',
'TUVALU'=>'TUVALU',
'UGANDA'=>'UGANDA',
'UKRAINE'=>'UKRAINE',
'UNITED ARAB EMIRATES'=>'UNITED ARAB EMIRATES',
'UNITED KINGDOM'=>'UNITED KINGDOM',
'UNITED NATIONS NEUTRAL ZONE'=>'UNITED NATIONS NEUTRAL ZONE',
'UNITED STATES'=>'UNITED STATES',
'UNITED STATES MINOR OUTLYING ISLANDS'=>'UNITED STATES MINOR OUTLYING ISLANDS',
'URUGUAY'=>'URUGUAY',
'US VIRGIN ISLANDS'=>'US VIRGIN ISLANDS',
'UZBEKISTAN'=>'UZBEKISTAN',
'VANUATU'=>'VANUATU',
'VATICAN CITY'=>'VATICAN CITY',
'VENEZUELA'=>'VENEZUELA',
'VIETNAM'=>'VIETNAM',
'WALLIS AND FUTUNA'=>'WALLIS AND FUTUNA',
'WESTERN SAHARA'=>'WESTERN SAHARA',
'YEMEN'=>'YEMEN',
'ZAMBIA'=>'ZAMBIA',
'ZIMBABWE'=>'ZIMBABWE',
'SERBIA'=>'SERBIA',
'MONTENEGRO'=>'MONTENEGRO',
'SAINT MARTIN'=>'SAINT MARTIN',
'SAINT BARTHELEMY'=>'SAINT BARTHELEMY'
        )
	),
	'dotitRegCode' => array(
		'label' => Language::_("Opensrs.domain.dotitRegCode", true),
		'type' => "text"
	),
	'dotitHideWhois' => array(
		'label' => Language::_("Opensrs.domain.dotitHideWhois", true),
		'type' => "select",
		'options' => array(
			'NO' => Language::_("Opensrs.domain.dotitHideWhois.no", true),
			'YES' => Language::_("Opensrs.domain.dotitHideWhois.yes", true)
		)
	),
	'dotitterms' => array(
		'label' => Language::_("Opensrs.domain.dotitterms", true),
		'type' => "checkbox",
		'options' => array(
			'I AGREE' => Language::_("Opensrs.domain.dotitterms.yes", true)
		)
	)


    /*,
	'dotitProvince' => array(
		'label' => Language::_("Opensrs.domain.dotitProvince", true),
		'type' => "text"
	)*/
));

// .US
Configure::set("Opensrs.domain_fields.us", array(
	'us_nexus' => array(
		'label' => Language::_("Opensrs.domain.RegistrantNexus", true),
		'type' => "select",
		'options' => array(
			'C11' => Language::_("Opensrs.domain.RegistrantNexus.c11", true),
			'C12' => Language::_("Opensrs.domain.RegistrantNexus.c12", true),
			'C21' => Language::_("Opensrs.domain.RegistrantNexus.c21", true),
			'C31' => Language::_("Opensrs.domain.RegistrantNexus.c31", true),
			'C32' => Language::_("Opensrs.domain.RegistrantNexus.c32", true)
		)
	),
	'us_purpose' => array(
		'label' => Language::_("Opensrs.domain.RegistrantPurpose", true),
		'type' => "select",
		'options' => array(
			'P1' => Language::_("Opensrs.domain.RegistrantPurpose.p1", true),
			'P2' => Language::_("Opensrs.domain.RegistrantPurpose.p2", true),
			'P3' => Language::_("Opensrs.domain.RegistrantPurpose.p3", true),
			'P4' => Language::_("Opensrs.domain.RegistrantPurpose.p4", true),
			'P5' => Language::_("Opensrs.domain.RegistrantPurpose.p5", true)
		)
	)
));

// .EU
Configure::set("Opensrs.domain_fields.eu", array(
	'eu_whoispolicy' => array(
		'label' => Language::_("Opensrs.domain.EUAgreeWhoisPolicy", true),
		'type' => "checkbox",
		'options' => array(
			'I AGREE' => Language::_("Opensrs.domain.EUAgreeWhoisPolicy.yes", true)
		)
	)
));

// .CA
Configure::set("Opensrs.domain_fields.ca", array(
	'cira_legal_type' => array(
		'label' => Language::_("Opensrs.domain.CIRALegalType", true),
		'type' => "select",
		'options' => array(
			'CCO' => Language::_("Opensrs.domain.RegistrantPurpose.cco", true),
			'CCT' => Language::_("Opensrs.domain.RegistrantPurpose.cct", true),
			'RES' => Language::_("Opensrs.domain.RegistrantPurpose.res", true),
			'GOV' => Language::_("Opensrs.domain.RegistrantPurpose.gov", true),
			'EDU' => Language::_("Opensrs.domain.RegistrantPurpose.edu", true),
			'ASS' => Language::_("Opensrs.domain.RegistrantPurpose.ass", true),
			'HOP' => Language::_("Opensrs.domain.RegistrantPurpose.hop", true),
			'PRT' => Language::_("Opensrs.domain.RegistrantPurpose.prt", true),
			'TDM' => Language::_("Opensrs.domain.RegistrantPurpose.tdm", true),
			'TRD' => Language::_("Opensrs.domain.RegistrantPurpose.trd", true),
			'PLT' => Language::_("Opensrs.domain.RegistrantPurpose.plt", true),
			'LAM' => Language::_("Opensrs.domain.RegistrantPurpose.lam", true),
			'TRS' => Language::_("Opensrs.domain.RegistrantPurpose.trs", true),
			'ABO' => Language::_("Opensrs.domain.RegistrantPurpose.abo", true),
			'INB' => Language::_("Opensrs.domain.RegistrantPurpose.inb", true),
			'LGR' => Language::_("Opensrs.domain.RegistrantPurpose.lgr", true),
			'OMK' => Language::_("Opensrs.domain.RegistrantPurpose.omk", true),
			'MAJ' => Language::_("Opensrs.domain.RegistrantPurpose.maj", true)
		)
	),
	'cira_whois_display' => array(
		'label' => Language::_("Opensrs.domain.CIRAWhoisDisplay", true),
		'type' => "select",
		'options' => array(
			'FULL' => Language::_("Opensrs.domain.CIRAWhoisDisplay.full", true),
			'PRIVATE' => Language::_("Opensrs.domain.CIRAWhoisDisplay.private", true),
		)
	),
	'cira_language' => array(
		'label' => Language::_("Opensrs.domain.CIRALanguage", true),
		'type' => "select",
		'options' => array(
			'en' => Language::_("Opensrs.domain.CIRALanguage.en", true),
			'fr' => Language::_("Opensrs.domain.CIRALanguage.fr", true),
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
Configure::set("Opensrs.domain_fields.uk", array(
	'uk_legal_type' => array(
		'label' => Language::_("Opensrs.domain.UKLegalType", true),
		'type' => "select",
		'options' => array(
			'IND' => Language::_("Opensrs.domain.UKLegalType.ind", true),
			'FIND' => Language::_("Opensrs.domain.UKLegalType.find", true),
			'LTD' => Language::_("Opensrs.domain.UKLegalType.ltd", true),
			'PLC' => Language::_("Opensrs.domain.UKLegalType.plc", true),
			'PTNR' => Language::_("Opensrs.domain.UKLegalType.ptnr", true),
			'LLP' => Language::_("Opensrs.domain.UKLegalType.llp", true),
			'IP' => Language::_("Opensrs.domain.UKLegalType.ip", true),
			'STRA' => Language::_("Opensrs.domain.UKLegalType.stra", true),
			'SCH' => Language::_("Opensrs.domain.UKLegalType.sch", true),
			'RCHAR' => Language::_("Opensrs.domain.UKLegalType.rchar", true),
			'GOV' => Language::_("Opensrs.domain.UKLegalType.gov", true),
			'OTHER' => Language::_("Opensrs.domain.UKLegalType.other", true),
			'CRC' => Language::_("Opensrs.domain.UKLegalType.crc", true),
			'FCORP' => Language::_("Opensrs.domain.UKLegalType.fcorp", true),
			'STAT' => Language::_("Opensrs.domain.UKLegalType.stat", true),
			'FOTHER' => Language::_("Opensrs.domain.UKLegalType.fother", true)
		)
	),
	'uk_reg_co_no' => array(
		'label' => Language::_("Opensrs.domain.UKCompanyID", true),
		'type' => "text"
	),
	'registered_for' => array(
		'label' => Language::_("Opensrs.domain.UKRegisteredfor", true),
		'type' => "text"
	)
));

// .ASIA
Configure::set("Opensrs.domain_fields.asia", array(
	'asia_cclocality' => array(
		'type' => "hidden",
		'options' => null
	),
	'asia_legalentitytype' => array(
		'label' => Language::_("Opensrs.domain.ASIALegalEntityType", true),
		'type' => "select",
		'options' => array(
			'corporation' => Language::_("Opensrs.domain.ASIALegalEntityType.corporation", true),
			'cooperative' => Language::_("Opensrs.domain.ASIALegalEntityType.cooperative", true),
			'partnership' => Language::_("Opensrs.domain.ASIALegalEntityType.partnership", true),
			'government' => Language::_("Opensrs.domain.ASIALegalEntityType.government", true),
			'politicalParty' => Language::_("Opensrs.domain.ASIALegalEntityType.politicalParty", true),
			'society' => Language::_("Opensrs.domain.ASIALegalEntityType.society", true),
			'institution' => Language::_("Opensrs.domain.ASIALegalEntityType.institution", true),
			'naturalPerson' => Language::_("Opensrs.domain.ASIALegalEntityType.naturalPerson", true)
		)
	),
	'asia_identform' => array(
		'label' => Language::_("Opensrs.domain.ASIAIdentForm", true),
		'type' => "select",
		'options' => array(
			'certificate' => Language::_("Opensrs.domain.ASIAIdentForm.certificate", true),
			'legislation' => Language::_("Opensrs.domain.ASIAIdentForm.legislation", true),
			'societyRegistry' => Language::_("Opensrs.domain.ASIAIdentForm.societyRegistry", true),
			'politicalPartyRegistry' => Language::_("Opensrs.domain.ASIAIdentForm.politicalPartyRegistry", true),
			'passport' => Language::_("Opensrs.domain.ASIAIdentForm.passport", true)
		)
	),
	'asia_ident:number' => array(
		'label' => Language::_("Opensrs.domain.ASIAIdentNumber", true),
		'type' => "text"
	)
));
?>