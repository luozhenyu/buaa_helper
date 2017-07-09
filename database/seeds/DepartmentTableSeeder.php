<?php

use App\Models\Department;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DepartmentTableSeeder extends Seeder
{
    const data = [
        [
            'number' => 1,
            'name' => '材料科学与工程学院',
            'avatar' => 'departmentLogo/1.png',
            'description' => '材料科学与工程学院',
        ],

        [
            'number' => 2,
            'name' => '电子信息工程学院',
            'avatar' => 'departmentLogo/2.png',
            'description' => '电子信息工程学院',
        ],

        [
            'number' => 3,
            'name' => '自动化科学与电气工程学院',
            'avatar' => 'departmentLogo/3.png',
            'description' => '自动化科学与电气工程学院',
        ],

        [
            'number' => 4,
            'name' => '能源与动力工程学院',
            'avatar' => 'departmentLogo/4.png',
            'description' => '能源与动力工程学院',
        ],

        [
            'number' => 5,
            'name' => '航空科学与工程学院',
            'avatar' => 'departmentLogo/5.png',
            'description' => '航空科学与工程学院',
        ],

        [
            'number' => 6,
            'name' => '计算机学院',
            'avatar' => 'departmentLogo/6.png',
            'description' => '计算机学院',
        ],

        [
            'number' => 7,
            'name' => '机械工程及自动化学院',
            'avatar' => 'departmentLogo/7.png',
            'description' => '机械工程及自动化学院',
        ],

        [
            'number' => 8,
            'name' => '经济管理学院',
            'avatar' => 'departmentLogo/8.png',
            'description' => '经济管理学院',
        ],

        [
            'number' => 9,
            'name' => '数学与系统科学学院',
            'avatar' => 'departmentLogo/9.png',
            'description' => '数学与系统科学学院',
        ],

        [
            'number' => 10,
            'name' => '生物与医学工程学院',
            'avatar' => 'departmentLogo/10.png',
            'description' => '生物与医学工程学院',
        ],

        [
            'number' => 11,
            'name' => '人文社会科学学院',
            'avatar' => 'departmentLogo/11.png',
            'description' => '人文社会科学学院',
        ],

        [
            'number' => 12,
            'name' => '外国语学院',
            'avatar' => 'departmentLogo/12.png',
            'description' => '外国语学院',
        ],

        [
            'number' => 13,
            'name' => '交通科学与工程学院',
            'avatar' => 'departmentLogo/13.png',
            'description' => '交通科学与工程学院',
        ],

        [
            'number' => 14,
            'name' => '可靠性与系统工程学院',
            'avatar' => 'departmentLogo/14.png',
            'description' => '可靠性与系统工程学院',
        ],

        [
            'number' => 15,
            'name' => '宇航学院',
            'avatar' => 'departmentLogo/15.png',
            'description' => '宇航学院',
        ],

        [
            'number' => 16,
            'name' => '飞行学院',
            'avatar' => 'departmentLogo/16.png',
            'description' => '飞行学院',
        ],

        [
            'number' => 17,
            'name' => '仪器科学与光电工程学院',
            'avatar' => 'departmentLogo/17.png',
            'description' => '仪器科学与光电工程学院',
        ],

        [
            'number' => 18,
            'name' => '北京学院',
            'avatar' => 'departmentLogo/18.png',
            'description' => '北京学院',
        ],

        [
            'number' => 19,
            'name' => '物理科学与核能工程学院',
            'avatar' => 'departmentLogo/19.png',
            'description' => '物理科学与核能工程学院',
        ],

        [
            'number' => 20,
            'name' => '法学院',
            'avatar' => 'departmentLogo/20.png',
            'description' => '法学院',
        ],

        [
            'number' => 21,
            'name' => '软件学院',
            'avatar' => 'departmentLogo/21.png',
            'description' => '软件学院',
        ],

        [
            'number' => 22,
            'name' => '现代远程教育学院',
            'avatar' => 'departmentLogo/22.png',
            'description' => '现代远程教育学院',
        ],

        [
            'number' => 23,
            'name' => '高等理工学院',
            'avatar' => 'departmentLogo/23.png',
            'description' => '高等理工学院',
        ],

        [
            'number' => 24,
            'name' => '中法工程师学院',
            'avatar' => 'departmentLogo/24.png',
            'description' => '中法工程师学院',
        ],

        [
            'number' => 25,
            'name' => '国际学院',
            'avatar' => 'departmentLogo/25.png',
            'description' => '国际学院',
        ],

        [
            'number' => 26,
            'name' => '新媒体艺术与设计学院',
            'avatar' => 'departmentLogo/26.png',
            'description' => '新媒体艺术与设计学院',
        ],

        [
            'number' => 27,
            'name' => '化学与环境学院',
            'avatar' => 'departmentLogo/27.png',
            'description' => '化学与环境学院',
        ],

        [
            'number' => 28,
            'name' => '马克思主义学院',
            'avatar' => 'departmentLogo/28.png',
            'description' => '马克思主义学院',
        ],

        [
            'number' => 29,
            'name' => '人文与社会科学高等研究院',
            'avatar' => 'departmentLogo/29.png',
            'description' => '人文与社会科学高等研究院',
        ],

        [
            'number' => 30,
            'name' => '空间与环境学院',
            'avatar' => 'departmentLogo/30.png',
            'description' => '空间与环境学院',
        ],

        [
            'number' => 71,
            'name' => '启明书院',
            'avatar' => 'departmentLogo/71.png',
            'description' => '启明书院',
        ],

        [
            'number' => 72,
            'name' => '冯如书院',
            'avatar' => 'departmentLogo/72.png',
            'description' => '冯如书院',
        ],

        [
            'number' => 79,
            'name' => '知行书院',
            'avatar' => 'departmentLogo/79.png',
            'description' => '知行书院',
        ],

        [
            'number' => 101,
            'name' => '学生处',
            'avatar' => 'departmentLogo/101.png',
            'description' => '学生处',
        ],

        [
            'number' => 102,
            'name' => '安全保卫处',
            'avatar' => 'departmentLogo/102.png',
            'description' => '安全保卫处',
        ],

        [
            'number' => 103,
            'name' => '教务处',
            'avatar' => 'departmentLogo/103.png',
            'description' => '教务处',
        ],

        [
            'number' => 104,
            'name' => '后勤保障处',
            'avatar' => 'departmentLogo/104.png',
            'description' => '后勤保障处',
        ],

        [
            'number' => 105,
            'name' => '国际交流合作处',
            'avatar' => 'departmentLogo/105.png',
            'description' => '国际交流合作处',
        ],

        [
            'number' => 106,
            'name' => '沙河校区',
            'avatar' => 'departmentLogo/106.png',
            'description' => '沙河校区',
        ],

        [
            'number' => 107,
            'name' => '财务处',
            'avatar' => 'departmentLogo/107.png',
            'description' => '财务处',
        ],

        [
            'number' => 108,
            'name' => '招生就业处',
            'avatar' => 'departmentLogo/108.png',
            'description' => '招生就业处',
        ],

        [
            'number' => 109,
            'name' => '网络信息中心',
            'avatar' => 'departmentLogo/109.png',
            'description' => '网络信息中心',
        ],

        [
            'number' => 110,
            'name' => '校团委',
            'avatar' => 'departmentLogo/110.png',
            'description' => '校团委',
        ],

        [
            'number' => 111,
            'name' => '档案馆',
            'avatar' => 'departmentLogo/111.png',
            'description' => '档案馆',
        ],

        [
            'number' => 112,
            'name' => '图书馆',
            'avatar' => 'departmentLogo/112.png',
            'description' => '图书馆',
        ],

        [
            'number' => 113,
            'name' => '校医院',
            'avatar' => 'departmentLogo/113.png',
            'description' => '校医院',
        ],

        [
            'number' => 114,
            'name' => '勤工助学中心',
            'avatar' => 'departmentLogo/114.png',
            'description' => '勤工助学中心',
        ],
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::beginTransaction();
        foreach (static::data as $item) {
            Department::create([
                'number' => $item['number'],
                'name' => $item['name'],
                'description' => $item['description'],
            ]);
        }
        DB::commit();
    }
}
