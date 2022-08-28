<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateCustomers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $partition="CREATE TABLE customers (
            id_cus_shopify BIGINT UNSIGNED NOT NULL,
            id_shops BIGINT,
            first_name VARCHAR(150) NULL,
            last_name VARCHAR(150) NULL,
            country VARCHAR(255) NULL,
            phone VARCHAR(100) NULL,
            email VARCHAR(150),
            total_order INT NULL,
            total_spent FLOAT NULL,
            cus_created_at DATETIME NOT NULL,
            created_at TIMESTAMP NULL,
            updated_at TIMESTAMP NULL,
            PRIMARY KEY (`id_cus_shopify`,`cus_created_at`)
            )
            PARTITION BY RANGE COLUMNS(cus_created_at)(
            PARTITION m0_n21 VALUES LESS THAN ('2022-01-01'), 
            PARTITION m1_n22 VALUES LESS THAN ('2022-02-01'), 
            PARTITION m2_n22 VALUES LESS THAN ('2022-03-01'), 
            PARTITION m3_n22 VALUES LESS THAN ('2022-04-01'), 
            PARTITION m4_n22 VALUES LESS THAN ('2022-05-01'), 
            PARTITION m5_n22 VALUES LESS THAN ('2022-06-01'), 
            PARTITION m6_n22 VALUES LESS THAN ('2022-07-01'), 
            PARTITION m7_n22 VALUES LESS THAN ('2022-08-01'), 
            PARTITION m8_n22 VALUES LESS THAN ('2022-09-01'), 
            PARTITION m9_n22 VALUES LESS THAN ('2022-10-01'), 
            PARTITION m10_n22 VALUES LESS THAN ('2022-11-01'), 
            PARTITION m11_n22 VALUES LESS THAN ('2022-12-01'),
            PARTITION m12_n22 VALUES LESS THAN ('2023-01-01'),
            PARTITION m1_n23 VALUES LESS THAN (MAXVALUE)
            );";
        DB::unprepared($partition);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
       
        Schema::dropIfExists('customers');
    }
}
