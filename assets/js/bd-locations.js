/* Bangladesh Districts and Thanas */
var BD_LOCATIONS = {
    "Dhaka": [
        "Dhamrai", "Dohar", "Keraniganj", "Nawabganj", "Savar",
        "Demra", "Gulshan", "Hazaribagh", "Kafrul", "Kadamtali",
        "Kamrangirchar", "Khilgaon", "Khilkhet", "Lalbagh", "Mirpur",
        "Mohammadpur", "Motijheel", "Pallabi", "Ramna", "Rayer Bazar",
        "Sabujbagh", "Shah Ali", "Shahbagh", "Sher-e-Bangla Nagar",
        "Shyampur", "Sutrapur", "Tejgaon", "Turag", "Uttara",
        "Wari", "Adabor", "Banani", "Bangshal", "Biman Bandar",
        "Cantonment", "Chackbazar", "Dakshinkhan", "Darus Salam",
        "Gendaria", "Jatrabari"
    ],
    "Chittagong": [
        "Anwara", "Banshkhali", "Boalkhali", "Chandanaish", "Fatikchhari",
        "Hathazari", "Karnaphuli", "Lohagara", "Mirsharai", "Patiya",
        "Rangunia", "Raozan", "Sandwip", "Satkania", "Sitakunda",
        "Akbar Shah", "Bakalia", "Bayazid", "Chandgaon", "Chawkbazar",
        "Double Mooring", "Eidgah", "Halishahar", "Khulshi", "Kotwali",
        "Pahartali", "Panchlaish", "Patenga", "Sadarghat"
    ],
    "Rajshahi": [
        "Bagha", "Bagmara", "Charghat", "Durgapur", "Godagari",
        "Mohanpur", "Paba", "Puthia", "Tanore",
        "Boalia", "Matihar", "Rajpara", "Shah Makhdum"
    ],
    "Khulna": [
        "Batiaghata", "Dacope", "Dighalia", "Dumuria", "Koyra",
        "Paikgachha", "Phultala", "Rupsa", "Terokhada",
        "Daulatpur", "Khan Jahan Ali", "Khalishpur", "Kotwali",
        "Sonadanga"
    ],
    "Sylhet": [
        "Balaganj", "Beanibazar", "Bishwanath", "Companiganj",
        "Fenchuganj", "Golapganj", "Gowainghat", "Jaintiapur",
        "Kanaighat", "Osmani Nagar", "South Surma", "Zakiganj",
        "Bayanibazar", "Kotwali", "Moglabazar", "Shah Paran"
    ],
    "Barisal": [
        "Agailjhara", "Babuganj", "Bakerganj", "Banaripara",
        "Gaurnadi", "Hizla", "Mehendiganj", "Muladi", "Wazirpur",
        "Airport", "Bandar", "Barisal Sadar", "Kawnia"
    ],
    "Rangpur": [
        "Badarganj", "Gangachara", "Kaunia", "Mithapukur",
        "Pirgachha", "Pirganj", "Taraganj",
        "Kotwali", "Harbanga", "Mahiganj", "Tajhat"
    ],
    "Mymensingh": [
        "Bhaluka", "Dhobaura", "Fulbaria", "Gaffargaon",
        "Gauripur", "Haluaghat", "Ishwarganj", "Muktagachha",
        "Nandail", "Phulpur", "Trishal",
        "Kotwali", "Mymensing Sadar"
    ],
    "Gazipur": [
        "Gazipur Sadar", "Kaliakair", "Kaliganj", "Kapasia",
        "Sreepur",
        "Joydebpur", "Tongi"
    ],
    "Narayanganj": [
        "Araihazar", "Bandar", "Narayanganj Sadar", "Rupganj", "Sonargaon",
        "Fatullah", "Siddhirganj"
    ],
    "Tangail": [
        "Basail", "Bhuapur", "Delduar", "Dhanbari", "Ghatail",
        "Gopalpur", "Kalihati", "Madhupur", "Mirzapur", "Nagarpur",
        "Sakhipur", "Tangail Sadar"
    ],
    "Cumilla": [
        "Barura", "Brahmanpara", "Burichang", "Chandina", "Chauddagram",
        "Daudkandi", "Debidwar", "Homna", "Laksam", "Lalmai",
        "Meghna", "Muradnagar", "Nangalkot", "Titas",
        "Adarsha Sadar", "Kotwali", "Sadar South"
    ],
    "Noakhali": [
        "Begumganj", "Chatkhil", "Companiganj", "Hatiya", "Kabirhat",
        "Senbagh", "Sonaimuri", "Subarnachar",
        "Sudharam", "Noakhali Sadar"
    ],
    "Feni": [
        "Chhagalnaiya", "Daganbhuiyan", "Feni Sadar", "Parshuram",
        "Sonagazi", "Fulgazi"
    ],
    "Brahmanbaria": [
        "Akhaura", "Ashuganj", "Bancharampur", "Bijoynagar",
        "Brahmanbaria Sadar", "Kasba", "Nabinagar", "Nasirnagar", "Sarail"
    ],
    "Chandpur": [
        "Chandpur Sadar", "Faridganj", "Haimchar", "Haziganj",
        "Kachua", "Matlab Dakshin", "Matlab Uttar", "Shahrasti"
    ],
    "Lakshmipur": [
        "Kamalnagar", "Lakshmipur Sadar", "Ramganj", "Ramgati", "Roypur"
    ],
    "Cox's Bazar": [
        "Chakaria", "Cox's Bazar Sadar", "Kutubdia", "Maheshkhali",
        "Pekua", "Ramu", "Teknaf", "Ukhiya"
    ],
    "Bandarban": [
        "Ali Kadam", "Bandarban Sadar", "Lama", "Naikhongchhari",
        "Rowangchhari", "Ruma", "Thanchi"
    ],
    "Rangamati": [
        "Bagaichhari", "Barkal", "Belaichhari", "Juraichhari",
        "Kaptai", "Kaukhali", "Langadu", "Naniarchar",
        "Rajasthali", "Rangamati Sadar"
    ],
    "Khagrachhari": [
        "Dighinala", "Khagrachhari Sadar", "Lakshmichhari", "Mahalchhari",
        "Manikchhari", "Matiranga", "Panchhari", "Ramgarh"
    ],
    "Jessore": [
        "Abhaynagar", "Bagherpara", "Chaugachha", "Jhikargachha",
        "Keshabpur", "Manirampur", "Sharsha", "Jessore Sadar"
    ],
    "Satkhira": [
        "Assasuni", "Debhata", "Kalaroa", "Kaliganj",
        "Satkhira Sadar", "Shyamnagar", "Tala"
    ],
    "Bagerhat": [
        "Bagerhat Sadar", "Chitalmari", "Fakirhat", "Kachua",
        "Mollahat", "Mongla", "Morrelganj", "Rampal", "Sarankhola"
    ],
    "Narail": [
        "Kalia", "Lohagara", "Narail Sadar"
    ],
    "Magura": [
        "Magura Sadar", "Mohammadpur", "Shalikha", "Sreepur"
    ],
    "Faridpur": [
        "Alfadanga", "Bhanga", "Boalmari", "Charbhadrasan",
        "Faridpur Sadar", "Madhukhali", "Nagarkanda", "Sadarpur", "Saltha"
    ],
    "Gopalganj": [
        "Gopalganj Sadar", "Kashiani", "Kotalipara", "Muksudpur", "Tungipara"
    ],
    "Madaripur": [
        "Kalkini", "Madaripur Sadar", "Rajoir", "Shibchar"
    ],
    "Shariatpur": [
        "Bhedarganj", "Damudya", "Gosairhat", "Naria",
        "Shariatpur Sadar", "Zajira", "Zanjira"
    ],
    "Munshiganj": [
        "Gazaria", "Lohajang", "Munshiganj Sadar",
        "Sirajdikhan", "Sreenagar", "Tongibari"
    ],
    "Narsingdi": [
        "Belabo", "Monohardi", "Narsingdi Sadar",
        "Palash", "Raipura", "Shibpur"
    ],
    "Manikganj": [
        "Daulatpur", "Ghior", "Harirampur", "Manikganj Sadar",
        "Saturia", "Shivalaya", "Singair"
    ],
    "Kishoreganj": [
        "Austagram", "Bajitpur", "Bhairab", "Hossainpur",
        "Itna", "Karimganj", "Katiadi", "Kishoreganj Sadar",
        "Kuliarchar", "Mithamain", "Nikli", "Pakundia", "Tarail"
    ],
    "Netrokona": [
        "Atpara", "Barhatta", "Durgapur", "Kendua",
        "Khaliajuri", "Kalmakanda", "Madan", "Mohanganj",
        "Netrokona Sadar", "Purbadhala"
    ],
    "Jamalpur": [
        "Bakshiganj", "Dewanganj", "Islampur", "Jamalpur Sadar",
        "Madarganj", "Melandaha", "Sarishabari"
    ],
    "Sherpur": [
        "Jhenaigati", "Nakla", "Nalitabari", "Sherpur Sadar", "Sreebardi"
    ],
    "Bogra": [
        "Adamdighi", "Bogra Sadar", "Dhunat", "Dupchanchia",
        "Gabtali", "Kahaloo", "Nandigram", "Sariakandi",
        "Shajahanpur", "Sherpur", "Shibganj", "Sonatala"
    ],
    "Naogaon": [
        "Atrai", "Badalgachhi", "Dhamoirhat", "Manda",
        "Mahadebpur", "Naogaon Sadar", "Niamatpur", "Patnitala",
        "Porsha", "Raninagar", "Sapahar"
    ],
    "Natore": [
        "Bagatipara", "Baraigram", "Gurudaspur", "Lalpur",
        "Natore Sadar", "Singra"
    ],
    "Chapai Nawabganj": [
        "Bholahat", "Chapai Nawabganj Sadar", "Gomastapur",
        "Nachole", "Shibganj"
    ],
    "Pabna": [
        "Atgharia", "Bera", "Bhangura", "Chatmohar",
        "Faridpur", "Ishwardi", "Pabna Sadar", "Santhia", "Sujanagar"
    ],
    "Sirajganj": [
        "Belkuchi", "Chauhali", "Kamarkhanda", "Kazipur",
        "Raiganj", "Shahjadpur", "Sirajganj Sadar",
        "Tarash", "Ullahpara"
    ],
    "Joypurhat": [
        "Akkelpur", "Joypurhat Sadar", "Kalai", "Khetlal", "Panchbibi"
    ],
    "Dinajpur": [
        "Birampur", "Birganj", "Biral", "Bochaganj",
        "Chirirbandar", "Dinajpur Sadar", "Fulbari",
        "Ghoraghat", "Hakimpur", "Kaharole",
        "Khansama", "Nawabganj", "Parbatipur"
    ],
    "Gaibandha": [
        "Fulchhari", "Gaibandha Sadar", "Gobindaganj",
        "Palashbari", "Sadullapur", "Saghata", "Sundarganj"
    ],
    "Kurigram": [
        "Bhurungamari", "Char Rajibpur", "Chilmari",
        "Kurigram Sadar", "Nageshwari", "Phulbari",
        "Rajibpur", "Rajarhat", "Rowmari", "Ulipur"
    ],
    "Lalmonirhat": [
        "Aditmari", "Hatibandha", "Kaliganj",
        "Lalmonirhat Sadar", "Patgram"
    ],
    "Nilphamari": [
        "Dimla", "Domar", "Jaldhaka", "Kishoreganj",
        "Nilphamari Sadar", "Saidpur"
    ],
    "Panchagarh": [
        "Atwari", "Boda", "Debiganj", "Panchagarh Sadar", "Tetulia"
    ],
    "Thakurgaon": [
        "Baliadangi", "Haripur", "Pirganj", "Ranisankail", "Thakurgaon Sadar"
    ],
    "Habiganj": [
        "Ajmiriganj", "Baniachong", "Bahubal", "Chunarughat",
        "Habiganj Sadar", "Lakhai", "Madhabpur", "Nabiganj", "Shaistaganj"
    ],
    "Moulvibazar": [
        "Barlekha", "Juri", "Kamalganj", "Kulaura",
        "Moulvibazar Sadar", "Rajnagar", "Sreemangal"
    ],
    "Sunamganj": [
        "Bishwamvarpur", "Chhatak", "Derai", "Dharampasha",
        "Dowarabazar", "Jagannathpur", "Jamalganj", "Sullah",
        "Sunamganj Sadar", "Tahirpur"
    ],
    "Pirojpur": [
        "Bhandaria", "Kawkhali", "Mathbaria", "Nazirpur",
        "Pirojpur Sadar", "Nesarabad", "Zianagar"
    ],
    "Jhalokati": [
        "Jhalokati Sadar", "Kanthalia", "Nalchity", "Rajapur"
    ],
    "Bhola": [
        "Bhola Sadar", "Borhanuddin", "Charfasson", "Daulatkhan",
        "Lalmohan", "Manpura", "Tazumuddin"
    ],
    "Patuakhali": [
        "Bauphal", "Dashmina", "Dumki", "Galachipa",
        "Kalapara", "Mirzaganj", "Patuakhali Sadar", "Rangabali"
    ],
    "Barguna": [
        "Amtali", "Bamna", "Barguna Sadar", "Betagi", "Patharghata", "Taltali"
    ],
    "Meherpur": [
        "Gangni", "Meherpur Sadar", "Mujibnagar"
    ],
    "Chuadanga": [
        "Alamdanga", "Chuadanga Sadar", "Damurhuda", "Jibannagar"
    ],
    "Kushtia": [
        "Bheramara", "Daulatpur", "Khoksa", "Kumarkhali",
        "Kushtia Sadar", "Mirpur"
    ],
    "Jhenaidah": [
        "Harinakunda", "Jhenaidah Sadar", "Kaliganj",
        "Kotchandpur", "Maheshpur", "Shailkupa"
    ],
    "Magura": [
        "Magura Sadar", "Mohammadpur", "Shalikha", "Sreepur"
    ],
    "Jashore": [
        "Abhaynagar", "Bagherpara", "Chaugachha", "Jhikargachha",
        "Keshabpur", "Manirampur", "Sharsha", "Jashore Sadar"
    ],
    "Khulna": [
        "Batiaghata", "Dacope", "Dighalia", "Dumuria", "Koyra",
        "Paikgachha", "Phultala", "Rupsa", "Terokhada",
        "Daulatpur", "Khan Jahan Ali", "Khalishpur", "Kotwali", "Sonadanga"
    ],
    "Narail": [
        "Kalia", "Lohagara", "Narail Sadar"
    ]
};
