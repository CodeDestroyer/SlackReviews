<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReviews extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('reviews', function($table)
		{
			$table->increments('id');
			$table->timestamp('submission_time')->nullable();
			$table->string('submission_user', 50)->nullable();
			$table->string('repo_link', 100)->nullable();
			$table->text('submission_comments')->nullable();
			$table->string('completion_user', 50)->nullable()->default(null);
			$table->text('completion_comments')->nullable();
			$table->timestamp('completion_time')->nullable();
			$table->string('repo_hex')->unique()->nullable();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('reviews');
	}

}
