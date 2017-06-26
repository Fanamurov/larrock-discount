<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class UpdateLarrockCategoryDiscountsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('cart', function(Blueprint $table)
		{
            $table->integer('discount_id')->unsigned()->nullable()->index('category_discount_id_foreign');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::table('cart', function (Blueprint $table) {
            $table->dropColumn('discount_id');
        });
	}

}
