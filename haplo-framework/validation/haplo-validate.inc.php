<?php
    /**
     * HaploValidate - validation checks for common formats
     * WARNING - untested
     *
     * This file is part of the Haplo Framework, a simple PHP MVC framework
     *
     * Copyright (C) 2008-2011, Brightfish Software Limited/Ed Eliot
     *
     * For the full copyright and license information, please view the LICENSE
     * file that was distributed with this source code
     *
     * @package HaploValidate
     **/
     
    class HaploValidate {
        static public function is_safe_string($input, $min = null, $max = null) {
            if (!is_null($min) && is_null($max)) {
                return preg_match('/^[a-z0-9\s._-]{'.$min.',}$/i', $input);
            } elseif (is_null($min) && !is_null($max)) {
                return preg_match('/^[a-z0-9\s._-]{'.$max.'}$/i', $input);
            } elseif (!is_null($min) && !is_null($max)) {
                return preg_match('/^[a-z0-9\s._-]{'.$min.','.$max.'$/i', $input);
            } else {
                return preg_match('/^[a-z0-9\s._-]+$/i', $input);
            }
        }
        
        static public function is_integer($input) {
            return filter_var($input, FILTER_VALIDATE_INT);
        }
        
        static public function is_float($input) {
            return filter_var($input, FILTER_VALIDATE_FLOAT);
        }
        
        static public function is_in_range($input, $min = null, $max = null) {
            if (is_numeric($input)) {
                return (($input >= $min || is_null($min)) && ($input <= $max || is_null($max)));
            }
            
            return false;
        }
        
        static public function is_valid_option($input, $allowedOptions) {
            return in_array($input, $allowedOptions);
        }
        
        static public function is_email($input) {
            // source - https://www.owasp.org/index.php/OWASP_Validation_Regex_Repository
            return preg_match('/^[a-z0-9+&*-]+(?:\.[a-z0-9_+&*-]+)*@(?:[a-z0-9-]+\.)+[a-z]{2,7}$/i', $input);
        }
        
        static public function is_url($input) {
            // source - https://www.owasp.org/index.php/OWASP_Validation_Regex_Repository
            // modified to remove additional protocols as generally only interested in http and https
            return preg_match("/^https?://(%[0-9a-f]{2}|[-()_.!~*';/?:@&=+$,a-z0-9])+)([).!';/?:,][[:blank:]])?$/i", $input);
        }
        
        static public function is_ip($input) {
            return filter_var($input, FILTER_VALIDATE_IP);
        }
        
        static public function is_hex_color($input, $sixDigitOnly = false) {
            if ($sixDigitOnly) {
                return preg_match('/^[0-9a-f]{6}$/i', $input);
            } else {
                return preg_match('/^([0-9a-f]{3}|[0-9a-f]{6})$/i', $input);
            }
        }
        
        static public function is_english_day_of_week($input) {
            // source - https://www.owasp.org/index.php/OWASP_Validation_Regex_Repository
            return preg_match('/^(Monday|Tuesday|Wednesday|Thursday|Friday|Saturday|Sunday)$/i', $input);
        }
        
        static public function is_english_abbr_day_of_week($input) {
            // source - https://www.owasp.org/index.php/OWASP_Validation_Regex_Repository
            return preg_match('/^(Mo|Tu|We|Th|Fr|Sa|Su)$/i', $input);
        }
        
        static public function is_english_month($input) {
            // source - https://www.owasp.org/index.php/OWASP_Validation_Regex_Repository
            return preg_match('/^(Jan|Feb|Mar|Apr|May|Jun|Jul|Aug|Sep|Oct|Nov|Dec)$/i', $input);
        }
        
        static public function is_english_abbr_month($input) {
            // source - https://www.owasp.org/index.php/OWASP_Validation_Regex_Repository
            return preg_match('/^(Jan|Feb|Mar|Apr|May|Jun|Jul|Aug|Sep|Oct|Nov|Dec)$/i', $input);
        }
        
        static public function is_english_digit_word($input) {
            // source - https://www.owasp.org/index.php/OWASP_Validation_Regex_Repository
            return preg_match('/^(zero|one|two|three|four|five|six|seven|eight|nine)$/i', $input);
        }
        
        static public function is_french_digit_word($input) {
            // source - https://www.owasp.org/index.php/OWASP_Validation_Regex_Repository
            return preg_match('/^(z[eé]ro|un|deux|trois|quatre|cinq|six|sept|huit|neuf)$/i', $input);
        }
        
        static public function is_german_digit_word($input) {
            // source - https://www.owasp.org/index.php/OWASP_Validation_Regex_Repository
            return preg_match('/^(null|eins|zwei|drei|vier|f(ue|ü)nf|sechs|sieben|acht|neun)$/i', $input);
        }
        
        static public function is_iso3_country_code($input) {
            return preg_match(
                '/^('.
                'ABW|AFG|AGO|AIA|ALA|ALB|AND|ARE|ARG|ARM|ASM|ATA|ATF|ATG|AUS|AUT|AZE|BDI|BEL|BEN|BES|BFA|BGD|'.
                'BGR|BHR|BHS|BIH|BLM|BLR|BLZ|BMU|BOL|BRA|BRB|BRN|BTN|BVT|BWA|CAF|CAN|CCK|CHE|CHL|CHN|CIV|CMR|'.
                'COD|COG|COK|COL|COM|CPV|CRI|CUB|CUW|CXR|CYM|CYP|CZE|DEU|DJI|DMA|DNK|DOM|DZA|ECU|EGY|ERI|ESH|'.
                'ESP|EST|ETH|FIN|FJI|FLK|FRA|FRO|FSM|GAB|GBR|GEO|GGY|GHA|GIB|GIN|GLP|GMB|GNB|GNQ|GRC|GRD|GRL|'.
                'GTM|GUF|GUM|GUY|HKG|HMD|HND|HRV|HTI|HUN|IDN|IMN|IND|IOT|IRL|IRN|IRQ|ISL|ISR|ITA|JAM|JEY|JOR|'.
                'JPN|KAZ|KEN|KGZ|KHM|KIR|KNA|KOR|KWT|LAO|LBN|LBR|LBY|LCA|LIE|LKA|LSO|LTU|LUX|LVA|MAC|MAF|MAR|'.
                'MCO|MDA|MDG|MDV|MEX|MHL|MKD|MLI|MLT|MMR|MNE|MNG|MNP|MOZ|MRT|MSR|MTQ|MUS|MWI|MYS|MYT|NAM|NCL|'.
                'NER|NFK|NGA|NIC|NIU|NLD|NOR|NPL|NRU|NZL|OMN|PAK|PAN|PCN|PER|PHL|PLW|PNG|POL|PRI|PRK|PRT|PRY|'.
                'PSE|PYF|QAT|REU|ROU|RUS|RWA|SAU|SDN|SEN|SGP|SGS|SHN|SJM|SLB|SLE|SLV|SMR|SOM|SPM|SRB|SSD|STP|'.
                'SUR|SVK|SVN|SWE|SWZ|SXM|SYC|SYR|TCA|TCD|TGO|THA|TJK|TKL|TKM|TLS|TON|TTO|TUN|TUR|TUV|TWN|TZA|'.
                'UGA|UKR|UMI|URY|USA|UZB|VAT|VCT|VEN|VGB|VIR|VNM|VUT|WLF|WSM|YEM|ZAF|ZMB|ZWE)'.
                '$/'
            , $input);
        }
        
        static public function is_iso2_country_code($input) {
            return preg_match(
                '/^('.
                'AD|AE|AF|AG|AI|AL|AM|AO|AQ|AR|AS|AT|AU|AW|AX|AZ|BA|BB|BD|BE|BF|BG|BH|BI|BJ|BL|BM|BN|BO|BQ|BQ|'.
                'BR|BS|BT|BV|BW|BY|BZ|CA|CC|CD|CF|CG|CH|CI|CK|CL|CM|CN|CO|CR|CU|CV|CW|CX|CY|CZ|DE|DJ|DK|DM|DO|'.
                'DZ|EC|EE|EG|EH|ER|ES|ET|FI|FJ|FK|FM|FO|FR|GA|GB|GD|GE|GF|GG|GH|GI|GL|GM|GN|GP|GQ|GR|GS|GT|GU|'.
                'GW|GY|HK|HM|HN|HR|HT|HU|ID|IE|IL|IM|IN|IO|IQ|IR|IS|IT|JE|JM|JO|JP|KE|KG|KH|KI|KM|KN|KP|KR|KW|'.
                'KY|KZ|LA|LB|LC|LI|LK|LR|LS|LT|LU|LV|LY|MA|MC|MD|ME|MF|MG|MH|MK|ML|MM|MN|MO|MP|MQ|MR|MS|MT|MU|'.
                'MV|MW|MX|MY|MZ|NA|NC|NE|NF|NG|NI|NL|NO|NP|NR|NU|NZ|OM|PA|PE|PF|PG|PH|PK|PL|PM|PN|PR|PS|PT|PW|'.
                'PY|QA|RE|RO|RS|RU|RW|SA|SB|SC|SD|SE|SG|SH|SI|SJ|SK|SL|SM|SN|SO|SR|SS|ST|SV|SX|SY|SZ|TC|TD|TF|'.
                'TG|TH|TJ|TK|TL|TM|TN|TO|TR|TT|TV|TW|TZ|UA|UG|UM|US|UY|UZ|VA|VC|VE|VG|VI|VN|VU|WF|WS|YE|YT|ZA|'.
                'ZM|ZW'.
                '$/'
            , $input);
        }
        
        static public function is_us_state_code($input) {
            // source - https://www.owasp.org/index.php/OWASP_Validation_Regex_Repository
            return preg_match(
                '/^('.
                'AE|AL|AK|AP|AS|AZ|AR|CA|CO|CT|DE|DC|FM|FL|GA|GU|HI|ID|IL|IN|IA|KS|KY|LA|ME|MH|MD|MA|MI|MN|MS|'.
                'MO|MP|MT|NE|NV|NH|NJ|NM|NY|NC|ND|OH|OK|OR|PW|PA|PR|RI|SC|SD|TN|TX|UT|VT|VI|VA|WA|WV|WI|WY'.
                ')$/'
            , $input);
        }
        
        static public function is_us_zip($input) {
            // source - https://www.owasp.org/index.php/OWASP_Validation_Regex_Repository
            return preg_match('/^\d{5}(-\d{4})?$/', $input);
        }
        
        static public function is_uk_postcode($input) {
            // source - http://en.wikipedia.org/wiki/Postcodes_in_the_United_Kingdom
            // not perfect but good enough to match all valid postcodes (may match a few invalid ones also)
            // modified to allow no spaces or more than one space between the two postcode parts
            return preg_match('/^[A-Z]{1,2}[0-9R][0-9A-Z]?\s[0-9][ABD-HJLNP-UW-Z]{2}$/i', $input);
        }
        
        static public function is_safe_password($input) {
            // source - https://www.owasp.org/index.php/OWASP_Validation_Regex_Repository
            // regex modified to require a minimum of 6 characters and allow a maximum of 50
            return preg_match('/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{6,50}$/', $input);
        }
        
        static public function is_reversed_date($input, $separator = '/') {
            // format - yyyy/mm/dd
            if (
                !preg_match("#^(?<year>[0-9]{4})$separator(?<month>[0-9]{1,2})$separator(?<day>[0-9]{1,2})$#", $input, $matches) || 
                !strtotime($input) ||
                ($matches['month'] == 2 && $matches['day'] == 29 && !date('L', strtotime($input))) // check for 29th in non-leap year
            ) {
                return false;
            }
            
            return true;
        }
        
        static public function is_us_date($input, $separator = '/') {
            // format mm/dd/yyyy
            if (
                !preg_match("#^(?<month>[0-9]{1,2})$separator(?<day>[0-9]{1,2})$separator(?<year>[0-9]{4})$#", $input, $matches) || 
                !strtotime($input) ||
                ($matches['month'] == 2 && $matches['day'] == 29 && !date('L', strtotime($input))) // check for 29th in non-leap year
            ) {
                return false;
            }
            
            return true;
        }
        
        static public function is_uk_date($input, $separator = '/') {
            // format dd/mm/yyyy
            if (
                !preg_match("#^(?<day>[0-9]{1,2})$separator(?<month>[0-9]{1,2})$separator(?<year>[0-9]{4})$#", $input, $matches) || 
                !strtotime($matches['year'].$separator.$matches['month'].$separator.$matches['day']) ||
                ($matches['month'] == 2 && $matches['day'] == 29 && !date('L', strtotime($input))) // check for 29th in non-leap year
            ) {
                return false;
            }
            
            return true;
        }
        
        static public function is_match($input, $pattern) {
            return preg_match($pattern, $input);
        }
    }
?>