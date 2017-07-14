<?php

use App\Models\City;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class CityTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * @throws Exception
     */
    public function run()
    {
        $txt = Storage::get('cities.txt');

        if (!mb_check_encoding($txt, 'UTF-8')) {
            throw new Exception('Charset is expected to be UTF-8.');
        }

        $cityStack = [];
        $lines = explode(PHP_EOL, $txt);

        $cities = [];
        $cnt = 1;

        foreach ($lines as $line) {
            if (preg_match('/^(\s*)(\d+)(\s*)([^\s#]+)/u', $line, $matches)) {
                $whiteSpace = mb_strlen($matches[1]);
                $code = intval($matches[2]);
                $name = $matches[4];
                while ($whiteSpace < count($cityStack)) {
                    array_pop($cityStack);
                }
                if (empty($cityStack)) {
                    $city = ['id' => $cnt++, 'code' => $code, 'name' => $name, 'parent_id' => null];
                } else {
                    $city = ['id' => $cnt++, 'code' => $code, 'name' => $name, 'parent_id' => end($cityStack)['id']];
                }
                $cities[] = $city;
                array_push($cityStack, $city);
            }
        }
        City::insert($cities);
    }
}
