<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateDiscountTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('discount', function(Blueprint $table)
		{
			$table->increments('id');
			$table->char('title');
			$table->text('description');
			$table->char('url', 191)->unique();
			$table->char('type');
			$table->char('word');
			$table->integer('cost_min');
			$table->integer('cost_max');
			$table->integer('percent');
			$table->integer('num');
			$table->integer('d_count');
			$table->timestamp('date_start')->nullable();
			$table->dateTime('date_end')->nullable();
			$table->integer('position')->default(0);
			$table->integer('active')->default(1);
			$table->timestamps();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('discount');
	}

}