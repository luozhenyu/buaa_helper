<?php

use App\Models\City;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
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

        DB::beginTransaction();
        foreach ($lines as $line) {
            if (preg_match('/^(\s*)(\d+)(\s*)([^\s#]+)/u', $line, $matches)) {
                $whiteSpace = mb_strlen($matches[1]);
                $code = intval($matches[2]);
                $name = $matches[4];
                while ($whiteSpace < count($cityStack)) {
                    array_pop($cityStack);
                }
                if (empty($cityStack)) {
                    $city = City::create(['code' => $code, 'name' => $name]);
                } else {
                    $city = end($cityStack)->children()->create(['code' => $code, 'name' => $name]);
                }
                array_push($cityStack, $city);
            }
        }
        DB::commit();
    }
}
