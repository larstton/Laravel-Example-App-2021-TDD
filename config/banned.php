<?php

//configuration file to set banned domains for CloudRadar
//supports exact matches and wildcards
return [
    //banned domains for monitoring
    'domains' => [
        '*.glitch.me*',
    ],
    //banned e-mails for registration
    'emails' => [
        '*@mozej.com',
    ],
];
