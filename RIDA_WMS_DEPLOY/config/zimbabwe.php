<?php

// Reference data for Zimbabwe's provinces, representative districts, and approximate
// geographic bounding box, used by database seeders to keep sample records realistic.
return [
    'bounds' => [
        'lat_min' => -22.4,
        'lat_max' => -15.6,
        'lng_min' => 25.0,
        'lng_max' => 33.1,
    ],

    'provinces' => [
        'Harare' => ['Harare'],
        'Bulawayo' => ['Bulawayo'],
        'Manicaland' => ['Mutare', 'Chimanimani', 'Chipinge', 'Makoni', 'Nyanga'],
        'Mashonaland Central' => ['Bindura', 'Guruve', 'Mazowe', 'Mount Darwin', 'Shamva'],
        'Mashonaland East' => ['Marondera', 'Murehwa', 'Mutoko', 'Goromonzi', 'Wedza'],
        'Mashonaland West' => ['Chinhoyi', 'Kariba', 'Karoi', 'Chegutu', 'Kadoma'],
        'Masvingo' => ['Masvingo', 'Chiredzi', 'Bikita', 'Gutu', 'Zaka'],
        'Matabeleland North' => ['Hwange', 'Lupane', 'Binga', 'Nkayi', 'Tsholotsho'],
        'Matabeleland South' => ['Gwanda', 'Beitbridge', 'Bulilima', 'Insiza', 'Matobo'],
        'Midlands' => ['Gweru', 'Kwekwe', 'Zvishavane', 'Shurugwi', 'Gokwe'],
    ],
];
