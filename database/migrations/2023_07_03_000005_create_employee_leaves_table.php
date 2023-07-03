<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeeLeavesTable extends Migration
{
    public function up()
    {
        Schema::create('employee_leaves', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->date('start_date');
            $table->date('end_date');
            $table->string('line_manager_approval')->nullable();
            $table->string('hr_approval')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
