<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ItEquipmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['department' => 'Gerencia', 'responsible' => 'William McLane', 'article' => 'Laptop', 'brand' => 'Lenovo', 'model' => 'Idealpad 3', 'serial_number' => 'PF62TVKA', 'image_url' => null, 'success_code' => null],
            ['department' => 'Gerencia', 'responsible' => 'William McLane', 'article' => 'Monitor Curvo', 'brand' => 'Xundefined', 'model' => 'G270CRa', 'serial_number' => 'PF62TVKA', 'image_url' => null, 'success_code' => null],
            ['department' => 'Sistemas', 'responsible' => 'Stefany Hernández', 'article' => 'PC', 'brand' => 'Asus', 'model' => 'DESKTOP-749N1KR', 'serial_number' => 'YCM10823120200588', 'image_url' => null, 'success_code' => null],
            ['department' => 'Sistemas', 'responsible' => 'Stefany Hernández', 'article' => 'Monitor', 'brand' => 'Benq', 'model' => 'GW2480', 'serial_number' => 'GW2480-T', 'image_url' => null, 'success_code' => null],
            ['department' => 'Sistemas', 'responsible' => 'Emilio', 'article' => 'Laptop', 'brand' => 'Lenovo IDEAL PAD 3', 'model' => 'PF9XB2504004', 'serial_number' => 'PF3Q1Q0C', 'image_url' => null, 'success_code' => null],
            ['department' => 'I+D', 'responsible' => 'Claudio', 'article' => 'Laptop', 'brand' => 'Lenovo IDEAL PAD 3', 'model' => 'PF9XB2504004', 'serial_number' => 'PF3PYXG5', 'image_url' => null, 'success_code' => null],
            ['department' => 'Calidad', 'responsible' => 'Cristhel', 'article' => 'Laptop', 'brand' => 'Lenovo IDEAL PAD 3', 'model' => 'PF9XB2504004', 'serial_number' => 'PF445FR4', 'image_url' => null, 'success_code' => null],
            ['department' => 'Oficina', 'responsible' => null, 'article' => 'PC', 'brand' => 'HP ALL IN ONE PC', 'model' => '24-df0011la', 'serial_number' => '8CC116321M', 'image_url' => null, 'success_code' => null],
            ['department' => 'Finanzas', 'responsible' => 'Rocio Galvan', 'article' => 'Laptop', 'brand' => 'HP', 'model' => '15-fd0007la', 'serial_number' => '5CD3164YF3', 'image_url' => null, 'success_code' => null],
            ['department' => 'Laboratorio', 'responsible' => 'Ethan', 'article' => 'PC', 'brand' => 'Lenovo', 'model' => 'IDEACENTRE AIO 3', 'serial_number' => 'MP297P0F', 'image_url' => null, 'success_code' => null],
            ['department' => 'Laboratorio', 'responsible' => 'Ethan', 'article' => 'PC', 'brand' => 'Dell', 'model' => 'INSPIRON 5400 AIO', 'serial_number' => 'M5NX6A00', 'image_url' => null, 'success_code' => null],
            ['department' => 'RH', 'responsible' => 'Manola', 'article' => 'PC', 'brand' => 'HP ALL IN ONE PC', 'model' => '24-df0011la', 'serial_number' => null, 'image_url' => null, 'success_code' => null],
            ['department' => 'Almacen', 'responsible' => 'Pedro', 'article' => 'PC', 'brand' => 'Dell', 'model' => 'INSPIRON 5400 AIO', 'serial_number' => null, 'image_url' => null, 'success_code' => null],
            ['department' => 'Oficina', 'responsible' => 'Servidor', 'article' => 'PC', 'brand' => 'Dell', 'model' => 'Reloj', 'serial_number' => '62206142985', 'image_url' => null, 'success_code' => null],
            ['department' => 'Oficina', 'responsible' => 'Servidor', 'article' => 'Monitor', 'brand' => 'Hacer', 'model' => 'V206HQL', 'serial_number' => 'MMLY6AA01E2300813285GL', 'image_url' => null, 'success_code' => null],
        ];

        foreach ($data as $item) {
            $item['created_at'] = now();
            $item['updated_at'] = now();
            DB::table('it_equipments')->insert($item);
        }
    }
}
