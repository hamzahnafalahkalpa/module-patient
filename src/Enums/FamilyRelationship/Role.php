<?php

namespace Hanafalah\ModulePatient\Enums\FamilyRelationship;

enum Role: int
{
    case ADIK_LAKI_LAKI = 1;
    case ADIK_PEREMPUAN = 2;
    case ANAK = 3;
    case AYAH = 4;
    case IBU = 5;
    case ISTRI = 6;
    case KAKAK_LAKI_LAKI = 7;
    case KAKAK_PEREMPUAN = 8;
    case KAKEK = 9;
    case KERABAT_DEKAT = 10;
    case KERABAT_JAUH = 11;
    case NENEK = 12;
    case SAUDARA = 13;
    case SEPUPU = 14;
    case SUAMI = 15;
    case TETANGGA = 16;
}
