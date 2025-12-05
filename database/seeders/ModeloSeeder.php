<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ModeloSeeder extends Seeder
{
    public function run(): void
    {
        $modelos = [
            // Ubiquiti - Equipos de radio enlace
            ['nombre' => 'LiteBeam M5'],
            ['nombre' => 'LiteBeam AC'],
            ['nombre' => 'PowerBeam M5'],
            ['nombre' => 'PowerBeam AC'],
            ['nombre' => 'NanoBeam M5'],
            ['nombre' => 'NanoBeam AC'],
            ['nombre' => 'AirMax AC'],
            ['nombre' => 'Rocket M5'],
            ['nombre' => 'Rocket AC'],
            ['nombre' => 'Rocket Prism'],
            ['nombre' => 'AirFiber 5U'],
            ['nombre' => 'AirFiber 24'],
            ['nombre' => 'AirFiber 60'],
            
            // Ubiquiti - Routers y switches
            ['nombre' => 'EdgeRouter X'],
            ['nombre' => 'EdgeRouter Lite'],
            ['nombre' => 'EdgeRouter Pro'],
            ['nombre' => 'EdgeRouter 4'],
            ['nombre' => 'EdgeRouter 6P'],
            ['nombre' => 'EdgeRouter 12'],
            ['nombre' => 'UniFi AP AC Lite'],
            ['nombre' => 'UniFi AP AC Pro'],
            ['nombre' => 'UniFi AP AC LR'],
            ['nombre' => 'UniFi AP HD'],
            ['nombre' => 'UniFi AP nanoHD'],
            ['nombre' => 'UniFi Switch 8'],
            ['nombre' => 'UniFi Switch 16'],
            ['nombre' => 'UniFi Switch 24'],
            ['nombre' => 'UniFi Switch 48'],
            ['nombre' => 'UniFi Dream Machine'],
            ['nombre' => 'UniFi Dream Machine Pro'],
            
            // TP-Link - Switches
            ['nombre' => 'TL-SG1024'],
            ['nombre' => 'TL-SG1016'],
            ['nombre' => 'TL-SG108'],
            ['nombre' => 'TL-SG105'],
            ['nombre' => 'TL-SG116'],
            ['nombre' => 'TL-SG1248'],

            // MikroTik - Routers
            ['nombre' => 'hAp Lite 941'],
            ['nombre' => 'RB750'],
            ['nombre' => 'RB760'],
            ['nombre' => 'RB2011'],
            ['nombre' => 'RB3011'],
            ['nombre' => 'RB4011'],
            ['nombre' => 'CCR1009'],
            ['nombre' => 'CCR1016'],
            ['nombre' => 'CCR2004'],
            ['nombre' => 'CCR2116'],
            ['nombre' => 'hAP ac'],
            ['nombre' => 'hAP ac²'],
            ['nombre' => 'hAP ac³'],
            ['nombre' => 'hAP ax³'],
            ['nombre' => 'wAP ac'],
            ['nombre' => 'wAP ax'],
            ['nombre' => 'SXT Lite5'],
            ['nombre' => 'SXTsq 5'],
            ['nombre' => 'SXTsq 5 ac'],


            // Cambium
            ['nombre' => 'Cambium ePMP 1000'],
            ['nombre' => 'Cambium ePMP 2000'],
            ['nombre' => 'Cambium ePMP 3000'],
            ['nombre' => 'Cambium cnPilot E400'],
            ['nombre' => 'Cambium cnPilot R500'],
            ['nombre' => 'Cambium cnPilot E600'],

            // Otros equipos
            ['nombre' => 'Mimosa A5C'],
            ['nombre' => 'Mimosa B5C'],
            ['nombre' => 'Mimosa C5C'],
            ['nombre' => 'Mimosa C5X'],
            ['nombre' => 'IgniteNet MetroLinq 60'],
            ['nombre' => 'IgniteNet MetroLinq 24'],
            ['nombre' => 'Ruckus R510'],
            ['nombre' => 'Ruckus R710'],
            ['nombre' => 'Aruba 303H'],
            ['nombre' => 'Aruba 505H'],

       
        ];

        DB::table('modelos')->insert($modelos);
    }
}