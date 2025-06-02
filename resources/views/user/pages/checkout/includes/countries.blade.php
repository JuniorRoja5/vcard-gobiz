<div class="mb-3">
    <label class="form-label required">{{ __('Country') }}</label>
    <select value="{{ Auth::user()->billing_country }}" name="billing_country" id="billing_country" class="form-control" required>
        <option {{ Auth::user()->billing_country == null ? 'selected' : '' }} value="" selected disabled> {{ __('Select a country') }}</option>
        <option {{ Auth::user()->billing_country == 'Afghanistan' ? 'selected' : '' }} value="Afghanistan"> {{ __('Afghanistan') }}</option>
        <option {{ Auth::user()->billing_country == 'Aland Islands' ? 'selected' : '' }} value="Aland Islands"> {{ __('Åland Islands') }}</option>
        <option {{ Auth::user()->billing_country == 'Albania' ? 'selected' : '' }} value="Albania">{{ __('Albania') }}</option>
        <option {{ Auth::user()->billing_country == 'Algeria' ? 'selected' : '' }} value="Algeria">{{ __('Algeria') }}</option>
        <option {{ Auth::user()->billing_country == 'American Samoa' ? 'selected' : '' }} value="American Samoa"> {{ __('American Samoa') }}</option>
        <option {{ Auth::user()->billing_country == 'Andorra' ? 'selected' : '' }} value="Andorra">{{ __('Andorra') }}</option>
        <option {{ Auth::user()->billing_country == 'Angola' ? 'selected' : '' }} value="Angola">{{ __('Angola') }}</option>
        <option {{ Auth::user()->billing_country == 'Anguilla' ? 'selected' : '' }} value="Anguilla"> {{ __('Anguilla') }}</option>
        <option {{ Auth::user()->billing_country == 'Antarctica' ? 'selected' : '' }} value="Antarctica"> {{ __('Antarctica ') }}</option>
        <option {{ Auth::user()->billing_country == 'Antigua and Barbuda' ? 'selected' : '' }} value="Antigua and Barbuda">{{ __('Antigua and Barbuda') }}</option>
        <option {{ Auth::user()->billing_country == 'Argentina' ? 'selected' : '' }} value="Argentina"> {{ __('Argentina ') }}</option>
        <option {{ Auth::user()->billing_country == 'Armenia' ? 'selected' : '' }} value="Armenia">{{ __('Armenia') }}</option>
        <option {{ Auth::user()->billing_country == 'Aruba' ? 'selected' : '' }} value="Aruba">{{ __('Aruba') }}</option>
        <option {{ Auth::user()->billing_country == 'Australia' ? 'selected' : '' }} value="Australia"> {{ __('Australia ') }}</option>
        <option {{ Auth::user()->billing_country == 'Austria' ? 'selected' : '' }} value="Austria">{{ __('Austria') }}</option>
        <option {{ Auth::user()->billing_country == 'Azerbaijan' ? 'selected' : '' }} value="Azerbaijan"> {{ __('Azerbaijan ') }}</option>
        <option {{ Auth::user()->billing_country == 'Bahamas' ? 'selected' : '' }} value="Bahamas">{{ __('Bahamas') }}</option>
        <option {{ Auth::user()->billing_country == 'Bahrain' ? 'selected' : '' }} value="Bahrain">{{ __('Bahrain') }}</option>
        <option {{ Auth::user()->billing_country == 'Bangladesh' ? 'selected' : '' }} value="Bangladesh"> {{ __('Bangladesh ') }}</option>
        <option {{ Auth::user()->billing_country == 'Barbados' ? 'selected' : '' }} value="Barbados"> {{ __('Barbados') }}</option>
        <option {{ Auth::user()->billing_country == 'Belarus' ? 'selected' : '' }} value="Belarus">{{ __('Belarus') }}</option>
        <option {{ Auth::user()->billing_country == 'Belgium' ? 'selected' : '' }} value="Belgium">{{ __('Belgium') }}</option>
        <option {{ Auth::user()->billing_country == 'Belize' ? 'selected' : '' }} value="Belize">{{ __('Belize') }}</option>
        <option {{ Auth::user()->billing_country == 'Benin' ? 'selected' : '' }} value="Benin">{{ __('Benin') }}</option>
        <option {{ Auth::user()->billing_country == 'Bermuda' ? 'selected' : '' }} value="Bermuda">{{ __('Bermuda') }}</option>
        <option {{ Auth::user()->billing_country == 'Bhutan' ? 'selected' : '' }} value="Bhutan">{{ __('Bhutan') }}</option>
        <option {{ Auth::user()->billing_country == 'Bolivia' ? 'selected' : '' }} value="Bolivia">{{ __('Bolivia') }}</option>
        <option {{ Auth::user()->billing_country == 'Bosnia and Herzegovina' ? 'selected' : '' }} value="Bosnia and Herzegovina">{{ __('Bosnia and Herzegovina') }}</option>
        <option {{ Auth::user()->billing_country == 'Botswana' ? 'selected' : '' }} value="Botswana"> {{ __('Botswana') }}</option>
        <option {{ Auth::user()->billing_country == 'Bouvet Island' ? 'selected' : '' }} value="Bouvet Island"> {{ __('Bouvet Island') }}</option>
        <option {{ Auth::user()->billing_country == 'Brazil' ? 'selected' : '' }} value="Brazil">{{ __('Brazil') }}</option>
        <option {{ Auth::user()->billing_country == 'British Indian Ocean Territory' ? 'selected' : '' }} value="British Indian Ocean Territory"> {{ __('British Indian Ocean Territory') }}</option>
        <option {{ Auth::user()->billing_country == 'Brunei Darussalam' ? 'selected' : '' }} value="Brunei Darussalam"> {{ __(' Brunei Darussalam') }}</option>
        <option {{ Auth::user()->billing_country == 'Bulgaria' ? 'selected' : '' }} value="Bulgaria"> {{ __('Bulgaria') }}</option>
        <option {{ Auth::user()->billing_country == 'Burkina Faso' ? 'selected' : '' }} value="Burkina Faso"> {{ __('Burkina Faso') }}</option>
        <option {{ Auth::user()->billing_country == 'Burundi' ? 'selected' : '' }} value="Burundi">{{ __('Burundi') }}</option>
        <option {{ Auth::user()->billing_country == 'Cambodia' ? 'selected' : '' }} value="Cambodia"> {{ __('Cambodia') }}</option>
        <option {{ Auth::user()->billing_country == 'Cameroon' ? 'selected' : '' }} value="Cameroon"> {{ __('Cameroon') }}</option>
        <option {{ Auth::user()->billing_country == 'Canada' ? 'selected' : '' }} value="Canada">{{ __('Canada') }}</option>
        <option {{ Auth::user()->billing_country == 'Cape Verde' ? 'selected' : '' }} value="Cape Verde"> {{ __('Cape Verde ') }}</option>
        <option {{ Auth::user()->billing_country == 'Cayman Islands' ? 'selected' : '' }} value="Cayman Islands"> {{ __('Cayman Islands') }}</option>
        <option {{ Auth::user()->billing_country == 'Central African Republic' ? 'selected' : '' }} value="Central African Republic">{{ __('Central African Republic') }}</option>
        <option {{ Auth::user()->billing_country == 'Chad' ? 'selected' : '' }} value="Chad">{{ __('Chad') }}</option>
        <option {{ Auth::user()->billing_country == 'Chile' ? 'selected' : '' }} value="Chile">{{ __('Chile') }}</option>
        <option {{ Auth::user()->billing_country == 'China' ? 'selected' : '' }} value="China">{{ __('China') }}</option>
        <option {{ Auth::user()->billing_country == 'Christmas Island' ? 'selected' : '' }} value="Christmas Island"> {{ __(' Christmas Island') }}</option>
        <option {{ Auth::user()->billing_country == 'Cocos (Keeling) Islands' ? 'selected' : '' }} value="Cocos (Keeling) Islands">{{ __('Cocos (Keeling) Islands') }}</option>
        <option {{ Auth::user()->billing_country == 'Colombia' ? 'selected' : '' }} value="Colombia"> {{ __('Colombia') }}</option>
        <option {{ Auth::user()->billing_country == 'Comoros' ? 'selected' : '' }} value="Comoros"> {{ __('Comoros') }}</option>
        <option {{ Auth::user()->billing_country == 'Congo' ? 'selected' : '' }} value="Congo">{{ __('Congo') }}</option>
        <option {{ Auth::user()->billing_country == 'Congo, The Democratic Republic of The' ? 'selected' : '' }} value="Congo, The Democratic Republic of The"> {{ __('Congo, The Democratic Republic of The') }}</option>
        <option {{ Auth::user()->billing_country == 'Cook Islands' ? 'selected' : '' }} value="Cook Islands"> {{ __('Cook Islands') }}</option>
        <option {{ Auth::user()->billing_country == 'Costa Rica' ? 'selected' : '' }} value="Costa Rica"> {{ __('Costa Rica ') }}</option>
        <option {{ Auth::user()->billing_country == "Cote D'ivoire" ? 'selected' : '' }} value="Cote D'ivoire"> {{ __('Cote D\'ivoire') }}</option>
        <option {{ Auth::user()->billing_country == 'Croatia' ? 'selected' : '' }} value="Croatia"> {{ __('Croatia') }}</option>
        <option {{ Auth::user()->billing_country == 'Cuba' ? 'selected' : '' }} value="Cuba">{{ __('Cuba') }}</option>
        <option {{ Auth::user()->billing_country == 'Cyprus' ? 'selected' : '' }} value="Cyprus">{{ __('Cyprus') }}</option>
        <option {{ Auth::user()->billing_country == 'Czech Republic' ? 'selected' : '' }} value="Czech Republic"> {{ __('Czech Republic') }}</option>
        <option {{ Auth::user()->billing_country == 'Denmark' ? 'selected' : '' }} value="Denmark"> {{ __('Denmark') }}</option>
        <option {{ Auth::user()->billing_country == 'Djibouti' ? 'selected' : '' }} value="Djibouti"> {{ __('Djibouti') }}</option>
        <option {{ Auth::user()->billing_country == 'Dominica' ? 'selected' : '' }} value="Dominica"> {{ __('Dominica') }}</option>
        <option {{ Auth::user()->billing_country == 'Dominican Republic' ? 'selected' : '' }} value="Dominican Republic">{{ __('Dominican Republic') }}</option>
        <option {{ Auth::user()->billing_country == 'Ecuador' ? 'selected' : '' }} value="Ecuador"> {{ __('Ecuador') }}</option>
        <option {{ Auth::user()->billing_country == 'Egypt' ? 'selected' : '' }} value="Egypt">{{ __('Egypt') }}</option>
        <option {{ Auth::user()->billing_country == 'El Salvador' ? 'selected' : '' }} value="El Salvador"> {{ __('El Salvador ') }}</option>
        <option {{ Auth::user()->billing_country == 'Equatorial Guinea' ? 'selected' : '' }} value="Equatorial Guinea">{{ __(' Equatorial Guinea') }}</option>
        <option {{ Auth::user()->billing_country == 'Eritrea' ? 'selected' : '' }} value="Eritrea"> {{ __('Eritrea') }}</option>
        <option {{ Auth::user()->billing_country == 'Estonia' ? 'selected' : '' }} value="Estonia"> {{ __('Estonia') }}</option>
        <option {{ Auth::user()->billing_country == 'Ethiopia' ? 'selected' : '' }} value="Ethiopia"> {{ __('Ethiopia') }}</option>
        <option {{ Auth::user()->billing_country == 'Falkland Islands (Malvinas)' ? 'selected' : '' }} value="Falkland Islands (Malvinas)"> {{ __('Falkland Islands (Malvinas) ') }}</option>
        <option {{ Auth::user()->billing_country == 'Faroe Islands' ? 'selected' : '' }} value="Faroe Islands"> {{ __('Faroe Islands') }}</option>
        <option {{ Auth::user()->billing_country == 'Fiji' ? 'selected' : '' }} value="Fiji">{{ __('Fiji') }}</option>
        <option {{ Auth::user()->billing_country == 'Finland' ? 'selected' : '' }} value="Finland"> {{ __('Finland') }}</option>
        <option {{ Auth::user()->billing_country == 'France' ? 'selected' : '' }} value="France">{{ __('France') }}</option>
        <option {{ Auth::user()->billing_country == 'French Guiana' ? 'selected' : '' }} value="French Guiana"> {{ __('French Guiana') }}</option>
        <option {{ Auth::user()->billing_country == 'French Polynesia' ? 'selected' : '' }} value="French Polynesia"> {{ __(' French Polynesia') }}</option>
        <option {{ Auth::user()->billing_country == 'French Southern Territories' ? 'selected' : '' }} value="French Southern Territories"> {{ __('French Southern Territories ') }}</option>
        <option {{ Auth::user()->billing_country == 'Gabon' ? 'selected' : '' }} value="Gabon">{{ __('Gabon') }}</option>
        <option {{ Auth::user()->billing_country == 'Gambia' ? 'selected' : '' }} value="Gambia">{{ __('Gambia') }}</option>
        <option {{ Auth::user()->billing_country == 'Georgia' ? 'selected' : '' }} value="Georgia"> {{ __('Georgia') }}</option>
        <option {{ Auth::user()->billing_country == 'Germany' ? 'selected' : '' }} value="Germany"> {{ __('Germany') }}</option>
        <option {{ Auth::user()->billing_country == 'Ghana' ? 'selected' : '' }} value="Ghana">{{ __('Ghana') }}</option>
        <option {{ Auth::user()->billing_country == 'Gibraltar' ? 'selected' : '' }} value="Gibraltar"> {{ __('Gibraltar ') }}</option>
        <option {{ Auth::user()->billing_country == 'Greece' ? 'selected' : '' }} value="Greece">{{ __('Greece') }}</option>
        <option {{ Auth::user()->billing_country == 'Greenland' ? 'selected' : '' }} value="Greenland"> {{ __('Greenland ') }}</option>
        <option {{ Auth::user()->billing_country == 'Grenada' ? 'selected' : '' }} value="Grenada"> {{ __('Grenada') }}</option>
        <option {{ Auth::user()->billing_country == 'Guadeloupe' ? 'selected' : '' }} value="Guadeloupe"> {{ __('Guadeloupe ') }}</option>
        <option {{ Auth::user()->billing_country == 'Guam' ? 'selected' : '' }} value="Guam">{{ __('Guam') }}</option>
        <option {{ Auth::user()->billing_country == 'Guatemala' ? 'selected' : '' }} value="Guatemala"> {{ __('Guatemala ') }}</option>
        <option {{ Auth::user()->billing_country == 'Guernsey' ? 'selected' : '' }} value="Guernsey"> {{ __('Guernsey') }}</option>
        <option {{ Auth::user()->billing_country == 'Guinea' ? 'selected' : '' }} value="Guinea">{{ __('Guinea') }}</option>
        <option {{ Auth::user()->billing_country == 'Guinea-bissau' ? 'selected' : '' }} value="Guinea-bissau"> {{ __(' Guinea-bissau') }}</option>
        <option {{ Auth::user()->billing_country == 'Guyana' ? 'selected' : '' }} value="Guyana">{{ __('Guyana') }}</option>
        <option {{ Auth::user()->billing_country == 'Haiti' ? 'selected' : '' }} value="Haiti">{{ __('Haiti') }}</option>
        <option {{ Auth::user()->billing_country == 'Heard Island and Mcdonald Islands' ? 'selected' : '' }} value="Heard Island and Mcdonald Islands"> {{ __('Heard Island and Mcdonald Islands') }}</option>
        <option {{ Auth::user()->billing_country == 'Holy See (Vatican City State)' ? 'selected' : '' }} value="Holy See (Vatican City State)"> {{ __('Holy See (Vatican City State) ') }}</option>
        <option {{ Auth::user()->billing_country == 'Honduras' ? 'selected' : '' }} value="Honduras"> {{ __('Honduras') }}</option>
        <option {{ Auth::user()->billing_country == 'Hong Kong' ? 'selected' : '' }} value="Hong Kong"> {{ __('Hong Kong ') }}</option>
        <option {{ Auth::user()->billing_country == 'Hungary' ? 'selected' : '' }} value="Hungary"> {{ __('Hungary') }}</option>
        <option {{ Auth::user()->billing_country == 'Iceland' ? 'selected' : '' }} value="Iceland"> {{ __('Iceland') }}</option>
        <option {{ Auth::user()->billing_country == 'India' ? 'selected' : '' }} value="India">{{ __('India') }}</option>
        <option {{ Auth::user()->billing_country == 'Indonesia' ? 'selected' : '' }} value="Indonesia"> {{ __('Indonesia ') }}</option>
        <option {{ Auth::user()->billing_country == 'Iran, Islamic Republic of' ? 'selected' : '' }} value="Iran, Islamic Republic of">{{ __('Iran, Islamic Republic of') }}</option>
        <option {{ Auth::user()->billing_country == 'Iraq' ? 'selected' : '' }} value="Iraq">{{ __('Iraq') }}</option>
        <option {{ Auth::user()->billing_country == 'Ireland' ? 'selected' : '' }} value="Ireland"> {{ __('Ireland') }}</option>
        <option {{ Auth::user()->billing_country == 'Isle of Man' ? 'selected' : '' }} value="Isle of Man"> {{ __('Isle of Man') }}</option>
        <option {{ Auth::user()->billing_country == 'Israel' ? 'selected' : '' }} value="Israel"> {{ __('Israel') }}</option>
        <option {{ Auth::user()->billing_country == 'Italy' ? 'selected' : '' }} value="Italy">{{ __('Italy') }}</option>
        <option {{ Auth::user()->billing_country == 'Jamaica' ? 'selected' : '' }} value="Jamaica"> {{ __('Jamaica') }}</option>
        <option {{ Auth::user()->billing_country == 'Japan' ? 'selected' : '' }} value="Japan">{{ __('Japan') }}</option>
        <option {{ Auth::user()->billing_country == 'Jersey' ? 'selected' : '' }} value="Jersey"> {{ __('Jersey') }}</option>
        <option {{ Auth::user()->billing_country == 'Jordan' ? 'selected' : '' }} value="Jordan"> {{ __('Jordan') }}</option>
        <option {{ Auth::user()->billing_country == 'Kazakhstan' ? 'selected' : '' }} value="Kazakhstan"> {{ __('Kazakhstan ') }}</option>
        <option {{ Auth::user()->billing_country == 'Kenya' ? 'selected' : '' }} value="Kenya">{{ __('Kenya') }}</option>
        <option {{ Auth::user()->billing_country == 'Kiribati' ? 'selected' : '' }} value="Kiribati"> {{ __('Kiribati ') }}</option>
        <option {{ Auth::user()->billing_country == "Korea, Democratic People's Republic of" ? 'selected' : '' }} value="Korea, Democratic People's Republic of">{{ __('Korea, Democratic People\'s Republic of') }}</option>
        <option {{ Auth::user()->billing_country == 'Korea, Republic of' ? 'selected' : '' }} value="Korea, Republic of">{{ __('Korea, Republic of') }}</option>
        <option {{ Auth::user()->billing_country == 'Kuwait' ? 'selected' : '' }} value="Kuwait"> {{ __('Kuwait') }}</option>
        <option {{ Auth::user()->billing_country == 'Kyrgyzstan' ? 'selected' : '' }} value="Kyrgyzstan"> {{ __('Kyrgyzstan ') }}</option>
        <option {{ Auth::user()->billing_country == "Lao People's Democratic Republic" ? 'selected' : '' }} value="Lao People's Democratic Republic">{{ __('Lao People\'s Democratic Republic') }}</option>
        <option {{ Auth::user()->billing_country == 'Latvia' ? 'selected' : '' }} value="Latvia"> {{ __('Latvia') }}</option>
        <option {{ Auth::user()->billing_country == 'Lebanon' ? 'selected' : '' }} value="Lebanon"> {{ __('Lebanon') }}</option>
        <option {{ Auth::user()->billing_country == 'Lesotho' ? 'selected' : '' }} value="Lesotho"> {{ __('Lesotho') }}</option>
        <option {{ Auth::user()->billing_country == 'Liberia' ? 'selected' : '' }} value="Liberia"> {{ __('Liberia') }}</option>
        <option {{ Auth::user()->billing_country == 'Libyan Arab Jamahiriya' ? 'selected' : '' }} value="Libyan Arab Jamahiriya">{{ __('Libyan Arab Jamahiriya') }}</option>
        <option {{ Auth::user()->billing_country == 'Liechtenstein' ? 'selected' : '' }} value="Liechtenstein"> {{ __(' Liechtenstein') }}</option>
        <option {{ Auth::user()->billing_country == 'Lithuania' ? 'selected' : '' }} value="Lithuania"> {{ __('Lithuania ') }}</option>
        <option {{ Auth::user()->billing_country == 'Luxembourg' ? 'selected' : '' }} value="Luxembourg"> {{ __('Luxembourg ') }}</option>
        <option {{ Auth::user()->billing_country == 'Macao' ? 'selected' : '' }} value="Macao">{{ __('Macao') }}</option>
        <option {{ Auth::user()->billing_country == 'Macedonia, The Former Yugoslav Republic of' ? 'selected' : '' }} value="Macedonia, The Former Yugoslav Republic of"> {{ __('Macedonia, The Former Yugoslav Republic of') }}</option>
        <option {{ Auth::user()->billing_country == 'Madagascar' ? 'selected' : '' }} value="Madagascar"> {{ __('Madagascar ') }}</option>
        <option {{ Auth::user()->billing_country == 'Malawi' ? 'selected' : '' }} value="Malawi"> {{ __('Malawi') }}</option>
        <option {{ Auth::user()->billing_country == 'Malaysia' ? 'selected' : '' }} value="Malaysia"> {{ __('Malaysia ') }}</option>
        <option {{ Auth::user()->billing_country == 'Maldives' ? 'selected' : '' }} value="Maldives"> {{ __('Maldives ') }}</option>
        <option {{ Auth::user()->billing_country == 'Mali' ? 'selected' : '' }} value="Mali">{{ __('Mali') }}</option>
        <option {{ Auth::user()->billing_country == 'Malta' ? 'selected' : '' }} value="Malta">{{ __('Malta') }}</option>
        <option {{ Auth::user()->billing_country == 'Marshall Islands' ? 'selected' : '' }} value="Marshall Islands"> {{ __(' Marshall Islands') }}</option>
        <option {{ Auth::user()->billing_country == 'Martinique' ? 'selected' : '' }} value="Martinique"> {{ __('Martinique ') }}</option>
        <option {{ Auth::user()->billing_country == 'Mauritania' ? 'selected' : '' }} value="Mauritania"> {{ __('Mauritania ') }}</option>
        <option {{ Auth::user()->billing_country == 'Mauritius' ? 'selected' : '' }} value="Mauritius"> {{ __('Mauritius ') }}</option>
        <option {{ Auth::user()->billing_country == 'Mayotte' ? 'selected' : '' }} value="Mayotte"> {{ __('Mayotte') }}</option>
        <option {{ Auth::user()->billing_country == 'Mexico' ? 'selected' : '' }} value="Mexico"> {{ __('Mexico') }}</option>
        <option {{ Auth::user()->billing_country == 'Micronesia, Federated States of' ? 'selected' : '' }} value="Micronesia, Federated States of"> {{ __('Micronesia, Federated States of') }}</option>
        <option {{ Auth::user()->billing_country == 'Moldova, Republic of' ? 'selected' : '' }} value="Moldova, Republic of">{{ __('Moldova, Republic of') }}</option>
        <option {{ Auth::user()->billing_country == 'Monaco' ? 'selected' : '' }} value="Monaco"> {{ __('Monaco') }}</option>
        <option {{ Auth::user()->billing_country == 'Mongolia' ? 'selected' : '' }} value="Mongolia"> {{ __('Mongolia ') }}</option>
        <option {{ Auth::user()->billing_country == 'Montenegro' ? 'selected' : '' }} value="Montenegro"> {{ __('Montenegro ') }}</option>
        <option {{ Auth::user()->billing_country == 'Montserrat' ? 'selected' : '' }} value="Montserrat"> {{ __('Montserrat ') }}</option>
        <option {{ Auth::user()->billing_country == 'Morocco' ? 'selected' : '' }} value="Morocco"> {{ __('Morocco') }}</option>
        <option {{ Auth::user()->billing_country == 'Mozambique' ? 'selected' : '' }} value="Mozambique"> {{ __('Mozambique ') }}</option>
        <option {{ Auth::user()->billing_country == 'Myanmar' ? 'selected' : '' }} value="Myanmar"> {{ __('Myanmar') }}</option>
        <option {{ Auth::user()->billing_country == 'Namibia' ? 'selected' : '' }} value="Namibia"> {{ __('Namibia') }}</option>
        <option {{ Auth::user()->billing_country == 'Nauru' ? 'selected' : '' }} value="Nauru">{{ __('Nauru') }}</option>
        <option {{ Auth::user()->billing_country == 'Nepal' ? 'selected' : '' }} value="Nepal">{{ __('Nepal') }}</option>
        <option {{ Auth::user()->billing_country == 'Netherlands' ? 'selected' : '' }} value="Netherlands"> {{ __(' Netherlands') }}</option>
        <option {{ Auth::user()->billing_country == 'Netherlands Antilles' ? 'selected' : '' }} value="Netherlands Antilles">{{ __('Netherlands Antilles') }}</option>
        <option {{ Auth::user()->billing_country == 'New Caledonia' ? 'selected' : '' }} value="New Caledonia"> {{ __('New Caledonia') }}</option>
        <option {{ Auth::user()->billing_country == 'New Zealand' ? 'selected' : '' }} value="New Zealand"> {{ __('New Zealand') }}</option>
        <option {{ Auth::user()->billing_country == 'Nicaragua' ? 'selected' : '' }} value="Nicaragua"> {{ __('Nicaragua ') }}</option>
        <option {{ Auth::user()->billing_country == 'Niger' ? 'selected' : '' }} value="Niger">{{ __('Niger') }}</option>
        <option {{ Auth::user()->billing_country == 'Nigeria' ? 'selected' : '' }} value="Nigeria"> {{ __('Nigeria') }}</option>
        <option {{ Auth::user()->billing_country == 'Niue' ? 'selected' : '' }} value="Niue">{{ __('Niue') }}</option>
        <option {{ Auth::user()->billing_country == 'Norfolk Island' ? 'selected' : '' }} value="Norfolk Island"> {{ __(' Norfolk Island') }}</option>
        <option {{ Auth::user()->billing_country == 'Northern Mariana Islands' ? 'selected' : '' }} value="Northern Mariana Islands">{{ __('Northern Mariana Islands') }}</option>
        <option {{ Auth::user()->billing_country == 'Norway' ? 'selected' : '' }} value="Norway"> {{ __('Norway') }}</option>
        <option {{ Auth::user()->billing_country == 'Oman' ? 'selected' : '' }} value="Oman">{{ __('Oman') }}</option>
        <option {{ Auth::user()->billing_country == 'Pakistan' ? 'selected' : '' }} value="Pakistan"> {{ __('Pakistan ') }}</option>
        <option {{ Auth::user()->billing_country == 'Palau' ? 'selected' : '' }} value="Palau">{{ __('Palau') }}</option>
        <option {{ Auth::user()->billing_country == 'Palestinian Territory, Occupied' ? 'selected' : '' }} value="Palestinian Territory, Occupied"> {{ __('Palestinian Territory, Occupied') }}</option>
        <option {{ Auth::user()->billing_country == 'Panama' ? 'selected' : '' }} value="Panama"> {{ __('Panama') }}</option>
        <option {{ Auth::user()->billing_country == 'Papua New Guinea' ? 'selected' : '' }} value="Papua New Guinea"> {{ __(' Papua New Guinea') }}</option>
        <option {{ Auth::user()->billing_country == 'Paraguay' ? 'selected' : '' }} value="Paraguay"> {{ __('Paraguay ') }}</option>
        <option {{ Auth::user()->billing_country == 'Peru' ? 'selected' : '' }} value="Peru">{{ __('Peru') }}</option>
        <option {{ Auth::user()->billing_country == 'Philippines' ? 'selected' : '' }} value="Philippines"> {{ __(' Philippines') }}</option>
        <option {{ Auth::user()->billing_country == 'Pitcairn' ? 'selected' : '' }} value="Pitcairn"> {{ __('Pitcairn ') }}</option>
        <option {{ Auth::user()->billing_country == 'Poland' ? 'selected' : '' }} value="Poland"> {{ __('Poland') }}</option>
        <option {{ Auth::user()->billing_country == 'Portugal' ? 'selected' : '' }} value="Portugal"> {{ __('Portugal ') }}</option>
        <option {{ Auth::user()->billing_country == 'Puerto Rico' ? 'selected' : '' }} value="Puerto Rico"> {{ __('Puerto Rico') }}</option>
        <option {{ Auth::user()->billing_country == 'Qatar' ? 'selected' : '' }} value="Qatar">{{ __('Qatar') }}</option>
        <option {{ Auth::user()->billing_country == 'Reunion' ? 'selected' : '' }} value="Reunion"> {{ __('Reunion') }}</option>
        <option {{ Auth::user()->billing_country == 'Romania' ? 'selected' : '' }} value="Romania"> {{ __('Romania') }}</option>
        <option {{ Auth::user()->billing_country == 'Russian Federation' ? 'selected' : '' }} value="Russian Federation">{{ __('Russian Federation') }}</option>
        <option {{ Auth::user()->billing_country == 'Rwanda' ? 'selected' : '' }} value="Rwanda"> {{ __('Rwanda') }}</option>
        <option {{ Auth::user()->billing_country == 'Saint Helena' ? 'selected' : '' }} value="Saint Helena"> {{ __('Saint Helena') }}</option>
        <option {{ Auth::user()->billing_country == 'Saint Kitts and Nevis' ? 'selected' : '' }} value="Saint Kitts and Nevis">{{ __('Saint Kitts and Nevis') }}</option>
        <option {{ Auth::user()->billing_country == 'Saint Lucia' ? 'selected' : '' }} value="Saint Lucia"> {{ __('Saint Lucia') }}</option>
        <option {{ Auth::user()->billing_country == 'Saint Pierre and Miquelon' ? 'selected' : '' }} value="Saint Pierre and Miquelon">{{ __('Saint Pierre and Miquelon') }}</option>
        <option {{ Auth::user()->billing_country == 'Saint Vincent and The Grenadines' ? 'selected' : '' }} value="Saint Vincent and The Grenadines"> {{ __('Saint Vincent and The Grenadines') }}</option>
        <option {{ Auth::user()->billing_country == 'Samoa' ? 'selected' : '' }} value="Samoa">{{ __('Samoa') }}</option>
        <option {{ Auth::user()->billing_country == 'San Marino' ? 'selected' : '' }} value="San Marino"> {{ __('San Marino ') }}</option>
        <option {{ Auth::user()->billing_country == 'Sao Tome and Principe' ? 'selected' : '' }} value="Sao Tome and Principe">{{ __('Sao Tome and Principe') }}</option>
        <option {{ Auth::user()->billing_country == 'Saudi Arabia' ? 'selected' : '' }} value="Saudi Arabia"> {{ __('Saudi Arabia') }}</option>
        <option {{ Auth::user()->billing_country == 'Senegal' ? 'selected' : '' }} value="Senegal"> {{ __('Senegal') }}</option>
        <option {{ Auth::user()->billing_country == 'Serbia' ? 'selected' : '' }} value="Serbia"> {{ __('Serbia') }}</option>
        <option {{ Auth::user()->billing_country == 'Seychelles' ? 'selected' : '' }} value="Seychelles"> {{ __('Seychelles ') }}</option>
        <option {{ Auth::user()->billing_country == 'Sierra Leone' ? 'selected' : '' }} value="Sierra Leone"> {{ __('Sierra Leone') }}</option>
        <option {{ Auth::user()->billing_country == 'Singapore' ? 'selected' : '' }} value="Singapore"> {{ __('Singapore ') }}</option>
        <option {{ Auth::user()->billing_country == 'Slovakia' ? 'selected' : '' }} value="Slovakia"> {{ __('Slovakia ') }}</option>
        <option {{ Auth::user()->billing_country == 'Slovenia' ? 'selected' : '' }} value="Slovenia"> {{ __('Slovenia ') }}</option>
        <option {{ Auth::user()->billing_country == 'Solomon Islands' ? 'selected' : '' }} value="Solomon Islands"> {{ __(' Solomon Islands') }}</option>
        <option {{ Auth::user()->billing_country == 'Somalia' ? 'selected' : '' }} value="Somalia"> {{ __('Somalia') }}</option>
        <option {{ Auth::user()->billing_country == 'South Africa' ? 'selected' : '' }} value="South Africa"> {{ __('South Africa') }}</option>
        <option {{ Auth::user()->billing_country == 'South Georgia and The South Sandwich Islands' ? 'selected' : '' }} value="South Georgia and The South Sandwich Islands"> {{ __('South Georgia and The South Sandwich Islands') }}</option>
        <option {{ Auth::user()->billing_country == 'Spain' ? 'selected' : '' }} value="Spain">{{ __('Spain') }}</option>
        <option {{ Auth::user()->billing_country == 'Sri Lanka' ? 'selected' : '' }} value="Sri Lanka"> {{ __('Sri Lanka ') }}</option>
        <option {{ Auth::user()->billing_country == 'Sudan' ? 'selected' : '' }} value="Sudan">{{ __('Sudan') }}</option>
        <option {{ Auth::user()->billing_country == 'Suriname' ? 'selected' : '' }} value="Suriname"> {{ __('Suriname ') }}</option>
        <option {{ Auth::user()->billing_country == 'Svalbard and Jan Mayen' ? 'selected' : '' }} value="Svalbard and Jan Mayen">{{ __('Svalbard and Jan Mayen') }}</option>
        <option {{ Auth::user()->billing_country == 'Swaziland' ? 'selected' : '' }} value="Swaziland"> {{ __('Swaziland ') }}</option>
        <option {{ Auth::user()->billing_country == 'Sweden' ? 'selected' : '' }} value="Sweden"> {{ __('Sweden') }}</option>
        <option {{ Auth::user()->billing_country == 'Switzerland' ? 'selected' : '' }} value="Switzerland"> {{ __(' Switzerland') }}</option>
        <option {{ Auth::user()->billing_country == 'Syrian Arab Republic' ? 'selected' : '' }} value="Syrian Arab Republic">{{ __('Syrian Arab Republic') }}</option>
        <option {{ Auth::user()->billing_country == 'Taiwan' ? 'selected' : '' }} value="Taiwan"> {{ __('Taiwan') }}</option>
        <option {{ Auth::user()->billing_country == 'Tajikistan' ? 'selected' : '' }} value="Tajikistan"> {{ __('Tajikistan ') }}</option>
        <option {{ Auth::user()->billing_country == 'Tanzania, United Republic of' ? 'selected' : '' }} value="Tanzania, United Republic of"> {{ __('Tanzania, United Republic of ') }}</option>
        <option {{ Auth::user()->billing_country == 'Thailand' ? 'selected' : '' }} value="Thailand"> {{ __('Thailand ') }}</option>
        <option {{ Auth::user()->billing_country == 'Timor-leste' ? 'selected' : '' }} value="Timor-leste"> {{ __(' Timor-leste') }}</option>
        <option {{ Auth::user()->billing_country == 'Togo' ? 'selected' : '' }} value="Togo">{{ __('Togo') }}</option>
        <option {{ Auth::user()->billing_country == 'Tokelau' ? 'selected' : '' }} value="Tokelau"> {{ __('Tokelau') }}</option>
        <option {{ Auth::user()->billing_country == 'Tonga' ? 'selected' : '' }} value="Tonga">{{ __('Tonga') }}</option>
        <option {{ Auth::user()->billing_country == 'Trinidad and Tobago' ? 'selected' : '' }} value="Trinidad and Tobago">{{ __('Trinidad and Tobago') }}</option>
        <option {{ Auth::user()->billing_country == 'Tunisia' ? 'selected' : '' }} value="Tunisia"> {{ __('Tunisia') }}</option>
        <option {{ Auth::user()->billing_country == 'Turkey' ? 'selected' : '' }} value="Turkey"> {{ __('Turkey') }}</option>
        <option {{ Auth::user()->billing_country == 'Turkmenistan' ? 'selected' : '' }} value="Turkmenistan"> {{ __(' Turkmenistan') }}</option>
        <option {{ Auth::user()->billing_country == 'Turks and Caicos Islands' ? 'selected' : '' }} value="Turks and Caicos Islands">{{ __('Turks and Caicos Islands') }}</option>
        <option {{ Auth::user()->billing_country == 'Tuvalu' ? 'selected' : '' }} value="Tuvalu"> {{ __('Tuvalu') }}</option>
        <option {{ Auth::user()->billing_country == 'Uganda' ? 'selected' : '' }} value="Uganda"> {{ __('Uganda') }}</option>
        <option {{ Auth::user()->billing_country == 'Ukraine' ? 'selected' : '' }} value="Ukraine"> {{ __('Ukraine') }}</option>
        <option {{ Auth::user()->billing_country == 'United Arab Emirates' ? 'selected' : '' }} value="United Arab Emirates">{{ __('United Arab Emirates') }}</option>
        <option {{ Auth::user()->billing_country == 'United Kingdom' ? 'selected' : '' }} value="United Kingdom"> {{ __(' United Kingdom') }}</option>
        <option {{ Auth::user()->billing_country == 'United States' ? 'selected' : '' }} value="United States"> {{ __('United States') }}</option>
        <option {{ Auth::user()->billing_country == 'United States Minor Outlying Islands' ? 'selected' : '' }} value="United States Minor Outlying Islands"> {{ __('United States Minor Outlying Islands') }}</option>
        <option {{ Auth::user()->billing_country == 'Uruguay' ? 'selected' : '' }} value="Uruguay"> {{ __('Uruguay') }}</option>
        <option {{ Auth::user()->billing_country == 'Uzbekistan' ? 'selected' : '' }} value="Uzbekistan"> {{ __('Uzbekistan ') }}</option>
        <option {{ Auth::user()->billing_country == 'Vanuatu' ? 'selected' : '' }} value="Vanuatu"> {{ __('Vanuatu') }}</option>
        <option {{ Auth::user()->billing_country == 'Venezuela' ? 'selected' : '' }} value="Venezuela"> {{ __('Venezuela ') }}</option>
        <option {{ Auth::user()->billing_country == 'Viet Nam' ? 'selected' : '' }} value="Viet Nam"> {{ __('Viet Nam ') }}</option>
        <option {{ Auth::user()->billing_country == 'Virgin Islands, British' ? 'selected' : '' }} value="Virgin Islands, British">{{ __('Virgin Islands, British') }}</option>
        <option {{ Auth::user()->billing_country == 'Virgin Islands, U.S.' ? 'selected' : '' }} value="Virgin Islands, U.S.">{{ __('Virgin Islands, U.S.') }}</option>
        <option {{ Auth::user()->billing_country == 'Wallis and Futuna' ? 'selected' : '' }} value="Wallis and Futuna">{{ __('Wallis and Futuna') }}</option>
        <option {{ Auth::user()->billing_country == 'Western Sahara' ? 'selected' : '' }} value="Western Sahara"> {{ __(' Western Sahara') }}</option>
        <option {{ Auth::user()->billing_country == 'Yemen' ? 'selected' : '' }} value="Yemen">{{ __('Yemen') }}</option>
        <option {{ Auth::user()->billing_country == 'Zambia' ? 'selected' : '' }} value="Zambia"> {{ __('Zambia') }}</option>
        <option {{ Auth::user()->billing_country == 'Zimbabwe' ? 'selected' : '' }} value="Zimbabwe"> {{ __('Zimbabwe ') }}</option>
    </select>
</div>