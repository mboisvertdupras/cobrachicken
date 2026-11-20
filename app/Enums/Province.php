<?php

namespace App\Enums;

enum Province: string
{
    case Alberta = 'AB';
    case BritishColumbia = 'BC';
    case Manitoba = 'MB';
    case NewBrunswick = 'NB';
    case NewfoundlandAndLabrador = 'NL';
    case NovaScotia = 'NS';
    case NorthwestTerritories = 'NT';
    case Nunavut = 'NU';
    case Ontario = 'ON';
    case PrinceEdwardIsland = 'PE';
    case Quebec = 'QC';
    case Saskatchewan = 'SK';
    case Yukon = 'YT';

    public function label(): string
    {
        return match ($this) {
            self::Alberta => 'Alberta',
            self::BritishColumbia => 'British Columbia',
            self::Manitoba => 'Manitoba',
            self::NewBrunswick => 'New Brunswick',
            self::NewfoundlandAndLabrador => 'Newfoundland and Labrador',
            self::NovaScotia => 'Nova Scotia',
            self::NorthwestTerritories => 'Northwest Territories',
            self::Nunavut => 'Nunavut',
            self::Ontario => 'Ontario',
            self::PrinceEdwardIsland => 'Prince Edward Island',
            self::Quebec => 'Quebec',
            self::Saskatchewan => 'Saskatchewan',
            self::Yukon => 'Yukon',
        };
    }
}
