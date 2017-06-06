<?php

use App\Models\City;
use App\Models\Property;
use Illuminate\Database\Seeder;

class PropertyTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->createGrade();
        $this->createClass();
        $this->createPoliticalStatus();
        $this->createNativePlace();
        $this->createFinancialDifficulty();
    }

    private function createGrade()
    {
        $propertyValues = Property::create([
            'name' => 'grade',
            'display_name' => '年级',
            'description' => '年级',
        ])->propertyValues();
        $propertyValues->create(['name' => 1, 'display_name' => '大一']);
        $propertyValues->create(['name' => 2, 'display_name' => '大二']);
        $propertyValues->create(['name' => 3, 'display_name' => '大三']);
        $propertyValues->create(['name' => 4, 'display_name' => '大四']);
    }

    private function createClass()
    {
        $propertyValues = Property::create([
            'name' => 'class',
            'display_name' => '班级',
            'description' => '班级',
        ])->propertyValues();
        $propertyValues->create(['name' => 1, 'display_name' => '一班']);
        $propertyValues->create(['name' => 2, 'display_name' => '二班']);
        $propertyValues->create(['name' => 3, 'display_name' => '三班']);
        $propertyValues->create(['name' => 4, 'display_name' => '四班']);
        $propertyValues->create(['name' => 5, 'display_name' => '五班']);
        $propertyValues->create(['name' => 5, 'display_name' => '六班']);
        $propertyValues->create(['name' => 5, 'display_name' => '七班']);
        $propertyValues->create(['name' => 5, 'display_name' => '八班']);
        $propertyValues->create(['name' => 5, 'display_name' => '九班']);
        $propertyValues->create(['name' => 5, 'display_name' => '十班']);
    }

    private function createPoliticalStatus()
    {
        $propertyValues = Property::create([
            'name' => 'political_status',
            'display_name' => '政治面貌',
            'description' => '政治面貌',
        ])->propertyValues();
        $propertyValues->create(['name' => 1, 'display_name' => '中共党员']);
        $propertyValues->create(['name' => 2, 'display_name' => '中共预备党员']);
        $propertyValues->create(['name' => 3, 'display_name' => '共青团员']);
        $propertyValues->create(['name' => 4, 'display_name' => '群众']);
    }

    private function createNativePlace()
    {
        $propertyValues = Property::create([
            'name' => 'native_place',
            'display_name' => '籍贯',
            'description' => '籍贯',
        ])->propertyValues();
        City::get()->each(function ($item, $key) use ($propertyValues) {
            $propertyValues->create(['name' => $item->code, 'display_name' => $item->name]);
        });
    }

    private function createFinancialDifficulty()
    {
        $propertyValues = Property::create([
            'name' => 'financial_difficulty',
            'display_name' => '经济困难',
            'description' => '经济困难',
        ])->propertyValues();
        $propertyValues->create(['name' => 0, 'display_name' => '否']);
        $propertyValues->create(['name' => 1, 'display_name' => '一般困难']);
        $propertyValues->create(['name' => 2, 'display_name' => '特别困难']);
    }
}
