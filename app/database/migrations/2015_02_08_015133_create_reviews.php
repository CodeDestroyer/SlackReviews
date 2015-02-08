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
			$table->timestamp('submission_time');
			$table->string('submission_user', 50);
			$table->string('repo_link', 100);
			$table->text('submission_comments');
			$table->string('completion_user', 50);
			$table->text('completion_comments');
			$table->timestamp('completion_time');
			$table->string('repo_hex')->unique();
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
