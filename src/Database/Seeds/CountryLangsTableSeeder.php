<?php
namespace MarghoobSuleman\HashtagCms\Database\Seeds;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CountryLangsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $table_name = 'country_langs';
        $date = date('Y-m-d H:i:s');

        if(DB::table($table_name)->get()->count() == 0) {
            DB::table($table_name)->insert(
                [
                    array('country_id' => '1','lang_id' => '1','name' => 'Germany','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '2','lang_id' => '1','name' => 'Austria','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '3','lang_id' => '1','name' => 'Belgium','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '4','lang_id' => '1','name' => 'Canada','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '5','lang_id' => '1','name' => 'China','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '6','lang_id' => '1','name' => 'Spain','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '7','lang_id' => '1','name' => 'Finland','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '8','lang_id' => '1','name' => 'France','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '9','lang_id' => '1','name' => 'Greece','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '10','lang_id' => '1','name' => 'Italy','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '11','lang_id' => '1','name' => 'Japan','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '12','lang_id' => '1','name' => 'Luxemburg','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '13','lang_id' => '1','name' => 'Netherlands','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '14','lang_id' => '1','name' => 'Poland','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '15','lang_id' => '1','name' => 'Portugal','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '16','lang_id' => '1','name' => 'Czech Republic','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '17','lang_id' => '1','name' => 'United Kingdom','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '18','lang_id' => '1','name' => 'Sweden','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '19','lang_id' => '1','name' => 'Switzerland','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '20','lang_id' => '1','name' => 'Denmark','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '21','lang_id' => '1','name' => 'United States','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '22','lang_id' => '1','name' => 'HongKong','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '23','lang_id' => '1','name' => 'Norway','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '24','lang_id' => '1','name' => 'Australia','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '25','lang_id' => '1','name' => 'Singapore','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '26','lang_id' => '1','name' => 'Ireland','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '27','lang_id' => '1','name' => 'New Zealand','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '28','lang_id' => '1','name' => 'South Korea','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '29','lang_id' => '1','name' => 'Israel','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '30','lang_id' => '1','name' => 'South Africa','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '31','lang_id' => '1','name' => 'Nigeria','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '32','lang_id' => '1','name' => 'Ivory Coast','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '33','lang_id' => '1','name' => 'Togo','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '34','lang_id' => '1','name' => 'Bolivia','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '35','lang_id' => '1','name' => 'Mauritius','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '36','lang_id' => '1','name' => 'Romania','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '37','lang_id' => '1','name' => 'Slovakia','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '38','lang_id' => '1','name' => 'Algeria','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '39','lang_id' => '1','name' => 'American Samoa','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '40','lang_id' => '1','name' => 'Andorra','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '41','lang_id' => '1','name' => 'Angola','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '42','lang_id' => '1','name' => 'Anguilla','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '43','lang_id' => '1','name' => 'Antigua and Barbuda','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '44','lang_id' => '1','name' => 'Argentina','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '45','lang_id' => '1','name' => 'Armenia','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '46','lang_id' => '1','name' => 'Aruba','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '47','lang_id' => '1','name' => 'Azerbaijan','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '48','lang_id' => '1','name' => 'Bahamas','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '49','lang_id' => '1','name' => 'Bahrain','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '50','lang_id' => '1','name' => 'Bangladesh','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '51','lang_id' => '1','name' => 'Barbados','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '52','lang_id' => '1','name' => 'Belarus','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '53','lang_id' => '1','name' => 'Belize','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '54','lang_id' => '1','name' => 'Benin','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '55','lang_id' => '1','name' => 'Bermuda','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '56','lang_id' => '1','name' => 'Bhutan','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '57','lang_id' => '1','name' => 'Botswana','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '58','lang_id' => '1','name' => 'Brazil','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '59','lang_id' => '1','name' => 'Brunei','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '60','lang_id' => '1','name' => 'Burkina Faso','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '61','lang_id' => '1','name' => 'Burma (Myanmar]','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '62','lang_id' => '1','name' => 'Burundi','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '63','lang_id' => '1','name' => 'Cambodia','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '64','lang_id' => '1','name' => 'Cameroon','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '65','lang_id' => '1','name' => 'Cape Verde','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '66','lang_id' => '1','name' => 'Central African Republic','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '67','lang_id' => '1','name' => 'Chad','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '68','lang_id' => '1','name' => 'Chile','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '69','lang_id' => '1','name' => 'Colombia','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '70','lang_id' => '1','name' => 'Comoros','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '71','lang_id' => '1','name' => 'Congo, Dem. Republic','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '72','lang_id' => '1','name' => 'Congo, Republic','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '73','lang_id' => '1','name' => 'Costa Rica','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '74','lang_id' => '1','name' => 'Croatia','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '75','lang_id' => '1','name' => 'Cuba','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '76','lang_id' => '1','name' => 'Cyprus','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '77','lang_id' => '1','name' => 'Djibouti','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '78','lang_id' => '1','name' => 'Dominica','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '79','lang_id' => '1','name' => 'Dominican Republic','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '80','lang_id' => '1','name' => 'East Timor','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '81','lang_id' => '1','name' => 'Ecuador','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '82','lang_id' => '1','name' => 'Egypt','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '83','lang_id' => '1','name' => 'El Salvador','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '84','lang_id' => '1','name' => 'Equatorial Guinea','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '85','lang_id' => '1','name' => 'Eritrea','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '86','lang_id' => '1','name' => 'Estonia','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '87','lang_id' => '1','name' => 'Ethiopia','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '88','lang_id' => '1','name' => 'Falkland Islands','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '89','lang_id' => '1','name' => 'Faroe Islands','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '90','lang_id' => '1','name' => 'Fiji','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '91','lang_id' => '1','name' => 'Gabon','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '92','lang_id' => '1','name' => 'Gambia','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '93','lang_id' => '1','name' => 'Georgia','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '94','lang_id' => '1','name' => 'Ghana','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '95','lang_id' => '1','name' => 'Grenada','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '96','lang_id' => '1','name' => 'Greenland','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '97','lang_id' => '1','name' => 'Gibraltar','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '98','lang_id' => '1','name' => 'Guadeloupe','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '99','lang_id' => '1','name' => 'Guam','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '100','lang_id' => '1','name' => 'Guatemala','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '101','lang_id' => '1','name' => 'Guernsey','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '102','lang_id' => '1','name' => 'Guinea','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '103','lang_id' => '1','name' => 'Guinea-Bissau','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '104','lang_id' => '1','name' => 'Guyana','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '105','lang_id' => '1','name' => 'Haiti','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '106','lang_id' => '1','name' => 'Heard Island and McDonald Islands','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '107','lang_id' => '1','name' => 'Vatican City State','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '108','lang_id' => '1','name' => 'Honduras','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '109','lang_id' => '1','name' => 'Iceland','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '110','lang_id' => '1','name' => 'India','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '111','lang_id' => '1','name' => 'Indonesia','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '112','lang_id' => '1','name' => 'Iran','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '113','lang_id' => '1','name' => 'Iraq','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '114','lang_id' => '1','name' => 'Man Island','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '115','lang_id' => '1','name' => 'Jamaica','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '116','lang_id' => '1','name' => 'Jersey','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '117','lang_id' => '1','name' => 'Jordan','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '118','lang_id' => '1','name' => 'Kazakhstan','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '119','lang_id' => '1','name' => 'Kenya','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '120','lang_id' => '1','name' => 'Kiribati','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '121','lang_id' => '1','name' => 'Korea, Dem. Republic of','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '122','lang_id' => '1','name' => 'Kuwait','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '123','lang_id' => '1','name' => 'Kyrgyzstan','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '124','lang_id' => '1','name' => 'Laos','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '125','lang_id' => '1','name' => 'Latvia','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '126','lang_id' => '1','name' => 'Lebanon','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '127','lang_id' => '1','name' => 'Lesotho','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '128','lang_id' => '1','name' => 'Liberia','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '129','lang_id' => '1','name' => 'Libya','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '130','lang_id' => '1','name' => 'Liechtenstein','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '131','lang_id' => '1','name' => 'Lithuania','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '132','lang_id' => '1','name' => 'Macau','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '133','lang_id' => '1','name' => 'Macedonia','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '134','lang_id' => '1','name' => 'Madagascar','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '135','lang_id' => '1','name' => 'Malawi','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '136','lang_id' => '1','name' => 'Malaysia','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '137','lang_id' => '1','name' => 'Maldives','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '138','lang_id' => '1','name' => 'Mali','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '139','lang_id' => '1','name' => 'Malta','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '140','lang_id' => '1','name' => 'Marshall Islands','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '141','lang_id' => '1','name' => 'Martinique','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '142','lang_id' => '1','name' => 'Mauritania','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '143','lang_id' => '1','name' => 'Hungary','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '144','lang_id' => '1','name' => 'Mayotte','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '145','lang_id' => '1','name' => 'Mexico','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '146','lang_id' => '1','name' => 'Micronesia','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '147','lang_id' => '1','name' => 'Moldova','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '148','lang_id' => '1','name' => 'Monaco','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '149','lang_id' => '1','name' => 'Mongolia','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '150','lang_id' => '1','name' => 'Montenegro','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '151','lang_id' => '1','name' => 'Montserrat','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '152','lang_id' => '1','name' => 'Morocco','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '153','lang_id' => '1','name' => 'Mozambique','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '154','lang_id' => '1','name' => 'Namibia','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '155','lang_id' => '1','name' => 'Nauru','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '156','lang_id' => '1','name' => 'Nepal','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '157','lang_id' => '1','name' => 'Netherlands Antilles','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '158','lang_id' => '1','name' => 'New Caledonia','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '159','lang_id' => '1','name' => 'Nicaragua','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '160','lang_id' => '1','name' => 'Niger','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '161','lang_id' => '1','name' => 'Niue','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '162','lang_id' => '1','name' => 'Norfolk Island','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '163','lang_id' => '1','name' => 'Northern Mariana Islands','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '164','lang_id' => '1','name' => 'Oman','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '165','lang_id' => '1','name' => 'Pakistan','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '166','lang_id' => '1','name' => 'Palau','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '167','lang_id' => '1','name' => 'Palestinian Territories','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '168','lang_id' => '1','name' => 'Panama','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '169','lang_id' => '1','name' => 'Papua New Guinea','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '170','lang_id' => '1','name' => 'Paraguay','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '171','lang_id' => '1','name' => 'Peru','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '172','lang_id' => '1','name' => 'Philippines','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '173','lang_id' => '1','name' => 'Pitcairn','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '174','lang_id' => '1','name' => 'Puerto Rico','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '175','lang_id' => '1','name' => 'Qatar','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '176','lang_id' => '1','name' => 'Reunion Island','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '177','lang_id' => '1','name' => 'Russian Federation','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '178','lang_id' => '1','name' => 'Rwanda','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '179','lang_id' => '1','name' => 'Saint Barthelemy','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '180','lang_id' => '1','name' => 'Saint Kitts and Nevis','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '181','lang_id' => '1','name' => 'Saint Lucia','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '182','lang_id' => '1','name' => 'Saint Martin','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '183','lang_id' => '1','name' => 'Saint Pierre and Miquelon','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '184','lang_id' => '1','name' => 'Saint Vincent and the Grenadines','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '185','lang_id' => '1','name' => 'Samoa','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '186','lang_id' => '1','name' => 'San Marino','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '187','lang_id' => '1','name' => 'São Tomé and Príncipe','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '188','lang_id' => '1','name' => 'Saudi Arabia','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '189','lang_id' => '1','name' => 'Senegal','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '190','lang_id' => '1','name' => 'Serbia','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '191','lang_id' => '1','name' => 'Seychelles','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '192','lang_id' => '1','name' => 'Sierra Leone','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '193','lang_id' => '1','name' => 'Slovenia','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '194','lang_id' => '1','name' => 'Solomon Islands','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '195','lang_id' => '1','name' => 'Somalia','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '196','lang_id' => '1','name' => 'South Georgia and the South Sandwich Islands','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '197','lang_id' => '1','name' => 'Sri Lanka','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '198','lang_id' => '1','name' => 'Sudan','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '199','lang_id' => '1','name' => 'Suriname','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '200','lang_id' => '1','name' => 'Svalbard and Jan Mayen','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '201','lang_id' => '1','name' => 'Swaziland','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '202','lang_id' => '1','name' => 'Syria','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '203','lang_id' => '1','name' => 'Taiwan','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '204','lang_id' => '1','name' => 'Tajikistan','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '205','lang_id' => '1','name' => 'Tanzania','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '206','lang_id' => '1','name' => 'Thailand','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '207','lang_id' => '1','name' => 'Tokelau','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '208','lang_id' => '1','name' => 'Tonga','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '209','lang_id' => '1','name' => 'Trinidad and Tobago','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '210','lang_id' => '1','name' => 'Tunisia','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '211','lang_id' => '1','name' => 'Turkey','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '212','lang_id' => '1','name' => 'Turkmenistan','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '213','lang_id' => '1','name' => 'Turks and Caicos Islands','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '214','lang_id' => '1','name' => 'Tuvalu','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '215','lang_id' => '1','name' => 'Uganda','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '216','lang_id' => '1','name' => 'Ukraine','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '217','lang_id' => '1','name' => 'United Arab Emirates','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '218','lang_id' => '1','name' => 'Uruguay','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '219','lang_id' => '1','name' => 'Uzbekistan','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '220','lang_id' => '1','name' => 'Vanuatu','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '221','lang_id' => '1','name' => 'Venezuela','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '222','lang_id' => '1','name' => 'Vietnam','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '223','lang_id' => '1','name' => 'Virgin Islands (British]','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '224','lang_id' => '1','name' => 'Virgin Islands (U.S.]','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '225','lang_id' => '1','name' => 'Wallis and Futuna','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '226','lang_id' => '1','name' => 'Western Sahara','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '227','lang_id' => '1','name' => 'Yemen','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '228','lang_id' => '1','name' => 'Zambia','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '229','lang_id' => '1','name' => 'Zimbabwe','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '230','lang_id' => '1','name' => 'Albania','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '231','lang_id' => '1','name' => 'Afghanistan','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '232','lang_id' => '1','name' => 'Antarctica','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '233','lang_id' => '1','name' => 'Bosnia and Herzegovina','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '234','lang_id' => '1','name' => 'Bouvet Island','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '235','lang_id' => '1','name' => 'British Indian Ocean Territory','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '236','lang_id' => '1','name' => 'Bulgaria','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '237','lang_id' => '1','name' => 'Cayman Islands','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '238','lang_id' => '1','name' => 'Christmas Island','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '239','lang_id' => '1','name' => 'Cocos (Keeling] Islands','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '240','lang_id' => '1','name' => 'Cook Islands','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '241','lang_id' => '1','name' => 'French Guiana','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '242','lang_id' => '1','name' => 'French Polynesia','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '243','lang_id' => '1','name' => 'French Southern Territories','created_at' => $date,'updated_at' => $date),
                    array('country_id' => '244','lang_id' => '1','name' => 'Åland Islands','created_at' => $date,'updated_at' => $date)
                ]
            );
        } else {
            echo "SeedingError: `$table_name` table is not empty\n";
        }


    }
}