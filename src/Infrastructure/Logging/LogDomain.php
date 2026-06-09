<?php

enum LogDomain: string
{
    case Runtime = 'PHP';
    case Database = 'MongoDB';
    case System = 'YanoDASH';
    case Security = 'Security';
    case API = 'YDAPI';
    case Other = 'Other';
}
